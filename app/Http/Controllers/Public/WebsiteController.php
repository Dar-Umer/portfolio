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
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
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
        // 1. Honeypot anti-spam check: bots fill hidden inputs
        if ($request->filled('website_url')) {
            // Silently return success to mislead spambots
            return back()->with('success', 'Thank you! Your inquiry has been received. I will get back to you shortly.');
        }

        // 2. Google reCAPTCHA v2 verification if configured
        $secretKey = Setting::get('recaptcha_secret_key', config('services.recaptcha.secret_key'));
        if (!empty($secretKey)) {
            $recaptchaResponse = $request->input('g-recaptcha-response');

            if (empty($recaptchaResponse)) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => ['Please check the reCAPTCHA box to confirm you are not a robot.'],
                ]);
            }

            try {
                $response = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->ip(),
                ]);

                if (!$response->successful() || !$response->json('success')) {
                    throw ValidationException::withMessages([
                        'g-recaptcha-response' => ['reCAPTCHA verification failed. Please try again.'],
                    ]);
                }
            } catch (\Illuminate\Validation\ValidationException $ve) {
                throw $ve;
            } catch (\Throwable $e) {
                report($e);
                // In case of network timeout to Google, keep error clean for user
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => ['Could not verify reCAPTCHA with Google servers. Please try again in a moment.'],
                ]);
            }
        }

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
