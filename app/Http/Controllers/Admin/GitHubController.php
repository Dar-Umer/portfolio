<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectRepository;
use App\Services\Audit\AuditService;
use App\Services\GitHub\GitHubIntegrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GitHubController extends Controller
{
    public function index(): View
    {
        $repositories = ProjectRepository::with(['project.client.user', 'syncLogs'])->latest()->paginate(15);
        $projects = Project::orderBy('name')->get();

        return view('admin.github.index', compact('repositories', 'projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'repo_owner' => ['required', 'string', 'max:100'],
            'repo_name' => ['required', 'string', 'max:100'],
            'default_branch' => ['required', 'string', 'max:50'],
            'access_token' => ['nullable', 'string', 'max:255'],
        ]);

        $repoUrl = "https://github.com/{$validated['repo_owner']}/{$validated['repo_name']}";

        $repo = ProjectRepository::create([
            'project_id' => $validated['project_id'],
            'repo_owner' => $validated['repo_owner'],
            'repo_name' => $validated['repo_name'],
            'default_branch' => $validated['default_branch'],
            'repo_url' => $repoUrl,
            'access_token' => $validated['access_token'],
            'last_sync_status' => 'pending',
            'is_active' => true,
        ]);

        AuditService::log('github_repo_connected', $repo->project, "Connected repository {$validated['repo_owner']}/{$validated['repo_name']}");

        return redirect()->route('admin.github.index')->with('success', "Connected repository {$repo->repo_owner}/{$repo->repo_name}.");
    }

    public function sync(ProjectRepository $repository): RedirectResponse
    {
        try {
            $result = GitHubIntegrationService::sync($repository);
            return back()->with('success', "Synchronization completed successfully. Latest release tag: " . ($result['latest_tag'] ?? 'None'));
        } catch (\Exception $e) {
            return back()->with('error', "GitHub sync error: {$e->getMessage()}");
        }
    }
}
