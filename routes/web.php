<?php

use App\Http\Controllers\Admin\AbsenteeController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\BudgetController;
use App\Http\Controllers\Admin\CashBookController;
use App\Http\Controllers\Admin\CellGroupController;
use App\Http\Controllers\Admin\CheckinController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DropdownOptionController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\FinancialRequestController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\NewSoulController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PettyCashController;
use App\Http\Controllers\Admin\PledgeController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\GuestPaymentController;
use App\Http\Controllers\MemberPortalController;
use App\Http\Controllers\SelfCheckinController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

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
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');

    // Members — admin + membership + pastor
    Route::middleware(RoleMiddleware::using('admin|membership|pastor'))
        ->group(function () {
            Route::resource('members', MemberController::class);
            Route::get('members/{member}/qr', [MemberController::class, 'downloadQr'])->name('members.qr');
            Route::get('members/{member}/print-card', [MemberController::class, 'printCard'])->name('members.print-card');
            Route::post('members/print-cards-bulk', [MemberController::class, 'printCards'])->name('members.print-cards-bulk');
            Route::get('memberss/template', [MemberController::class, 'downloadTemplate'])->name('members.template');
            Route::post('members/import', [MemberController::class, 'importExcel'])->name('members.import');
            Route::get('members-archived', [MemberController::class, 'archived'])->name('members.archived');
            Route::post('members/{id}/restore', [MemberController::class, 'restore'])->name('members.restore');
            Route::delete('members/{id}/force-delete', [MemberController::class, 'forceDelete'])->name('members.force-delete');

            Route::get('/souls', [NewSoulController::class, 'index'])->name('souls.index');
            Route::post('/souls', [NewSoulController::class, 'store'])->name('souls.store');
            Route::get('/souls/{soul}', [NewSoulController::class, 'show'])->name('souls.show');
            Route::post('/souls/{soul}/status', [NewSoulController::class, 'updateStatus'])->name('souls.status');
            Route::post('/souls/{soul}/followup', [NewSoulController::class, 'addFollowup'])->name('souls.followup');
            Route::get('/souls/{soul}/convert', [NewSoulController::class, 'convertToMember'])->name('souls.convert');
            Route::delete('/souls/{soul}', [NewSoulController::class, 'destroy'])->name('souls.destroy');

            Route::resource('cells', CellGroupController::class);
            Route::post('cells/{cell}/members', [CellGroupController::class, 'addMember'])->name('cells.members.add');
            Route::delete('cells/{cell}/members/{member}', [CellGroupController::class, 'removeMember'])->name('cells.members.remove');
        });

    // Visitors — admin + membership + usher + pastor
    Route::middleware(RoleMiddleware::using('admin|membership|usher|pastor'))
        ->group(function () {
            Route::resource('visitors', VisitorController::class);
            Route::post('visitors/quick', [VisitorController::class, 'quickStore'])->name('visitors.quick');
        });

    // Absentees — admin + membership + pastor
    Route::middleware(RoleMiddleware::using('admin|membership|pastor'))
        ->group(function () {
            Route::get('/absentees', [AbsenteeController::class, 'index'])->name('absentees.index');
            Route::post('/absentees/flag', [AbsenteeController::class, 'flag'])->name('absentees.flag');
            Route::post('/absentees/scan', [AbsenteeController::class, 'runScan'])->name('absentees.scan');
            Route::patch('/absentees/{flag}/status', [AbsenteeController::class, 'updateStatus'])->name('absentees.status');
            Route::delete('/absentees/{flag}/unflag', [AbsenteeController::class, 'unflag'])->name('absentees.unflag');
        });

    // Events — admin + pastor
    Route::middleware(RoleMiddleware::using('admin|pastor'))
        ->group(function () {
            Route::resource('events', EventController::class);
            Route::post('events/{event}/activate', [EventController::class, 'activate'])->name('events.activate');
            Route::post('events/{event}/close', [EventController::class, 'close'])->name('events.close');
            Route::get('events/{event}/qr', [EventController::class, 'downloadQr'])->name('events.qr');
        });

    // Check-in — admin + usher + membership + pastor
    Route::middleware(RoleMiddleware::using('admin|usher|membership|pastor'))
        ->group(function () {
            Route::get('/checkin', [CheckinController::class, 'index'])->name('checkin.index');
            Route::get('/checkin/search', [CheckinController::class, 'search'])->name('checkin.search');
            Route::post('/checkin/process', [CheckinController::class, 'checkin'])->name('checkin.process');
            Route::post('/checkin/qr', [CheckinController::class, 'scanQr'])->name('checkin.qr');
            Route::delete('/checkin/remove', [CheckinController::class, 'removeCheckin'])->name('checkin.remove');

            Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
            Route::post('/attendance/headcount', [AttendanceController::class, 'storeHeadcount'])->name('attendance.headcount.store');
            Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
            Route::get('/attendance/history/export/excel', [AttendanceController::class, 'exportHeadcountExcel'])->name('attendance.history.export.excel');
            Route::get('/attendance/history/export/pdf', [AttendanceController::class, 'exportHeadcountPdf'])->name('attendance.history.export.pdf');
        });

    // Reports — admin + membership + pastor
    Route::middleware(RoleMiddleware::using('admin|membership|pastor'))
        ->group(function () {
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/absentees', [ReportController::class, 'absentees'])->name('reports.absentees');
            Route::get('/reports/export/attendance/excel', [ReportController::class, 'exportAttendanceExcel'])->name('reports.export.attendance.excel');
            Route::get('/reports/export/members/excel', [ReportController::class, 'exportMembersExcel'])->name('reports.export.members.excel');
            Route::get('/reports/export/visitors/excel', [ReportController::class, 'exportVisitorsExcel'])->name('reports.export.visitors.excel');
            Route::get('/reports/export/attendance/pdf', [ReportController::class, 'exportAttendancePdf'])->name('reports.export.attendance.pdf');
            Route::get('/reports/export/members/pdf', [ReportController::class, 'exportMembersPdf'])->name('reports.export.members.pdf');
            Route::get('/reports/export/visitors/pdf', [ReportController::class, 'exportVisitorsPdf'])->name('reports.export.visitors.pdf');
            Route::get('/reports/export/event/{event}/pdf', [ReportController::class, 'exportEventPdf'])->name('reports.export.event.pdf');
            Route::get('/reports/export/absentees/excel', [ReportController::class, 'exportAbsenteesExcel'])->name('reports.export.absentees.excel');
            Route::get('/reports/export/absentees/pdf', [ReportController::class, 'exportAbsenteesPdf'])->name('reports.export.absentees.pdf');
            Route::get('/reports/souls', [ReportController::class, 'soulsReport'])->name('reports.souls');
            Route::get('/reports/souls/export/pdf', [ReportController::class, 'exportSoulsPdf'])->name('reports.souls.pdf');
            Route::get('/reports/souls/export/excel', [ReportController::class, 'exportSoulsExcel'])->name('reports.souls.excel');
        });

    // Finance — admin + finance + pastor
    Route::middleware(RoleMiddleware::using('admin|finance|pastor|finance_chairman'))
        ->group(function () {
            Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
            Route::get('/finance/income', [FinanceController::class, 'income'])->name('finance.income');
            Route::post('/finance/income', [FinanceController::class, 'storeIncome'])->name('finance.income.store');
            Route::delete('/finance/income/{income}', [FinanceController::class, 'destroyIncome'])->name('finance.income.destroy');
            Route::get('/finance/income-archived', [FinanceController::class, 'archivedIncome'])->name('finance.income.archived');
            Route::post('/finance/income/{id}/restore', [FinanceController::class, 'restoreIncome'])->name('finance.income.restore');
            Route::delete('/finance/income/{id}/force-delete', [FinanceController::class, 'forceDeleteIncome'])->name('finance.income.force-delete');
            Route::get('/finance/expenses', [FinanceController::class, 'expenses'])->name('finance.expenses');
            Route::post('/finance/expenses', [FinanceController::class, 'storeExpense'])->name('finance.expenses.store');
            Route::delete('/finance/expenses/{expense}', [FinanceController::class, 'destroyExpense'])->name('finance.expenses.destroy');
            Route::get('/finance/expenses-archived', [FinanceController::class, 'archivedExpenses'])->name('finance.expenses.archived');
            Route::post('/finance/expenses/{id}/restore', [FinanceController::class, 'restoreExpense'])->name('finance.expenses.restore');
            Route::delete('/finance/expenses/{id}/force-delete', [FinanceController::class, 'forceDeleteExpense'])->name('finance.expenses.force-delete');
            Route::get('/finance/bulk-income', [FinanceController::class, 'bulkIncome'])->name('finance.bulk-income');
            Route::post('/finance/bulk-income', [FinanceController::class, 'storeBulkIncome'])->name('finance.bulk-income.store');
            Route::get('/finance/income-template', [FinanceController::class, 'downloadTemplate'])->name('finance.income-template');
            Route::post('/finance/upload-excel', [FinanceController::class, 'uploadExcel'])->name('finance.upload-excel');
            Route::get('/finance/online-payments', [FinanceController::class, 'onlinePayments'])->name('finance.online-payments');
            Route::post('/finance/online-payments/{payment}/confirm', [FinanceController::class, 'confirmPayment'])->name('finance.payments.confirm');
            //            Route::post('/finance/online-payments/{payment}/reject',      [FinanceController::class, 'rejectPayment'])->name('finance.payments.reject');
            //            Route::post('/finance/online-payments/bulk-confirm',          [FinanceController::class, 'bulkConfirm'])->name('finance.payments.bulk-confirm');
            Route::get('/finance/member-tithes', [FinanceController::class, 'memberTithes'])->name('finance.member-tithes');
            Route::get('/finance/report', [FinanceController::class, 'report'])->name('finance.report');
            Route::get('/finance/export/pdf', [FinanceController::class, 'exportPdf'])->name('finance.export.pdf');
            Route::get('/finance/export/excel', [FinanceController::class, 'exportExcel'])->name('finance.export.excel');
            Route::get('/finance/sunday-tithes', [FinanceController::class, 'sundayTithes'])->name('finance.sunday-tithes');
            Route::post('/finance/sunday-tithes', [FinanceController::class, 'storeSundayTithes'])->name('finance.sunday-tithes.store');

            Route::get('/pledges', [PledgeController::class, 'index'])->name('pledges.index');
            Route::post('/pledges', [PledgeController::class, 'store'])->name('pledges.store');
            Route::get('/pledges/{pledge}', [PledgeController::class, 'show'])->name('pledges.show');
            Route::post('/pledges/{pledge}/payment', [PledgeController::class, 'addPayment'])->name('pledges.payment');
            Route::post('/pledges/{pledge}/cancel', [PledgeController::class, 'cancel'])->name('pledges.cancel');
            Route::delete('/pledges/{pledge}', [PledgeController::class, 'destroy'])->name('pledges.destroy');

            Route::get('/finance/petty-cash', [PettyCashController::class, 'index'])->name('petty-cash.index');
            Route::post('/finance/petty-cash/replenish', [PettyCashController::class, 'replenish'])->name('petty-cash.replenish');
            Route::post('/finance/petty-cash/disburse', [PettyCashController::class, 'disburse'])->name('petty-cash.disburse');
            Route::delete('/finance/petty-cash/{transaction}', [PettyCashController::class, 'destroy'])->name('petty-cash.destroy');
            Route::get('/finance/petty-cash-archived', [PettyCashController::class, 'archived'])->name('petty-cash.archived');
            Route::post('/finance/petty-cash/{id}/restore', [PettyCashController::class, 'restore'])->name('petty-cash.restore');

            Route::get('/finance/budgets', [BudgetController::class, 'index'])->name('budgets.index');
            Route::post('/finance/budgets', [BudgetController::class, 'store'])->name('budgets.store');
            Route::get('/finance/budgets/template', [BudgetController::class, 'downloadTemplate'])->name('budgets.template');
            Route::post('/finance/budgets/upload', [BudgetController::class, 'uploadTemplate'])->name('budgets.upload');
            Route::post('/finance/budget-lines', [BudgetController::class, 'storeBudgetLine'])->name('budget-lines.store');
            Route::put('/finance/budget-lines/{budgetLine}', [BudgetController::class, 'updateBudgetLine'])->name('budget-lines.update');
            Route::delete('/finance/budget-lines/{budgetLine}', [BudgetController::class, 'destroyBudgetLine'])->name('budget-lines.destroy');

            Route::get('/finance/requests', [FinancialRequestController::class, 'index'])->name('financial-requests.index');
            Route::post('/finance/requests', [FinancialRequestController::class, 'store'])->name('financial-requests.store');
            Route::get('/finance/requests/{financialRequest}', [FinancialRequestController::class, 'show'])->name('financial-requests.show');
            Route::post('/finance/requests/{financialRequest}/approve-pastor', [FinancialRequestController::class, 'approvePastor'])->name('financial-requests.approve-pastor');
            Route::post('/finance/requests/{financialRequest}/approve-super-admin', [FinancialRequestController::class, 'approveSuperAdmin'])->name('financial-requests.approve-super-admin');
            Route::post('/finance/requests/{financialRequest}/reject', [FinancialRequestController::class, 'reject'])->name('financial-requests.reject');
            Route::post('/finance/requests/{financialRequest}/generate-pv', [FinancialRequestController::class, 'generatePv'])->name('financial-requests.generate-pv');
            Route::get('/finance/requests/{financialRequest}/pv', [FinancialRequestController::class, 'downloadPv'])->name('financial-requests.pv.download');

            Route::get('/finance/cash-book', [CashBookController::class, 'index'])->name('cash-book.index');
            Route::post('/finance/cash-book/opening-balance', [CashBookController::class, 'updateOpeningBalance'])->name('cash-book.opening-balance');

            Route::get('/finance/bank-accounts', [BankAccountController::class, 'index'])->name('bank-accounts.index');
            Route::post('/finance/bank-accounts', [BankAccountController::class, 'store'])->name('bank-accounts.store');
            Route::put('/finance/bank-accounts/{bankAccount}', [BankAccountController::class, 'update'])->name('bank-accounts.update');
            Route::delete('/finance/bank-accounts/{bankAccount}', [BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');
        });

    // Notifications — admin only
    Route::middleware(RoleMiddleware::using('admin'))
        ->group(function () {
            Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
            Route::post('/notifications/send', [NotificationController::class, 'sendManual'])->name('notifications.send');
            Route::post('/notifications/run-command', [NotificationController::class, 'runCommand'])->name('notifications.run-command');
        });

    // Users & Settings — admin only
    Route::middleware(RoleMiddleware::using('admin'))
        ->group(function () {
            Route::resource('users', UserController::class);
            Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
            Route::post('/settings/church-info', [SettingsController::class, 'updateChurchInfo'])->name('settings.church-info');
            Route::get('/settings/check-sms-balance', [SettingsController::class, 'checkSMSBalance'])->name('settings.check-sms-balance');

            Route::get('/settings/dropdowns', [DropdownOptionController::class, 'index'])->name('settings.dropdowns.index');
            Route::post('/settings/dropdowns', [DropdownOptionController::class, 'store'])->name('settings.dropdowns.store');
            Route::put('/settings/dropdowns/{dropdownOption}', [DropdownOptionController::class, 'update'])->name('settings.dropdowns.update');
            Route::delete('/settings/dropdowns/{dropdownOption}', [DropdownOptionController::class, 'destroy'])->name('settings.dropdowns.destroy');
        });
});

