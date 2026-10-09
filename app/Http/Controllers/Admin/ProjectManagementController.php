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
        return view('admin.projects.create', compact('clients'));
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
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'],
            'status' => 'Planning',
            'priority' => $validated['priority'],
            'progress_percentage' => 0,
            'budget' => $validated['budget'],
            'technologies' => $techArray,
            'internal_notes' => $validated['internal_notes'],
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
            'tickets.status',
            'invoices',
        ]);

        $developers = User::role(['Super Admin', 'Administrator', 'Developer'])->get();

        return view('admin.projects.show', compact('project', 'developers'));
    }

    public function addMilestone(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'is_client_visible' => ['nullable', 'boolean'],
        ]);

        $milestone = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
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
            'title' => 'Milestone Created',
            'description' => "Added milestone: {$milestone->title}",
            'is_client_visible' => $milestone->is_client_visible,
        ]);

        return back()->with('success', 'Milestone added successfully.');
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
}
