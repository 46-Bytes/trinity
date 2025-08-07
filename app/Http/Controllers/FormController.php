<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormEntry;
use Illuminate\Http\Request;

class FormController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $forms = Form::all();
        return view('forms.index', compact('forms'));
    }

    public function store(Request $request) {
        $form = Form::create($request->all());
        return redirect()->route('forms.index')->with('success', 'Form created successfully.');
    }

    public function create() {
        return view('forms.create');
    }

    public function edit(Form $form) {
        return view('forms.edit', compact('form'));
    }

    public function update(Request $request, Form $form) {
        $form->update($request->all());
        return redirect()->route('forms.index')->with('success', 'Form updated successfully.');
    }

    public function destroy(Form $form) {
        $form->delete();
        return redirect()->route('forms.index')->with('success', 'Form deleted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id) {
        $form = Form::findOrFail($id);
        $incompleteFormEntry = FormEntry::where('user_id', auth()->id())
            ->where('form_id', $form->id)
            ->where('status', '!=', 'completed')
            ->first();

        return view('forms.show', compact('form', 'incompleteFormEntry'));

    }


    // Save form JSON (for form creation or editing)

    public function saveJson(Request $request) {
        $formJSON = $request->json('form_json');

        // Save or update the form JSON in the 'forms' table
        // Here, you can either create a new form or update an existing one.
        // Assuming the form ID is passed along with the request (optional)

        if ($request->has('form_id')) {
            $form = Form::find($request->form_id);
            $form->form_json = $formJSON;
            $form->save();
        } else {
            $form = Form::create([
                'form_json' => $formJSON
            ]);
        }

        return response()->json(['message' => 'Form JSON saved successfully', 'form_id' => $form->id]);
    }

    // Save survey responses (as form entries)

    public function saveResponses(Request $request) {
        $formResponses = $request->all();  // Get survey responses

        // Save the form entry in the 'form_entries' table
        $formEntry = FormEntry::create([
            'user_id' => auth()->id(),  // Assuming the user is authenticated
            'form_id' => $request->form_id,  // Assuming the form ID is sent with the request
            'responses' => json_encode($formResponses)
        ]);

        return response()->json(['message' => 'Form responses saved successfully', 'form_entry_id' => $formEntry->id]);
    }

}
