@extends('layouts.app')

@section('title', 'Add New Library Book')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/library.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Content Header --}}
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" class="text-primary" style="width: 1.75rem; height: 1.75rem;"></i>
                Add New Library Book
            </h1>
            <p class="text-muted mb-0">Register textbooks, reference materials, or literature to the school catalog</p>
        </div>
        <div>
            <a href="{{ route('library.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                <span>Back to Catalog</span>
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i data-lucide="alert-circle" style="width:1.25rem;height:1.25rem;"></i>
                <strong class="fw-bold">Please fix the following validation errors:</strong>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('library.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            {{-- Main Info Column --}}
            <div class="col-lg-8">
                {{-- Section 1: Book Specifications --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="book" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            1. Book Metadata &amp; Classification
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-dark small">Book Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Fundamentals of Physics (Extended 10th Ed)" value="{{ old('title') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Book Code / SKU</label>
                                <input type="text" name="book_code" class="form-control" placeholder="Auto-generated if blank" value="{{ old('book_code') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Author Name <span class="text-danger">*</span></label>
                                <input type="text" name="author" class="form-control" placeholder="e.g. David Halliday & Robert Resnick" value="{{ old('author') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Publisher Name</label>
                                <input type="text" name="publisher" class="form-control" placeholder="e.g. Wiley Global Education" value="{{ old('publisher') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">ISBN Number</label>
                                <input type="text" name="isbn" class="form-control" placeholder="e.g. 978-1118230725" value="{{ old('isbn') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Book Unit Price (PKR)</label>
                                <input type="number" step="0.01" name="price" class="form-control" min="0" placeholder="0.00" value="{{ old('price', '0.00') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Physical Inventory & Placement --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="layers" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            2. Stock Copies &amp; Shelf Location
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Total Copies Received <span class="text-danger">*</span></label>
                                <input type="number" name="total_copies" class="form-control" min="1" value="{{ old('total_copies', 1) }}" required>
                                <div class="text-muted mt-1" style="font-size:0.75rem;">Available copies will be initialized to this number.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Shelf / Rack Location</label>
                                <input type="text" name="rack_location" class="form-control" placeholder="e.g. Shelf B-3 (Science Section)" value="{{ old('rack_location') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column: Cover Image & Description --}}
            <div class="col-lg-4">
                {{-- Cover Image Card --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="image" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            3. Book Cover Image
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Upload Cover Photo (JPG, PNG, WEBP)</label>
                            <input type="file" name="cover_image" class="form-control" accept="image/*" onchange="previewCoverImage(this)">
                        </div>

                        <div id="coverPreviewContainer" class="text-center p-2 border rounded bg-light" style="display: none;">
                            <img id="coverPreviewImg" src="" alt="Cover Preview" class="img-fluid rounded book-cover-large">
                        </div>
                    </div>
                </div>

                {{-- Synopsis / Description Card --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="file-text" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            4. Book Synopsis / Remarks
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <textarea name="description" class="form-control" rows="4" placeholder="Enter book summary, table of contents, target grade level, or condition notes...">{{ old('description') }}</textarea>
                    </div>
                    <div class="card-footer bg-light border-top p-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i data-lucide="save" style="width:1rem;height:1rem;"></i>
                            <span>Save Book to Catalog</span>
                        </button>
                        <a href="{{ route('library.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function previewCoverImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('coverPreviewImg').src = e.target.result;
            document.getElementById('coverPreviewContainer').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
