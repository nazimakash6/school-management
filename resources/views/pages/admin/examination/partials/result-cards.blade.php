    <!-- Examinations List & Class-wise Performance Accordions -->
    @forelse($examinations as $exam)
        @php
            $marks = $exam->marks;
            $studentsGrouped = $marks->groupBy('admission_id');
            
            // Group evaluated students by their class_name
            $evaluatedByClass = $studentsGrouped->groupBy(function($stMarks) {
                return optional($stMarks->first()->admission)->class_name ?: 'General';
            });

            if ($exam->isAllClasses()) {
                $targetClassesList = $evaluatedByClass->keys()->toArray();
            } else {
                $targetClassesList = $exam->target_classes_array;
            }

            if (empty($targetClassesList)) {
                $targetClassesList = [$exam->class_name];
            }

            $overallEvaluated = $studentsGrouped->count();
        @endphp

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i data-lucide="award" style="width:1.25rem;height:1.25rem;" class="text-primary"></i>
                        {{ $exam->title }}
                    </h5>
                    <div class="small text-muted">
                        Exam Type: <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill me-2">{{ optional($exam->examType)->name }}</span>
                        Target Class: 
                        @if($exam->isAllClasses())
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill me-2">All Classes</span>
                        @else
                            @foreach($exam->target_classes_array as $tCls)
                                <span class="badge bg-light text-dark border me-1">{{ $tCls }}</span>
                            @endforeach
                        @endif
                        | Session: {{ optional($exam->academicSession)->session_name ?: 'N/A' }}
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('examination.marks', $exam) }}" class="btn btn-outline-success btn-sm">
                        <i data-lucide="edit-3" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Marks Entry
                    </a>
                    <a href="{{ route('examination.show', $exam) }}" class="btn btn-outline-primary btn-sm">
                        <i data-lucide="eye" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Details
                    </a>
                </div>
            </div>
            <div class="card-body p-3">
                @if($overallEvaluated > 0 || !empty($targetClassesList))
                <div class="accordion d-flex flex-column gap-3" id="examAccordion_{{ $exam->id }}">
                    @foreach($targetClassesList as $clsIdx => $clsName)
                        @php
                            $clsStudentsGrouped = $evaluatedByClass->get($clsName, collect());
                            $clsEvaluated = $clsStudentsGrouped->count();

                            $clsPassedCount = 0;
                            $clsFailedCount = 0;

                            foreach($clsStudentsGrouped as $stId => $stMarks) {
                                $obtained = $stMarks->where('is_absent', false)->sum('marks_obtained');
                                $total = $stMarks->sum('total_marks');
                                $pct = $total > 0 ? ($obtained / $total) * 100 : 0;
                                if ($pct >= 40) {
                                    $clsPassedCount++;
                                } else {
                                    $clsFailedCount++;
                                }
                            }

                            $clsPassPct = $clsEvaluated > 0 ? round(($clsPassedCount / $clsEvaluated) * 100, 1) : 0;
                            $accId = 'exam_' . $exam->id . '_cls_' . Str::slug($clsName ?: 'class_' . $clsIdx);
                        @endphp

                        <div class="accordion-item border rounded-3 overflow-hidden shadow-sm">
                            <h2 class="accordion-header" id="heading_{{ $accId }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} bg-light fw-bold text-dark py-3" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse_{{ $accId }}" 
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                        aria-controls="collapse_{{ $accId }}">
                                    <div class="d-flex align-items-center justify-content-between w-100 me-3 flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <i data-lucide="graduation-cap" style="width:1.25rem;height:1.25rem;" class="text-primary"></i>
                                            <span class="fs-6 fw-bold text-dark">{{ $clsName }}</span>
                                            <span class="text-muted small ms-1">Class Result</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary text-white rounded-pill px-3 py-1.5 fs-7">
                                                {{ $clsEvaluated }} Student(s) Evaluated
                                            </span>
                                            @if($clsEvaluated > 0)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fs-7">
                                                    {{ $clsPassedCount }} Passed
                                                </span>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fs-7">
                                                    {{ $clsFailedCount }} Failed
                                                </span>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 fs-7">
                                                    Pass Rate: {{ $clsPassPct }}%
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse_{{ $accId }}" 
                                 class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                 aria-labelledby="heading_{{ $accId }}" 
                                 data-bs-parent="#examAccordion_{{ $exam->id }}">
                                <div class="accordion-body p-4">
                                    <!-- Summary Stat Cards for this Class -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-3">
                                            <div class="p-3 bg-light rounded-3 border text-center">
                                                <div class="text-muted small fw-semibold">EVALUATED STUDENTS</div>
                                                <div class="h4 fw-bold text-dark mb-0 mt-1">{{ $clsEvaluated }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 bg-success-subtle text-success border border-success-subtle rounded-3 text-center">
                                                <div class="small fw-semibold">PASSED STUDENTS</div>
                                                <div class="h4 fw-bold mb-0 mt-1">{{ $clsPassedCount }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 bg-danger-subtle text-danger border border-danger-subtle rounded-3 text-center">
                                                <div class="small fw-semibold">FAILED STUDENTS</div>
                                                <div class="h4 fw-bold mb-0 mt-1">{{ $clsFailedCount }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 bg-primary-subtle text-primary border border-primary-subtle rounded-3 text-center">
                                                <div class="small fw-semibold">PASS RATE</div>
                                                <div class="h4 fw-bold mb-0 mt-1">{{ $clsPassPct }}%</div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($clsEvaluated > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3">Student Name</th>
                                                    <th>Admission No</th>
                                                    <th>Marks Obtained / Total</th>
                                                    <th>Percentage</th>
                                                    <th>Result</th>
                                                    <th class="pe-3 text-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($clsStudentsGrouped as $stId => $stMarks)
                                                    @php
                                                        $st = optional($stMarks->first())->admission;
                                                        if(!$st) continue;
                                                        $obtained = $stMarks->where('is_absent', false)->sum('marks_obtained');
                                                        $total = $stMarks->sum('total_marks');
                                                        $pct = $total > 0 ? round(($obtained / $total) * 100, 1) : 0;
                                                        $isPass = $pct >= 40;
                                                    @endphp
                                                    <tr>
                                                        <td class="ps-3 fw-bold text-dark">{{ $st->student_name }}</td>
                                                        <td><span class="font-monospace fw-semibold">{{ $st->admission_no }}</span></td>
                                                        <td><span class="fw-bold text-dark">{{ $obtained }}</span> / {{ $total }}</td>
                                                        <td><span class="fw-bold fs-6 {{ $isPass ? 'text-success' : 'text-danger' }}">{{ $pct }}%</span></td>
                                                        <td>
                                                            <span class="badge {{ $isPass ? 'bg-success' : 'bg-danger' }} text-white rounded-pill">
                                                                {{ $isPass ? 'PASS' : 'FAIL' }}
                                                            </span>
                                                        </td>
                                                        <td class="pe-3 text-end d-flex justify-content-end gap-1">
                                                            @php
                                                                $stName  = $st->student_name;
                                                                $stEmail = $st->father_email ?? ($st->guardian_email ?? ($st->email ?? ''));
                                                                $exTitle = $exam->title;
                                                                $resText = $isPass ? 'PASS' : 'FAIL';
                                                                $examSubject = "Exam Result: {$stName} — {$exam->title} ({$resText})";
                                                                $examMsg = "Dear Parent,\n\nThe examination result for {$stName} has been published.\n\nExam: {$exTitle}\nMarks: {$obtained}/{$total} ({$pct}%)\nResult: {$resText}\n\nFor detailed result card, please contact the school.\n\nRegards,\nExamination Department";
                                                            @endphp
                                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Send Email Result Notice"
                                                                    onclick="openEmailModal({
                                                                        name: @js($stName),
                                                                        email: @js($stEmail),
                                                                        type: 'Parent',
                                                                        template: 'exam',
                                                                        subject: @js($examSubject),
                                                                        message: @js($examMsg)
                                                                    })">
                                                                <i data-lucide="mail" style="width:13px;height:13px;"></i>
                                                            </button>
                                                            <a href="{{ route('examination.result-card', [$st->id, 'examination_id' => $exam->id]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i data-lucide="printer" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Result Card
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-4 text-muted small">No marks recorded yet for {{ $clsName }} in this examination.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4 text-muted small">No marks recorded yet for this examination.</div>
                @endif
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm rounded-3 p-5 text-center text-muted">
            <i data-lucide="bar-chart" style="width:3rem;height:3rem;" class="mx-auto text-secondary mb-2"></i>
            <h5>No Results Found</h5>
            <p class="small mb-0">No examinations with submitted marks match your active filters.</p>
        </div>
    @endforelse
