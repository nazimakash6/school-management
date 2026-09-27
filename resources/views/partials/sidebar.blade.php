@php
    $u = auth()->user();
    if (!$u) return;
@endphp
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard.index') }}" target="_self" class="brand-link">
            @php
                $logoUrl = $globalSchoolInfo->logo_url ?? null;
                $brandName = $globalSchoolInfo->school_name ?? \App\Models\CustomSetting::getByKey('brand_name', 'EduCore ERP');
            @endphp
            @if ($logoUrl)
                <img
                    src="{{ $logoUrl }}"
                    alt="{{ $brandName }}"
                    class="brand-logo me-2"
                    style="max-height: 32px; max-width: 32px; object-fit: contain"
                />
            @else
                <span class="brand-icon"
                    ><i data-lucide="graduation-cap" style="width: 1.25rem; height: 1.25rem"></i
                ></span>
            @endif
            <span class="brand-text">{{ $brandName }}</span>
        </a>
        <button class="sidebar-close" id="sidebarClose">
            <i data-lucide="x" style="width: 1.25rem; height: 1.25rem"></i>
        </button>
    </div>
    <div class="sidebar-content">
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a
                    href="{{ route('dashboard.index') }}"
                    target="_self"
                    class="sidebar-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}"
                >
                    <i data-lucide="layout-dashboard" class="sidebar-link-icon"></i>
                    <span class="sidebar-link-text">Dashboard</span>
                </a>
            </li>

            <!-- Students Section -->
            @if ($u->hasPermission('students.view') || $u->hasPermission('admissions.view') || $u->hasPermission('admissions.create') || $u->hasPermission('students.promote'))
                <li
                    class="sidebar-section-group {{ request()->routeIs('student-list.*', 'admission.*', 'student-promotion.*') ? 'open' : '' }}"
                    data-section="students"
                >
                    <div class="sidebar-section-header">
                        <div class="sidebar-section-title">
                            <i data-lucide="graduation-cap" class="sidebar-section-icon"></i>
                            <span class="sidebar-section-text">Students</span>
                        </div>
                        <i data-lucide="chevron-down" class="sidebar-section-arrow"></i>
                    </div>
                    <ul class="sidebar-section-menu">
                        @if ($u->hasPermission('students.view'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('student-list.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('student-list.*') ? 'active' : '' }}"
                                    ><i data-lucide="users" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Student List</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('admissions.view'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('admission.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('admission.index') ? 'active' : '' }}"
                                    ><i data-lucide="user-plus" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Admission List</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('admissions.create'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('admission.create') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('admission.create') ? 'active' : '' }}"
                                    ><i data-lucide="user-plus" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">New Admission</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('students.promote'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('student-promotion.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('student-promotion.*') ? 'active' : '' }}"
                                    ><i data-lucide="arrow-up-circle" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Promotion</span></a
                                >
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <!-- HR & Finance Section -->
            @if ($u->hasPermission('teachers.manage') || $u->hasPermission('staff.manage') || $u->hasPermission('staff_advances.manage') || $u->hasPermission('payroll.manage') || $u->hasPermission('fees.manage') || $u->hasPermission('fee_statement.view'))
                <li
                    class="sidebar-section-group {{ request()->routeIs('teacher.*', 'staff.*', 'staff-advances.*', 'payroll.*', 'fee-management.*') ? 'open' : '' }}"
                    data-section="hr-finance"
                >
                    <div class="sidebar-section-header">
                        <div class="sidebar-section-title">
                            <i data-lucide="briefcase" class="sidebar-section-icon"></i>
                            <span class="sidebar-section-text">HR & Finance</span>
                        </div>
                        <i data-lucide="chevron-down" class="sidebar-section-arrow"></i>
                    </div>
                    <ul class="sidebar-section-menu">
                        @if ($u->hasPermission('teachers.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('teacher.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('teacher.*') ? 'active' : '' }}"
                                    ><i data-lucide="user-check" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Teachers</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('staff.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('staff.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('staff.*') ? 'active' : '' }}"
                                    ><i data-lucide="briefcase" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Staff</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('staff_advances.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('staff-advances.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('staff-advances.*') ? 'active' : '' }}"
                                    ><i data-lucide="coins" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Salary Advances</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('payroll.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('payroll.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('payroll.index', 'payroll.create', 'payroll.edit', 'payroll.show', 'payroll.trash') ? 'active' : '' }}"
                                    ><i data-lucide="banknote" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Payroll</span></a
                                >
                            </li>
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('payroll.statement') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('payroll.statement*') ? 'active' : '' }}"
                                    ><i data-lucide="file-spreadsheet" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Payroll Statement</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('fees.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('fee-management.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('fee-management.index') ? 'active' : '' }}"
                                    ><i data-lucide="wallet" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Fee Management</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('fee_statement.view'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('fee-management.statement') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('fee-management.statement') ? 'active' : '' }}"
                                    ><i data-lucide="file-spreadsheet" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Fee Statement</span></a
                                >
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <!-- School Setup Section -->
            @if ($u->hasPermission('school_info.manage') || $u->hasPermission('academic_sessions.manage') || $u->hasPermission('classes.manage') || $u->hasPermission('subjects.manage') || $u->hasPermission('groups.manage') || $u->hasPermission('houses.manage'))
                <li
                    class="sidebar-section-group {{ request()->routeIs('school-info.*', 'academic-sessions.*', 'classes.*', 'subjects.*', 'subject-types.*', 'groups.*', 'houses.*') ? 'open' : '' }}"
                    data-section="school-setup"
                >
                    <div class="sidebar-section-header">
                        <div class="sidebar-section-title">
                            <i data-lucide="building-2" class="sidebar-section-icon"></i>
                            <span class="sidebar-section-text">School Setup</span>
                        </div>
                        <i data-lucide="chevron-down" class="sidebar-section-arrow"></i>
                    </div>
                    <ul class="sidebar-section-menu">
                        @if ($u->hasPermission('school_info.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('school-info.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('school-info.*') ? 'active' : '' }}"
                                    ><i data-lucide="school" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">School Information</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('academic_sessions.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('academic-sessions.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('academic-sessions.*') ? 'active' : '' }}"
                                    ><i data-lucide="calendar-days" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Academic Sessions</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('groups.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('groups.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('groups.*') ? 'active' : '' }}"
                                    ><i data-lucide="group" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Academic Groups</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('classes.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('classes.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('classes.*') ? 'active' : '' }}"
                                    ><i data-lucide="book-open" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Classes</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('subjects.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('subjects.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}"
                                    ><i data-lucide="library" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Subjects</span></a
                                >
                            </li>
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('subject-types.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('subject-types.*') ? 'active' : '' }}"
                                    ><i data-lucide="tags" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Subject Types</span></a
                                >
                            </li>
                        @endif

                        @if ($u->hasPermission('houses.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('houses.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('houses.*') ? 'active' : '' }}"
                                    ><i data-lucide="home" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Houses</span></a
                                >
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <!-- Academic Section -->
            @if ($u->hasPermission('attendance.manage') || $u->hasPermission('homework.manage') || $u->hasPermission('classwork.manage') || $u->hasPermission('examinations.manage') || $u->hasPermission('events.manage') || $u->hasPermission('discipline.manage') || $u->hasPermission('quran.manage'))
                <li
                    class="sidebar-section-group {{ request()->routeIs('attendance.*', 'homework.*', 'classwork.*', 'examination.*', 'exam-types.*', 'events.*', 'event-activities.*', 'event-categories.*', 'academic-planning.*', 'discipline.*', 'quran-module.*') ? 'open' : '' }}"
                    data-section="academic"
                >
                    <div class="sidebar-section-header">
                        <div class="sidebar-section-title">
                            <i data-lucide="book-open" class="sidebar-section-icon"></i>
                            <span class="sidebar-section-text">Academic</span>
                        </div>
                        <i data-lucide="chevron-down" class="sidebar-section-arrow"></i>
                    </div>
                    <ul class="sidebar-section-menu">
                        @if ($u->hasPermission('attendance.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('attendance.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}"
                                    ><i data-lucide="check-square" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Attendance</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('homework.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('homework.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('homework.*') ? 'active' : '' }}"
                                    ><i data-lucide="book-copy" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Homework</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('classwork.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('classwork.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('classwork.*') ? 'active' : '' }}"
                                    ><i data-lucide="file-check" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Class Work</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('examinations.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('examination.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('examination.*') || request()->routeIs('exam-types.*') ? 'active' : '' }}"
                                    ><i data-lucide="file-text" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Examination</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('events.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('events.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('events.*') || request()->routeIs('academic-planning.*') ? 'active' : '' }}"
                                    ><i data-lucide="calendar" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Planning & Events</span></a
                                >
                            </li>
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('event-activities.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('event-activities.*') ? 'active' : '' }}"
                                    ><i data-lucide="trophy" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Event Activities</span></a
                                >
                            </li>
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('event-categories.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('event-categories.*') ? 'active' : '' }}"
                                    ><i data-lucide="tag" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Event Categories</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('quran.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('quran-module.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('quran-module.*') ? 'active' : '' }}"
                                    ><i data-lucide="book-marked" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Quran Module</span></a
                                >
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <!-- Operations Section -->
            @if ($u->hasPermission('inventory.manage') || $u->hasPermission('library.manage') || $u->hasPermission('transport.manage'))
                <li
                    class="sidebar-section-group {{ request()->routeIs('inventory.*', 'library.*', 'transport.*') ? 'open' : '' }}"
                    data-section="operations"
                >
                    <div class="sidebar-section-header">
                        <div class="sidebar-section-title">
                            <i data-lucide="layers" class="sidebar-section-icon"></i>
                            <span class="sidebar-section-text">Operations</span>
                        </div>
                        <i data-lucide="chevron-down" class="sidebar-section-arrow"></i>
                    </div>
                    <ul class="sidebar-section-menu">
                        @if ($u->hasPermission('inventory.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('inventory.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}"
                                    ><i data-lucide="package" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Inventory</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('library.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('library.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('library.*') ? 'active' : '' }}"
                                    ><i data-lucide="book" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Library</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('transport.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('transport.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('transport.*') ? 'active' : '' }}"
                                    ><i data-lucide="bus" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Transport</span></a
                                >
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <!-- Communication Section -->
            @if ($u->hasPermission('email.manage') || $u->hasPermission('whatsapp.manage') || $u->hasPermission('meetings.manage'))
                <li
                    class="sidebar-section-group {{ request()->routeIs('email.*', 'meetings.*') ? 'open' : '' }}"
                    data-section="communication"
                >
                    <div class="sidebar-section-header">
                        <div class="sidebar-section-title">
                            <i data-lucide="message-square" class="sidebar-section-icon"></i>
                            <span class="sidebar-section-text">Communication</span>
                        </div>
                        <i data-lucide="chevron-down" class="sidebar-section-arrow"></i>
                    </div>
                    <ul class="sidebar-section-menu">
                        @if ($u->hasPermission('email.manage') || $u->hasPermission('whatsapp.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('email.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('email.*') ? 'active' : '' }}"
                                    ><i data-lucide="mail" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Email Notifications</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('meetings.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('meetings.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('meetings.*') ? 'active' : '' }}"
                                    ><i data-lucide="video" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Meetings</span></a
                                >
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <!-- Administration Section -->
            @if ($u->hasPermission('visitors.manage'))
                <li
                    class="sidebar-section-group {{ request()->routeIs('visitors.*') ? 'open' : '' }}"
                    data-section="administration"
                >
                    <div class="sidebar-section-header">
                        <div class="sidebar-section-title">
                            <i data-lucide="contact" class="sidebar-section-icon"></i>
                            <span class="sidebar-section-text">Administration</span>
                        </div>
                        <i data-lucide="chevron-down" class="sidebar-section-arrow"></i>
                    </div>
                    <ul class="sidebar-section-menu">
                        <li class="sidebar-item">
                            <a
                                href="{{ route('visitors.index') }}"
                                target="_self"
                                class="sidebar-link {{ request()->routeIs('visitors.*') ? 'active' : '' }}"
                                ><i data-lucide="contact" class="sidebar-link-icon"></i
                                ><span class="sidebar-link-text">Visitor Management</span></a
                            >
                        </li>
                    </ul>
                </li>
            @endif

            <!-- Reports & Settings Section -->
            @if ($u->hasPermission('discipline.manage') || $u->hasPermission('certificates.manage') || $u->hasPermission('skills_institute.manage') || $u->hasPermission('id_cards.manage') || $u->hasPermission('audit_logs.view') || $u->hasPermission('reports.view') || $u->hasPermission('settings.manage'))
                <li
                    class="sidebar-section-group {{ request()->routeIs('discipline.*', 'certificates.*', 'skills-institute.*', 'id-cards.*', 'audit-logs.*', 'custom-settings.*') ? 'open' : '' }}"
                    data-section="reports-settings"
                >
                    <div class="sidebar-section-header">
                        <div class="sidebar-section-title">
                            <i data-lucide="bar-chart-3" class="sidebar-section-icon"></i>
                            <span class="sidebar-section-text">Reports & Settings</span>
                        </div>
                        <i data-lucide="chevron-down" class="sidebar-section-arrow"></i>
                    </div>
                    <ul class="sidebar-section-menu">
                        @if ($u->hasPermission('discipline.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('discipline.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('discipline.*') ? 'active' : '' }}"
                                    ><i data-lucide="scale" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Discipline Performance</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('certificates.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('certificates.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}"
                                    ><i data-lucide="file-badge" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Certificates</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('skills_institute.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('skills-institute.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('skills-institute.*') ? 'active' : '' }}"
                                    ><i data-lucide="award" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Skills Institute</span></a
                                >
                            </li>
                        @endif

                        @if ($u->hasPermission('id_cards.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('id-cards.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('id-cards.*') ? 'active' : '' }}"
                                    ><i data-lucide="id-card" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">ID Cards</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('audit_logs.view') || $u->hasPermission('reports.view'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('audit-logs.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}"
                                    ><i data-lucide="clipboard-list" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Audit Logs</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('settings.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('custom-settings.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('custom-settings.*') ? 'active' : '' }}"
                                    ><i data-lucide="settings" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Custom Settings</span></a
                                >
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <!-- System Administration Section -->
            @if ($u->hasPermission('users.manage') || $u->hasPermission('roles.manage') || $u->hasPermission('security.manage'))
                <li
                    class="sidebar-section-group {{ request()->routeIs('users.*', 'roles.*', 'security.*') ? 'open' : '' }}"
                    data-section="system"
                >
                    <div class="sidebar-section-header">
                        <div class="sidebar-section-title">
                            <i data-lucide="shield-check" class="sidebar-section-icon"></i>
                            <span class="sidebar-section-text">System</span>
                        </div>
                        <i data-lucide="chevron-down" class="sidebar-section-arrow"></i>
                    </div>
                    <ul class="sidebar-section-menu">
                        @if ($u->hasPermission('users.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('users.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                                    ><i data-lucide="users" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">User Management</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('roles.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('roles.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                                    ><i data-lucide="shield-check" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Roles & Permissions</span></a
                                >
                            </li>
                        @endif
                        @if ($u->hasPermission('security.manage'))
                            <li class="sidebar-item">
                                <a
                                    href="{{ route('security.index') }}"
                                    target="_self"
                                    class="sidebar-link {{ request()->routeIs('security.*') ? 'active' : '' }}"
                                    ><i data-lucide="lock" class="sidebar-link-icon"></i
                                    ><span class="sidebar-link-text">Security</span></a
                                >
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
        </ul>
    </div>
    <div class="sidebar-footer">
        <div class="sidebar-user" data-bs-toggle="dropdown">
            <div class="avatar avatar-sm">
                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff"
                    alt="{{ auth()->user()->name }}"
                />
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">{{ auth()->user()->role }}</div>
            </div>
            <i data-lucide="chevrons-up-down" class="sidebar-arrow" style="width: 0.875rem; height: 0.875rem"></i>
        </div>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" target="_self" href="{{ route('user-profile.index') }}"
                    ><i data-lucide="user" style="width: 1rem; height: 1rem"></i> Profile</a
                >
            </li>
            <li>
                <a class="dropdown-item" target="_self" href="{{ route('password.edit') }}"
                    ><i data-lucide="shield" style="width: 1rem; height: 1rem"></i> Password Management</a
                >
            </li>
            <li>
                <hr class="dropdown-divider" />
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item text-danger" type="submit">
                        <i data-lucide="log-out" style="width: 1rem; height: 1rem"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
