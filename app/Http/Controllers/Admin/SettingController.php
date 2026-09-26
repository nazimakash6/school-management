<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        return view('pages.admin.settings.index');
    }

    public function create()
    {
        return view('pages.admin.settings.create');
    }

    public function show(
        $id
    )
    {
        return view('pages.admin.settings.show', compact('id'));
    }

    public function edit(
        $id
    )
    {
        return view('pages.admin.settings.edit', compact('id'));
    }
}
