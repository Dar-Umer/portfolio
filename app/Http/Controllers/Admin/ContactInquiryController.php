<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\Audit\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactInquiryController extends Controller
{
    public function index(Request $request): View
    {
        $query = ContactMessage::query();

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $inquiries = $query->latest()->paginate(15)->withQueryString();
        $unreadCount = ContactMessage::where('is_read', false)->count();

        return view('admin.inquiries.index', compact('inquiries', 'unreadCount'));
    }

    public function markRead(ContactMessage $inquiry): RedirectResponse
    {
        $newStatus = !$inquiry->is_read;
        $inquiry->update(['is_read' => $newStatus]);

        AuditService::log('inquiry_status_updated', null, "Marked inquiry #{$inquiry->id} from {$inquiry->name} as " . ($newStatus ? 'read' : 'unread'));

        return back()->with('success', 'Inquiry status updated.');
    }

    public function convertToClient(ContactMessage $inquiry): RedirectResponse
    {
        $inquiry->update(['is_read' => true]);

        return redirect()->route('admin.clients.create', [
            'name' => $inquiry->name,
            'email' => $inquiry->email,
            'phone' => $inquiry->phone,
            'notes' => "Lead inquiry: {$inquiry->subject} - {$inquiry->message}",
        ])->with('info', "Client details pre-filled from lead inquiry #{$inquiry->id}.");
    }

    public function destroy(ContactMessage $inquiry): RedirectResponse
    {
        $inquiry->delete();
        AuditService::log('inquiry_deleted', null, "Deleted inquiry #{$inquiry->id} from {$inquiry->name}");

        return back()->with('success', 'Inquiry deleted successfully.');
    }
}
