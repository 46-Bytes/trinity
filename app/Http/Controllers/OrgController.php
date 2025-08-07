<?php

namespace App\Http\Controllers;

use App\Models\Org;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OrgController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'website' => 'nullable|url|max:255',
                'address_line1' => 'nullable|string|max:255',
                'address_line2' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'user_id' => 'nullable|exists:users,id',
                'status' => 'required|in:active,inactive',
            ]);
            
            // Create the organization
            $org = new Org();
            $org->name = $validated['name'];
            $org->slug = \Illuminate\Support\Str::slug($validated['name']);
            $org->description = $validated['description'] ?? null;
            $org->website = $validated['website'] ?? null;
            $org->address_line1 = $validated['address_line1'] ?? null;
            $org->address_line2 = $validated['address_line2'] ?? null;
            $org->city = $validated['city'] ?? null;
            $org->state = $validated['state'] ?? null;
            $org->postal_code = $validated['postal_code'] ?? null;
            $org->country = $validated['country'] ?? null;
            $org->user_id = $validated['user_id'] ?? null;
            $org->status = $validated['status'];
            $org->date_joined = now();
            $org->save();
            
            \Illuminate\Support\Facades\Log::info('Organization created successfully', ['org_id' => $org->id, 'org_name' => $org->name]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Organization created successfully.']);
            }
            
            return redirect()->route('admin.orgs')->with('success', 'Organization created successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Organization creation error: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Failed to create organization: ' . $e->getMessage()], 500);
            }
            
            return back()->withInput()->withErrors(['error' => 'Failed to create organization: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Org $org)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Org $org)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Org $org)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable',
            'website' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);
        if (!str_starts_with($validated['website'], 'http://') && !str_starts_with($validated['website'], 'https://')) {
            $validated['website'] = 'https://' . $validated['website'];
        }
        try{
            $org->update($validated);
        }catch(\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Business details updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Org $org)
    {
        try {
            // Store the name for the success message
            $orgName = $org->name;
            
            // Delete the organization
            $org->delete();
            
            \Illuminate\Support\Facades\Log::info('Organization deleted successfully', ['org_name' => $orgName]);
            
            return redirect()->route('admin.orgs')->with('success', 'Organization deleted successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Organization deletion error: ' . $e->getMessage());
            
            return redirect()->route('admin.orgs')->with('error', 'Failed to delete organization: ' . $e->getMessage());
        }
    }
}
