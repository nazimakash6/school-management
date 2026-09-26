<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class UserProfileController extends Controller
{
    public function index()
    {
        return view('pages.admin.user-profile.index');
    }

    public function create()
    {
        return view('pages.admin.user-profile.create');
    }

    public function show(
        $id
    )
    {
        return view('pages.admin.user-profile.show', compact('id'));
    }

    public function edit(
        $id
    )
    {
        return view('pages.admin.user-profile.edit', compact('id'));
    }
}
