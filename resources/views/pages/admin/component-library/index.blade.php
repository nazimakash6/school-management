@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/component-library.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Component Library</h1>
            <p class="page-subtitle">Reusable UI components and design system reference.</p>
          </div>
        </div>

        <div class="row g-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header"><h5 class="mb-0 fw-bold">Buttons</h5></div>
              <div class="card-body d-flex flex-wrap gap-2">
                <button class="btn btn-primary">Primary</button>
                <button class="btn btn-secondary">Secondary</button>
                <button class="btn btn-success">Success</button>
                <button class="btn btn-danger">Danger</button>
                <button class="btn btn-ghost">Ghost</button>
                <button class="btn btn-primary btn-sm">Small</button>
                <button class="btn btn-primary btn-lg">Large</button>
                <button class="btn btn-primary btn-icon"><i data-lucide="plus" style="width:1rem;height:1rem;"></i></button>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header"><h5 class="mb-0 fw-bold">Badges</h5></div>
              <div class="card-body d-flex flex-wrap gap-2">
                <span class="badge badge-primary">Primary</span>
                <span class="badge badge-success">Success</span>
                <span class="badge badge-warning">Warning</span>
                <span class="badge badge-danger">Danger</span>
                <span class="badge badge-info">Info</span>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header"><h5 class="mb-0 fw-bold">Form Inputs</h5></div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-4"><label class="form-label">Text Input</label><input type="text" class="form-control" placeholder="Enter text"></div>
                  <div class="col-md-4"><label class="form-label">Select</label><select class="form-select"><option>Option 1</option><option>Option 2</option></select></div>
                  <div class="col-md-4"><label class="form-label">Date</label><input type="date" class="form-control"></div>
                  <div class="col-md-4"><label class="form-label">Valid</label><input type="text" class="form-control is-valid" value="Valid value"></div>
                  <div class="col-md-4"><label class="form-label">Invalid</label><input type="text" class="form-control is-invalid" value="Invalid value"><div class="invalid-feedback">Please correct this field.</div></div>
                  <div class="col-md-4"><label class="form-label">Disabled</label><input type="text" class="form-control" value="Disabled" disabled></div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header"><h5 class="mb-0 fw-bold">Alerts</h5></div>
              <div class="card-body d-flex flex-column gap-3">
                <div class="alert alert-primary"><i data-lucide="info" style="width:1.25rem;height:1.25rem;"></i><div>This is a primary alert message.</div></div>
                <div class="alert alert-success"><i data-lucide="check-circle" style="width:1.25rem;height:1.25rem;"></i><div>This is a success alert message.</div></div>
                <div class="alert alert-warning"><i data-lucide="alert-triangle" style="width:1.25rem;height:1.25rem;"></i><div>This is a warning alert message.</div></div>
                <div class="alert alert-danger"><i data-lucide="x-circle" style="width:1.25rem;height:1.25rem;"></i><div>This is a danger alert message.</div></div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header"><h5 class="mb-0 fw-bold">Progress & Loading</h5></div>
              <div class="card-body">
                <div class="progress mb-3"><div class="progress-bar" style="width: 70%;"></div></div>
                <div class="d-flex gap-3">
                  <div class="skeleton" style="width: 200px; height: 20px;"></div>
                  <div class="skeleton" style="width: 120px; height: 20px;"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header"><h5 class="mb-0 fw-bold">Avatars</h5></div>
              <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-sm">SM</div>
                <div class="avatar">MD</div>
                <div class="avatar avatar-lg">LG</div>
                <div class="avatar avatar-xl">XL</div>
                <div class="avatar"><img src="https://ui-avatars.com/api/?name=Admin+User&background=6366f1&color=fff" alt="Admin"></div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header"><h5 class="mb-0 fw-bold">Statistic Cards</h5></div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-3">
                    <div class="stat-card">
                      <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stat-icon" style="background-color: var(--color-primary-100); color: var(--color-primary-700);"><i data-lucide="users" style="width:1.25rem;height:1.25rem;"></i></div>
                        <span class="stat-change up"><i data-lucide="trending-up" style="width:0.75rem;height:0.75rem;"></i> 12%</span>
                      </div>
                      <div class="stat-value">2,847</div>
                      <div class="stat-label">Total Students</div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="stat-card">
                      <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stat-icon" style="background-color: var(--color-success-100); color: var(--color-emerald-700);"><i data-lucide="wallet" style="width:1.25rem;height:1.25rem;"></i></div>
                        <span class="stat-change up"><i data-lucide="trending-up" style="width:0.75rem;height:0.75rem;"></i> 8%</span>
                      </div>
                      <div class="stat-value">Rs. 4.2M</div>
                      <div class="stat-label">Fee Collection</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header"><h5 class="mb-0 fw-bold">Tabs</h5></div>
              <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                  <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1" type="button">Overview</button></li>
                  <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab2" type="button">Details</button></li>
                  <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab3" type="button">History</button></li>
                </ul>
                <div class="tab-content pt-3">
                  <div class="tab-pane fade show active" id="tab1">Overview content goes here.</div>
                  <div class="tab-pane fade" id="tab2">Details content goes here.</div>
                  <div class="tab-pane fade" id="tab3">History content goes here.</div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header"><h5 class="mb-0 fw-bold">Timeline</h5></div>
              <div class="card-body">
                <div class="timeline">
                  <div class="timeline-item"><div class="fw-semibold">Project Started</div><div class="text-sm text-tertiary">Initial planning and setup completed.</div></div>
                  <div class="timeline-item"><div class="fw-semibold">Design Phase</div><div class="text-sm text-tertiary">UI/UX design finalized.</div></div>
                  <div class="timeline-item"><div class="fw-semibold">Development</div><div class="text-sm text-tertiary">Frontend implementation in progress.</div></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      
@endsection
