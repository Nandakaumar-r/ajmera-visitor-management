<?php

use App\Http\Controllers\AssetCollectionController;
use App\Http\Controllers\AssetRequestController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BankDetailController;
use App\Http\Controllers\CabinBookingController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatbotFeedbackController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CompanyPolicyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\DocumentCenterController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\DisciplinaryActionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\ExitInterviewController;
use App\Http\Controllers\ExternalReimbursementController;
use App\Http\Controllers\FarewellController;
use App\Http\Controllers\FnFSettlementController;
use App\Http\Controllers\Form16Controller;
use App\Http\Controllers\FormController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\HelpRequestController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\IdCardController;
use App\Http\Controllers\InternalOnboardingCandidateDetailsController;
use App\Http\Controllers\InternalOnboardingJoiningDocController;
use App\Http\Controllers\InternalORFApprovalController;
use App\Http\Controllers\InternalORFCreationController;
use App\Http\Controllers\InternalSalaryBreakupController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\LeaveBalanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OrgChartController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelievingLetterController;
use App\Http\Controllers\ResignationController;
use App\Http\Controllers\ResignationPredictionController;
use App\Http\Controllers\RolesAndPermissionsController;
use App\Http\Controllers\RotatingShiftController;
use App\Http\Controllers\RotatingWorkTypeController;
use App\Http\Controllers\ShiftRequestController;
use App\Http\Controllers\SnipeITController;
use App\Http\Controllers\SocialFeedController;
use App\Http\Controllers\TravelRequestController;
use App\Http\Controllers\TravelReimbursementController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\WebcamAuthController;
use App\Http\Controllers\WfhController;
use App\Http\Controllers\WorkFromHomeController;
use App\Http\Controllers\WorkTypeRequestController;
use App\Http\Middleware\CheckHrRole;
use App\Http\Middleware\CheckManagerRole;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;
use App\Http\Controllers\Admin\VendorBillApprovalController;
use App\Http\Controllers\Admin\VendorBillController;

require __DIR__ . '/web/vendor.php';
Route::get('/org-chart-public', [DashboardController::class, 'orgChart']);


// Internal ORF Creation and Approval
Route::post('/orf-internal',  [InternalORFCreationController::class, 'store'])->name('internal_onboarding.store');
Route::get('/orf-internal', [InternalORFCreationController::class, 'create'])->name('internal_onboarding.create');
Route::get('/candidate-details', [InternalORFApprovalController::class, 'show'])->name('internal_onboarding.show');
Route::get('/candidate-details/show/{id}', [InternalORFApprovalController::class, 'view'])->name('onboarding.view');
Route::get('/internal-onboarding/candidate-create/{id}', [InternalOnboardingCandidateDetailsController::class, 'create'])
    ->name('internal_onboarding.form');
Route::post('/onboarding/candidate-details', [InternalOnboardingCandidateDetailsController::class, 'store'])
    ->name('internal_onboarding_candidate_details.store');
Route::get('/offer-letter/{id}/{role}', [InternalSalaryBreakupController::class, 'candidateFinalOffer'])->name('offer.letter');
Route::get('sample/offer-letter/{id}', [InternalSalaryBreakupController::class, 'generateOfferLetter'])->name('sample.offer.letter');

// Internal ORF Approval
Route::post('/orf/approve/{role}/{id}', [InternalORFApprovalController::class, 'approve'])->name('orf.approve');
Route::post('/orf/reject/{role}/{id}', [InternalORFApprovalController::class, 'reject'])->name('orf.reject');
Route::post('/orf/{role}/{id}/offer', [InternalORFApprovalController::class, 'markOffered'])->name('orf.offer');
Route::post('/orf/{role}/{id}/cancel', [InternalORFApprovalController::class, 'markCancelled'])->name('orf.cancel');
Route::post('/joining-docs/store', [InternalOnboardingJoiningDocController::class, 'store'])->name('joining-docs.store');
Route::get('/orf/view/{role}/{id}', [InternalORFApprovalController::class, 'viewByRole'])->name('orf.view.role');
Route::post('/candidate/{id}/approve', [InternalORFApprovalController::class, 'approve'])->name('candidate.approve');
Route::post('/candidate/{id}/reject', [InternalORFApprovalController::class, 'reject'])->name('candidate.reject');

// List page per role
Route::middleware(['auth'])->group(function () {
    Route::get('/orf/{role}', [InternalORFApprovalController::class, 'showRoleList'])->name('orf.role.list');
});

