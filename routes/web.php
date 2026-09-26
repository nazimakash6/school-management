<?php

use App\Http\Controllers\Admin\AcademicPlanningController;
use App\Http\Controllers\Admin\AcademicSessionController;
use App\Http\Controllers\Admin\AdmissionController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AuditLogsController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ClassWorkController;
use App\Http\Controllers\Admin\ComponentLibraryController;
use App\Http\Controllers\Admin\CustomSettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DisciplineController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventActivityController;
use App\Http\Controllers\Admin\EventCategoryController;
use App\Http\Controllers\Admin\ExaminationController;
use App\Http\Controllers\Admin\ExamTypeController;
use App\Http\Controllers\Admin\FeeManagementController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\HelpController;
use App\Http\Controllers\Admin\HouseController;
use App\Http\Controllers\Admin\HomeWorkController;
use App\Http\Controllers\Admin\IdCardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\LibraryController;
use App\Http\Controllers\Admin\LostFoundController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ParentController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\QuranModuleController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SchoolInfoController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SkillsInstituteController;
use App\Http\Controllers\Admin\SmsController;
use App\Http\Controllers\Admin\StaffAdvanceController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StudentClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentPromotionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SubjectTypeController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TransportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\VisitorController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return Auth::check()
		? redirect()->route('dashboard.index')
		: redirect()->route('login');
});

