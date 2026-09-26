<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HelpController extends Controller
{
    public function index()
    {
        return view('pages.admin.help.index');
    }

    public function create()
    {
        return view('pages.admin.help.create');
    }

    public function show(
        $id
    )
    {
        return view('pages.admin.help.show', compact('id'));
    }

    public function edit(
        $id
    )
    {
        return view('pages.admin.help.edit', compact('id'));
    }
}