// View ORF details by role
Route::middleware(['auth'])->group(function () {
    Route::get('/orf/hrbp/{id}', [InternalORFApprovalController::class, 'viewHRBP'])->name('orf.view.hrbp');
    Route::get('/orf/account-manager/{id}', [InternalORFApprovalController::class, 'viewAccountManager'])->name('orf.view.account_manager');
    Route::get('/orf/delivery-manager/{id}', [InternalORFApprovalController::class, 'viewDeliveryManager'])->name('orf.view.delivery_manager');
    Route::get('/orf/coo/{id}', [InternalORFApprovalController::class, 'viewCOO'])->name('orf.view.coo');
    Route::get('/orf/cfo/{id}', [InternalORFApprovalController::class, 'viewCFO'])->name('orf.view.cfo');
    Route::get('/orf/chro/{id}', [InternalORFApprovalController::class, 'viewCHRO'])->name('orf.view.chro');
    Route::get('/orf/hr/{id}', [InternalORFApprovalController::class, 'viewHR'])->name('orf.view.hr');
});

// Internal Salary Breakup
Route::get('/internal-salary-breakup', [InternalSalaryBreakupController::class, 'create'])
    ->name('internal_salary_breakup.create');
Route::post('/salary-breakups/import/{candidate_id}', [InternalSalaryBreakupController::class, 'import'])->name('salary_breakups.import');

Route::post('/joining-docs/store', [InternalOnboardingJoiningDocController::class, 'store'])->name('joining-docs.store');
Route::get('/joining-docs/bgv-show', [InternalOnboardingJoiningDocController::class, 'bgvShow'])->name('joining-docs.bgv.show');
Route::get('/joining-docs/bgv-view/{id}', [InternalOnboardingJoiningDocController::class, 'bgvView'])->name('joining-docs.view');
Route::get('/joining-docs/bgv-download/{id}/{field}', [InternalOnboardingJoiningDocController::class, 'bgvDownload'])->name('joining-docs.bgv.download');
Route::get('/joining-docs/{id}/view/{field}', [InternalOnboardingJoiningDocController::class, 'bgvPreview'])->name('joining-docs.bgv.view');


Route::get('/joining-docs/create/{id}', [InternalOnboardingJoiningDocController::class, 'create'])->name('joining-docs.create');
Route::get('/joining-docs/bgv/{id}', [InternalOnboardingJoiningDocController::class, 'createBgv'])->name('joining-docs.bgv');

//External Reimbursement
Route::get('/external-reimbursements/export', [ExternalReimbursementController::class, 'export'])
    ->name('reimbursements.export');
Route::get('/internal-reimbursements/export', [travelReimbursementController::class, 'export'])
    ->name('internal-reimbursements.export');
Route::post('/external-reimbursements/bulk-approve', [ExternalReimbursementController::class, 'bulkHandleApproval'])
    ->name('reimbursements.bulk-approve');
Route::post('/internal-reimbursements/bulk-approve', [travelReimbursementController::class, 'internalBulkApproval'])
    ->name('internal-reimbursements.bulk-approve');
Route::post('/bank-details/import', [BankDetailController::class, 'import'])->name('bank-details.import');
Route::get('/bank-details/export', [travelReimbursementController::class, 'exportCSV'])
    ->name('bank-details.export');
Route::get('/external-reimbursements/bank-details/', [ExternalReimbursementController::class, 'exportExternalCSV'])
    ->name('external-bank-details.export');
Route::get('/test-second/user/', [TravelReimbursementController::class, 'testSecondTable'])
    ->name('test-second.user');


//Internal Reimbursement
// Route::get('/travel-reimbursement', [TravelReimbursementController::class, 'store']);
Route::middleware(['auth'])->group(function () {
    Route::get('/travel/reimbursements/create', [TravelReimbursementController::class, 'create'])
        ->name('travel.reimbursements.create');

    Route::post('/travel/reimbursements/store', [TravelReimbursementController::class, 'store'])
        ->name('travel.reimbursements.store');

    Route::get('/travel/reimbursements/show', [TravelReimbursementController::class, 'show'])
        ->name('travel.reimbursements.show');
});
Route::get('/travel/reimbursements/internal_review', [TravelReimbursementController::class, 'showInternalReview'])
    ->name('travel.reimbursements.showInternalReview');
Route::get('/travel/reimbursements/accountant', [TravelReimbursementController::class, 'showInternalAccountant'])
    ->name('travel.reimbursements.showInternalReview');
Route::get('/accountant/reimbursement/{id}', [TravelReimbursementController::class, 'accountantActionView'])
    ->name('accountant.reimbursement.action');
Route::get('/travel/reimbursements/cfo', [TravelReimbursementController::class, 'showInternalCFO'])
    ->name('travel.reimbursements.showInternalReview');
Route::get('/travel/reimbursements/finance', [TravelReimbursementController::class, 'showInternalFinance'])
    ->name('travel.reimbursements.showInternalReview');
Route::get('/travel/reimbursements/processed', [TravelReimbursementController::class, 'showInternalProcessed'])
    ->name('travel.reimbursements.showInternalReview');
Route::get('/internal-reimbursements/approve/{id}', [TravelReimbursementController::class, 'internalApprove'])
    ->name('travel.reimbursements.internalApprove');
