<?php

use App\Http\Controllers\Admin\AbsenteeController;
use App\Http\Controllers\Admin\CheckinController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\GuestPaymentController;
use App\Http\Controllers\MemberPortalController;
use App\Http\Controllers\SelfCheckinController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProfileController;

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
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('welcome');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard — all staff roles
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile',                     [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile',                    [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password',           [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/photo',              [ProfileController::class, 'updatePhoto'])->name('profile.photo');

    // Members — admin + membership
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('admin|membership'))
        ->group(function () {
            Route::resource('members', MemberController::class);
            Route::get('members/{member}/qr',           [MemberController::class, 'downloadQr'])->name('members.qr');
            Route::get('members/{member}/print-card',   [MemberController::class, 'printCard'])->name('members.print-card');
            Route::post('members/print-cards-bulk',     [MemberController::class, 'printCards'])->name('members.print-cards-bulk');
            Route::get('memberss/template',              [MemberController::class, 'downloadTemplate'])->name('members.template');
            Route::post('members/import',               [MemberController::class, 'importExcel'])->name('members.import');
            Route::get('members-archived',              [MemberController::class, 'archived'])->name('members.archived');
            Route::post('members/{id}/restore',         [MemberController::class, 'restore'])->name('members.restore');
            Route::delete('members/{id}/force-delete',  [MemberController::class, 'forceDelete'])->name('members.force-delete');
        });

    // Visitors — admin + membership + usher
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('admin|membership|usher'))
        ->group(function () {
            Route::resource('visitors', VisitorController::class);
            Route::post('visitors/quick', [VisitorController::class, 'quickStore'])->name('visitors.quick');
        });

    // Absentees — admin + membership
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('admin|membership'))
        ->group(function () {
            Route::get('/absentees',                [AbsenteeController::class, 'index'])->name('absentees.index');
            Route::post('/absentees/flag',          [AbsenteeController::class, 'flag'])->name('absentees.flag');
            Route::post('/absentees/scan',          [AbsenteeController::class, 'runScan'])->name('absentees.scan');
            Route::patch('/absentees/{flag}/status',[AbsenteeController::class, 'updateStatus'])->name('absentees.status');
            Route::delete('/absentees/{flag}/unflag',[AbsenteeController::class, 'unflag'])->name('absentees.unflag');
        });

    // Events — admin only
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('admin'))
        ->group(function () {
            Route::resource('events', EventController::class);
            Route::post('events/{event}/activate',    [EventController::class, 'activate'])->name('events.activate');
            Route::post('events/{event}/close',       [EventController::class, 'close'])->name('events.close');
            Route::get('events/{event}/qr',           [EventController::class, 'downloadQr'])->name('events.qr');
        });

    // Check-in — admin + usher
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('admin|usher'))
        ->group(function () {
            Route::get('/checkin',          [CheckinController::class, 'index'])->name('checkin.index');
            Route::get('/checkin/search',   [CheckinController::class, 'search'])->name('checkin.search');
            Route::post('/checkin/process', [CheckinController::class, 'checkin'])->name('checkin.process');
            Route::post('/checkin/qr',      [CheckinController::class, 'scanQr'])->name('checkin.qr');
            Route::delete('/checkin/remove',[CheckinController::class, 'removeCheckin'])->name('checkin.remove');
        });

    // Reports — admin only
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('admin|membership'))
        ->group(function () {
            Route::get('/reports',                          [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/absentees',                [ReportController::class, 'absentees'])->name('reports.absentees');
            Route::get('/reports/export/attendance/excel',  [ReportController::class, 'exportAttendanceExcel'])->name('reports.export.attendance.excel');
            Route::get('/reports/export/members/excel',     [ReportController::class, 'exportMembersExcel'])->name('reports.export.members.excel');
            Route::get('/reports/export/visitors/excel',    [ReportController::class, 'exportVisitorsExcel'])->name('reports.export.visitors.excel');
            Route::get('/reports/export/attendance/pdf',    [ReportController::class, 'exportAttendancePdf'])->name('reports.export.attendance.pdf');
            Route::get('/reports/export/members/pdf',       [ReportController::class, 'exportMembersPdf'])->name('reports.export.members.pdf');
            Route::get('/reports/export/visitors/pdf',      [ReportController::class, 'exportVisitorsPdf'])->name('reports.export.visitors.pdf');
            Route::get('/reports/export/event/{event}/pdf', [ReportController::class, 'exportEventPdf'])->name('reports.export.event.pdf');
            Route::get('/reports/export/absentees/excel',   [ReportController::class, 'exportAbsenteesExcel'])->name('reports.export.absentees.excel');
            Route::get('/reports/export/absentees/pdf',     [ReportController::class, 'exportAbsenteesPdf'])->name('reports.export.absentees.pdf');
        });

    // Finance — admin + finance
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('admin|finance'))
        ->group(function () {
            Route::get('/finance',                                        [FinanceController::class, 'index'])->name('finance.index');
            Route::get('/finance/income',                                 [FinanceController::class, 'income'])->name('finance.income');
            Route::post('/finance/income',                                [FinanceController::class, 'storeIncome'])->name('finance.income.store');
            Route::delete('/finance/income/{income}',                     [FinanceController::class, 'destroyIncome'])->name('finance.income.destroy');
            Route::get('/finance/income-archived',                        [FinanceController::class, 'archivedIncome'])->name('finance.income.archived');
            Route::post('/finance/income/{id}/restore',                   [FinanceController::class, 'restoreIncome'])->name('finance.income.restore');
            Route::delete('/finance/income/{id}/force-delete',            [FinanceController::class, 'forceDeleteIncome'])->name('finance.income.force-delete');
            Route::get('/finance/expenses',                               [FinanceController::class, 'expenses'])->name('finance.expenses');
            Route::post('/finance/expenses',                              [FinanceController::class, 'storeExpense'])->name('finance.expenses.store');
            Route::delete('/finance/expenses/{expense}',                  [FinanceController::class, 'destroyExpense'])->name('finance.expenses.destroy');
            Route::get('/finance/expenses-archived',                      [FinanceController::class, 'archivedExpenses'])->name('finance.expenses.archived');
            Route::post('/finance/expenses/{id}/restore',                 [FinanceController::class, 'restoreExpense'])->name('finance.expenses.restore');
            Route::delete('/finance/expenses/{id}/force-delete',          [FinanceController::class, 'forceDeleteExpense'])->name('finance.expenses.force-delete');
            Route::get('/finance/bulk-income',                            [FinanceController::class, 'bulkIncome'])->name('finance.bulk-income');
            Route::post('/finance/bulk-income',                           [FinanceController::class, 'storeBulkIncome'])->name('finance.bulk-income.store');
            Route::get('/finance/income-template',                        [FinanceController::class, 'downloadTemplate'])->name('finance.income-template');
            Route::post('/finance/upload-excel',                          [FinanceController::class, 'uploadExcel'])->name('finance.upload-excel');
            Route::get('/finance/online-payments',                        [FinanceController::class, 'onlinePayments'])->name('finance.online-payments');
            Route::post('/finance/online-payments/{payment}/confirm',     [FinanceController::class, 'confirmPayment'])->name('finance.payments.confirm');
            Route::post('/finance/online-payments/{payment}/reject',      [FinanceController::class, 'rejectPayment'])->name('finance.payments.reject');
            Route::post('/finance/online-payments/bulk-confirm',          [FinanceController::class, 'bulkConfirm'])->name('finance.payments.bulk-confirm');
            Route::get('/finance/member-tithes',                          [FinanceController::class, 'memberTithes'])->name('finance.member-tithes');
            Route::get('/finance/report',                                 [FinanceController::class, 'report'])->name('finance.report');
            Route::get('/finance/export/pdf',                             [FinanceController::class, 'exportPdf'])->name('finance.export.pdf');
            Route::get('/finance/export/excel',                           [FinanceController::class, 'exportExcel'])->name('finance.export.excel');
        });

    // Notifications — admin only
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('admin'))
        ->group(function () {
            Route::get('/notifications',              [NotificationController::class, 'index'])->name('notifications.index');
            Route::post('/notifications/send',        [NotificationController::class, 'sendManual'])->name('notifications.send');
            Route::post('/notifications/run-command', [NotificationController::class, 'runCommand'])->name('notifications.run-command');
        });

    // Users & Settings — admin only
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('admin'))
        ->group(function () {
            Route::resource('users', UserController::class);
            Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            Route::get('/settings',                    [SettingsController::class, 'index'])->name('settings.index');
            Route::post('/settings/departments',       [SettingsController::class, 'updateDepartments'])->name('settings.departments');
            Route::post('/settings/income-categories', [SettingsController::class, 'updateIncomeCategories'])->name('settings.income-categories');
            Route::post('/settings/expense-categories',[SettingsController::class, 'updateExpenseCategories'])->name('settings.expense-categories');
        });
});

