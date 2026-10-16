<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\ProjectTimelineEvent;
use App\Models\User;
use App\Services\Audit\AuditService;
use App\Services\Common\DocumentNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Project::with(['client.user', 'milestones', 'tickets']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('project_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->latest()->paginate(15)->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        $clients = ClientProfile::with('user')->where('account_status', 'active')->get();
        $categoriesSetting = \App\Models\Setting::get('project_categories');
        if ($categoriesSetting) {
            $categories = array_filter(array_map('trim', explode(',', $categoriesSetting)));
        } else {
            $categories = ['Web Application', 'SaaS Platform', 'API & Microservices', 'DevOps & Infrastructure', 'Mobile API'];
        }
        return view('admin.projects.create', compact('clients', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_profile_id' => ['required', 'exists:client_profiles,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'expected_delivery_date' => ['nullable', 'date'],
            'priority' => ['required', 'in:Low,Normal,High,Critical'],
            'budget' => ['required', 'numeric', 'min:0'],
            'technologies' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $projectNumber = DocumentNumberService::generate('PRJ', 'projects', 'project_number');
        $slug = Str::slug($validated['name']) . '-' . strtolower(Str::random(5));

        $techArray = !empty($validated['technologies'])
            ? array_map('trim', explode(',', $validated['technologies']))
            : [];

        $project = Project::create([
            'client_profile_id' => $validated['client_profile_id'],
            'project_number' => $projectNumber,
            'name' => $validated['name'],
            'slug' => $slug,
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
            'status' => 'Planning',
            'priority' => $validated['priority'],
            'progress_percentage' => 0,
            'budget' => $validated['budget'],
            'technologies' => $techArray,
            'internal_notes' => $validated['internal_notes'] ?? null,
        ]);

        // Record initial timeline event
        ProjectTimelineEvent::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'event_type' => 'created',
            'title' => 'Project Initiated',
            'description' => "Project {$project->name} ({$project->project_number}) initialized in Planning stage.",
            'is_client_visible' => true,
        ]);

        AuditService::log('project_created', $project, "Created project {$project->project_number} ({$project->name})");

        return redirect()->route('admin.projects.show', $project)->with('success', "Project {$project->project_number} created successfully.");
    }

    public function show(Project $project): View
    {
        $project->load([
            'client.user',
            'milestones',
            'timelineEvents.user',
            'members.user',
            'repositories.syncLogs',
            'requirements.user',
            'tickets.status',
            'invoices.allocations',
            'quotations',
            'documents.uploader',
        ]);

        $developers = User::role(['Super Admin', 'Administrator', 'Developer'])->get();

        return view('admin.projects.show', compact('project', 'developers'));
    }

    public function addMilestone(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'acceptance_criteria' => ['nullable', 'string'],
            'demo_url' => ['nullable', 'url'],
            'deliverable_type' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'is_client_visible' => ['nullable', 'boolean'],
        ]);

        $milestone = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'acceptance_criteria' => $validated['acceptance_criteria'],
            'demo_url' => $validated['demo_url'],
            'deliverable_type' => $validated['deliverable_type'] ?? 'Feature',
            'due_date' => $validated['due_date'],
            'status' => 'pending',
            'progress' => 0,
            'is_client_visible' => $request->boolean('is_client_visible', true),
            'sort_order' => $project->milestones()->count() + 1,
        ]);

        ProjectTimelineEvent::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'event_type' => 'milestone_added',
            'title' => "Milestone Created: {$milestone->title}",
            'description' => "Added deliverable scope ({$milestone->deliverable_type}).",
            'is_client_visible' => $milestone->is_client_visible,
        ]);

        return back()->with('success', 'Deliverable milestone added successfully.');
    }

    public function convertRequirementToMilestone(Request $request, Project $project, \App\Models\ProjectRequirement $requirement): RedirectResponse
    {
        $request->validate([
            'due_date' => ['nullable', 'date'],
            'staff_feedback' => ['nullable', 'string'],
        ]);

        $milestone = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => $requirement->title,
            'description' => $requirement->details,
            'deliverable_type' => 'Feature',
            'due_date' => $request->due_date,
            'status' => 'pending',
            'progress' => 0,
            'is_client_visible' => true,
            'requested_by_user_id' => $requirement->user_id,
            'sort_order' => $project->milestones()->count() + 1,
        ]);

        $requirement->update([
            'status' => 'Planned',
            'converted_milestone_id' => $milestone->id,
            'staff_feedback' => $request->staff_feedback ?: 'Approved and converted into sprint deliverable.',
        ]);

        $project->recalculateProgress();

        ProjectTimelineEvent::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'event_type' => 'milestone_added',
            'title' => "Requirement Scheduled: {$milestone->title}",
            'description' => "Client requirement accepted into project deliverables.",
            'is_client_visible' => true,
        ]);

        return back()->with('success', "Requirement '{$requirement->title}' converted into project deliverable milestone!");
    }

    public function storeRequirement(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string'],
            'priority' => ['required', 'in:Low,Medium,High,Critical'],
            'status' => ['required', 'in:Submitted,Under Review,Planned,Completed,Rejected'],
            'staff_feedback' => ['nullable', 'string'],
        ]);

        $project->requirements()->create([
            'user_id' => $project->client->user_id,
            'title' => $validated['title'],
            'details' => $validated['details'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'staff_feedback' => $validated['staff_feedback'],
        ]);

        AuditService::log('project_requirement_created', $project, "Admin added client requirement: {$validated['title']}");

        return back()->with('success', "Requirement '{$validated['title']}' added successfully.");
    }

    public function updateRequirement(Request $request, Project $project, \App\Models\ProjectRequirement $requirement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string'],
            'priority' => ['required', 'in:Low,Medium,High,Critical'],
            'status' => ['required', 'in:Submitted,Under Review,Planned,Completed,Rejected'],
            'staff_feedback' => ['nullable', 'string'],
        ]);

        $requirement->update($validated);

        AuditService::log('project_requirement_updated', $project, "Updated project requirement #{$requirement->id} ({$requirement->title})");

        return back()->with('success', "Requirement '{$requirement->title}' updated successfully.");
    }

    public function destroyRequirement(Project $project, \App\Models\ProjectRequirement $requirement): RedirectResponse
    {
        $title = $requirement->title;
        $requirement->delete();

        AuditService::log('project_requirement_deleted', $project, "Deleted project requirement {$title}");

        return back()->with('success', "Requirement '{$title}' removed.");
    }

    public function updateStatus(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'timeline_comment' => ['nullable', 'string'],
            'is_client_visible' => ['nullable', 'boolean'],
        ]);

        $oldStatus = $project->status;
        $project->update([
            'status' => $validated['status'],
            'progress_percentage' => $validated['progress_percentage'],
        ]);

        ProjectTimelineEvent::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'event_type' => 'status_changed',
            'title' => "Status Changed: {$validated['status']}",
            'description' => $validated['timeline_comment'] ?? "Progress updated to {$validated['progress_percentage']}%.",
            'is_client_visible' => $request->boolean('is_client_visible', true),
        ]);

        AuditService::log('project_status_update', $project, "Updated project {$project->project_number} status from {$oldStatus} to {$validated['status']}");

        return back()->with('success', 'Project status and progress updated.');
    }

    public function updateMilestoneStatus(Request $request, Project $project, ProjectMilestone $milestone): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $status = $validated['status'];
        $progress = $validated['progress'] ?? ($status === 'completed' ? 100 : ($status === 'in_progress' ? 50 : 0));
        $completionDate = $status === 'completed' ? now() : null;

        $milestone->update([
            'status' => $status,
            'progress' => $progress,
            'completion_date' => $completionDate,
        ]);

        $newProjectProgress = $project->recalculateProgress();

        ProjectTimelineEvent::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'event_type' => 'milestone_updated',
            'title' => "Milestone Updated: {$milestone->title}",
            'description' => "Status changed to " . ucfirst(str_replace('_', ' ', $status)) . " ({$progress}%). Project overall progress is now {$newProjectProgress}%.",
            'is_client_visible' => $milestone->is_client_visible,
        ]);

        AuditService::log('milestone_updated', $project, "Updated milestone {$milestone->title} to {$status}");

        return back()->with('success', "Milestone '{$milestone->title}' updated and project progress recalculated ({$newProjectProgress}%).");
    }

    public function edit(Project $project): View
    {
        $clients = ClientProfile::with('user')->where('account_status', 'active')->get();
        $categoriesSetting = \App\Models\Setting::get('project_categories');
        if ($categoriesSetting) {
            $categories = array_filter(array_map('trim', explode(',', $categoriesSetting)));
        } else {
            $categories = ['Web Application', 'SaaS Platform', 'API & Microservices', 'DevOps & Infrastructure', 'Mobile API'];
        }

        return view('admin.projects.edit', compact('project', 'clients', 'categories'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'client_profile_id' => ['required', 'exists:client_profiles,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'expected_delivery_date' => ['nullable', 'date'],
            'actual_completion_date' => ['nullable', 'date'],
            'priority' => ['required', 'in:Low,Normal,High,Critical'],
            'status' => ['required', 'string'],
            'budget' => ['required', 'numeric', 'min:0'],
            'technologies' => ['nullable', 'string'],
            'current_deployed_version' => ['nullable', 'string', 'max:50'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $techArray = !empty($validated['technologies'])
            ? array_map('trim', explode(',', $validated['technologies']))
            : [];

        $project->update([
            'client_profile_id' => $validated['client_profile_id'],
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
            'actual_completion_date' => $validated['actual_completion_date'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'budget' => $validated['budget'],
            'technologies' => $techArray,
            'current_deployed_version' => $validated['current_deployed_version'] ?? null,
            'internal_notes' => $validated['internal_notes'] ?? null,
        ]);

        AuditService::log('project_updated', $project, "Updated project details for {$project->project_number}");

        return redirect()->route('admin.projects.show', $project)->with('success', "Project {$project->project_number} details updated successfully.");
    }

    public function destroy(Project $project): RedirectResponse
    {
        $pNumber = $project->project_number;

        // Disallow deletion if active invoices exist
        if ($project->invoices()->exists()) {
            return back()->with('error', "Cannot delete project {$pNumber} because invoices have been issued against it. Remove invoices first.");
        }

        $project->members()->delete();
        $project->milestones()->delete();
        $project->requirements()->delete();
        $project->delete();

        AuditService::log('project_deleted', null, "Deleted project {$pNumber}");

        return redirect()->route('admin.projects.index')->with('success', "Project {$pNumber} was deleted successfully.");
    }

    public function updateMilestone(Request $request, Project $project, ProjectMilestone $milestone): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'deliverable_type' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'demo_url' => ['nullable', 'url'],
            'description' => ['nullable', 'string'],
            'acceptance_criteria' => ['nullable', 'string'],
            'is_client_visible' => ['nullable', 'boolean'],
        ]);

        $milestone->update([
            'title' => $validated['title'],
            'deliverable_type' => $validated['deliverable_type'] ?? 'Feature',
            'due_date' => $validated['due_date'],
            'demo_url' => $validated['demo_url'],
            'description' => $validated['description'],
            'acceptance_criteria' => $validated['acceptance_criteria'],
            'is_client_visible' => $request->boolean('is_client_visible', true),
        ]);

        AuditService::log('milestone_edited', $project, "Edited deliverable milestone {$milestone->title}");

        return back()->with('success', "Milestone '{$milestone->title}' details updated.");
    }

    public function destroyMilestone(Project $project, ProjectMilestone $milestone): RedirectResponse
    {
        $title = $milestone->title;
        $milestone->delete();

        $newProgress = $project->recalculateProgress();

        AuditService::log('milestone_deleted', $project, "Deleted milestone {$title}");

        return back()->with('success', "Milestone '{$title}' removed and progress recalculated ({$newProgress}%).");
    }

    public function addMember(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role_in_project' => ['required', 'string', 'max:100'],
        ]);

        // Prevent duplicate assignment
        $exists = $project->members()->where('user_id', $validated['user_id'])->exists();
        if ($exists) {
            return back()->with('error', 'This staff member is already assigned to this project.');
        }

        $project->members()->create([
            'user_id' => $validated['user_id'],
            'role_in_project' => $validated['role_in_project'],
        ]);

        $assignedUser = User::find($validated['user_id']);

        ProjectTimelineEvent::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'event_type' => 'team_assigned',
            'title' => "Staff Assigned: {$assignedUser->name}",
            'description' => "Assigned as {$validated['role_in_project']}.",
            'is_client_visible' => true,
        ]);

        AuditService::log('project_member_added', $project, "Assigned {$assignedUser->name} as {$validated['role_in_project']} to project {$project->project_number}");

        return back()->with('success', "Team member {$assignedUser->name} assigned successfully.");
    }

    public function removeMember(Project $project, \App\Models\ProjectMember $member): RedirectResponse
    {
        $userName = $member->user->name ?? 'Staff member';
        $member->delete();

        AuditService::log('project_member_removed', $project, "Removed {$userName} from project {$project->project_number}");

        return back()->with('success', "Removed {$userName} from the project team.");
    }

    public function uploadDocument(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document' => ['required', 'file', 'max:20480'], // Up to 20MB
            'category' => ['required', 'string', 'max:50'],
            'is_client_visible' => ['nullable', 'boolean'],
        ]);

        $file = $request->file('document');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $size = $file->getSize();

        $path = $file->store('project_documents/' . $project->id, 'public');

        $doc = $project->documents()->create([
            'uploaded_by_user_id' => auth()->id(),
            'title' => $validated['title'],
            'file_path' => $path,
            'file_name' => $originalName,
            'file_type' => strtolower($extension),
            'file_size' => $size,
            'category' => $validated['category'],
            'is_client_visible' => $request->boolean('is_client_visible', true),
        ]);

        ProjectTimelineEvent::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'event_type' => 'document_uploaded',
            'title' => "Document Uploaded: {$doc->title}",
            'description' => "Uploaded {$originalName} ({$doc->category}).",
            'is_client_visible' => $doc->is_client_visible,
        ]);

        AuditService::log('project_document_uploaded', $project, "Uploaded document {$doc->title} ({$originalName}) for project {$project->project_number}");

        return back()->with('success', "Document '{$doc->title}' uploaded successfully.");
    }

    public function downloadDocument(Project $project, \App\Models\ProjectDocument $document)
    {
        if ($document->project_id !== $project->id) {
            abort(404);
        }

        $filePath = storage_path('app/public/' . $document->file_path);
        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found on server.');
        }

        return response()->download($filePath, $document->file_name);
    }

    public function destroyDocument(Project $project, \App\Models\ProjectDocument $document): RedirectResponse
    {
        if ($document->project_id !== $project->id) {
            abort(404);
        }

        $title = $document->title;
        $filePath = storage_path('app/public/' . $document->file_path);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        $document->delete();

        AuditService::log('project_document_deleted', $project, "Deleted document {$title} from project {$project->project_number}");

        return back()->with('success', "Document '{$title}' removed.");
    }

    public function downloadSummaryPdf(Project $project)
    {
        $project->load([
            'client.user',
            'milestones',
            'members.user',
            'invoices',
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.project_summary', compact('project'));

        return $pdf->download("Project-Summary-{$project->project_number}.pdf");
    }
}
