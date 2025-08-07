<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller {
    public function index() {
        $settings = Setting::all(); // You can paginate or filter if needed
        return view('admin.prompts.index', compact('settings'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable',
            'setting_name' => 'required|string|max:255',
            'setting_value' => 'required',
            'user_id' => 'nullable|exists:users,id',
        ]);

        Setting::create($request->all());

        return redirect()->route('admin.prompts')->with('success', 'Setting created successfully');
    }

    public function create() {
        return view('admin.prompts.create');
    }

    public function edit(Setting $setting) {
        return view('admin.prompts.edit', compact('setting'));
    }

    public function update(Request $request, Setting $setting) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable',
            'setting_name' => 'required|string|max:255',
            'setting_value' => 'required',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $setting->update($request->all());

        return redirect()->route('admin.prompts')->with('success', 'Setting updated successfully');
    }

    public function destroy(Setting $setting) {
        $setting->delete();
        return redirect()->route('admin.prompts')->with('success', 'Setting deleted successfully');
    }
}