// Public self check-in routes (no auth required)
Route::prefix('checkin')->name('checkin.')->group(function () {
    Route::get('/{token}',        [SelfCheckinController::class, 'show'])->name('show');
    Route::post('/{token}/lookup', [SelfCheckinController::class, 'lookup'])->name('lookup');
    Route::post('/{token}/confirm',[SelfCheckinController::class, 'checkin'])->name('confirm');
});

// Member self-service portal
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/',              [MemberPortalController::class, 'login'])->name('login');
    Route::post('/lookup',       [MemberPortalController::class, 'lookup'])->name('lookup');
    Route::get('/lookup',          fn() => redirect()->route('portal.login'));
    Route::get('/otp',  [MemberPortalController::class, 'showOtp'])->name('otp.show');
    Route::post('/otp', [MemberPortalController::class, 'verifyOtp'])->name('verify-otp');
    Route::post('/verify-otp',   [MemberPortalController::class, 'verifyOtp'])->name('verify-otp');
    Route::get('/verify-otp',      fn() => redirect()->route('portal.login'));
    Route::get('/dashboard',     [MemberPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance',    [MemberPortalController::class, 'attendance'])->name('attendance');
    Route::get('/profile',       [MemberPortalController::class, 'profile'])->name('profile');
    Route::post('/profile',      [MemberPortalController::class, 'updateProfile'])->name('profile.update');
    Route::get('/qr-download',   [MemberPortalController::class, 'downloadQr'])->name('qr-download');
    Route::post('/logout',       [MemberPortalController::class, 'logout'])->name('logout');

    Route::get('/pay',          [MemberPortalController::class, 'paymentPage'])->name('pay');
    Route::post('/pay',         [MemberPortalController::class, 'submitPayment'])->name('pay.submit');
    Route::get('/payments', [MemberPortalController::class, 'payments'])->name('payments');
});

// Guest payment (no login required)
Route::get('/give',        [GuestPaymentController::class, 'show'])->name('give.show');
Route::post('/give',       [GuestPaymentController::class, 'submit'])->name('give.submit');
Route::get('/give/thanks', [GuestPaymentController::class, 'thanks'])->name('give.thanks');

require __DIR__.'/auth.php';