Route::middleware('guest')->group(function () {
	Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
	Route::get('/school-info/register', fn() => redirect()->route('register'));
	Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
	Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
	Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
	Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
	Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
	Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
	Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware(['auth', 'active', 'track.activity'])->group(function () {
	Route::match(['get', 'post'], '/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
	Route::get('/password', [PasswordController::class, 'edit'])->name('password.edit');
	Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

	Route::get('academic-planning', function () {
		return redirect()->route('events.index');
	})->name('academic-planning.index');
	Route::get('/academic-sessions/trash', [AcademicSessionController::class, 'trashPage'])->name('academic-sessions.trash');
	Route::post('/academic-sessions/{id}/restore', [AcademicSessionController::class, 'restore'])->name('academic-sessions.restore');
	Route::post('/academic-sessions/{id}/force-delete', [AcademicSessionController::class, 'forceDelete'])->name('academic-sessions.force-delete');
	Route::delete('/academic-sessions/bulk-trash', [AcademicSessionController::class, 'bulkTrash'])
		->name('academic-sessions.bulk-trash');
	Route::post('/academic-sessions/bulk-action', [AcademicSessionController::class, 'bulkAction'])
		->name('academic-sessions.bulk-action');
	Route::resource('academic-sessions', AcademicSessionController::class);
	Route::get('admission-trash', [AdmissionController::class, 'trash'])->name('admission.trash');
	Route::get('admission/export', [AdmissionController::class, 'export'])->name('admission.export');
	Route::get('admission/sample-csv', [AdmissionController::class, 'sampleCsv'])->name('admission.sample-csv');
	Route::post('admission/import', [AdmissionController::class, 'import'])->name('admission.import');
	Route::post('admission/bulk-action', [AdmissionController::class, 'bulkAction'])->name('admission.bulk-action');
	Route::post('admission/{id}/restore', [AdmissionController::class, 'restore'])->name('admission.restore');
	Route::delete('admission/{id}/force-delete', [AdmissionController::class, 'forceDelete'])->name('admission.force-delete');
	Route::get('admission/blank-form', [AdmissionController::class, 'blankForm'])->name('admission.blank-form');
	Route::get('admission/{admission}/print', [AdmissionController::class, 'print'])->name('admission.print');
	Route::resource('admission', AdmissionController::class);
	Route::get('student-list/export', [StudentController::class, 'export'])->name('student-list.export');
	Route::get('student-list/{student}/print', [StudentController::class, 'print'])->name('student-list.print');
	Route::resource('student-list', StudentController::class)->except(['create', 'store']);
	Route::post('attendance/mark', [AttendanceController::class, 'markAttendance'])->name('attendance.mark');
	Route::post('attendance/settings', [AttendanceController::class, 'updateSettings'])->name('attendance.settings');
	Route::resource('attendance', AttendanceController::class)->except(['store', 'update', 'destroy']);
	// Audit Logs Routes
	Route::get('audit-logs/export', [AuditLogsController::class, 'export'])->name('audit-logs.export');
	Route::post('audit-logs/clear', [AuditLogsController::class, 'clear'])->name('audit-logs.clear');
	Route::resource('audit-logs', AuditLogsController::class)->only(['index', 'show']);

	Route::resource('backup', BackupController::class)->except(['store', 'update', 'destroy']);
	Route::get('certificates/students-by-class', [CertificateController::class, 'getStudentsByClass'])->name('certificates.students-by-class');
	Route::post('certificates/print', [CertificateController::class, 'print'])->name('certificates.print');
	Route::resource('certificates', CertificateController::class)->except(['store', 'update', 'destroy']);
	Route::get('classes/trash', [StudentClassController::class, 'trash'])->name('classes.trash');
	Route::post('classes/{id}/restore', [StudentClassController::class, 'restore'])->name('classes.restore');
	Route::delete('classes/{id}/force-delete', [StudentClassController::class, 'forceDelete'])->name('classes.force-delete');
	Route::delete('classes/bulk-trash', [StudentClassController::class, 'bulkTrash'])->name('classes.bulk-trash');
	Route::post('classes/bulk-action', [StudentClassController::class, 'bulkAction'])->name('classes.bulk-action');
	Route::resource('classes', StudentClassController::class)->parameters([
		'classes' => 'studentClass',
	]);
	Route::resource('component-library', ComponentLibraryController::class)->except(['store', 'update', 'destroy']);

	// Custom Settings Routes
	Route::get('custom-settings', [CustomSettingController::class, 'index'])->name('custom-settings.index');
	Route::post('custom-settings', [CustomSettingController::class, 'update'])->name('custom-settings.update');

	// Security Management Routes
	Route::get('security', [SecurityController::class, 'index'])->name('security.index');
	Route::post('security', [SecurityController::class, 'update'])->name('security.update');
	Route::post('security/ip-ban', [SecurityController::class, 'banIp'])->name('security.ip-ban');
	Route::delete('security/ip-ban/{ipBan}', [SecurityController::class, 'unbanIp'])->name('security.unban-ip');
	Route::resource('dashboard', DashboardController::class)->except(['store', 'update', 'destroy']);
	Route::get('discipline/get-students/{className}', [DisciplineController::class, 'getStudentsByClass'])->name('discipline.get-students');
	Route::get('discipline/get-attendance-score/{studentId}', [DisciplineController::class, 'getAttendanceScore'])->name('discipline.get-attendance-score');
	Route::get('discipline/{id}/print', [DisciplineController::class, 'print'])->name('discipline.print');
	Route::get('discipline/{id}/daily-print', [DisciplineController::class, 'dailyPrint'])->name('discipline.daily-print');
	Route::resource('discipline', DisciplineController::class);
	Route::get('email', [EmailController::class, 'index'])->name('email.index');
	Route::post('email/send', [EmailController::class, 'send'])->name('email.send');
	Route::delete('email/{id}', [EmailController::class, 'destroy'])->name('email.destroy');
	Route::resource('events', EventController::class);
	Route::resource('event-activities', EventActivityController::class);
	Route::resource('event-categories', EventCategoryController::class);
	Route::resource('groups', GroupController::class);

	// Exam Types Management
	Route::resource('exam-types', ExamTypeController::class)->except(['create', 'show', 'edit']);

	// Examination System Routes
	Route::get('examination/filter-options', [ExaminationController::class, 'filterOptions'])->name('examination.filter-options');
	Route::get('examination/results', [ExaminationController::class, 'results'])->name('examination.results');
	Route::get('examination/performance', [ExaminationController::class, 'studentPerformance'])->name('examination.performance');
	Route::get('examination/result-card/{admission}', [ExaminationController::class, 'resultCard'])->name('examination.result-card');
	Route::get('examination/complete-marksheet/{admission}', [ExaminationController::class, 'completeMarksheet'])->name('examination.complete-marksheet');
	Route::get('examination/{examination}/marks', [ExaminationController::class, 'marks'])->name('examination.marks');
	Route::post('examination/{examination}/marks', [ExaminationController::class, 'saveMarks'])->name('examination.save-marks');
	Route::resource('examination', ExaminationController::class);
	Route::resource('houses', HouseController::class);
	Route::get('collect-payment/create', [FeeManagementController::class, 'createCollectPayment'])->name('collect-payment.create');
	Route::post('collect-payment/store', [FeeManagementController::class, 'storeGeneralCollectPayment'])->name('collect-payment.store');
	Route::get('collect-payment/{id}/edit', [FeeManagementController::class, 'editPayment'])->name('collect-payment.edit');
	Route::put('collect-payment/{id}', [FeeManagementController::class, 'updatePayment'])->name('collect-payment.update');
	Route::delete('collect-payment/{id}', [FeeManagementController::class, 'destroyPayment'])->name('collect-payment.destroy');
	Route::get('fee-management/trash', [FeeManagementController::class, 'trash'])->name('fee-management.trash');
	Route::get('fee-statement', [FeeManagementController::class, 'statement'])->name('fee-management.statement');
	Route::get('fee-statement/export', [FeeManagementController::class, 'exportStatement'])->name('fee-management.statement.export');
	Route::get('fee-statement/print', [FeeManagementController::class, 'printStatement'])->name('fee-management.statement.print');
	Route::get('fee-management/collect-payment', [FeeManagementController::class, 'collectPaymentFormGeneral'])->name('fee-management.collect-payment-general');
	Route::get('fee-management/{id}/print', [FeeManagementController::class, 'printVoucher'])->name('fee-management.print');
	Route::get('fee-management/{id}/collect-payment', [FeeManagementController::class, 'collectPaymentForm'])->name('fee-management.collect-payment');
	Route::post('fee-management/{id}/collect-payment', [FeeManagementController::class, 'storePayment'])->name('fee-management.store-payment');
	Route::get('fee-management/payment/{paymentId}/receipt', [FeeManagementController::class, 'printPaymentReceipt'])->name('fee-management.payment-receipt');
	Route::post('fee-management/{id}/payment', [FeeManagementController::class, 'recordPayment'])->name('fee-management.payment');
	Route::post('fee-management/{id}/restore', [FeeManagementController::class, 'restore'])->name('fee-management.restore');
	Route::delete('fee-management/{id}/force-delete', [FeeManagementController::class, 'forceDelete'])->name('fee-management.force-delete');
	Route::resource('fee-management', FeeManagementController::class);
	Route::resource('help', HelpController::class)->except(['store', 'update', 'destroy']);
	Route::get('homework/get-subjects/{classId}', [HomeWorkController::class, 'getSubjects'])->name('homework.get-subjects');
	Route::get('homework/{id}/print', [HomeWorkController::class, 'print'])->name('homework.print');
	Route::post('homework/destroy-group', [HomeWorkController::class, 'destroyGroup'])->name('homework.destroy-group');
	Route::resource('homework', HomeWorkController::class);
	Route::get('classwork/get-subjects/{classId}', [ClassWorkController::class, 'getSubjects'])->name('classwork.get-subjects');
	Route::get('classwork/{id}/print', [ClassWorkController::class, 'print'])->name('classwork.print');
	Route::resource('classwork', ClassWorkController::class);
	Route::get('id-cards/students-by-session/{sessionId}', [IdCardController::class, 'getStudentsBySession'])->name('id-cards.students-by-session');
	Route::get('id-cards/student-data/{id}', [IdCardController::class, 'getStudentData'])->name('id-cards.student-data');
	Route::get('id-cards/staff-data/{id}', [IdCardController::class, 'getStaffData'])->name('id-cards.staff-data');
	Route::resource('id-cards', IdCardController::class)->except(['store', 'update', 'destroy']);
	Route::resource('inventory', InventoryController::class);
	Route::post('library/issue-book', [LibraryController::class, 'issueBook'])->name('library.issue-book');
	Route::post('library/return-book/{issueId}', [LibraryController::class, 'returnBook'])->name('library.return-book');
	Route::resource('library', LibraryController::class);
	Route::resource('lost-found', LostFoundController::class)->except(['store', 'update', 'destroy']);
	Route::post('meetings/{id}/minutes', [MeetingController::class, 'updateMinutes'])->name('meetings.update-minutes');
	Route::post('meetings/{id}/attendees/{attendeeId}', [MeetingController::class, 'updateAttendeeStatus'])->name('meetings.update-attendee');
	Route::resource('meetings', MeetingController::class);
	Route::resource('notifications', NotificationController::class)->except(['store', 'update', 'destroy']);

	Route::get('payroll-statement', [PayrollController::class, 'statement'])->name('payroll.statement');
	Route::get('payroll-statement/export', [PayrollController::class, 'exportStatement'])->name('payroll.statement.export');
	Route::get('payroll-statement/print', [PayrollController::class, 'printStatement'])->name('payroll.statement.print');
	Route::get('payroll-trash', [PayrollController::class, 'trash'])->name('payroll.trash');
	Route::post('payroll/bulk-action', [PayrollController::class, 'bulkAction'])->name('payroll.bulk-action');
	Route::post('payroll/generate-monthly', [PayrollController::class, 'generateMonthly'])->name('payroll.generate-monthly');
	Route::get('payroll/staff-details', [PayrollController::class, 'getStaffDetails'])->name('payroll.staff-details');
	Route::post('payroll/{id}/restore', [PayrollController::class, 'restore'])->name('payroll.restore');
	Route::delete('payroll/{id}/force-delete', [PayrollController::class, 'forceDelete'])->name('payroll.force-delete');
	Route::resource('payroll', PayrollController::class);
	Route::get('quran-module/get-classes-by-session/{sessionId}', [QuranModuleController::class, 'getClassesBySession'])->name('quran-module.get-classes-by-session');
	Route::get('quran-module/get-students/{className}', [QuranModuleController::class, 'getStudentsByClass'])->name('quran-module.get-students');
	Route::get('quran-module/latest-progress/{studentId}', [QuranModuleController::class, 'getStudentLatestProgress'])->name('quran-module.latest-progress');
	Route::resource('quran-module', QuranModuleController::class);
	

	Route::resource('roles', RoleController::class);
	Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
	Route::resource('users', UserController::class);
	Route::post('school-info/update', [SchoolInfoController::class, 'updateSchoolInfo'])->name('school-info.update-info');
	Route::resource('school-info', SchoolInfoController::class);
	Route::resource('sections', SectionController::class)->except(['store', 'update', 'destroy']);
	Route::resource('settings', SettingController::class)->except(['store', 'update', 'destroy']);
	Route::get('skills-institute/get-students/{className}', [SkillsInstituteController::class, 'getStudentsByClass'])->name('skills-institute.get-students');
	Route::get('skills-institute/student-history/{studentId}', [SkillsInstituteController::class, 'getStudentSkillsHistory'])->name('skills-institute.student-history');
	Route::resource('skills-institute', SkillsInstituteController::class);
	Route::resource('sms', SmsController::class)->except(['store', 'update', 'destroy']);
	Route::get('staff-trash', [StaffController::class, 'trash'])->name('staff.trash');
	Route::get('staff/export', [StaffController::class, 'export'])->name('staff.export');
	Route::get('staff/sample-csv', [StaffController::class, 'sampleCsv'])->name('staff.sample-csv');
	Route::post('staff/import', [StaffController::class, 'import'])->name('staff.import');
	Route::post('staff/bulk-action', [StaffController::class, 'bulkAction'])->name('staff.bulk-action');
	Route::post('staff/{id}/restore', [StaffController::class, 'restore'])->name('staff.restore');
	Route::get('staff-advances/trash', [StaffAdvanceController::class, 'trash'])->name('staff-advances.trash');
	Route::post('staff-advances/bulk-action', [StaffAdvanceController::class, 'bulkAction'])->name('staff-advances.bulk-action');
	Route::get('staff-advances/{id}/print', [StaffAdvanceController::class, 'print'])->name('staff-advances.print');
	Route::post('staff-advances/{id}/repayment', [StaffAdvanceController::class, 'recordRepayment'])->name('staff-advances.repayment');
	Route::post('staff-advances/{id}/restore', [StaffAdvanceController::class, 'restore'])->name('staff-advances.restore');
	Route::delete('staff-advances/{id}/force-delete', [StaffAdvanceController::class, 'forceDelete'])->name('staff-advances.force-delete');
	Route::resource('staff-advances', StaffAdvanceController::class);
	Route::get('staff/{staff}/print', [StaffController::class, 'print'])->name('staff.print');
	Route::resource('staff', StaffController::class);
	Route::resource('student-promotion', StudentPromotionController::class);
	Route::resource('subjects', SubjectController::class);
	Route::resource('subject-types', SubjectTypeController::class);
	Route::post('transport/assign-student', [TransportController::class, 'assignStudent'])->name('transport.assign-student');
	Route::delete('transport/remove-student/{allocationId}', [TransportController::class, 'removeStudent'])->name('transport.remove-student');
	Route::post('transport/add-driver-staff', [TransportController::class, 'addDriverStaff'])->name('transport.add-driver-staff');
	Route::resource('transport', TransportController::class);
	Route::resource('user-profile', UserProfileController::class)->except(['store', 'update', 'destroy']);
	Route::post('visitors/{id}/check-out', [VisitorController::class, 'checkOut'])->name('visitors.check-out');
	Route::get('visitors/get-students/{className}', [VisitorController::class, 'getStudentsByClass'])->name('visitors.get-students');
	Route::resource('visitors', VisitorController::class);
	Route::get('teachers/trash', [TeacherController::class, 'trash'])->name('teacher.trash');
	Route::post('teachers/bulk-action', [TeacherController::class, 'bulkAction'])->name('teacher.bulk-action');
	Route::post('teachers/{id}/restore', [TeacherController::class, 'restore'])->name('teacher.restore');
	Route::delete('teachers/{id}/force-delete', [TeacherController::class, 'forceDelete'])->name('teacher.force-delete');
	Route::delete('teachers/{id}', [TeacherController::class, 'destroy'])->name('teacher.destroy');
	Route::get('teachers', [TeacherController::class, 'index'])->name('teacher.index');

});