<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ComponentLibraryController extends Controller
{
    public function index()
    {
        return view('pages.admin.component-library.index');
    }

    public function create()
    {
        return view('pages.admin.component-library.create');
    }

    public function show(
        $id
    )
    {
        return view('pages.admin.component-library.show', compact('id'));
    }

    public function edit(
        $id
    )
    {
        return view('pages.admin.component-library.edit', compact('id'));
    }
}