Route::get('/reimbursement/send-all-pending-emails', [TravelReimbursementController::class, 'sendPendingApprovalEmails'])->name('reimbursement.send-all-pending-emails');
Route::get('external/reimbursement/send-emails', [ExternalReimbursementController::class, 'sendExternalPendingApproval'])->name('external-reimbursement.send-emails');


Route::post('/travel/reimbursements', [TravelReimbursementController::class, 'tableCreation'])
    ->name('travel.reimbursements.tableCreation');

Route::get('/reimbursement/action/{travelRequestId}', [TravelReimbursementController::class, 'showActionForm']);
Route::post('/reimbursement/action/{id}', [TravelReimbursementController::class, 'processAction'])->name('internal_handle');
//Route::get('/reimbursement/process/{id}', [ReimbursementController::class, 'processByFinance'])->name('reimbursement.process');
//External Reimbursement
Route::get('/external-reimbursements', [ExternalReimbursementController::class, 'create'])->name('external.reimbursements.create');
Route::post('/external-reimbursements', [ExternalReimbursementController::class, 'store'])->name('external.reimbursements.store');
Route::get('/external-reimbursements/table', [ExternalReimbursementController::class, 'showAll'])->name('external-reimbursements.index');
Route::get('/external-reimbursements/{id}', [ExternalReimbursementController::class, 'show'])->name('external-reimbursements.show');
Route::get('/reimbursement/{id}/download-attachments', [ExternalReimbursementController::class, 'downloadAllAttachments'])->name('reimbursement.download.attachments');
Route::get('/internal-reimbursement/download/attachments/{id}', [TravelReimbursementController::class, 'downloadAllBills'])->name('internal-reimbursement.download.bills');
Route::get('/external-reimbursements/delete/{id}', [ExternalReimbursementController::class, 'delete'])->name('external-reimbursements.delete');

Route::get('/external-reimbursements/approve/{id}', [ExternalReimbursementController::class, 'showApprovalForm'])
    ->name('external_reimbursements.approval_form');
Route::post('/reimbursement/{id}/approve/', [ExternalReimbursementController::class, 'handleApproval'])
    ->name('reimbursement.handle.approval');
Route::get('/review', [ExternalReimbursementController::class, 'review'])
    ->name('external-reimbursements.review');
Route::get('/accountant/approval', [ExternalReimbursementController::class, 'accountantApproval'])
    ->name('external-reimbursements.review');
Route::get('/cfo/approval', [ExternalReimbursementController::class, 'cfoApproval'])
    ->name('external-reimbursements.review');
Route::get('/finance/approval', [ExternalReimbursementController::class, 'financeApproval'])
    ->name('external-reimbursements.review');
Route::get('/final/processed', [ExternalReimbursementController::class, 'finalProcessed'])
    ->name('external-reimbursements.review');


Route::get('/send-test-email', function () {
    Mail::to('chetan.n@fidelisgroup.in')->send(new TestEmail());
    return 'Test email sent!';
});

Route::get('/', function () {
    $userAgent = request()->header('User-Agent');

    // Detect if opened inside Microsoft Teams WebView
    $isTeams = str_contains(strtolower($userAgent), 'teams');

    if ($isTeams) {
        // Directly show the login page view (no redirect)
        return view('auth.login');
    }

    // Normal browser behavior
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route('dashboard');
});

