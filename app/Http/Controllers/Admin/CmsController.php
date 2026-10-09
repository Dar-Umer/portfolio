<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use App\Models\Service;
use App\Models\Testimonial;
use App\Services\Audit\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CmsController extends Controller
{
    // Homepage Sections
    public function homepage(): View
    {
        $sections = HomepageSection::orderBy('sort_order')->get();
        return view('admin.cms.homepage', compact('sections'));
    }

    public function updateHomepageSection(Request $request, HomepageSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'is_enabled' => ['nullable', 'boolean'],
            'content' => ['nullable', 'array'],
        ]);

        $section->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'is_enabled' => $request->boolean('is_enabled'),
            'content' => $validated['content'] ?? $section->content,
        ]);

        AuditService::log('cms_homepage_updated', null, "Updated homepage section: {$section->section_key}");

        return back()->with('success', "Section '{$section->title}' updated successfully.");
    }

    // Services CMS
    public function services(): View
    {
        $services = Service::orderBy('sort_order')->get();
        return view('admin.cms.services', compact('services'));
    }

    public function storeService(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'icon' => ['required', 'string', 'max:50'],
            'short_description' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'starting_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        Service::create(array_merge($validated, [
            'is_active' => true,
            'sort_order' => Service::count() + 1,
        ]));

        return back()->with('success', 'Service added successfully.');
    }

    public function deleteService(Service $service): RedirectResponse
    {
        $service->delete();
        return back()->with('success', 'Service deleted successfully.');
    }

    // Portfolio CMS
    public function portfolio(): View
    {
        $projects = PortfolioProject::with('category')->orderBy('sort_order')->paginate(15);
        $categories = PortfolioCategory::all();
        return view('admin.cms.portfolio', compact('projects', 'categories'));
    }

    public function storePortfolio(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'portfolio_category_id' => ['nullable', 'exists:portfolio_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'project_type' => ['required', 'string', 'max:50'],
            'technologies' => ['nullable', 'string'],
            'project_url' => ['nullable', 'url'],
            'github_url' => ['nullable', 'url'],
        ]);

        $techArray = !empty($validated['technologies'])
            ? array_map('trim', explode(',', $validated['technologies']))
            : [];

        PortfolioProject::create([
            'portfolio_category_id' => $validated['portfolio_category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . strtolower(Str::random(5)),
            'short_description' => $validated['short_description'],
            'description' => $validated['description'],
            'client_name' => $validated['client_name'],
            'project_type' => $validated['project_type'],
            'technologies' => $techArray,
            'project_url' => $validated['project_url'],
            'github_url' => $validated['github_url'],
            'status' => 'Completed',
            'is_featured' => true,
            'is_published' => true,
            'sort_order' => PortfolioProject::count() + 1,
        ]);

        return back()->with('success', 'Portfolio project created successfully.');
    }

    public function deletePortfolio(PortfolioProject $project): RedirectResponse
    {
        $project->delete();
        return back()->with('success', 'Portfolio project removed.');
    }
}
