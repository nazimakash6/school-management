@extends('layouts.app')

@section('title', 'Edit Event - ' . $event->title)

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Event</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Edit School Event Details</h1>
            <p class="text-muted small mb-0">Update event date, venue, status, budget, or agenda outline</p>
        </div>
        <div>
            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Calendar
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i data-lucide="alert-triangle" class="me-1" style="width:1rem;height:1rem;"></i> Validation Errors</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Main Form Column -->
            <div class="col-lg-8">
                <!-- 1. Event Overview -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="calendar" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            1. Event Overview & Category
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-dark">Event Title / Name <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $event->title) }}" required>
                            </div>

                            @php
                                $selectedCatIds = old('categories', $event->categories ? $event->categories->pluck('id')->toArray() : ($event->event_category_id ? [$event->event_category_id] : []));
                            @endphp
                            <div class="col-md-12">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <label class="form-label fw-semibold text-dark mb-0">Event Categories <span class="text-danger">*</span></label>
                                    <a href="{{ route('event-categories.create') }}" class="small text-primary text-decoration-none fw-semibold" target="_blank">+ Add New Category</a>
                                </div>

                                <div class="mb-2">
                                    <select id="category_search_select" class="form-select select2-searchable" data-placeholder="-- Search & Select Category --">
                                        <option value="">-- Search & Select Category --</option>
                                        @if(isset($categoriesList) && count($categoriesList) > 0)
                                            @foreach($categoriesList as $cat)
                                                <option value="{{ $cat->id }}" data-name="{{ $cat->name }}" data-badge="{{ $cat->badge_class ?? 'bg-primary' }}">
                                                    {{ $cat->name }} {{ $cat->code ? '('.$cat->code.')' : '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <select name="categories[]" id="categories_real_select" multiple class="d-none" required>
                                    @if(isset($categoriesList) && count($categoriesList) > 0)
                                        @foreach($categoriesList as $cat)
                                            <option value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCatIds) || old('event_category_id', $event->event_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    @endif
                                </select>

                                <!-- Tag Box Container Below -->
                                <div class="card border border-light-subtle bg-light-subtle rounded-3 p-3 shadow-none">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="small fw-semibold text-muted text-uppercase tracking-wider">
                                            <i data-lucide="tags" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Selected Categories Box
                                        </span>
                                        <span id="selected_count_badge" class="badge bg-secondary-subtle text-secondary rounded-pill fs-7">0 selected</span>
                                    </div>
                                    <div id="selected_categories_box" class="d-flex flex-wrap gap-2 align-items-center min-vh-25">
                                        <!-- Dynamic tags render here -->
                                    </div>
                                </div>
                                @error('categories')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Target Audience <span class="text-danger">*</span></label>
                                <select name="target_audience" class="form-select @error('target_audience') is-invalid @enderror" required>
                                    @foreach($audiences as $aud)
                                        <option value="{{ $aud }}" {{ old('target_audience', $event->target_audience) == $aud ? 'selected' : '' }}>{{ $aud }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Organizer Committee / Department</label>
                                <input type="text" name="organizer" class="form-control" value="{{ old('organizer', $event->organizer) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Event Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    @foreach($statuses as $st)
                                        <option value="{{ $st }}" {{ old('status', $event->status) == $st ? 'selected' : '' }}>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Date, Time & Venue -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="clock" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            2. Date, Time Schedule & Venue Location
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $event->start_date ? $event->start_date->format('Y-m-d') : '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d') : '') }}">
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch my-1">
                                    <input class="form-check-input" type="checkbox" name="is_all_day" id="is_all_day" value="1" {{ old('is_all_day', $event->is_all_day) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark" for="is_all_day">All Day Event</label>
                                </div>
                            </div>

                            <div class="col-md-6 time-fields">
                                <label class="form-label fw-semibold text-dark">Start Time</label>
                                <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $event->start_time) }}">
                            </div>

                            <div class="col-md-6 time-fields">
                                <label class="form-label fw-semibold text-dark">End Time</label>
                                <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $event->end_time) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Venue Location <span class="text-danger">*</span></label>
                                <input type="text" name="location" class="form-control" value="{{ old('location', $event->location) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Budget & Description -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="file-text" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            3. Budget & Cover Flyer
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Budget Allocated (PKR)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" step="0.01" name="budget_pkr" class="form-control" value="{{ old('budget_pkr', $event->budget_pkr) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Replace Cover Photo</label>
                                <input type="file" name="banner_image" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div>
                            <label class="form-label fw-semibold text-dark">Event Description & Instructions</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $event->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0">Save Event Changes</h6>
                    </div>
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i data-lucide="save" style="width:1.25rem;height:1.25rem;"></i> Update Event
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    function initCategoryTagBox(selectId, realSelectId, boxId, countId) {
        const $searchSelect = $('#' + selectId);
        const $realSelect   = $('#' + realSelectId);
        const $box          = $('#' + boxId);
        const $count        = $('#' + countId);

        $searchSelect.select2({
            theme: 'bootstrap-5',
            placeholder: '-- Search & Select Category --',
            allowClear: true,
            width: '100%'
        });

        function renderTags() {
            $box.empty();
            const selectedVals = $realSelect.val() || [];
            
            if (selectedVals.length === 0) {
                $box.html('<span class="text-muted small fst-italic py-1"><i data-lucide="info" style="width:0.875rem;height:0.875rem;" class="me-1"></i>No categories selected yet. Use the search dropdown above to add categories.</span>');
                if (window.lucide) lucide.createIcons();
                $count.text('0 selected');
                return;
            }

            $count.text(selectedVals.length + ' selected');

            selectedVals.forEach(function(val) {
                const $opt = $searchSelect.find('option[value="' + val + '"]');
                const name = $opt.data('name') || $opt.text().trim();
                const badgeClass = $opt.data('badge') || 'bg-primary';

                const tagHtml = `
                    <span class="badge ${badgeClass} text-white px-3 py-2 fs-6 rounded-pill d-inline-flex align-items-center gap-2 shadow-sm">
                        <span>${name}</span>
                        <button type="button" class="btn-close btn-close-white remove-cat-tag" data-id="${val}" style="font-size:0.65rem;" aria-label="Remove"></button>
                    </span>
                `;
                $box.append(tagHtml);
            });
        }

        $searchSelect.on('change', function() {
            const val = $(this).val();
            if (!val) return;

            let currentVals = $realSelect.val() || [];
            if (!currentVals.map(String).includes(String(val))) {
                currentVals.push(val);
                $realSelect.val(currentVals).trigger('change');
            }
            $searchSelect.val('').trigger('change.select2');
            renderTags();
        });

        $box.on('click', '.remove-cat-tag', function(e) {
            e.preventDefault();
            const removeId = $(this).data('id').toString();
            let currentVals = $realSelect.val() || [];
            currentVals = currentVals.filter(id => id.toString() !== removeId);
            $realSelect.val(currentVals).trigger('change');
            renderTags();
        });

        renderTags();
    }

    initCategoryTagBox('category_search_select', 'categories_real_select', 'selected_categories_box', 'selected_count_badge');
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
.select2-container--bootstrap-5 .select2-selection {
    border-color: #dee2e6;
    padding: 0.375rem 0.75rem;
    font-size: 0.9rem;
    border-radius: 0.375rem;
    min-height: 38px;
}
.min-vh-25 {
    min-height: 48px;
}
</style>
@endpush