Route::get('/login/webcam', [WebcamAuthController::class, 'showLoginForm'])->name('webcam.login');
Route::post('/login/webcam/process', [WebcamAuthController::class, 'processLogin'])->name('webcam.login.process');

Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Basic routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/face/update', [WebcamAuthController::class, 'updateFaceProfile'])->name('profile.face.update');

    Route::get('/org-chart', [OrgChartController::class, 'index'])->name('org-chart');

    // Chatbot routes
    Route::get('/hr-assistant', [ChatbotController::class, 'index'])->name('chatbot');
    Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])->name('chatbot.message');
    Route::post('/chatbot/feedback', [ChatbotFeedbackController::class, 'store'])->name('chatbot.feedback');

    // Social Feed routes
    Route::get('/social-feed', [PostController::class, 'index'])->name('social-feed.index');
    Route::get('/social-feed/load-more', [PostController::class, 'loadMore'])->name('social-feed.load-more');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::post('/posts/fetch-url', [PostController::class, 'fetchUrlMetadata'])->name('posts.fetch-url');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
    Route::post('/posts/{post}/unlike', [PostController::class, 'unlike'])->name('posts.unlike');

    // Comment routes
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Employee routes
    Route::middleware([\App\Http\Middleware\CheckEmployeeExists::class])->group(function () {
        // Help Center routes
        // Route::get('/help', [HelpController::class, 'index'])->name('help.index');
        // Route::post('/help/submit', [HelpController::class, 'submit'])->name('help.submit');

        // Help Request routes
        Route::get('/help-requests', [HelpRequestController::class, 'index'])->name('help-requests.index');
        Route::get('/help-requests/create', [HelpRequestController::class, 'create'])->name('help-requests.create');
        Route::post('/help-requests', [HelpRequestController::class, 'store'])->name('help-requests.store');
        Route::get('/help-requests/{helpRequest}', [HelpRequestController::class, 'show'])->name('help-requests.show');
        Route::post('/help-requests/{helpRequest}/close', [HelpRequestController::class, 'close'])->name('help-requests.close');

        // Holiday routes
        Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');

        // Attendance routes
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
        Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
        Route::get('/attendance/hybrid', [AttendanceController::class, 'hybrid'])->name('attendance.hybrid');
        Route::post('/attendance/mode', [AttendanceController::class, 'updateMode'])->name('attendance.mode');
        Route::post('/attendance/log-wfh', [AttendanceController::class, 'logWfh'])->name('attendance.log-wfh');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::post('/attendance/mark', [AttendanceController::class, 'mark'])->name('attendance.mark');
        Route::get('/attendance/status', [AttendanceController::class, 'status'])->name('attendance.status');

        // Leave Balance routes
        Route::get('/leaves/balance', [LeaveBalanceController::class, 'index'])->name('leaves.balance');

        // Leave routes
        Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
        Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
        Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
        Route::get('/leaves/{leave}', [LeaveController::class, 'show'])->name('leaves.show');
        Route::get('/leaves/history', [LeaveController::class, 'history'])->name('leaves.history');
        Route::delete('/leaves/{leave}', [LeaveController::class, 'destroy'])->name('leaves.destroy');

        // Resignation routes for employees
        Route::resource('resignations', ResignationController::class);

        // Asset Request routes
        Route::prefix('assets')->group(function () {
            Route::get('/', [AssetRequestController::class, 'index'])->name('assets.index');
            Route::get('/request', [AssetRequestController::class, 'create'])->name('assets.request');
            Route::post('/', [AssetRequestController::class, 'store'])->name('assets.store');
            Route::get('/{assetRequest}', [AssetRequestController::class, 'show'])->name('assets.show');
            Route::post('/{assetRequest}/approve', [AssetRequestController::class, 'approve'])->name('assets.approve');
            Route::post('/{assetRequest}/reject', [AssetRequestController::class, 'reject'])->name('assets.reject');
            Route::get('/category/{categoryId}/assets', [AssetRequestController::class, 'getAssetsByCategory']);
        });

        // Cabin Booking routes
        Route::prefix('bookings')->group(function () {
            Route::get('/', [CabinBookingController::class, 'index'])->name('bookings.index');
            Route::get('/create', [CabinBookingController::class, 'create'])->name('bookings.create');
            Route::post('/', [CabinBookingController::class, 'store'])->name('bookings.store');
            Route::get('/calendar', [CabinBookingController::class, 'calendar'])->name('bookings.calendar');
            Route::get('/check-availability', [CabinBookingController::class, 'checkAvailability'])->name('bookings.check-availability');
            Route::get('/statistics', [CabinBookingController::class, 'getStatistics'])->name('bookings.statistics');
            Route::get('/{booking}', [CabinBookingController::class, 'show'])->name('bookings.details');
            Route::delete('/{booking}', [CabinBookingController::class, 'destroy'])->name('bookings.destroy');
            Route::post('/{id}/extend', [CabinBookingController::class, 'extendBooking'])->name('bookings.extend');
            Route::post('/{id}/cancel', [CabinBookingController::class, 'cancelEarly'])->name('bookings.cancel');
            Route::patch('/{booking}/notes', [CabinBookingController::class, 'updateNotes'])->name('bookings.update-notes');
            Route::patch('/{booking}/minutes', [CabinBookingController::class, 'updateMinutes'])->name('bookings.update-minutes');
            Route::post('/{booking}/attendees', [CabinBookingController::class, 'addAttendees'])->name('bookings.add-attendees');
            Route::delete('/{booking}/attendees/{attendee}', [CabinBookingController::class, 'removeAttendee'])->name('bookings.remove-attendee');
            Route::post('/{booking}/teams-meeting', [CabinBookingController::class, 'createTeamsMeeting'])->name('bookings.create-teams-meeting');
        });
    });

    // Farewell Email Routes
    Route::get('/farewell/{resignation}/create', [FarewellController::class, 'create'])->name('farewell.create');
    Route::post('/farewell/{resignation}', [FarewellController::class, 'store'])->name('farewell.store');

    // Relieving Letter Routes
    Route::get('/relieving-letter/{resignation}/create', [RelievingLetterController::class, 'create'])->name('relieving_letter.create');
    Route::post('/relieving-letter/{resignation}', [RelievingLetterController::class, 'store'])->name('relieving_letter.store');

    // Travel Request Routes
    Route::prefix('travel')->group(function () {
        Route::get('/', [TravelRequestController::class, 'index'])->name('travel.index');
        Route::get('/create', [TravelRequestController::class, 'create'])->name('travel.create');
        Route::post('/', [TravelRequestController::class, 'store'])->name('travel.store');
        Route::get('/{travelRequest}', [TravelRequestController::class, 'show'])->name('travel.show');
        Route::post('/{travelRequest}/approve-manager', [TravelRequestController::class, 'approveManager'])->name('travel.approve.manager');
        Route::post('/{travelRequest}/approve-cfo', [TravelRequestController::class, 'approveCFO'])->name('travel.approve.cfo');
        Route::post('/{travelRequest}/reject', [TravelRequestController::class, 'reject'])->name('travel.reject');
        Route::post('/{travelRequest}/update-booking', [TravelRequestController::class, 'updateBooking'])->name('travel.update.booking');
    });

    // Employee Management Routes
    Route::middleware(['auth'])->prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');

        // Document Requests
        Route::resource('document-requests', DocumentRequestController::class);

        // Shift Management
        Route::resource('shift-requests', ShiftRequestController::class);
        Route::resource('rotating-shifts', RotatingShiftController::class);

        // Work Type Management
        Route::resource('work-type-requests', WorkTypeRequestController::class);
        Route::resource('rotating-work-types', RotatingWorkTypeController::class);

        // Disciplinary Actions
        Route::resource('disciplinary-actions', DisciplinaryActionController::class);

        // Policies
        Route::resource('policies', PolicyController::class);
        Route::post('policies/{policy}/acknowledge', [PolicyController::class, 'acknowledge'])->name('policies.acknowledge');

        // Employee Profile Sections
        Route::prefix('{employee}')->group(function () {
            Route::get('attendance', [EmployeeProfileController::class, 'attendance'])->name('attendance');
            Route::get('leave', [EmployeeProfileController::class, 'leave'])->name('leave');
            Route::get('payroll', [EmployeeProfileController::class, 'payroll'])->name('payroll');
            Route::get('allowances', [EmployeeProfileController::class, 'allowances'])->name('allowances');
            Route::get('performance', [EmployeeProfileController::class, 'performance'])->name('performance');
            Route::get('permissions', [EmployeeProfileController::class, 'permissions'])->name('permissions');
            Route::get('documents', [EmployeeProfileController::class, 'documents'])->name('documents');
            Route::get('mail-log', [EmployeeProfileController::class, 'mailLog'])->name('mail-log');
            Route::get('bonus-points', [EmployeeProfileController::class, 'bonusPoints'])->name('bonus-points');
        });
    });

    // Manager routes
    Route::prefix('manager')->middleware([CheckManagerRole::class])->group(function () {
        // Attendance management
        Route::get('/attendance/pending', [ManagerController::class, 'pendingAttendance'])
            ->name('manager.attendance.pending');
        Route::post('/attendance/{attendance}/approve', [ManagerController::class, 'approveAttendance'])
            ->name('manager.attendance.approve');
        Route::post('/attendance/{attendance}/reject', [ManagerController::class, 'rejectAttendance'])
            ->name('manager.attendance.reject');

        // Leave management
        Route::get('/leaves/pending', [ManagerController::class, 'pendingLeaves'])
            ->name('manager.leaves.pending');
        Route::post('/leaves/{leave}/approve', [ManagerController::class, 'approveLeave'])
            ->name('manager.leaves.approve');
        Route::post('/leaves/{leave}/reject', [ManagerController::class, 'rejectLeave'])
            ->name('manager.leaves.reject');

        // Resignation management
        Route::get('/resignations', [ResignationController::class, 'pending_resignations'])
            ->name('resignations.manager.pending_resignations');
        Route::post('/resignations/accept/{id}', [ResignationController::class, 'accept'])
            ->name('resignations.accept');
        Route::post('/resignations/decline/{id}', [ResignationController::class, 'decline'])
            ->name('resignations.decline');
    });

    // HR routes
    Route::middleware(['auth'])->group(function () {
        // Employee management
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::get('/employees/import', [EmployeeController::class, 'showImportForm'])->name('employees.import');
        Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import.store');
        Route::get('/org_chart', [EmployeeController::class, 'organizationChart'])->name('org_chart');
        Route::post('/send-welcome-email', [EmployeeController::class, 'sendWelcomeEmail'])->name('employees.send-welcome-email');

        // Manager management
        Route::resource('managers', ManagerController::class);

        // Dashboard
        Route::get('/visitors/dashboard', [VisitorController::class, 'hrDashboard'])->name('visitors.dashboard');

        // Interview and ID Card
        Route::get('/hr/resignations/interview', [ResignationController::class, 'interview_process'])->name('resignations.interview');
        Route::get('/hr/resignations/{id}/interview', [ResignationController::class, 'start_interview_process'])->name('resignations.interview.process');
        Route::get('/hr/id-card-submission', [IdCardController::class, 'create'])->name('idcard.create');
        Route::post('/hr/id-card-submission', [IdCardController::class, 'store'])->name('idcard.store');

        // Asset Collection
        Route::get('/asset-collection', [AssetCollectionController::class, 'index'])->name('asset-collection.index');
        Route::get('/asset-collection/{resignation_id}', [AssetCollectionController::class, 'show'])->name('asset-collection.show');
        Route::post('/asset-collection/{resignation_id}', [AssetCollectionController::class, 'collect'])->name('asset-collection.collect');
        Route::post('/generate-noc/{resignation_id}', [AssetCollectionController::class, 'generateNOC'])->name('asset-collection.noc');

        // Departments and Designations
        Route::resource('departments', DepartmentController::class);
        Route::resource('designations', DesignationController::class);
    });

    // HR Routes
    Route::middleware(['auth', CheckHrRole::class])->prefix('hr')->group(function () {
        // Resignation Prediction routes
        Route::prefix('resignations/predictions')->group(function () {
            Route::get('/', [ResignationPredictionController::class, 'index'])->name('resignations.predictions.index');
            Route::get('/{employeeId}', [ResignationPredictionController::class, 'show'])->name('resignations.predictions.show');
            Route::post('/{employeeId}/analyze', [ResignationPredictionController::class, 'analyze'])->name('resignations.predictions.analyze');
            Route::post('/analyze-all', [ResignationPredictionController::class, 'analyzeAll'])->name('resignations.predictions.analyze-all');
        });

        // Bulk Leave Import routes
        Route::get('/leaves/bulk-import', [LeaveController::class, 'bulkImportForm'])->name('leaves.bulk-import');
        Route::post('/leaves/bulk-import', [LeaveController::class, 'bulkImport'])->name('leaves.bulk-import.store');
    });

    // Holiday routes
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    Route::get('/holidays/create', [HolidayController::class, 'create'])->name('holidays.create');
    Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');

    // Snipe-IT routes
    Route::get('/snipeit/user', [SnipeITController::class, 'showCurrentUser'])->name('snipeit.user.show');
    Route::get('/snipeit/assets', [SnipeITController::class, 'showUserHardware'])->name('snipeit.assets.show');
    Route::get('/asset/details/{id}', [SnipeITController::class, 'assetDetails'])->name('asset.details');
    Route::get('/snipeit/accessories', [SnipeITController::class, 'showUserAccessories'])->name('snipeit.accessories.show');
    Route::get('/snipeit/licenses', [SnipeITController::class, 'showUserLicenses'])->name('snipeit.licenses.show');
    Route::get('/snipeit/users/{user}', [SnipeITController::class, 'showUser'])->name('snipeit.user.show.single');

    // Asset routes
    Route::get('/assets/create', [AssetRequestController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AssetRequestController::class, 'store'])->name('assets.store');
    Route::get('/assets/by-category/{categoryId}', [AssetRequestController::class, 'getAssetsByCategory'])->name('assets.by-category');

      // 1️⃣ Approval routes should come first
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::get('/bills/approval', [VendorBillApprovalController::class, 'index'])->name('admin.bills.approval.index');
        Route::get('/bills/approval/{id}', [VendorBillApprovalController::class, 'show'])->name('admin.bills.approval.show');
        Route::match(['post', 'put'], '/bills/approval/{id}', [VendorBillApprovalController::class, 'updateStatus'])->name('admin.bills.approval.update');
        Route::match(['post', 'put'], '/bills/approval/{id}/process-payment', [VendorBillApprovalController::class, 'processPayment'])
            ->name('admin.bills.approval.process-payment');
        Route::post('/bills/{bill}/tds', [VendorBillApprovalController::class, 'saveTds'])
            ->name('admin.bills.tds.save');
        Route::put('/admin/bills/{id}/update-date', [VendorBillApprovalController::class, 'updateDate'])
            ->name('admin.bills.updateDate');

    });

    // 2️⃣ Then your generic bill routes
    Route::prefix('admin/bills')->name('admin.bills.')->group(function () {
        Route::get('/', [VendorBillController::class, 'index'])->name('index');
        Route::get('/{id}', [VendorBillController::class, 'show'])->name('show');
        Route::get('/{id}/download', [VendorBillController::class, 'download'])->name('download');
        Route::get('/export', [VendorBillController::class, 'export'])->name('export');
    });

    // Document Center routes
    Route::get('document-center', [DocumentCenterController::class, 'index'])->name('document-center.index');
    Route::resource('payslips', PayslipController::class);
    Route::resource('form16s', Form16Controller::class);
    Route::resource('policies', CompanyPolicyController::class);
    Route::resource('forms', FormController::class);

    // Exit Interview routes
    Route::get('/exit-interview', [ExitInterviewController::class, 'showForm'])->name('exit_interview.show');
    Route::post('/exit-interview', [ExitInterviewController::class, 'submitForm'])->name('exit_interview.submit');
    Route::post('/interview/save/{resignation}', [InterviewController::class, 'store'])->name('interview.save');
    Route::post('/resignation/transfer/{resignation}', [InterviewController::class, 'transfer'])->name('resignation.transfer');
    Route::get('/department/{id}/manager', [InterviewController::class, 'getManager']);
    Route::get('/employee-feedback/{resignationId}', [ExitInterviewController::class, 'showEmployeeFeedback'])->name('employee.feedback');


    Route::post('/resignation/revive/{resignation}', [InterviewController::class, 'revive'])->name('resignation.revive');

    // FnF Settlement routes
    Route::get('/fnf', [FnFSettlementController::class, 'index'])->name('fnf.index');
    Route::get('/fnf/{resignation_id}', [FnFSettlementController::class, 'show'])->name('fnf.show');
    Route::post('/fnf/{resignation_id}/calculate', [FnFSettlementController::class, 'calculate'])->name('fnf.calculate');
    Route::post('/fnf/{resignation_id}/generate', [FnFSettlementController::class, 'generate'])->name('fnf.generate');

    // Help routes
    Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    Route::post('/help/submit', [HelpController::class, 'submit'])->name('help.submit');
    Route::get('/documents', [DocumentCenterController::class, 'index'])->name('document.index');

    // Roles and Permissions routes
    Route::post('/roles/switch', [RolesAndPermissionsController::class, 'switchRole'])->name('roles.switch');
    Route::get('/roles-and-permissions', [RolesAndPermissionsController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RolesAndPermissionsController::class, 'createRole'])->name('roles.createRole');
    Route::post('/roles', [RolesAndPermissionsController::class, 'storeRole'])->name('roles.storeRole');
    Route::post('/assign-role', [RolesAndPermissionsController::class, 'assignRole'])->name('assign-role');
    Route::post('/revoke-role', [RolesAndPermissionsController::class, 'revokeRole'])->name('revoke-role');
    Route::post('/assign-permission', [RolesAndPermissionsController::class, 'assignPermission'])->name('assign-permission');
    Route::post('/revoke-permission', [RolesAndPermissionsController::class, 'revokePermission'])->name('revoke-permission');
    Route::get('/roles/{role}/delete', [RolesAndPermissionsController::class, 'deleteRole'])->name('roles.deleteRole');
    Route::delete('/roles/{role}', [RolesAndPermissionsController::class, 'destroyRole'])->name('roles.destroyRole');
    Route::post('/roles/revoke', [RolesAndPermissionsController::class, 'revokeRoleFromUser'])->name('roles.revokeRoleFromUser');
    Route::post('/bulk-assign-roles', [RolesAndPermissionsController::class, 'bulkAssignRoles'])->name('bulk-assign-roles');

    // Cabin Booking routes
    // Removed these routes as they are now in the bookings prefix group

    // Cabin QR Code Management Routes
    // Route::middleware(['auth'])->group(function () {
    //     Route::get('/cabins/admin', [CabinBookingController::class, 'adminIndex'])->name('cabins.admin');
    //     Route::get('/cabins/qr-codes', [CabinBookingController::class, 'qrCodes'])->name('cabins.qr-codes');
    //     Route::get('/cabins/{cabin}/book', [CabinBookingController::class, 'showQrBooking'])->name('cabins.book');
    //     Route::get('/bookings/{booking}', [CabinBookingController::class, 'getBookingDetails'])->name('bookings.details.admin');
    //     Route::post('/cabins/{cabin}/qr-code', [CabinBookingController::class, 'generateQrCode'])->name('cabins.qr-code.generate');
    // });

    // Travel Management Routes
    Route::prefix('travel')->middleware(['auth'])->group(function () {
        Route::get('/', [TravelRequestController::class, 'index'])->name('travel.index');
        Route::get('/create', [TravelRequestController::class, 'create'])->name('travel.create');
        Route::post('/', [TravelRequestController::class, 'store'])->name('travel.store');
        Route::get('/{travelRequest}', [TravelRequestController::class, 'show'])->name('travel.show');

        // Manager and CFO approval routes
        Route::post('/{travelRequest}/approve-manager', [TravelRequestController::class, 'approveManager'])
            ->name('travel.approve.manager')
            ->middleware('can:approveAsManager,travelRequest');
        Route::post('/{travelRequest}/approve-cfo', [TravelRequestController::class, 'approveCFO'])
            ->name('travel.approve.cfo')
            ->middleware('can:approveAsCFO,travelRequest');
        Route::post('/{travelRequest}/reject', [TravelRequestController::class, 'reject'])
            ->name('travel.reject')
            ->middleware('can:reject,travelRequest');

        // Booking management
        Route::post('/{travelRequest}/booking', [TravelRequestController::class, 'updateBooking'])
            ->name('travel.booking.update')
            ->middleware('can:updateBooking,travelRequest');

        // Admin routes
        Route::middleware([CheckHrRole::class])->group(function () {
            Route::get('/admin/dashboard', [TravelRequestController::class, 'adminDashboard'])->name('travel.admin');
        });
    });

    // Social Feed Routes
    // Route::middleware(['auth'])->group(function () {
    //     Route::get('/social-feed', [SocialFeedController::class, 'index'])->name('social-feed.index');
    // });

    // Test routes
    Route::get('/test-manager', function () {
        if (auth()->check() && auth()->user()->hasRole('Manager')) {
            return 'You have Manager role!';
        }
        return 'You do not have Manager role.';
    });

    Route::get('/test-chatbot', function () {
        return view('test-chatbot');
    })->middleware(['auth', 'verified'])->name('test-chatbot');

    Route::get('/test-permissions', function () {
        if (auth()->check()) {
            $user = auth()->user();
            return [
                'user' => $user->name,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ];
        }
        return 'Not authenticated';
    })->name('test.permissions');

    // Test route for chatbot
    Route::get('/test-chatbot-view', function (Request $request) {
        return view('test-chatbot');
    })->middleware('auth');

    // Test route for resignation predictions
    Route::get('/test-predictions', function () {
        $controller = app()->make(ResignationPredictionController::class);
        return $controller->analyzeAll();
    })->middleware('auth');
});

