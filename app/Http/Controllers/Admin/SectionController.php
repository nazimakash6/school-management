<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SectionController extends Controller
{
    public function index()
    {
        return view('pages.admin.sections.index');
    }

    public function create()
    {
        return view('pages.admin.sections.create');
    }

    public function show(
        $id
    )
    {
        return view('pages.admin.sections.show', compact('id'));
    }

    public function edit(
        $id
    )
    {
        return view('pages.admin.sections.edit', compact('id'));
    }
}
