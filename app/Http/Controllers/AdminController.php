<?php

namespace App\Http\Controllers;

use App\Models\Org;
use App\Models\User;

class AdminController extends Controller {
    public function prompts() {}

    public function users() {
        $users = User::with(['roles', 'org'])->get();

        return view('admin.users.index', compact('users'));
    }

    public function orgs() {
        return view('admin.orgs.index', ['orgs' => Org::all()]);
    }
}
