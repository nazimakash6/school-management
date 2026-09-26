<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        return view('pages.admin.notifications.index');
    }

    public function create()
    {
        return view('pages.admin.notifications.create');
    }

    public function show(
        $id
    )
    {
        return view('pages.admin.notifications.show', compact('id'));
    }

    public function edit(
        $id
    )
    {
        return view('pages.admin.notifications.edit', compact('id'));
    }
}
