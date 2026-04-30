<?php

use App\Http\Controllers\VisitorController;

Route::middleware(['auth'])->group(function () {
    Route::resource('visitors', VisitorController::class)->except(['edit', 'update', 'destroy']);
    Route::post('visitors/ocr', [VisitorController::class, 'processVisitingCard'])->name('visitors.ocr');
    
    Route::middleware([\App\Http\Middleware\CheckHrRole::class])->group(function() {
        Route::post('visitors/{visitor}/approve', [VisitorController::class, 'approve'])->name('visitors.approve');
        Route::post('visitors/{visitor}/reject', [VisitorController::class, 'reject'])->name('visitors.reject');
    });
    
    Route::middleware([\App\Http\Middleware\CheckReceptionistRole::class])->group(function() {
        Route::post('visitors/{visitor}/checkout', [VisitorController::class, 'checkout'])->name('visitors.checkout');
    });
});