// Public self check-in routes (no auth required)
Route::prefix('checkin')->name('checkin.')->group(function () {
    Route::get('/{token}', [SelfCheckinController::class, 'show'])->name('show');
    Route::post('/{token}/lookup', [SelfCheckinController::class, 'lookup'])->name('lookup');
    Route::post('/{token}/confirm', [SelfCheckinController::class, 'checkin'])->name('confirm');
});

// Member self-service portal
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [MemberPortalController::class, 'login'])->name('login');
    Route::post('/lookup', [MemberPortalController::class, 'lookup'])->name('lookup');
    Route::get('/lookup', fn () => redirect()->route('portal.login'));
    Route::get('/otp', [MemberPortalController::class, 'showOtp'])->name('otp.show');
    Route::post('/otp', [MemberPortalController::class, 'verifyOtp'])->name('verify-otp');
    Route::post('/verify-otp', [MemberPortalController::class, 'verifyOtp'])->name('verify-otp');
    Route::get('/verify-otp', fn () => redirect()->route('portal.login'));
    Route::get('/dashboard', [MemberPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance', [MemberPortalController::class, 'attendance'])->name('attendance');
    Route::get('/profile', [MemberPortalController::class, 'profile'])->name('profile');
    Route::post('/profile', [MemberPortalController::class, 'updateProfile'])->name('profile.update');
    Route::get('/qr-download', [MemberPortalController::class, 'downloadQr'])->name('qr-download');
    Route::post('/logout', [MemberPortalController::class, 'logout'])->name('logout');

    Route::get('/pay', [MemberPortalController::class, 'paymentPage'])->name('pay');
    Route::post('/pay', [MemberPortalController::class, 'submitPayment'])->name('pay.submit');
    Route::get('/payments', [MemberPortalController::class, 'payments'])->name('payments');
});

// Guest payment (no login required)
Route::get('/give', [GuestPaymentController::class, 'show'])->name('give.show');
Route::post('/give', [GuestPaymentController::class, 'submit'])->name('give.submit');
Route::get('/give/thanks', [GuestPaymentController::class, 'thanks'])->name('give.thanks');

require __DIR__.'/auth.php';
