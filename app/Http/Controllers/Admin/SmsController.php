<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SmsController extends Controller
{
    public function index()
    {
        return view('pages.admin.sms.index');
    }

    public function create()
    {
        return view('pages.admin.sms.create');
    }

    public function show(
        $id
    )
    {
        return view('pages.admin.sms.show', compact('id'));
    }

    public function edit(
        $id
    )
    {
        return view('pages.admin.sms.edit', compact('id'));
    }
}
