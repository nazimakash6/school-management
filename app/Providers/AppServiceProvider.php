<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Share $globalSchoolInfo globally across all views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            if (\Illuminate\Support\Facades\Schema::hasTable('school_infos')) {
                $schoolInfo = \App\Models\SchoolInfo::first();
                if (! $schoolInfo) {
                    $schoolInfo = \App\Models\SchoolInfo::create([
                        'school_name'       => 'The Academy School',
                        'school_code'       => 'TAS-001',
                        'tagline'           => 'Excellence in Education',
                        'email'             => 'info@academyschool.edu.pk',
                        'phone'             => '+92 300 1234567',
                        'website'           => 'https://academyschool.edu.pk',
                        'address'           => '123 Education Road, Gulberg III',
                        'city'              => 'Lahore',
                        'state'             => 'Punjab',
                        'postal_code'       => '54000',
                        'established_year'  => '1998',
                        'affiliation_board' => 'BISE Lahore',
                        'registration_no'   => 'REG-2026-LHR',
                        'principal_name'    => 'Dr. Muhammad Usman',
                        'currency_symbol'   => 'Rs.',
                    ]);
                }
                $view->with('globalSchoolInfo', $schoolInfo);
            }
        });
    }
}
