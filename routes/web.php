<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPlanController;
use App\Http\Controllers\AdminPlanRequest;
use App\Http\Controllers\ListingAdminController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PlanRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicListingController;
use App\Http\Controllers\TeacherProfileController;
use App\Http\Controllers\AdminTeacherController;
use Illuminate\Support\Facades\Route;
use App\Models\Listing; // ⬅️ add this
use App\Models\PlanRequest;

Route::get('/kursus', [PublicListingController::class, 'catalog'])->name('catalog');

Route::get('/kursus/{slug}/{region:slug}', [PublicListingController::class, 'show'])->name('listing.show');

/**
 * WhatsApp redirect tracker
 * - Increments wa_clicks (if the column exists)
 * - Builds a wa.me link from teacher profile number (fallback: just open WA with text)
 */
Route::get('/go/wa/{listing}', function (Listing $listing) {
    try {
        $listing->increment('wa_clicks');
    } catch (\Throwable $e) { /* column may not exist yet */
    }

    $teacher = optional($listing->user)->teacherProfile;
    $number  = $teacher->whatsapp_number ?? null;

    // build message
    $msg = 'Halo Kak ' . ($listing->user->name ?? 'Guru')
        . ', saya lihat listing ' . $listing->title
        . ' di ' . ($listing->region->name ?? 'kota Anda') . '.';

    $to   = $number ? preg_replace('/\D+/', '', $number) : '';
    $href = $to
        ? 'https://wa.me/' . $to . '?text=' . urlencode($msg)
        : 'https://wa.me/?text=' . urlencode($msg);

    return redirect()->away($href);
})->name('go.wa');

Route::middleware('auth')->group(function () {
    Route::get('/teacher', [ListingController::class, 'index'])->name('teacher.dashboard');
    Route::get('/teacher/profile', [TeacherProfileController::class, 'edit'])->name('teacher.profile.edit');
    Route::post('/teacher/profile', [TeacherProfileController::class, 'store'])->name('teacher.profile.store');
    Route::put('/teacher/profile',  [TeacherProfileController::class, 'update'])->name('teacher.profile.update');

    Route::resource('listings', ListingController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/plans/{plan}/wa', [PlanController::class, 'wa'])->name('plans.wa');
    Route::post('/plan-requests', [PlanRequestController::class, 'store'])->name('plan-requests.store');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    Route::get('/listings', [ListingAdminController::class, 'index'])->name('listings.index');
    Route::patch('/listings/{listing}/status', [ListingAdminController::class, 'updateStatus'])->name('listings.updateStatus');
    Route::delete('/listings/{listing}', [ListingAdminController::class, 'destroy'])->name('listings.destroy');

    Route::get('/plan-requests', [AdminPlanRequest::class, 'index'])->name('plan-requests.index');
    Route::post('/plan-requests/{req}/approve', [AdminPlanRequest::class, 'approve'])->name('plan-requests.approve');
    Route::post('/plan-requests/{req}/reject', [AdminPlanRequest::class, 'reject'])->name('plan-requests.reject');

    Route::resource('plans', AdminPlanController::class)->except(['show']);
    Route::get('/teacher-plans', [AdminTeacherController::class, 'index'])->name('teacher-plans.index');
    Route::post('/teacher-plans/{userId}/remove', [AdminTeacherController::class, 'removePlan'])->name('teacher-plans.remove');
    Route::post('/teacher-plans/{userId}/extend', [AdminTeacherController::class, 'extendPlan'])->name('teacher-plans.extend');
    Route::post('/teacher-plans/{userId}/update', [AdminTeacherController::class, 'updatePlan'])->name('teacher-plans.update');


});

Route::get('/', function () {
    return ('welcome');
});

require __DIR__ . '/auth.php';