require __DIR__ . '/auth.php';

// Include visitor routes
require __DIR__ . '/web/visitors.php';

// Add HR dashboard route
Route::middleware(['auth', CheckHrRole::class])->group(function () {
    Route::get('/hr/visitors', [VisitorController::class, 'hrDashboard'])->name('visitors.hr-dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Attendance Routes
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::post('check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
    });

    // Document Requests Routes
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('document-requests', [DocumentRequestController::class, 'index'])->name('document-requests.index');
        Route::get('document-requests/create', [DocumentRequestController::class, 'create'])->name('document-requests.create');
        Route::post('document-requests', [DocumentRequestController::class, 'store'])->name('document-requests.store');
        Route::get('document-requests/{documentRequest}', [DocumentRequestController::class, 'show'])->name('document-requests.show');
        Route::put('document-requests/{documentRequest}', [DocumentRequestController::class, 'update'])->name('document-requests.update');
        Route::delete('document-requests/{documentRequest}', [DocumentRequestController::class, 'destroy'])->name('document-requests.destroy');
        Route::get('document-requests/{documentRequest}/download', [DocumentRequestController::class, 'download'])->name('document-requests.download');

        // Shift Requests Routes
        Route::get('shift-requests', [ShiftRequestController::class, 'index'])->name('shift-requests.index');
        Route::get('shift-requests/create', [ShiftRequestController::class, 'create'])->name('shift-requests.create');
        Route::post('shift-requests', [ShiftRequestController::class, 'store'])->name('shift-requests.store');
        Route::get('shift-requests/{shiftRequest}', [ShiftRequestController::class, 'show'])->name('shift-requests.show');
        Route::put('shift-requests/{shiftRequest}', [ShiftRequestController::class, 'update'])->name('shift-requests.update');
        Route::delete('shift-requests/{shiftRequest}', [ShiftRequestController::class, 'destroy'])->name('shift-requests.destroy');
    });
});
// WFH  Request Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/wfh/apply', [WfhController::class, 'index'])->name('wfh.apply');
    Route::post('/wfh/apply', [WfhController::class, 'store'])->name('wfh.store');

    // Manager/HR approval routes
    Route::post('/wfh/approve/{id}', [WfhController::class, 'approve'])->name('wfh.approve');
    Route::post('/wfh/reject/{id}', [WfhController::class, 'reject'])->name('wfh.reject');
    Route::get('/wfh/requests', [WfhController::class, 'manageRequests'])->name('wfh.manage');

    Route::get('/wfh/approve/{id}/confirm', [WfhController::class, 'showApproveConfirm'])
        ->name('wfh.approve.confirm');

    Route::get('/wfh/reject/{id}/confirm', [WfhController::class, 'showRejectConfirm'])
        ->name('wfh.reject.confirm');
});

// Work From Home (WFH) Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/wfh/signin', [WorkFromHomeController::class, 'signIn'])->name('workfromhome.signin');
    Route::post('/wfh/signout', [WorkFromHomeController::class, 'signOut'])->name('workfromhome.signout');
    Route::get('/workfromhome/samples', [WorkFromHomeController::class, 'samples'])->name('workfromhome.samples');
    Route::get('/workfromhome', [WorkFromHomeController::class, 'index'])->name('wfh.index');
    Route::get('/wfh/export', [WorkFromHomeController::class, 'exportCsv'])->name('wfh.export');
    Route::post('/upload-photo', [WorkFromHomeController::class, 'uploadPhoto'])->name('upload.photo');

});

Route::get('/location/reverse', [LocationController::class, 'reverse']);
