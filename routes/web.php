<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/incentive-form', [App\Http\Controllers\PublicIncentiveController::class, 'showForm']);
Route::post('/incentive-form/upload', [App\Http\Controllers\PublicIncentiveController::class, 'uploadXlsx']);
Route::post('/incentive-form/submit', [App\Http\Controllers\PublicIncentiveController::class, 'submitSingle']);
Route::get('/incentive-form/sample', [App\Http\Controllers\PublicIncentiveController::class, 'downloadSample']);


Route::get('/thank-you', function () {
    return view('employee.thank_you');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/clients', [App\Http\Controllers\ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/data', [App\Http\Controllers\ClientController::class, 'getClients'])->name('clients.data');
    Route::post('/clients', [App\Http\Controllers\ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{id}/edit', [App\Http\Controllers\ClientController::class, 'edit']);
    Route::put('/clients/{id}', [App\Http\Controllers\ClientController::class, 'update']);
    Route::delete('/clients/{id}', [App\Http\Controllers\ClientController::class, 'destroy']);
    Route::get('/clients/download', [App\Http\Controllers\ClientController::class, 'download'])->name('clients.download');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/vendors', [App\Http\Controllers\VendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/data', [App\Http\Controllers\VendorController::class, 'getVendors'])->name('vendors.data');
    Route::get('/vendors/{id}/edit', [App\Http\Controllers\VendorController::class, 'edit']);
    Route::post('/vendors', [App\Http\Controllers\VendorController::class, 'store'])->name('vendors.store');
    Route::put('/vendors/{id}', [App\Http\Controllers\VendorController::class, 'update']);
    Route::delete('/vendors/{id}', [App\Http\Controllers\VendorController::class, 'destroy']);
});
Route::middleware(['auth'])->group(function () {
    Route::get('/current/projects', [App\Http\Controllers\CurrentProjectController::class, 'index'])->name('current_projects.index');
    Route::post('/current/projects/store', [App\Http\Controllers\CurrentProjectController::class, 'store'])->name('current_projects.store');
    Route::post('/current-projects/delete-by-pn', [App\Http\Controllers\CurrentProjectController::class, 'deleteByPn'])->name('projects.deleteByPn');;
    Route::get('/current-projects/download', [App\Http\Controllers\CurrentProjectController::class, 'download'])->name('current_projects.download');
    Route::post('/pending-projects/store', [App\Http\Controllers\PendingProjectController::class, 'store'])->name('pending_projects.store');
    Route::get('/pending/projects', [App\Http\Controllers\PendingProjectController::class, 'index'])->name('pending.projects.index');
    Route::post('/pending-projects/bulk-update', [App\Http\Controllers\PendingProjectController::class, 'bulkUpdate'])->name('pending.bulkUpdate');
    Route::get('/pending-projects/download', [App\Http\Controllers\PendingProjectController::class, 'download'])->name('pending_projects.download');
    Route::get('/closed/projects', [App\Http\Controllers\PendingProjectController::class, 'closedProjects'])->name('closed.projects.index');
    Route::get('/closed-projects/download', [App\Http\Controllers\PendingProjectController::class, 'closedDownload'])->name('closed_projects.download');
    Route::get('/open/quarter/projects', [App\Http\Controllers\PendingProjectController::class, 'openLastQuarter'])->name('open.quarter.projects.index');
    Route::post('/pending-projects/move', [App\Http\Controllers\PendingProjectController::class, 'moveOpenQuarterProject'])->name('pending_projects.move');
    Route::get('/pending-projects/search', [App\Http\Controllers\PendingProjectController::class, 'pendingajaxSearch'])->name('pending.search');
    Route::get('/closed-projects/search', [App\Http\Controllers\PendingProjectController::class, 'closedajaxSearch'])->name('closed.search');
    Route::get('/open/quarter/download', [App\Http\Controllers\PendingProjectController::class, 'open_quarter_download'])->name('open_quarter.download');
    Route::get('/open-last-quarter/search', [App\Http\Controllers\PendingProjectController::class, 'openLastQuarterAjaxSearch'])->name('openlastquarter.search');


    Route::get('/search-projects', [App\Http\Controllers\ProjectSearchController::class, 'index'])->name('search.projects.index');
    Route::post('/search-projects/ajax', [App\Http\Controllers\ProjectSearchController::class, 'ajaxSearch'])->name('search.projects.ajax');
    Route::get('/search-projects/download', [App\Http\Controllers\ProjectSearchController::class, 'download'])->name('search.projects.download');
    Route::get('respondent-incentive', [App\Http\Controllers\RespondentIncentiveController::class, 'index'])->name('respondent.index');
    Route::post('respondent-incentive/store', [App\Http\Controllers\RespondentIncentiveController::class, 'store'])->name('respondent.store');
    Route::delete('/respondent-incentive/{id}', [App\Http\Controllers\RespondentIncentiveController::class, 'destroy'])->name('respondent.destroy');
    Route::get('/respondent-incentives/download', [App\Http\Controllers\RespondentIncentiveController::class, 'download'])->name('respondent.download');
    Route::post('/pending-projects/update-status/{id}', [App\Http\Controllers\PendingProjectController::class, 'updateStatus'])->name('pending-projects.updateStatus');
    Route::get('/current-projects/sample', [App\Http\Controllers\CurrentProjectController::class, 'downloadSample'])->name('current_projects.sample');
    Route::post('/current-projects/bulk-upload', [App\Http\Controllers\CurrentProjectController::class, 'bulkUpload'])->name('current_projects.bulk_upload');
    Route::get('communication', [App\Http\Controllers\CommunicationController::class, 'index'])->name('communication.index');
    Route::post('communication/store', [App\Http\Controllers\CommunicationController::class, 'store'])->name('communication.store');
    Route::delete('/communication/delete/{id}', [App\Http\Controllers\CommunicationController::class, 'destroy'])->name('communication.destroy');
    Route::get('/communication/search', [App\Http\Controllers\CommunicationController::class, 'search'])->name('communication.search');


});

require __DIR__.'/auth.php';