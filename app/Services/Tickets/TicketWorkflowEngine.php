<?php

namespace App\Services\Tickets;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketStatusHistory;
use App\Models\User;
use App\Services\Audit\AuditService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class TicketWorkflowEngine
{
    /**
     * Compute SLA due dates based on priority.
     */
    public static function calculateSla(string $priority): array
    {
        $now = Carbon::now();

        return match ($priority) {
            'Critical' => [
                'first_response_due_at' => $now->copy()->addHours(2),
                'resolution_due_at' => $now->copy()->addHours(12),
            ],
            'High' => [
                'first_response_due_at' => $now->copy()->addHours(4),
                'resolution_due_at' => $now->copy()->addHours(24),
            ],
            'Normal' => [
                'first_response_due_at' => $now->copy()->addHours(8),
                'resolution_due_at' => $now->copy()->addHours(48),
            ],
            default => [ // Low
                'first_response_due_at' => $now->copy()->addHours(24),
                'resolution_due_at' => $now->copy()->addHours(96),
            ],
        };
    }

    /**
     * Transition ticket status following defined workflow rules.
     */
    public static function transition(Ticket $ticket, TicketStatus $newStatus, ?User $actor = null, ?string $reason = null, array $resolutionData = []): Ticket
    {
        return DB::transaction(function () use ($ticket, $newStatus, $actor, $reason, $resolutionData) {
            $currentStatus = $ticket->status;

            // Enforce permitted workflow transitions if defined
            if ($currentStatus && !empty($currentStatus->allowed_transitions)) {
                if (!in_array($newStatus->slug, $currentStatus->allowed_transitions)) {
                    throw new Exception("Invalid status transition from '{$currentStatus->name}' to '{$newStatus->name}'.");
                }
            }

            $updateData = [
                'ticket_status_id' => $newStatus->id,
            ];

            // If transitioning to a resolved state, validate and save resolution details
            if ($newStatus->is_resolved) {
                $now = Carbon::now();
                $updateData['resolved_at'] = $now;
                if ($ticket->resolution_due_at && $now->gt($ticket->resolution_due_at)) {
                    $updateData['is_sla_breached'] = true;
                }
                if (!empty($resolutionData['root_cause'])) {
                    $updateData['root_cause'] = $resolutionData['root_cause'];
                }
                if (!empty($resolutionData['fix_description'])) {
                    $updateData['fix_description'] = $resolutionData['fix_description'];
                }
                if (!empty($resolutionData['fixed_version'])) {
                    $updateData['fixed_version'] = $resolutionData['fixed_version'];
                }
                if (!empty($resolutionData['customer_instructions'])) {
                    $updateData['customer_instructions'] = $resolutionData['customer_instructions'];
                }
            }

            if ($newStatus->is_closed) {
                $updateData['closed_at'] = Carbon::now();
            }

            // Update ticket
            $ticket->update($updateData);

            // Record history
            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'changed_by_user_id' => $actor?->id,
                'from_status' => $currentStatus ? $currentStatus->name : 'None',
                'to_status' => $newStatus->name,
                'reason' => $reason,
            ]);

            // Audit
            AuditService::log('ticket_status_transition', $ticket, "Changed ticket {$ticket->ticket_number} status from {$currentStatus?->name} to {$newStatus->name}", [
                'from_status' => $currentStatus?->slug,
                'to_status' => $newStatus->slug,
                'reason' => $reason,
            ]);

            return $ticket->fresh(['status', 'statusHistories']);
        });
    }

    /**
     * Track staff responses, calculate first response SLA compliance, and advance initial ticket status.
     */
    public static function recordStaffResponse(Ticket $ticket, \App\Models\TicketComment $comment, ?User $actor = null): void
    {
        if ($comment->is_internal_note) {
            return;
        }

        $now = Carbon::now();
        $updates = [];

        if (is_null($ticket->first_responded_at)) {
            $updates['first_responded_at'] = $now;
            if ($ticket->first_response_due_at && $now->gt($ticket->first_response_due_at)) {
                $updates['is_sla_breached'] = true;
            }
        }

        if (!empty($updates)) {
            $ticket->update($updates);
        }
    }

    /**
     * Automatically handle customer responses by moving tickets out of waiting or resolved states.
     */
    public static function handleCustomerReply(Ticket $ticket, \App\Models\TicketComment $comment, ?User $actor = null): void
    {
        $currentSlug = $ticket->status?->slug;

        // If awaiting customer response, advance back to In Progress
        if ($currentSlug === 'awaiting-customer') {
            $inProgressStatus = TicketStatus::where('slug', 'in-progress')->first();
            if ($inProgressStatus) {
                self::transition($ticket, $inProgressStatus, $actor, 'Customer replied via client portal');
            }
        } elseif ($currentSlug === 'resolved') {
            // If resolved, automatically reopen the ticket
            $reopenedStatus = TicketStatus::where('slug', 'reopened')->first();
            if ($reopenedStatus) {
                self::transition($ticket, $reopenedStatus, $actor, 'Customer reopened ticket via reply in client portal');
            }
        }

        $ticket->touch();
    }

    /**
     * Batch check and flag any tickets that breached SLA due dates.
     */
    public static function checkAndFlagSlaBreaches(): int
    {
        $now = Carbon::now();

        return Ticket::where('is_sla_breached', false)
            ->where(function ($q) use ($now) {
                $q->where(function ($sq) use ($now) {
                    $sq->whereNull('first_responded_at')
                       ->whereNotNull('first_response_due_at')
                       ->where('first_response_due_at', '<', $now);
                })->orWhere(function ($sq) use ($now) {
                    $sq->whereNull('resolved_at')
                       ->whereNotNull('resolution_due_at')
                       ->where('resolution_due_at', '<', $now);
                });
            })
            ->update(['is_sla_breached' => true]);
    }
}
