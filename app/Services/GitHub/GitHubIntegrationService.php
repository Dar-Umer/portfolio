<?php

namespace App\Services\GitHub;

use App\Models\GithubSyncLog;
use App\Models\ProjectRepository;
use App\Models\ProjectTimelineEvent;
use App\Services\Audit\AuditService;
use Exception;
use Illuminate\Support\Facades\Http;

class GitHubIntegrationService
{
    /**
     * Synchronize repository releases, tags, and commits using GitHub REST API.
     */
    public static function sync(ProjectRepository $repository): array
    {
        $owner = $repository->repo_owner;
        $repo = $repository->repo_name;
        $baseUrl = "https://api.github.com/repos/{$owner}/{$repo}";

        $headers = [
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'TheUmerStore-GitHubClient/1.0',
        ];

        if (!empty($repository->access_token)) {
            $headers['Authorization'] = "token {$repository->access_token}";
        }

        try {
            // 1. Fetch Repository Details
            $repoResponse = Http::withHeaders($headers)->timeout(10)->get($baseUrl);

            if ($repoResponse->failed()) {
                throw new Exception("GitHub API Error ({$repoResponse->status()}): " . $repoResponse->body());
            }

            $repoData = $repoResponse->json();
            $defaultBranch = $repoData['default_branch'] ?? $repository->default_branch;

            // 2. Fetch Latest Releases and Tags
            $releasesResponse = Http::withHeaders($headers)->timeout(10)->get("{$baseUrl}/releases/latest");
            $latestTag = null;
            $publishedAt = null;

            if ($releasesResponse->successful()) {
                $releaseData = $releasesResponse->json();
                $latestTag = $releaseData['tag_name'] ?? null;
                $publishedAt = isset($releaseData['published_at']) ? date('Y-m-d H:i:s', strtotime($releaseData['published_at'])) : null;
            } else {
                // Check if any tag exists
                $tagsResponse = Http::withHeaders($headers)->timeout(10)->get("{$baseUrl}/tags?per_page=1");
                if ($tagsResponse->successful() && !empty($tagsResponse->json())) {
                    $firstTag = $tagsResponse->json()[0] ?? null;
                    $latestTag = $firstTag['name'] ?? null;
                }
            }

            // 3. Fetch Recent Commits
            $commitsResponse = Http::withHeaders($headers)->timeout(10)->get("{$baseUrl}/commits?per_page=5");
            $recentCommits = [];
            if ($commitsResponse->successful()) {
                foreach ($commitsResponse->json() as $c) {
                    $recentCommits[] = [
                        'sha' => substr($c['sha'] ?? '', 0, 7),
                        'message' => $c['commit']['message'] ?? '',
                        'author' => $c['commit']['author']['name'] ?? '',
                        'date' => isset($c['commit']['author']['date']) ? date('d M Y, H:i', strtotime($c['commit']['author']['date'])) : '',
                    ];
                }
            }

            // Update repository record
            $repository->update([
                'default_branch' => $defaultBranch,
                'latest_release_tag' => $latestTag,
                'latest_release_published_at' => $publishedAt,
                'last_synced_at' => now(),
                'last_sync_status' => 'success',
            ]);

            // If a new release was found, record a project timeline event
            if ($latestTag && $latestTag !== $repository->getOriginal('latest_release_tag')) {
                ProjectTimelineEvent::create([
                    'project_id' => $repository->project_id,
                    'user_id' => auth()->id(),
                    'event_type' => 'release_published',
                    'title' => "Software Release Tagged: {$latestTag}",
                    'description' => "New version {$latestTag} synced from repository {$owner}/{$repo}.",
                    'is_client_visible' => true,
                ]);
            }

            GithubSyncLog::create([
                'project_repository_id' => $repository->id,
                'event_type' => 'manual_sync',
                'status' => 'success',
                'message' => "Successfully synchronized repository {$owner}/{$repo}. Latest release: " . ($latestTag ?? 'None'),
                'payload' => [
                    'stars' => $repoData['stargazers_count'] ?? 0,
                    'forks' => $repoData['forks_count'] ?? 0,
                    'open_issues' => $repoData['open_issues_count'] ?? 0,
                    'latest_tag' => $latestTag,
                    'commits' => $recentCommits,
                ],
            ]);

            AuditService::log('github_sync_success', $repository->project, "Synced GitHub repository {$owner}/{$repo}");

            return [
                'status' => 'success',
                'latest_tag' => $latestTag,
                'default_branch' => $defaultBranch,
                'commits' => $recentCommits,
            ];
        } catch (Exception $e) {
            $repository->update([
                'last_synced_at' => now(),
                'last_sync_status' => 'failed',
            ]);

            GithubSyncLog::create([
                'project_repository_id' => $repository->id,
                'event_type' => 'manual_sync',
                'status' => 'failed',
                'message' => $e->getMessage(),
            ]);

            AuditService::log('github_sync_failure', $repository->project, "GitHub synchronization failed for {$owner}/{$repo}: {$e->getMessage()}");

            throw $e;
        }
    }
}
