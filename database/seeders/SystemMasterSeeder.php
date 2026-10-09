<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\TicketCategory;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SystemMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $developerRole = Role::firstOrCreate(['name' => 'Developer']);
        $clientRole = Role::firstOrCreate(['name' => 'Customer']);

        // 3. Define Permissions
        $permissions = [
            'manage_settings',
            'manage_cms',
            'view_audit_logs',
            'manage_clients',
            'manage_projects',
            'view_projects',
            'manage_tickets',
            'view_tickets',
            'create_tickets',
            'reply_tickets',
            'add_ticket_internal_notes',
            'resolve_tickets',
            'manage_github',
            'manage_quotations',
            'view_quotations',
            'manage_invoices',
            'view_invoices',
            'record_payments',
            'view_payments',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Assign all permissions to Super Admin
        $superAdminRole->syncPermissions(Permission::all());

        // Assign Staff permissions
        $adminRole->syncPermissions([
            'manage_cms',
            'view_audit_logs',
            'manage_clients',
            'manage_projects',
            'view_projects',
            'manage_tickets',
            'view_tickets',
            'reply_tickets',
            'add_ticket_internal_notes',
            'resolve_tickets',
            'manage_github',
            'manage_quotations',
            'view_quotations',
            'manage_invoices',
            'view_invoices',
            'record_payments',
            'view_payments',
        ]);

        // Assign Developer permissions
        $developerRole->syncPermissions([
            'view_projects',
            'view_tickets',
            'reply_tickets',
            'add_ticket_internal_notes',
            'resolve_tickets',
            'manage_github',
        ]);

        // Assign Client permissions
        $clientRole->syncPermissions([
            'view_projects',
            'view_tickets',
            'create_tickets',
            'reply_tickets',
            'view_quotations',
            'view_invoices',
            'view_payments',
        ]);

        // 4. Create Default Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@theumer.store'],
            [
                'name' => 'Umer Architect (Super Admin)',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('Super Admin');

        $dev = User::firstOrCreate(
            ['email' => 'dev@theumer.store'],
            [
                'name' => 'Lead Developer',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $dev->assignRole('Developer');

        $clientUser = User::firstOrCreate(
            ['email' => 'client@acme.com'],
            [
                'name' => 'John Client',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $clientUser->assignRole('Customer');

        $clientProfile = \App\Models\ClientProfile::firstOrCreate(
            ['user_id' => $clientUser->id],
            [
                'client_number' => 'CLT-2026-000001',
                'company_name' => 'Acme Corporation Ltd.',
                'phone' => '+91 98765 43210',
                'billing_address' => 'Suite 404, Tech Business Park, Sector 62',
                'city' => 'Noida',
                'state' => 'Uttar Pradesh',
                'postal_code' => '201301',
                'country' => 'India',
                'tax_number' => '09AAACA1234A1Z5',
                'client_category' => 'Enterprise',
                'account_status' => 'active',
            ]
        );

        // 5. Seed Ticket Categories
        $categories = [
            ['name' => 'Bug Report', 'slug' => 'bug-report', 'description' => 'System defect or error requiring fix', 'sort_order' => 1],
            ['name' => 'Feature Request', 'slug' => 'feature-request', 'description' => 'New feature proposal or enhancement', 'sort_order' => 2],
            ['name' => 'Technical Support', 'slug' => 'technical-support', 'description' => 'Configuration, server, or API assistance', 'sort_order' => 3],
            ['name' => 'UI / Design', 'slug' => 'ui-design', 'description' => 'Visual layout, styling, or responsive adjustments', 'sort_order' => 4],
            ['name' => 'Security & Performance', 'slug' => 'security-performance', 'description' => 'Speed optimization or vulnerability remediation', 'sort_order' => 5],
            ['name' => 'General Inquiry', 'slug' => 'general-inquiry', 'description' => 'General consulting and question', 'sort_order' => 6],
        ];
        foreach ($categories as $cat) {
            TicketCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 6. Seed Ticket Statuses with Transition Rules
        $statuses = [
            [
                'name' => 'Open',
                'slug' => 'open',
                'color' => '#17a2b8',
                'is_initial' => true,
                'is_resolved' => false,
                'is_closed' => false,
                'allowed_transitions' => ['acknowledged', 'under-investigation', 'in-progress', 'rejected'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Acknowledged',
                'slug' => 'acknowledged',
                'color' => '#6c757d',
                'is_initial' => false,
                'is_resolved' => false,
                'is_closed' => false,
                'allowed_transitions' => ['in-progress', 'awaiting-customer', 'awaiting-developer'],
                'sort_order' => 2,
            ],
            [
                'name' => 'In Progress',
                'slug' => 'in-progress',
                'color' => '#007bff',
                'is_initial' => false,
                'is_resolved' => false,
                'is_closed' => false,
                'allowed_transitions' => ['awaiting-customer', 'fix-submitted', 'ready-for-testing', 'resolved'],
                'sort_order' => 3,
            ],
            [
                'name' => 'Awaiting Customer Response',
                'slug' => 'awaiting-customer',
                'color' => '#ffc107',
                'is_initial' => false,
                'is_resolved' => false,
                'is_closed' => false,
                'allowed_transitions' => ['in-progress', 'resolved', 'closed'],
                'sort_order' => 4,
            ],
            [
                'name' => 'Fix Submitted',
                'slug' => 'fix-submitted',
                'color' => '#6610f2',
                'is_initial' => false,
                'is_resolved' => false,
                'is_closed' => false,
                'allowed_transitions' => ['ready-for-testing', 'resolved', 'in-progress'],
                'sort_order' => 5,
            ],
            [
                'name' => 'Ready for Testing',
                'slug' => 'ready-for-testing',
                'color' => '#20c997',
                'is_initial' => false,
                'is_resolved' => false,
                'is_closed' => false,
                'allowed_transitions' => ['resolved', 'in-progress'],
                'sort_order' => 6,
            ],
            [
                'name' => 'Resolved',
                'slug' => 'resolved',
                'color' => '#28a745',
                'is_initial' => false,
                'is_resolved' => true,
                'is_closed' => false,
                'allowed_transitions' => ['closed', 'reopened'],
                'sort_order' => 7,
            ],
            [
                'name' => 'Reopened',
                'slug' => 'reopened',
                'color' => '#fd7e14',
                'is_initial' => false,
                'is_resolved' => false,
                'is_closed' => false,
                'allowed_transitions' => ['in-progress', 'under-investigation'],
                'sort_order' => 8,
            ],
            [
                'name' => 'Closed',
                'slug' => 'closed',
                'color' => '#343a40',
                'is_initial' => false,
                'is_resolved' => false,
                'is_closed' => true,
                'allowed_transitions' => ['reopened'],
                'sort_order' => 9,
            ],
            [
                'name' => 'Rejected',
                'slug' => 'rejected',
                'color' => '#dc3545',
                'is_initial' => false,
                'is_resolved' => false,
                'is_closed' => true,
                'allowed_transitions' => ['reopened'],
                'sort_order' => 10,
            ],
        ];
        foreach ($statuses as $stat) {
            TicketStatus::firstOrCreate(['slug' => $stat['slug']], $stat);
        }

        // 7. Seed System Settings
        $settings = [
            ['key' => 'site_name', 'value' => 'The Umer Store', 'group' => 'general', 'type' => 'string', 'is_public' => true],
            ['key' => 'site_tagline', 'value' => 'Enterprise Web Applications & Systems Engineering', 'group' => 'general', 'type' => 'string', 'is_public' => true],
            ['key' => 'contact_email', 'value' => 'contact@theumer.store', 'group' => 'general', 'type' => 'string', 'is_public' => true],
            ['key' => 'contact_phone', 'value' => '+91 98765 43210', 'group' => 'general', 'type' => 'string', 'is_public' => true],
            ['key' => 'business_address', 'value' => 'Tech Tower, 8th Floor, Cyber City', 'group' => 'business', 'type' => 'string', 'is_public' => true],
            ['key' => 'seller_gstin', 'value' => '09ABCDE1234F1Z5', 'group' => 'business', 'type' => 'string', 'is_public' => true],
            ['key' => 'seller_state', 'value' => 'Uttar Pradesh', 'group' => 'business', 'type' => 'string', 'is_public' => true],
            ['key' => 'default_currency', 'value' => 'INR', 'group' => 'business', 'type' => 'string', 'is_public' => true],
            ['key' => 'ticket_reopen_days', 'value' => '7', 'group' => 'tickets', 'type' => 'integer', 'is_public' => false],
        ];
        foreach ($settings as $st) {
            Setting::set($st['key'], $st['value'], $st['group'], $st['type'], $st['is_public']);
        }

        // 8. Seed Services
        $services = [
            [
                'title' => 'Custom Web Application Development',
                'icon' => 'fas fa-laptop-code',
                'short_description' => 'Full-stack enterprise applications built with modern Laravel, Vue/React, and relational architectures.',
                'starting_price' => 25000.00,
                'sort_order' => 1,
            ],
            [
                'title' => 'API Design & Third-Party Integration',
                'icon' => 'fas fa-network-wired',
                'short_description' => 'Secure RESTful & GraphQL APIs, GitHub automation, and payment gateways with rock-solid reliability.',
                'starting_price' => 15000.00,
                'sort_order' => 2,
            ],
            [
                'title' => 'DevOps, CI/CD & Cloud Infrastructure',
                'icon' => 'fas fa-server',
                'short_description' => 'High-availability deployment, automated Docker pipelines, Nginx optimization, and zero-downtime releases.',
                'starting_price' => 20000.00,
                'sort_order' => 3,
            ],
            [
                'title' => 'Code Audits & Performance Optimization',
                'icon' => 'fas fa-tachometer-alt',
                'short_description' => 'Database indexing, slow query refactoring, bottleneck mitigation, and comprehensive security reviews.',
                'starting_price' => 12000.00,
                'sort_order' => 4,
            ],
        ];
        foreach ($services as $srv) {
            Service::firstOrCreate(['title' => $srv['title']], $srv);
        }

        // 9. Seed Portfolio Category and Projects
        $catEcommerce = PortfolioCategory::firstOrCreate(['slug' => 'e-commerce'], ['name' => 'E-Commerce Platforms']);
        $catGov = PortfolioCategory::firstOrCreate(['slug' => 'government-portals'], ['name' => 'Government Portals']);
        $catOffice = PortfolioCategory::firstOrCreate(['slug' => 'office-management'], ['name' => 'Office Record Systems']);
        $catDelivery = PortfolioCategory::firstOrCreate(['slug' => 'delivery-platforms'], ['name' => 'Delivery Ecosystems']);
        $cat = PortfolioCategory::firstOrCreate(['slug' => 'saas-platforms'], ['name' => 'SaaS Platforms']);

        PortfolioProject::firstOrCreate(
            ['slug' => 'magray-orchards'],
            [
                'portfolio_category_id' => $catEcommerce->id,
                'title' => 'Magray Orchards — E-Commerce & Agricultural Platform',
                'short_description' => 'A complete e-commerce store for premium pesticides, farming tools, orchard booking and admin control panel.',
                'description' => 'High-performance e-commerce store featuring dynamic catalog, category & brand filtering, cart, UPI/COD payments, wallet balance, order tracking, and agricultural service booking.',
                'technologies' => ['PHP Laravel', 'MySQL', 'Razorpay', 'Bootstrap', 'REST API'],
                'client_name' => 'Magray Orchards',
                'project_type' => 'Client Project',
                'completion_date' => now()->subMonths(3),
                'project_url' => 'https://magrayorchards.com',
                'status' => 'Completed',
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        PortfolioProject::firstOrCreate(
            ['slug' => 'soil-testing-laboratory'],
            [
                'portfolio_category_id' => $catGov->id,
                'title' => 'Soil Testing Laboratory — Govt. of Jammu & Kashmir',
                'short_description' => 'Official government portal under the Horticulture Department with multilingual report tracking & Soil Health Cards.',
                'description' => 'Official portal for Soil Testing Laboratory, Pulwama. Full multilingual support (English, Hindi, Urdu), soil report tracking by phone number, sample requests, and digital Soil Health Cards.',
                'technologies' => ['PHP Laravel', 'MySQL', 'Multilingual i18n', 'Bootstrap'],
                'client_name' => 'Horticulture Dept, Govt. of J&K',
                'project_type' => 'Government Portal',
                'completion_date' => now()->subMonths(4),
                'project_url' => 'https://stl.theumer.store',
                'status' => 'Completed',
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 2,
            ]
        );

        PortfolioProject::firstOrCreate(
            ['slug' => 'horticulture-development-office'],
            [
                'portfolio_category_id' => $catOffice->id,
                'title' => 'Horticulture Development Office — Record System',
                'short_description' => 'Centralized document tracking, role-based departmental access, and real-time record dispatch updates.',
                'description' => 'Official record management system engineered for the Horticulture Development Office. Centralized document tracking, internal staff workflow approvals, and live dispatch audits.',
                'technologies' => ['PHP Laravel', 'MySQL', 'Role-Based Access', 'Audit Logs'],
                'client_name' => 'Horticulture Development Office',
                'project_type' => 'Government Department',
                'completion_date' => now()->subMonths(2),
                'project_url' => 'https://office.theumer.store',
                'status' => 'Completed',
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 3,
            ]
        );

        PortfolioProject::firstOrCreate(
            ['slug' => 'jozash-delivery-platform'],
            [
                'portfolio_category_id' => $catDelivery->id,
                'title' => 'Jozash — Multi-Vendor Hyperlocal Delivery Ecosystem',
                'short_description' => 'Full delivery ecosystem for grocery, food, pharmacy, shop and parcel services with customer Android app.',
                'description' => 'Hyperlocal multi-vendor delivery platform supporting vendor & rider onboarding, automated order dispatch, live tracking zones, and customer Android app on Google Play Store.',
                'technologies' => ['PHP Laravel', 'MySQL', 'Multi-Vendor Architecture', 'Android App', 'REST API'],
                'client_name' => 'Jozash Inc.',
                'project_type' => 'Commercial Ecosystem',
                'completion_date' => now()->subMonth(),
                'project_url' => 'https://dashboard.jozash.com',
                'status' => 'Completed',
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 4,
            ]
        );

        PortfolioProject::firstOrCreate(
            ['slug' => 'enterprise-erp-portal'],
            [
                'portfolio_category_id' => $cat->id,
                'title' => 'Enterprise ERP & Client Management Platform',
                'short_description' => 'Comprehensive platform featuring real-time project dashboards, automated GST invoicing, role-based controls, and GitHub webhook synchronization.',
                'description' => 'End-to-end client management system with automated billing, ticketing, and Git integration.',
                'technologies' => ['Laravel 12', 'MySQL', 'Bootstrap 5', 'REST API', 'Redis'],
                'client_name' => 'Acme Corporation',
                'project_type' => 'Client Project',
                'completion_date' => now()->subMonths(1),
                'project_url' => 'https://example.com/erp',
                'status' => 'Completed',
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 5,
            ]
        );

        // 10. Seed Testimonials
        Testimonial::firstOrCreate(
            ['client_name' => 'Rajesh Sharma'],
            [
                'client_position' => 'Chief Technology Officer',
                'company_name' => 'Acme Global Ventures',
                'comment' => 'The architectural precision, fast delivery, and robust issue tracking capabilities completely transformed how our software operations run.',
                'rating' => 5,
                'is_featured' => true,
                'sort_order' => 1,
            ]
        );

        // 11. Seed Dynamic Homepage Sections
        $sections = [
            [
                'section_key' => 'hero',
                'title' => 'Senior Full-Stack Architect & Engineering Consultant',
                'subtitle' => 'Building high-performance web systems, custom client portals, and secure cloud infrastructure.',
                'content' => [
                    'cta_primary_text' => 'View Client Portal',
                    'cta_primary_link' => '/login',
                    'cta_secondary_text' => 'Explore Portfolio',
                    'cta_secondary_link' => '#portfolio',
                    'years_experience' => '8+',
                    'projects_completed' => '45+',
                    'happy_clients' => '30+',
                ],
                'is_enabled' => true,
                'sort_order' => 1,
            ],
            [
                'section_key' => 'about',
                'title' => 'About Me & Professional Background',
                'subtitle' => 'Senior Laravel Architect & Full-Stack Systems Engineer',
                'content' => [
                    'bio' => 'With over 8 years of production experience, I design and build secure, maintainable web systems, client management ecosystems, and resilient software architectures.',
                    'focus_areas' => ['Laravel & PHP', 'Database Engineering', 'API Architectures', 'Application Security', 'DevOps & CI/CD'],
                ],
                'is_enabled' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($sections as $sec) {
            HomepageSection::firstOrCreate(['section_key' => $sec['section_key']], $sec);
        }
    }
}
