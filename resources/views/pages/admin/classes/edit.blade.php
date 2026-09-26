@extends ('layouts.app')

@section ('title', 'Edit Class')

@push ('styles')
    <link rel="stylesheet" href="{{ asset('css/classes-edit.css') }}" />
@endpush

@section ('content')
    <div class="content-header">
        <div>
            <h1 class="page-title">Edit Class</h1>
            <p class="page-subtitle">Update class information</p>
        </div>
        <div class="content-header-actions">
            <button type="submit" form="studentClassForm" class="btn btn-primary btn-sm">
                <i data-lucide="save" style="width: 1rem; height: 1rem"></i> Update
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form
                id="studentClassForm"
                class="row g-3"
                method="POST"
                action="{{ route('classes.update', $studentClass) }}"
            >
                @csrf
                @method ('PUT')
                <div class="col-md-4">
                    <label class="form-label">Class Name <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name', $studentClass->name) }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Academic Level <span class="text-danger">*</span></label>
                    <select name="level" class="form-select" required>
                        @foreach (['Primary', 'Middle', 'Secondary', 'Higher Secondary'] as $level)
                            <option value="{{ $level }}" @selected (old('level', $studentClass->level) === $level)>
                                {{ $level }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Academic Group</label>
                    <select name="group" class="form-select">
                        <option value="">Select Group (Optional / General)</option>
                        @foreach ($groups as $group)
                            <option
                                value="{{ $group->name }}"
                                @selected (old('group', $studentClass->group) === $group->name)
                            >
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-tertiary">Auto-fetched from Groups</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sections</label>
                    <input
                        type="number"
                        class="form-control"
                        value="{{ count(old('section_names', $studentClass->section_names ?? [])) }}"
                        readonly
                    />
                    <small class="text-tertiary">Auto-calculated from section tags</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sections Name <span class="text-danger">*</span></label>
                    <div class="form-control p-2" id="sectionTagsEdit" style="min-height: 44px">
                        <div class="d-flex flex-wrap gap-2 align-items-center" data-tags-wrap></div>
                        <input
                            type="text"
                            class="border-0 w-100 mt-2"
                            data-tags-input
                            placeholder="Type section and press Enter"
                        />
                    </div>
                    <div data-tags-hidden-inputs></div>
                    <small class="text-tertiary">Press Enter to add each section tag</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Class Incharge</label>
                    <select name="staff_table_id" class="form-select">
                        <option value="">Select Class Incharge (Optional)</option>
                        @foreach ($teachers as $teacher)
                            <option
                                value="{{ $teacher->id }}"
                                @selected (old('staff_table_id', $studentClass->staff_table_id) == $teacher->id)
                            >
                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active" @selected (old('status', $studentClass->status) === 'active')>
                            Active
                        </option>
                        <option value="inactive" @selected (old('status', $studentClass->status) === 'inactive')>
                            Inactive
                        </option>
                    </select>
                </div>
                @if (isset($classSubjects) && $classSubjects->count() > 0)
                    <div class="col-12">
                        <label class="form-label">Assigned Subjects for {{ $studentClass->name }}</label>
                        <div class="p-3 border rounded bg-light d-flex flex-wrap gap-2">
                            @foreach ($classSubjects as $sub)
                                <span
                                    class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"
                                >
                                    {{ $sub->subject_name }} ({{ $sub->subject_code }})
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea
                        name="description"
                        class="form-control"
                        rows="4"
                        >{{ old('description', $studentClass->description) }}</textarea
                    >
                </div>
                <div class="col-12 d-flex justify-content-between">
                    <a href="{{ route('classes.show', $studentClass) }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Update Class</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push ('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tagRoot = document.getElementById('sectionTagsEdit');
            if (!tagRoot) return;

            var form = document.getElementById('studentClassForm');
            var tagsWrap = tagRoot.querySelector('[data-tags-wrap]');
            var tagInput = tagRoot.querySelector('[data-tags-input]');
            var hiddenInputsWrap = form.querySelector('[data-tags-hidden-inputs]');
            var sectionCountInput = form.querySelector('input[readonly]');

            var tags = @json (array_values(old('section_names', $studentClass->section_names ?? [])));

            function renderTags() {
                tagsWrap.innerHTML = '';
                hiddenInputsWrap.innerHTML = '';

                tags.forEach(function (tag, index) {
                    var chip = document.createElement('span');
                    chip.className = 'badge bg-secondary d-inline-flex align-items-center gap-1';
                    chip.innerHTML = '<span>' + tag + '</span>';

                    var removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm text-white p-0 border-0 bg-transparent';
                    removeBtn.style.lineHeight = '1';
                    removeBtn.textContent = 'x';
                    removeBtn.addEventListener('click', function () {
                        tags.splice(index, 1);
                        renderTags();
                    });

                    chip.appendChild(removeBtn);
                    tagsWrap.appendChild(chip);

                    var hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'section_names[]';
                    hiddenInput.value = tag;
                    hiddenInputsWrap.appendChild(hiddenInput);
                });

                sectionCountInput.value = tags.length;
            }

            function addTagFromInput() {
                var value = tagInput.value.trim();
                if (value === '') return;
                if (tags.includes(value)) {
                    tagInput.value = '';
                    return;
                }

                tags.push(value);
                tagInput.value = '';
                renderTags();
            }

            tagInput.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter') return;
                event.preventDefault();
                addTagFromInput();
            });

            form.addEventListener('submit', function (event) {
                addTagFromInput();
                if (tags.length > 0) return;

                event.preventDefault();
                alert('Please add at least one section name.');
                tagInput.focus();
            });

            renderTags();
        });
    </script>
@endpush
