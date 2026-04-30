<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendorRegistrationController;
use App\Http\Controllers\VendorPortalController;
use App\Http\Controllers\Admin\VendorManagementController;
use App\Http\Controllers\Admin\VendorBillController;
use App\Http\Controllers\Admin\VendorBillApprovalController;
use App\Http\Controllers\Admin\VendorDocumentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Middleware\CheckVendorRole;
// Role middleware is now handled by our custom RoleMiddleware
use Spatie\Permission\Middleware\PermissionMiddleware;

/*
|--------------------------------------------------------------------------
| Vendor Routes
|--------------------------------------------------------------------------
|
| Here is where you can register vendor related routes for your application.
|
*/

// Public vendor registration routes
Route::get('/vendor/register', [VendorRegistrationController::class, 'showRegistrationForm'])->name('vendor.register');
Route::post('/vendor/register', [VendorRegistrationController::class, 'register'])->name('vendor.register.submit');
Route::get('/vendor/register/success', [VendorRegistrationController::class, 'showRegistrationSuccess'])->name('vendor.register.success');
Route::get('/vendor/verify/{token}', [VendorRegistrationController::class, 'verifyEmail'])->name('vendor.verify');
Route::post('/vendor/complete-profile', [VendorRegistrationController::class, 'completeProfile'])->name('vendor.complete-profile');

// Vendor login alias to core auth routes
Route::middleware('guest')->group(function () {
    Route::get('/vendor/login', [AuthenticatedSessionController::class, 'create'])->name('vendor.login');
    Route::post('/vendor/login', [AuthenticatedSessionController::class, 'store'])->name('vendor.login.store');
});

// Vendor portal routes (authenticated vendors with vendor role)
Route::middleware(['auth', App\Http\Middleware\CheckVendorRole::class])->prefix('vendor-portal')->name('vendor.')->group(function () {

    // Verify
    Route::get('/verify/pending', [VendorPortalController::class, 'verifyPending'])
        ->name('verify.pending');
    // ->middleware(PermissionMiddleware::class . ':view-vendor-dashboard');

    Route::get('/verify/blocked', [VendorPortalController::class, 'verifyBlocked'])
        ->name('verify.blocked')
        ->middleware(PermissionMiddleware::class . ':view-vendor-dashboard');

    // vendor.verification.update
    Route::put('/verify/update', [VendorPortalController::class, 'updateVendorDetails'])
        ->name('verification.update');
    // ->middleware(PermissionMiddleware::class . ':view-vendor-dashboard');

    // Dashboard
    Route::get('/dashboard', [VendorPortalController::class, 'dashboard'])
        ->name('dashboard');
    //->middleware(PermissionMiddleware::class . ':view-vendor-dashboard');

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [VendorPortalController::class, 'profile'])
            ->name('profile');
        //->middleware(PermissionMiddleware::class . ':view-vendor-profile');

        Route::get('/edit', [VendorPortalController::class, 'editProfile'])
            ->name('profile.edit');
        // ->middleware(PermissionMiddleware::class . ':update-vendor-profile');

        Route::put('/', [VendorPortalController::class, 'updateProfile'])
            ->name('profile.update');
        // ->middleware(PermissionMiddleware::class . ':update-vendor-profile');
    });

    // Bank details
    Route::prefix('bank-details')->group(function () {
        Route::get('/add', [VendorPortalController::class, 'addBankDetails'])
            ->name('bank-details.add');

        Route::post('/', [VendorPortalController::class, 'storeBankDetails'])
            ->name('bank-details.store');
    });

    // Bills
    Route::prefix('bills')->group(function () {
        Route::get('/', [VendorPortalController::class, 'bills'])
            ->name('bills');
        // ->middleware(PermissionMiddleware::class . ':view-bills');

        // Route::middleware(PermissionMiddleware::class . ':upload-bills')->group(function () {
        Route::get('/create', [VendorPortalController::class, 'createBill'])
            ->name('bills.create');

        Route::get('/create-credit-note/{billId}', [VendorPortalController::class, 'createCreditNote'])
            ->name('bills.create-credit-note');

        Route::post('/', [VendorPortalController::class, 'storeBill'])
            ->name('bills.store');
            
        // ✳️ New routes for Edit & Update functionality
        Route::get('/{id}/edit', [VendorPortalController::class, 'editBill'])
            ->name('bills.edit');

        Route::post('/{id}/update', [VendorPortalController::class, 'updateBill'])
            ->name('bills.update');

        // });

        Route::get('/{id}', [VendorPortalController::class, 'showBill'])
            ->name('bills.show');
        // ->middleware(PermissionMiddleware::class . ':view-bills');

        Route::get('/{id}/download', [VendorPortalController::class, 'downloadBill'])
            ->name('bills.download');
        // ->middleware(PermissionMiddleware::class . ':download-bills');
    });
});

