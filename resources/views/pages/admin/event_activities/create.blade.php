@extends('layouts.app')

@section('title', 'Add Event Activity')

@section('content')
  <div class="content-header mb-4">
    <div>
      <h1 class="page-title">Add Event Activity / Competition</h1>
      <p class="page-subtitle">Configure sub-activity details, participating houses, and schedule under a parent event</p>
    </div>
    <div class="content-header-actions">
      <a href="{{ route('event-activities.index') }}" class="btn btn-outline-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Activities
      </a>
    </div>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
      <h6 class="fw-bold mb-2">Please correct the following errors:</h6>
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <form action="{{ route('event-activities.store') }}" method="POST">
        @csrf
        
        <div class="row g-3">
          <div class="col-md-6">
            <label for="event_id" class="form-label fw-semibold">Parent Event <span class="text-danger">*</span></label>
            <select name="event_id" id="event_id" class="form-select" required>
              <option value="">Select Event...</option>
              @foreach($events as $ev)
                <option value="{{ $ev->id }}" {{ old('event_id', $selectedEventId) == $ev->id ? 'selected' : '' }}>
                  {{ $ev->title }} ({{ $ev->start_date ? $ev->start_date->format('M Y') : 'N/A' }})
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label for="name" class="form-label fw-semibold">Activity Name / Title <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. 100m Sprint Race, Debate Competition, Cricket Match" required>
          </div>

          <div class="col-md-12">
            <div class="d-flex align-items-center justify-content-between mb-1.5">
              <label class="form-label fw-semibold text-dark mb-0">Activity Categories <span class="text-danger">*</span></label>
              <a href="{{ route('event-categories.create') }}" class="small text-primary text-decoration-none fw-semibold" target="_blank">+ Add New Category</a>
            </div>

            <div class="mb-2">
              <select id="category_search_select" class="form-select select2-searchable" data-placeholder="-- Search & Select Category --">
                <option value="">-- Search & Select Category --</option>
                @if(isset($categoriesList) && count($categoriesList) > 0)
                  @foreach($categoriesList as $catObj)
                    <option value="{{ $catObj->id }}" data-name="{{ $catObj->name }}" data-badge="{{ $catObj->badge_class ?? 'bg-primary' }}">
                      {{ $catObj->name }} {{ $catObj->code ? '('.$catObj->code.')' : '' }}
                    </option>
                  @endforeach
                @endif
              </select>
            </div>

            <select name="categories[]" id="categories_real_select" multiple class="d-none" required>
              @if(isset($categoriesList) && count($categoriesList) > 0)
                @foreach($categoriesList as $catObj)
                  <option value="{{ $catObj->id }}" {{ (is_array(old('categories')) && in_array($catObj->id, old('categories'))) || old('event_category_id') == $catObj->id ? 'selected' : '' }}>{{ $catObj->name }}</option>
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
            <label for="venue" class="form-label fw-semibold">Venue / Location</label>
            <input type="text" name="venue" id="venue" class="form-control" value="{{ old('venue') }}" placeholder="e.g. Main Sports Complex Ground, Auditorium">
          </div>

          <div class="col-md-4">
            <label for="activity_date" class="form-label fw-semibold">Activity Date</label>
            <input type="date" name="activity_date" id="activity_date" class="form-control" value="{{ old('activity_date') }}">
          </div>

          <div class="col-md-4">
            <label for="start_time" class="form-label fw-semibold">Start Time</label>
            <input type="text" name="start_time" id="start_time" class="form-control" value="{{ old('start_time') }}" placeholder="e.g. 09:00 AM">
          </div>

          <div class="col-md-4">
            <label for="end_time" class="form-label fw-semibold">End Time</label>
            <input type="text" name="end_time" id="end_time" class="form-control" value="{{ old('end_time') }}" placeholder="e.g. 11:30 AM">
          </div>

          <!-- Participating Houses Checkboxes -->
          <div class="col-12">
            <label class="form-label fw-semibold">Participating Houses <span class="text-danger">*</span></label>
            <p class="text-muted small mb-2">Select which houses are participating in this activity/competition:</p>
            <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded border">
              @php $oldHouses = old('house_ids', []); @endphp
              @foreach($houses as $house)
                <div class="form-check form-check-inline me-4">
                  <input class="form-check-input" type="checkbox" name="house_ids[]" id="house_{{ $house->id }}" value="{{ $house->id }}" {{ in_array($house->id, $oldHouses) ? 'checked' : '' }}>
                  <label class="form-check-label fw-semibold d-inline-flex align-items-center gap-1" for="house_{{ $house->id }}">
                    <span class="d-inline-block rounded-circle" style="width:12px;height:12px;background-color:{{ $house->color ?: '#6366f1' }};"></span>
                    {{ $house->name }} ({{ $house->code }})
                  </label>
                </div>
              @endforeach
            </div>
          </div>

          <div class="col-md-6">
            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select" required>
              @foreach($statuses as $st)
                <option value="{{ $st }}" {{ old('status', 'Scheduled') == $st ? 'selected' : '' }}>{{ $st }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12">
            <label for="rules_notes" class="form-label fw-semibold">Rules, Guidelines & Notes</label>
            <textarea name="rules_notes" id="rules_notes" class="form-control" rows="3" placeholder="Specify competition rules, eligibility, scoring criteria, or referee notes...">{{ old('rules_notes') }}</textarea>
          </div>

          <div class="col-12 text-end mt-4">
            <button type="submit" class="btn btn-primary px-4">
              <i data-lucide="save" style="width:1rem;height:1rem;" class="me-1"></i> Save Activity
            </button>
          </div>
        </div>
      </form>
    </div>
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
