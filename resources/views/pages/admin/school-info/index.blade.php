@extends('layouts.app')

@section('title', 'School Information')

@section('content')
  <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
  <div class="content-header mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1.5 fs-7">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
          <li class="breadcrumb-item text-muted">School Setup</li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">School Information</li>
        </ol>
      </nav>
      <div class="d-flex align-items-center gap-2.5">
        <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
          <i data-lucide="school" style="width:1.5rem;height:1.5rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-dark tracking-tight">School Information & Profile</h3>
          <p class="text-muted mb-0 fs-7">Manage global institution profile, official logo, stamp seal, and contact details</p>
        </div>
      </div>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
      <i data-lucide="check-circle" style="width:1.25rem;height:1.25rem;" class="me-2"></i>
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
      <h6 class="fw-bold mb-2">Please fix the following validation errors:</h6>
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <form action="{{ route('school-info.update-info') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Top Preview Header Card -->
    <div class="card border-0 shadow-sm mb-4 bg-white overflow-hidden">
      <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
          <div class="position-relative">
            <div class="rounded-circle border border-2 border-primary-subtle p-2 bg-light d-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px; overflow: hidden;">
              @if($schoolInfo->logo_url)
                <img id="logoPreview" src="{{ $schoolInfo->logo_url }}" alt="School Logo" class="img-fluid object-fit-contain" style="max-height: 85px;">
              @else
                <i id="logoPlaceholder" data-lucide="school" style="width: 3rem; height: 3rem;" class="text-primary"></i>
                <img id="logoPreview" src="" alt="School Logo" class="img-fluid object-fit-contain d-none" style="max-height: 85px;">
              @endif
            </div>
          </div>
          <div class="flex-grow-1 text-center text-md-start">
            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
              <h3 class="fw-bold text-dark mb-0">{{ $schoolInfo->school_name }}</h3>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-7">{{ $schoolInfo->school_code }}</span>
            </div>
            <p class="text-muted mb-1 fs-6">{{ $schoolInfo->tagline }}</p>
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-3 fs-7 text-secondary mt-2">
              <span><i data-lucide="map-pin" style="width:0.875rem;height:0.875rem;" class="me-1 text-primary"></i> {{ $schoolInfo->city }}, {{ $schoolInfo->state }}</span>
              <span><i data-lucide="phone" style="width:0.875rem;height:0.875rem;" class="me-1 text-primary"></i> {{ $schoolInfo->phone }}</span>
              <span><i data-lucide="mail" style="width:0.875rem;height:0.875rem;" class="me-1 text-primary"></i> {{ $schoolInfo->email }}</span>
            </div>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-primary px-4 py-2">
              <i data-lucide="save" style="width:1.1rem;height:1.1rem;" class="me-1"></i> Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Configuration Sections -->
    <div class="row g-4">
      <!-- Left Column: General & Academic -->
      <div class="col-lg-7">
        <!-- Basic School Information -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
            <i data-lucide="info" class="text-primary me-2" style="width:1.25rem;height:1.25rem;"></i>
            <h5 class="card-title fw-bold mb-0">Basic Institution Details</h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              <div class="col-md-8">
                <label for="school_name" class="form-label fw-semibold">School Name <span class="text-danger">*</span></label>
                <input type="text" name="school_name" id="school_name" class="form-control" value="{{ old('school_name', $schoolInfo->school_name) }}" required>
              </div>

              <div class="col-md-4">
                <label for="school_code" class="form-label fw-semibold">School Code</label>
                <input type="text" name="school_code" id="school_code" class="form-control" value="{{ old('school_code', $schoolInfo->school_code) }}" placeholder="e.g. TAS-001">
              </div>

              <div class="col-md-12">
                <label for="tagline" class="form-label fw-semibold">School Motto / Tagline</label>
                <input type="text" name="tagline" id="tagline" class="form-control" value="{{ old('tagline', $schoolInfo->tagline) }}" placeholder="e.g. Excellence in Education">
              </div>

              <div class="col-md-6">
                <label for="principal_name" class="form-label fw-semibold">Principal / Headmaster Name</label>
                <input type="text" name="principal_name" id="principal_name" class="form-control" value="{{ old('principal_name', $schoolInfo->principal_name) }}">
              </div>

              <div class="col-md-3">
                <label for="established_year" class="form-label fw-semibold">Est. Year</label>
                <input type="text" name="established_year" id="established_year" class="form-control" value="{{ old('established_year', $schoolInfo->established_year) }}" placeholder="e.g. 1998">
              </div>

              <div class="col-md-3">
                <label for="currency_symbol" class="form-label fw-semibold">Currency Symbol</label>
                <input type="text" name="currency_symbol" id="currency_symbol" class="form-control" value="{{ old('currency_symbol', $schoolInfo->currency_symbol ?: 'Rs.') }}">
              </div>
            </div>
          </div>
        </div>

        <!-- Academic & Legal Board -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
            <i data-lucide="award" class="text-primary me-2" style="width:1.25rem;height:1.25rem;"></i>
            <h5 class="card-title fw-bold mb-0">Academic & Legal Affiliation</h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="affiliation_board" class="form-label fw-semibold">Affiliation Board</label>
                <input type="text" name="affiliation_board" id="affiliation_board" class="form-control" value="{{ old('affiliation_board', $schoolInfo->affiliation_board) }}" placeholder="e.g. BISE Lahore / Federal Board">
              </div>

              <div class="col-md-6">
                <label for="registration_no" class="form-label fw-semibold">Registration / License No</label>
                <input type="text" name="registration_no" id="registration_no" class="form-control" value="{{ old('registration_no', $schoolInfo->registration_no) }}" placeholder="e.g. REG-2026-LHR">
              </div>
            </div>
          </div>
        </div>

        <!-- Contact & Address -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
            <i data-lucide="map-pin" class="text-primary me-2" style="width:1.25rem;height:1.25rem;"></i>
            <h5 class="card-title fw-bold mb-0">Contact & Address Information</h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="email" class="form-label fw-semibold">Official Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $schoolInfo->email) }}">
              </div>

              <div class="col-md-6">
                <label for="website" class="form-label fw-semibold">Official Website</label>
                <input type="text" name="website" id="website" class="form-control" value="{{ old('website', $schoolInfo->website) }}" placeholder="https://example.com">
              </div>

              <div class="col-md-6">
                <label for="phone" class="form-label fw-semibold">Primary Phone Number</label>
                <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone', $schoolInfo->phone) }}">
              </div>

              <div class="col-md-6">
                <label for="alternate_phone" class="form-label fw-semibold">Alternate Phone / Mobile</label>
                <input type="tel" name="alternate_phone" id="alternate_phone" class="form-control" value="{{ old('alternate_phone', $schoolInfo->alternate_phone) }}">
              </div>

              <div class="col-md-12">
                <label for="address" class="form-label fw-semibold">Street Address</label>
                <textarea name="address" id="address" class="form-control" rows="2">{{ old('address', $schoolInfo->address) }}</textarea>
              </div>

              <div class="col-md-4">
                <label for="city" class="form-label fw-semibold">City</label>
                <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $schoolInfo->city) }}">
              </div>

              <div class="col-md-4">
                <label for="state" class="form-label fw-semibold">State / Province</label>
                <input type="text" name="state" id="state" class="form-control" value="{{ old('state', $schoolInfo->state) }}">
              </div>

              <div class="col-md-4">
                <label for="postal_code" class="form-label fw-semibold">Postal Code</label>
                <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code', $schoolInfo->postal_code) }}">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Branding Assets & Social -->
      <div class="col-lg-5">
        <!-- Logo & Official Stamp Upload -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
            <i data-lucide="image" class="text-primary me-2" style="width:1.25rem;height:1.25rem;"></i>
            <h5 class="card-title fw-bold mb-0">Branding & Official Seal</h5>
          </div>
          <div class="card-body p-4">
            <!-- School Logo Input -->
            <div class="mb-4">
              <label for="logoInput" class="form-label fw-semibold">Upload School Logo</label>
              <input type="file" name="logo" id="logoInput" class="form-control" accept="image/*">
              <small class="text-muted">Recommended: Square transparent PNG or SVG (Max: 2MB)</small>
            </div>

            <hr class="my-4">

            <!-- Official Stamp Input -->
            <div class="mb-3">
              <label for="stampInput" class="form-label fw-semibold">Upload Official Stamp / Principal Seal</label>
              <input type="file" name="stamp" id="stampInput" class="form-control" accept="image/*">
              <small class="text-muted mb-2 d-block">Used on printable report cards, fee challans & certificates</small>
              
              <div class="mt-3 p-3 bg-light rounded border text-center">
                <small class="text-muted d-block mb-2 fw-semibold">Current Official Stamp</small>
                @if($schoolInfo->stamp_url)
                  <img id="stampPreview" src="{{ $schoolInfo->stamp_url }}" alt="School Stamp" class="img-fluid object-fit-contain" style="max-height: 100px;">
                @else
                  <div id="stampPlaceholder" class="text-muted py-2">
                    <i data-lucide="stamp" style="width: 2.5rem; height: 2.5rem;" class="mb-1 d-block mx-auto text-secondary"></i>
                    <small>No official stamp uploaded yet</small>
                  </div>
                  <img id="stampPreview" src="" alt="School Stamp" class="img-fluid object-fit-contain d-none" style="max-height: 100px;">
                @endif
              </div>
            </div>
          </div>
        </div>

        <!-- Social Media Profiles -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
            <i data-lucide="globe" class="text-primary me-2" style="width:1.25rem;height:1.25rem;"></i>
            <h5 class="card-title fw-bold mb-0">Social Media Links</h5>
          </div>
          <div class="card-body p-4">
            <div class="mb-3">
              <label for="social_facebook" class="form-label fw-semibold">Facebook Page URL</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i data-lucide="facebook" style="width:1rem;height:1rem;" class="text-primary"></i></span>
                <input type="text" name="social_facebook" id="social_facebook" class="form-control" value="{{ old('social_facebook', $schoolInfo->social_facebook) }}" placeholder="https://facebook.com/your-school">
              </div>
            </div>

            <div class="mb-3">
              <label for="social_instagram" class="form-label fw-semibold">Instagram Profile</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i data-lucide="instagram" style="width:1rem;height:1rem;" class="text-danger"></i></span>
                <input type="text" name="social_instagram" id="social_instagram" class="form-control" value="{{ old('social_instagram', $schoolInfo->social_instagram) }}" placeholder="https://instagram.com/your-school">
              </div>
            </div>

            <div class="mb-3">
              <label for="social_twitter" class="form-label fw-semibold">Twitter / X Handle</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i data-lucide="twitter" style="width:1rem;height:1rem;" class="text-info"></i></span>
                <input type="text" name="social_twitter" id="social_twitter" class="form-control" value="{{ old('social_twitter', $schoolInfo->social_twitter) }}" placeholder="https://twitter.com/your-school">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Submit Action Footer Bar -->
      <div class="col-12 text-end mb-5">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold">
          <i data-lucide="save" style="width:1.25rem;height:1.25rem;" class="me-1"></i> Save All School Information
        </button>
      </div>
    </div>
  </form>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const logoInput = document.getElementById('logoInput');
        const logoPreview = document.getElementById('logoPreview');
        const logoPlaceholder = document.getElementById('logoPlaceholder');

        const stampInput = document.getElementById('stampInput');
        const stampPreview = document.getElementById('stampPreview');
        const stampPlaceholder = document.getElementById('stampPlaceholder');

        logoInput.addEventListener('change', function (e) {
          const file = e.target.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
              logoPreview.src = event.target.result;
              logoPreview.classList.remove('d-none');
              if (logoPlaceholder) logoPlaceholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
          }
        });

        stampInput.addEventListener('change', function (e) {
          const file = e.target.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
              stampPreview.src = event.target.result;
              stampPreview.classList.remove('d-none');
              if (stampPlaceholder) stampPlaceholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
          }
        });
      });
    </script>
  @endpush
@endsection
