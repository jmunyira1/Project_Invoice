<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliverableController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;


require __DIR__ . '/auth.php';
// ── Authenticated + tenant-scoped routes ──────────────────────────────────
Route::middleware(['auth', 'verified', 'tenant'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    // Templates
    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/{template}/preview', [TemplateController::class, 'preview'])
        ->name('templates.preview');
    Route::post('/templates/{template}/default', [TemplateController::class, 'setDefault'])
        ->name('templates.setDefault')
        ->middleware('role:owner');

    // ── Coming next (uncomment as we build each module) ───────────
    Route::resource('clients', ClientController::class);
    Route::resource('projects', ProjectController::class);
    Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus'])
        ->name('projects.status');

// Deliverables — nested under projects
    Route::post('projects/{project}/deliverables', [DeliverableController::class, 'store'])
        ->name('projects.deliverables.store');
    Route::patch('projects/{project}/deliverables/{deliverable}', [DeliverableController::class, 'update'])
        ->name('projects.deliverables.update');
    Route::delete('projects/{project}/deliverables/{deliverable}', [DeliverableController::class, 'destroy'])
        ->name('projects.deliverables.destroy');

// Costs — nested under projects
    Route::post('projects/{project}/costs', [CostController::class, 'store'])
        ->name('projects.costs.store');
    Route::patch('projects/{project}/costs/{cost}', [CostController::class, 'update'])
        ->name('projects.costs.update');
    Route::delete('projects/{project}/costs/{cost}', [CostController::class, 'destroy'])
        ->name('projects.costs.destroy');

    // Documents
    Route::resource('documents', DocumentController::class)->except(['edit', 'update']);
    Route::patch('documents/{document}/sent', [DocumentController::class, 'markSent'])->name('documents.markSent');
    Route::get('documents/{document}/pdf', [DocumentController::class, 'pdf'])->name('documents.pdf');

// Payments
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'destroy']);


// Add inside auth+tenant middleware group in routes/web.php


// Settings — index readable by all, mutations restricted in controller
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');

// Organisation
    Route::patch('/settings/organisation', [SettingController::class, 'updateOrganisation'])
        ->name('settings.organisation.update')
        ->middleware('role:owner');
    Route::post('/settings/logo', [SettingController::class, 'uploadLogo'])
        ->name('settings.logo.upload')
        ->middleware('role:owner');
    Route::delete('/settings/logo', [SettingController::class, 'removeLogo'])
        ->name('settings.logo.remove')
        ->middleware('role:owner');

// Team
    Route::post('/settings/team', [SettingController::class, 'inviteMember'])
        ->name('settings.team.invite')
        ->middleware('role:owner');
    Route::patch('/settings/team/{user}/role', [SettingController::class, 'updateMemberRole'])
        ->name('settings.team.role')
        ->middleware('role:owner');
    Route::delete('/settings/team/{user}', [SettingController::class, 'removeMember'])
        ->name('settings.team.remove')
        ->middleware('role:owner');

// Personal — any authenticated user
    Route::patch('/settings/profile', [SettingController::class, 'updateProfile'])
        ->name('settings.profile.update');
    Route::patch('/settings/password', [SettingController::class, 'updatePassword'])
        ->name('settings.password.update');
});

// ── Super admin routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', fn() => view('admin.dashboard'))->name('dashboard');
        // Route::resource('organisations', Admin\OrganisationController::class);
    });
