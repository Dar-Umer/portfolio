<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\HomepageSection;
use App\Models\PortfolioProject;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function home(): View
    {
        $sections = HomepageSection::where('is_enabled', true)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('section_key');

        $services = Service::where('is_active', true)->orderBy('sort_order')->take(6)->get();
        $projects = PortfolioProject::where('is_published', true)->orderBy('sort_order')->take(6)->get();
        $testimonials = Testimonial::where('is_featured', true)->orderBy('sort_order')->get();

        return view('public.home', compact('sections', 'services', 'projects', 'testimonials'));
    }

    public function portfolio(): View
    {
        $projects = PortfolioProject::where('is_published', true)->orderBy('sort_order')->paginate(9);
        return view('public.portfolio', compact('projects'));
    }

    public function projectDetail(string $slug): View
    {
        $project = PortfolioProject::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return view('public.project-detail', compact('project'));
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ContactMessage::create(array_merge($validated, [
            'ip_address' => $request->ip(),
        ]));

        return back()->with('success', 'Thank you! Your inquiry has been received. I will get back to you shortly.');
    }
}
