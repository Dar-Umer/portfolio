<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ClientManagementController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\ContactInquiryController;
use App\Http\Controllers\Admin\GitHubController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ProjectManagementController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TicketManagementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Client\ClientPortalController;
use App\Http\Controllers\Public\WebsiteController;
use Illuminate\Support\Facades\Route;

// 1. Public Website Routes
Route::get('/', [WebsiteController::class, 'home'])->name('home');
Route::get('/portfolio', [WebsiteController::class, 'portfolio'])->name('portfolio');
Route::get('/portfolio/{slug}', [WebsiteController::class, 'projectDetail'])->name('portfolio.detail');
Route::get('/contact', [WebsiteController::class, 'contact'])->name('contact');
Route::post('/contact', [WebsiteController::class, 'submitContact'])->name('contact.post');

// 2. Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// 3. Enterprise Admin & Staff Console (Super Admin, Administrator & Developer)
Route::middleware(['auth', 'role:Super Admin|Administrator|Developer'])->prefix('admin')->name('admin.')->group(function () {
    // Overview Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Customer Inquiries & Lead Management
    Route::get('/inquiries', [ContactInquiryController::class, 'index'])->name('inquiries.index');
    Route::post('/inquiries/{inquiry}/read', [ContactInquiryController::class, 'markRead'])->name('inquiries.markRead');
    Route::post('/inquiries/{inquiry}/convert', [ContactInquiryController::class, 'convertToClient'])->name('inquiries.convertToClient');
    Route::delete('/inquiries/{inquiry}', [ContactInquiryController::class, 'destroy'])->name('inquiries.destroy');

    // Clients Directory
    Route::get('/clients', [ClientManagementController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientManagementController::class, 'create'])->name('clients.create')->middleware('role:Super Admin|Administrator');
    Route::post('/clients', [ClientManagementController::class, 'store'])->name('clients.store')->middleware('role:Super Admin|Administrator');
    Route::get('/clients/{client}', [ClientManagementController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [ClientManagementController::class, 'edit'])->name('clients.edit')->middleware('role:Super Admin|Administrator');
    Route::put('/clients/{client}', [ClientManagementController::class, 'update'])->name('clients.update')->middleware('role:Super Admin|Administrator');

    // Projects Directory
    Route::get('/projects', [ProjectManagementController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectManagementController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectManagementController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectManagementController::class, 'show'])->name('projects.show');
    Route::post('/projects/{project}/milestones', [ProjectManagementController::class, 'addMilestone'])->name('projects.milestone');
    Route::post('/projects/{project}/milestones/{milestone}/status', [ProjectManagementController::class, 'updateMilestoneStatus'])->name('projects.milestones.status');
    Route::post('/projects/{project}/requirements', [ProjectManagementController::class, 'storeRequirement'])->name('projects.requirements.store');
    Route::post('/projects/{project}/requirements/{requirement}/convert', [ProjectManagementController::class, 'convertRequirementToMilestone'])->name('projects.requirements.convert');
    Route::put('/projects/{project}/requirements/{requirement}', [ProjectManagementController::class, 'updateRequirement'])->name('projects.requirements.update');
    Route::delete('/projects/{project}/requirements/{requirement}', [ProjectManagementController::class, 'destroyRequirement'])->name('projects.requirements.destroy');
    Route::post('/projects/{project}/status', [ProjectManagementController::class, 'updateStatus'])->name('projects.status');

    // Support Tickets Management
    Route::get('/tickets', [TicketManagementController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketManagementController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/comment', [TicketManagementController::class, 'addComment'])->name('tickets.comment');
    Route::post('/tickets/{ticket}/status', [TicketManagementController::class, 'updateStatus'])->name('tickets.status');
    Route::post('/tickets/{ticket}/assign', [TicketManagementController::class, 'assign'])->name('tickets.assign');

    // Quotations Directory
    Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create')->middleware('role:Super Admin|Administrator');
    Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store')->middleware('role:Super Admin|Administrator');
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert')->middleware('role:Super Admin|Administrator');
    Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.pdf');

    // Invoices & Billing Directory
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');

    // GitHub Integration
    Route::get('/github', [GitHubController::class, 'index'])->name('github.index');
    Route::post('/github', [GitHubController::class, 'store'])->name('github.store');
    Route::post('/github/{repository}/sync', [GitHubController::class, 'sync'])->name('github.sync');

    // Administrative-restricted operations (Super Admin & Administrator)
    Route::middleware('role:Super Admin|Administrator')->group(function () {
        // Payment recording
        Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoices.payment');

        // CMS Management
        Route::get('/cms/homepage', [CmsController::class, 'homepage'])->name('cms.homepage');
        Route::put('/cms/homepage/{section}', [CmsController::class, 'updateHomepageSection'])->name('cms.homepage.update');
        Route::get('/cms/services', [CmsController::class, 'services'])->name('cms.services');
        Route::post('/cms/services', [CmsController::class, 'storeService'])->name('cms.services.store');
        Route::delete('/cms/services/{service}', [CmsController::class, 'deleteService'])->name('cms.services.destroy');
        Route::get('/cms/portfolio', [CmsController::class, 'portfolio'])->name('cms.portfolio');
        Route::post('/cms/portfolio', [CmsController::class, 'storePortfolio'])->name('cms.portfolio.store');
        Route::delete('/cms/portfolio/{project}', [CmsController::class, 'deletePortfolio'])->name('cms.portfolio.destroy');

        // System Settings & Audit Logs
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('/audit-logs', [SettingsController::class, 'auditLogs'])->name('settings.audit');

        // Analytics, Reports & Exports
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/{type}', [ReportsController::class, 'exportCsv'])->name('reports.export');

        // Roles & Staff Permissions
        Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::put('/roles/{role}', [RolePermissionController::class, 'updateRolePermissions'])->name('roles.update');
        Route::post('/roles/staff', [RolePermissionController::class, 'storeStaff'])->name('roles.staff.store');
    });
});

// 4. Client Management Portal Routes (Restricted to authenticated Customer)
Route::middleware(['auth', 'role:Customer'])->prefix('portal')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientPortalController::class, 'dashboard'])->name('dashboard');

    // Support Tickets (Customer-facing)
    Route::get('/tickets', [ClientPortalController::class, 'tickets'])->name('tickets.index');
    Route::get('/tickets/create', [ClientPortalController::class, 'createTicket'])->name('tickets.create');
    Route::post('/tickets', [ClientPortalController::class, 'storeTicket'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [ClientPortalController::class, 'showTicket'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [ClientPortalController::class, 'replyTicket'])->name('tickets.reply');

    // Projects & Deliverable Workflows
    Route::get('/projects', [ClientPortalController::class, 'projects'])->name('projects.index');
    Route::get('/projects/{project}', [ClientPortalController::class, 'showProject'])->name('projects.show');
    Route::post('/projects/{project}/requirements', [ClientPortalController::class, 'storeRequirement'])->name('projects.requirements.store');
    Route::post('/projects/{project}/milestones/{milestone}/approve', [ClientPortalController::class, 'approveMilestone'])->name('projects.milestones.approve');
    Route::post('/projects/{project}/milestones/{milestone}/revision', [ClientPortalController::class, 'requestMilestoneRevision'])->name('projects.milestones.revision');

    // Quotations
    Route::get('/quotations', [ClientPortalController::class, 'quotations'])->name('quotations.index');
    Route::get('/quotations/{quotation}', [ClientPortalController::class, 'showQuotation'])->name('quotations.show');
    Route::post('/quotations/{quotation}/accept', [ClientPortalController::class, 'acceptQuotation'])->name('quotations.accept');

    // Invoices
    Route::get('/invoices', [ClientPortalController::class, 'invoices'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [ClientPortalController::class, 'showInvoice'])->name('invoices.show');
});
