<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class LostFoundController extends Controller
{
    public function index()
    {
        return view('pages.admin.lost-found.index');
    }

    public function create()
    {
        return view('pages.admin.lost-found.create');
    }

    public function show(
        $id
    )
    {
        return view('pages.admin.lost-found.show', compact('id'));
    }

    public function edit(
        $id
    )
    {
        return view('pages.admin.lost-found.edit', compact('id'));
    }
}
