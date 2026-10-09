<?php

use App\Http\Controllers\Admin\AdminDashboardController;
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

// 3. Admin Console Routes (Restricted to Super Admin & Administrator)
Route::middleware(['auth', 'role:Super Admin|Administrator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Clients Directory
    Route::get('/clients', [\App\Http\Controllers\Admin\ClientManagementController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [\App\Http\Controllers\Admin\ClientManagementController::class, 'create'])->name('clients.create');
    Route::post('/clients', [\App\Http\Controllers\Admin\ClientManagementController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [\App\Http\Controllers\Admin\ClientManagementController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [\App\Http\Controllers\Admin\ClientManagementController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [\App\Http\Controllers\Admin\ClientManagementController::class, 'update'])->name('clients.update');

    // Projects Directory
    Route::get('/projects', [\App\Http\Controllers\Admin\ProjectManagementController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [\App\Http\Controllers\Admin\ProjectManagementController::class, 'create'])->name('projects.create');
    Route::post('/projects', [\App\Http\Controllers\Admin\ProjectManagementController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [\App\Http\Controllers\Admin\ProjectManagementController::class, 'show'])->name('projects.show');
    Route::post('/projects/{project}/milestones', [\App\Http\Controllers\Admin\ProjectManagementController::class, 'addMilestone'])->name('projects.milestone');
    Route::post('/projects/{project}/status', [\App\Http\Controllers\Admin\ProjectManagementController::class, 'updateStatus'])->name('projects.status');

    // Support Tickets Management
    Route::get('/tickets', [\App\Http\Controllers\Admin\TicketManagementController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\Admin\TicketManagementController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/comment', [\App\Http\Controllers\Admin\TicketManagementController::class, 'addComment'])->name('tickets.comment');
    Route::post('/tickets/{ticket}/status', [\App\Http\Controllers\Admin\TicketManagementController::class, 'updateStatus'])->name('tickets.status');
    Route::post('/tickets/{ticket}/assign', [\App\Http\Controllers\Admin\TicketManagementController::class, 'assign'])->name('tickets.assign');

    // Quotations
    Route::get('/quotations', [\App\Http\Controllers\Admin\QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [\App\Http\Controllers\Admin\QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [\App\Http\Controllers\Admin\QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [\App\Http\Controllers\Admin\QuotationController::class, 'show'])->name('quotations.show');
    Route::post('/quotations/{quotation}/convert', [\App\Http\Controllers\Admin\QuotationController::class, 'convert'])->name('quotations.convert');
    Route::get('/quotations/{quotation}/pdf', [\App\Http\Controllers\Admin\QuotationController::class, 'downloadPdf'])->name('quotations.pdf');

    // Invoices & Billing
    Route::get('/invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/{invoice}/payment', [\App\Http\Controllers\Admin\InvoiceController::class, 'recordPayment'])->name('invoices.payment');
    Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\Admin\InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');

    // GitHub Integration
    Route::get('/github', [\App\Http\Controllers\Admin\GitHubController::class, 'index'])->name('github.index');
    Route::post('/github', [\App\Http\Controllers\Admin\GitHubController::class, 'store'])->name('github.store');
    Route::post('/github/{repository}/sync', [\App\Http\Controllers\Admin\GitHubController::class, 'sync'])->name('github.sync');

    // CMS Management
    Route::get('/cms/homepage', [\App\Http\Controllers\Admin\CmsController::class, 'homepage'])->name('cms.homepage');
    Route::put('/cms/homepage/{section}', [\App\Http\Controllers\Admin\CmsController::class, 'updateHomepageSection'])->name('cms.homepage.update');

    // System Settings & Audit Logs
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::get('/audit-logs', [\App\Http\Controllers\Admin\SettingsController::class, 'auditLogs'])->name('settings.audit');

    // Analytics, Reports & Exports
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/{type}', [\App\Http\Controllers\Admin\ReportsController::class, 'exportCsv'])->name('reports.export');

    // Roles & Staff Permissions
    Route::get('/roles', [\App\Http\Controllers\Admin\RolePermissionController::class, 'index'])->name('roles.index');
    Route::put('/roles/{role}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'updateRolePermissions'])->name('roles.update');
    Route::post('/roles/staff', [\App\Http\Controllers\Admin\RolePermissionController::class, 'storeStaff'])->name('roles.staff.store');
});

// 4. Client Management Portal Routes (Restricted to authenticated Customer)
Route::middleware(['auth', 'role:Customer'])->prefix('portal')->name('client.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Client\ClientPortalController::class, 'dashboard'])->name('dashboard');
    
    // Support Tickets (Customer-facing)
    Route::get('/tickets', [\App\Http\Controllers\Client\ClientPortalController::class, 'tickets'])->name('tickets.index');
    Route::get('/tickets/create', [\App\Http\Controllers\Client\ClientPortalController::class, 'createTicket'])->name('tickets.create');
    Route::post('/tickets', [\App\Http\Controllers\Client\ClientPortalController::class, 'storeTicket'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\Client\ClientPortalController::class, 'showTicket'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [\App\Http\Controllers\Client\ClientPortalController::class, 'replyTicket'])->name('tickets.reply');

    // Projects
    Route::get('/projects', [\App\Http\Controllers\Client\ClientPortalController::class, 'projects'])->name('projects.index');
    Route::get('/projects/{project}', [\App\Http\Controllers\Client\ClientPortalController::class, 'showProject'])->name('projects.show');

    // Quotations
    Route::get('/quotations', [\App\Http\Controllers\Client\ClientPortalController::class, 'quotations'])->name('quotations.index');
    Route::get('/quotations/{quotation}', [\App\Http\Controllers\Client\ClientPortalController::class, 'showQuotation'])->name('quotations.show');
    Route::post('/quotations/{quotation}/accept', [\App\Http\Controllers\Client\ClientPortalController::class, 'acceptQuotation'])->name('quotations.accept');

    // Invoices
    Route::get('/invoices', [\App\Http\Controllers\Client\ClientPortalController::class, 'invoices'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [\App\Http\Controllers\Client\ClientPortalController::class, 'showInvoice'])->name('invoices.show');
});
