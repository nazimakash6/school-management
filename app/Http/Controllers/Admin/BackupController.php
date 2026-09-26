<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BackupController extends Controller
{
    public function index()
    {
        return view('pages.admin.backup.index');
    }

    public function create()
    {
        return view('pages.admin.backup.create');
    }

    public function show(
        $id
    )
    {
        return view('pages.admin.backup.show', compact('id'));
    }

    public function edit(
        $id
    )
    {
        return view('pages.admin.backup.edit', compact('id'));
    }
}
