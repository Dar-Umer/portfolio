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
                $updateData['resolved_at'] = Carbon::now();
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
}
