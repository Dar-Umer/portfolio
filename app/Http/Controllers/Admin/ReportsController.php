<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Project Distribution by Status
        $projectsByStatus = Project::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // 2. Ticket Distribution by Priority
        $ticketsByPriority = Ticket::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->pluck('total', 'priority')
            ->toArray();

        // 3. Ticket Distribution by Status
        $ticketsByStatus = Ticket::with('status')
            ->select('ticket_status_id', DB::raw('count(*) as total'))
            ->groupBy('ticket_status_id')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status->name => $item->total])
            ->toArray();

        // 4. Financial Monthly Trends (Last 6 Months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $yearMonth = $month->format('Y-m');
            $label = $month->format('M Y');

            $invoiced = (float) Invoice::where('issue_date', 'like', "{$yearMonth}%")->sum('grand_total');
            $collected = (float) Payment::where('status', 'completed')->where('payment_date', 'like', "{$yearMonth}%")->sum('amount');

            $monthlyRevenue[] = [
                'month' => $label,
                'invoiced' => $invoiced,
                'collected' => $collected,
            ];
        }

        // 5. High-Level Summary Stats
        $stats = [
            'total_receivables' => (float) Invoice::sum('balance_due'),
            'total_invoiced' => (float) Invoice::sum('grand_total'),
            'total_collected' => (float) Payment::where('status', 'completed')->sum('amount'),
            'sla_breached_tickets' => Ticket::where('is_sla_breached', true)->count(),
            'total_tickets' => Ticket::count(),
            'total_projects' => Project::count(),
            'total_clients' => ClientProfile::count(),
        ];

        return view('admin.reports.index', compact('projectsByStatus', 'ticketsByPriority', 'ticketsByStatus', 'monthlyRevenue', 'stats'));
    }

    public function exportCsv(string $type): Response
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($type) {
            $file = fopen('php://output', 'w');

            if ($type === 'invoices') {
                fputcsv($file, ['Invoice Number', 'Client', 'Issue Date', 'Due Date', 'Grand Total', 'Amount Paid', 'Balance Due', 'Status']);
                $invoices = Invoice::with('client.user')->latest()->get();
                foreach ($invoices as $inv) {
                    fputcsv($file, [
                        $inv->invoice_number,
                        $inv->client_snapshot['company_name'] ?? ($inv->client->company_name ?? $inv->client->user->name),
                        $inv->issue_date->format('Y-m-d'),
                        $inv->due_date->format('Y-m-d'),
                        $inv->grand_total,
                        $inv->amount_paid,
                        $inv->balance_due,
                        $inv->status,
                    ]);
                }
            } elseif ($type === 'tickets') {
                fputcsv($file, ['Ticket Number', 'Subject', 'Client', 'Priority', 'Status', 'Issue Type', 'Created Date', 'Resolved Date']);
                $tickets = Ticket::with(['client.user', 'status'])->latest()->get();
                foreach ($tickets as $tkt) {
                    fputcsv($file, [
                        $tkt->ticket_number,
                        $tkt->subject,
                        $tkt->client->company_name ?? $tkt->client->user->name,
                        $tkt->priority,
                        $tkt->status->name,
                        $tkt->issue_type,
                        $tkt->created_at->format('Y-m-d H:i:s'),
                        $tkt->resolved_at ? $tkt->resolved_at->format('Y-m-d H:i:s') : 'N/A',
                    ]);
                }
            } elseif ($type === 'payments') {
                fputcsv($file, ['Receipt Number', 'Client', 'Date', 'Amount', 'Payment Method', 'Transaction Reference', 'Status']);
                $payments = Payment::with('client.user')->latest()->get();
                foreach ($payments as $p) {
                    fputcsv($file, [
                        $p->receipt_number,
                        $p->client->company_name ?? $p->client->user->name,
                        $p->payment_date->format('Y-m-d'),
                        $p->amount,
                        $p->payment_method,
                        $p->transaction_reference ?? 'N/A',
                        $p->status,
                    ]);
                }
            }

            fclose($file);
        };

        $filename = "export_{$type}_" . date('Ymd_His') . ".csv";
        $headers['Content-Disposition'] = "attachment; filename=\"{$filename}\"";

        return response()->stream($callback, 200, $headers);
    }
}