// Admin vendor management routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Vendor management - only Admin and HR can manage vendors
    Route::get('/vendors', [VendorManagementController::class, 'index'])
        ->name('vendors.index')
        ->middleware('auth');

    Route::get('/vendors/{id}', [VendorManagementController::class, 'show'])
        ->name('vendors.show')
        ->middleware('auth');

    Route::get('/vendors/{id}/workflow-config', [VendorManagementController::class, 'showWorkflowConfig'])
        ->name('vendors.workflow-config');
    //->middleware(PermissionMiddleware::class . ':manage-vendors');

    Route::put('/vendors/{id}/workflow-config', [VendorManagementController::class, 'updateWorkflowConfig'])
        ->name('vendors.update-workflow-config');
    //->middleware(PermissionMiddleware::class . ':manage-vendors');

    Route::put('/vendors/{id}/status', [VendorManagementController::class, 'updateStatus'])
        ->name('vendors.update-status');
    // ->middleware(PermissionMiddleware::class . ':verify-vendors');

    Route::get('/vendors/document/{id}/download', [VendorManagementController::class, 'downloadDocument'])
        ->name('vendors.document.download')
        ->middleware(PermissionMiddleware::class . ':view-vendor-profile');

    Route::get('/vendors/export', [VendorManagementController::class, 'export'])
        ->name('vendors.export')
        ->middleware(PermissionMiddleware::class . ':export-vendor-data');

    Route::post('/vendors/{id}/send-welcome-email', [VendorManagementController::class, 'sendWelcomeEmail'])
        ->name('vendors.send-welcome-email');
    //->middleware(PermissionMiddleware::class . ':manage-vendors');

    Route::post('/vendors/create', [VendorManagementController::class, 'saveVendor'])
        ->name('vendors.store');
    //->middleware(PermissionMiddleware::class . ':manage-vendors');

    // Vendor Document Management
    Route::name('vendors.')->prefix('vendors/{vendor}')->group(function () {
        // Document listing and management
        Route::get('/documents', [VendorDocumentController::class, 'index'])
            ->name('documents.index')
            ->middleware('auth');

        Route::get('/documents/create', [VendorDocumentController::class, 'create'])
            ->name('documents.create')
            ->middleware('auth');

        Route::post('/documents', [VendorDocumentController::class, 'store'])
            ->name('documents.store')
            ->middleware('auth');

        Route::get('/documents/{document}/edit', [VendorDocumentController::class, 'edit'])
            ->name('documents.edit')
            ->middleware('auth');

        Route::put('/documents/{document}', [VendorDocumentController::class, 'update'])
            ->name('documents.update')
            ->middleware('auth');

        Route::delete('/documents/{document}', [VendorDocumentController::class, 'destroy'])
            ->name('documents.destroy')
            ->middleware('auth');

        Route::get('/documents/{document}/download', [VendorDocumentController::class, 'download'])
            ->name('documents.download')
            ->middleware('auth');

        // Document verification routes (admin only)
        // Route::middleware(PermissionMiddleware::class . ':verify-vendors')->group(function () {
        Route::get('/documents/{document}/verify', [VendorDocumentController::class, 'verify'])
            ->name('documents.verify');

        Route::post('/documents/{document}/verify', [VendorDocumentController::class, 'processVerification'])
            ->name('documents.process-verification');
    });
    // })->middleware('auth');

    // Bank Details Management
    Route::prefix('vendors/{vendor}/bank-details')->name('vendors.bank-details.')->group(function () {
        Route::get('/create', [VendorManagementController::class, 'createBankDetail'])
            ->name('create')
            ->middleware(PermissionMiddleware::class . ':manage-vendors');

        Route::post('/', [VendorManagementController::class, 'storeBankDetail'])
            ->name('store')
            ->middleware(PermissionMiddleware::class . ':manage-vendors');

        Route::get('/{bankDetail}/edit', [VendorManagementController::class, 'editBankDetail'])
            ->name('edit');
        //->middleware(PermissionMiddleware::class . ':manage-vendors');

        Route::put('/{bankDetail}', [VendorManagementController::class, 'updateBankDetail'])
            ->name('update')
            ->middleware(PermissionMiddleware::class . ':manage-vendors');

        Route::delete('/{bankDetail}', [VendorManagementController::class, 'destroyBankDetail'])
            ->name('destroy');
        // ->middleware(PermissionMiddleware::class . ':manage-vendors');
    });

    // General bills management (admin access)
    Route::prefix('bills')->name('bills.')->group(function () {
        // View all bills - available to all admin/HR/Finance
        Route::get('/', [VendorBillController::class, 'index'])
            ->name('index');
        // ->middleware('auth');
    });

    // Vendor-specific bill management
    Route::prefix('vendors/{vendor}/bills')->name('vendors.bills.')->group(function () {
        // View vendor's bills - available to all admin/HR/Finance
        Route::get('/', [VendorBillController::class, 'vendorBills'])
            ->name('index')
            ->middleware('auth');

        // Create new bill for specific vendor - only Admin and HR can create
        Route::middleware(['auth'])->group(function () {
            Route::get('/create', [VendorBillController::class, 'create'])
                ->name('create')
                ->middleware(PermissionMiddleware::class . ':create-vendor-bills');

            Route::post('/', [VendorBillController::class, 'store'])
                ->name('store')
                ->middleware(PermissionMiddleware::class . ':create-vendor-bills');
        });

        // View bill details - available to all admin/HR/Finance
        Route::get('/{id}', [VendorBillController::class, 'show'])
            ->name('show');
        // ->middleware('auth');

        // Approve/Reject bills - only Admin and HR can approve
        Route::middleware(['auth'])->group(function () {
            Route::put('/{id}/status', [VendorBillController::class, 'updateStatus'])
                ->name('update-status')
                ->middleware(PermissionMiddleware::class . ':approve-vendor-bills');
        });

        // Download bill - available to all with permission
        Route::get('/{id}/download', [VendorBillController::class, 'download'])
            ->name('download')
            ->middleware(PermissionMiddleware::class . ':download-bills');

        // Export bills - only Admin and Finance can export
        Route::middleware(['auth'])->group(function () {
            Route::get('/export', [VendorBillController::class, 'export'])
                ->name('export')
                ->middleware(PermissionMiddleware::class . ':export-vendor-data');
        });

        // Bill approval workflow
        Route::prefix('approval')->name('approval.')->middleware('auth')->group(function () {
            Route::get('/', [VendorBillApprovalController::class, 'index'])
                ->name('index');

            Route::get('/{id}', [VendorBillApprovalController::class, 'show'])
                ->name('show');

            Route::put('/{id}/status', [VendorBillApprovalController::class, 'updateStatus'])
                ->name('update-status');

            Route::put('/{id}/payment', [VendorBillApprovalController::class, 'processPayment'])
                ->name('process-payment');
        });
    });
});
