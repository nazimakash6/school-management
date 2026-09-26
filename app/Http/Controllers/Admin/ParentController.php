<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ParentController extends Controller
{
    public function index()
    {
        return view('pages.admin.parents.index');
    }

    public function create()
    {
        return view('pages.admin.parents.create');
    }

    public function show(
        $id
    )
    {
        return view('pages.admin.parents.show', compact('id'));
    }

    public function edit(
        $id
    )
    {
        return view('pages.admin.parents.edit', compact('id'));
    }
}
