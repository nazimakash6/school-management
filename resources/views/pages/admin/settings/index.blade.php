@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/settings.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Account Settings</h1>
            <p class="page-subtitle">Manage your account preferences and security</p>
          </div>
          <div class="content-header-actions">
            <button class="btn btn-primary btn-sm"><i data-lucide="save" style="width:1rem;height:1rem;"></i> Save Changes</button>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between py-3 border-bottom" style="border-color: var(--border-color) !important;">
              <div>
                <div class="fw-semibold">Email Notifications</div>
                <div class="text-sm text-tertiary">Receive email updates</div>
              </div>
              <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" checked></div>
            </div>
            <div class="d-flex align-items-center justify-content-between py-3 border-bottom" style="border-color: var(--border-color) !important;">
              <div>
                <div class="fw-semibold">SMS Notifications</div>
                <div class="text-sm text-tertiary">Receive SMS alerts</div>
              </div>
              <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" ></div>
            </div>
            <div class="d-flex align-items-center justify-content-between py-3 border-bottom" style="border-color: var(--border-color) !important;">
              <div>
                <div class="fw-semibold">Dark Mode</div>
                <div class="text-sm text-tertiary">Use dark theme by default</div>
              </div>
              <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" ></div>
            </div>
            <div class="d-flex align-items-center justify-content-between py-3 border-bottom" style="border-color: var(--border-color) !important;">
              <div>
                <div class="fw-semibold">Two-Factor Auth</div>
                <div class="text-sm text-tertiary">Enable 2FA for this account</div>
              </div>
              <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" checked></div>
            </div>
          </div>
        </div>
      
@endsection
