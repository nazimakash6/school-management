@extends('layouts.app')

@section('title', 'Edit Book - ' . $book->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/library.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Content Header --}}
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="pencil" class="text-primary" style="width: 1.75rem; height: 1.75rem;"></i>
                Edit Library Book
            </h1>
            <p class="text-muted mb-0">SKU: <span class="fw-bold font-monospace text-dark">{{ $book->book_code }}</span> · {{ $book->title }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('library.show', $book->id) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="eye" style="width: 1rem; height: 1rem;"></i>
                <span>View Details</span>
            </a>
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

    <form action="{{ route('library.update', $book->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                                <input type="text" name="title" class="form-control" placeholder="e.g. Fundamentals of Physics" value="{{ old('title', $book->title) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Book Code / SKU</label>
                                <input type="text" name="book_code" class="form-control font-monospace" placeholder="Book Code" value="{{ old('book_code', $book->book_code) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Author Name <span class="text-danger">*</span></label>
                                <input type="text" name="author" class="form-control" placeholder="e.g. David Halliday" value="{{ old('author', $book->author) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Publisher Name</label>
                                <input type="text" name="publisher" class="form-control" placeholder="e.g. Wiley" value="{{ old('publisher', $book->publisher) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $book->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">ISBN Number</label>
                                <input type="text" name="isbn" class="form-control" placeholder="e.g. 978-1118230725" value="{{ old('isbn', $book->isbn) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Book Unit Price (PKR)</label>
                                <input type="number" step="0.01" name="price" class="form-control" min="0" value="{{ old('price', $book->price) }}">
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
                                <input type="number" name="total_copies" class="form-control" min="1" value="{{ old('total_copies', $book->total_copies) }}" required>
                                <div class="text-muted mt-1" style="font-size:0.75rem;">Currently available copies: {{ $book->available_copies }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Shelf / Rack Location</label>
                                <input type="text" name="rack_location" class="form-control" placeholder="e.g. Shelf B-3" value="{{ old('rack_location', $book->rack_location) }}">
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
                        <div class="mb-3 text-center">
                            <label class="form-label fw-semibold text-dark small d-block">Current Cover Photo</label>
                            <img id="coverPreviewImg" src="{{ $book->cover_image_url }}" alt="Cover Art" class="book-cover-large img-fluid rounded mb-2">
                        </div>

                        <div>
                            <label class="form-label fw-semibold text-dark small">Replace Cover Image (Optional)</label>
                            <input type="file" name="cover_image" class="form-control form-control-sm" accept="image/*" onchange="previewCoverImage(this)">
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
                        <textarea name="description" class="form-control" rows="4" placeholder="Enter book summary...">{{ old('description', $book->description) }}</textarea>
                    </div>
                    <div class="card-footer bg-light border-top p-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i data-lucide="save" style="width:1rem;height:1rem;"></i>
                            <span>Update Book Details</span>
                        </button>
                        <a href="{{ route('library.show', $book->id) }}" class="btn btn-outline-secondary w-100">Cancel</a>
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
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
