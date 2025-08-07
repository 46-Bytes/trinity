<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Models\Message;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NoteController extends Controller {
    public function index(Request $request) {
        // Fetch all notes
        $notes = Note::where('user_id', auth()->id())->get();
        $selectedNote = null;

        // Check if a specific note is selected
        if ($request->has('note_id')) {
            $selectedNote = Note::find($request->input('note_id'));
            $selectedNote->content = Str::markdown($selectedNote->content); // Convert Markdown to HTML

        }

        // Build the menu items by categories
        $menuItems = [];

//        foreach ($categories as $category => $colorHex) {
//            // Filter notes based on the category
//            $filteredNotes = $notes->filter(function ($note) use ($category) {
//                return $note->category === $category;
//            });
//
//            // Create menu items for the filtered notes
//            $categoryMenuItems = [];
//            foreach ($filteredNotes as $note) {
//                $categoryMenuItems[] = [
//                    'title' => $note->title,
//                    'route' => route('notes.index', ['note_id' => $note->id]),
//                ];
//            }
//
//            // Only add the category if it has matching notes
//            if (count($categoryMenuItems) > 0) {
//                $menuItems[] = [
//                    'parentMenuItemName' => ucwords($category),
//                    'parentMenuItemIcon' => 'fa fa-circle-thin',
//                    'parentMenuItemColor' => $colorHex,
//                    'menuItems' => $categoryMenuItems
//                ];
//            }
//        }

        // Build the $menuItems array from the notes (without categories)
        $menuItems = $notes->map(function ($note) {
            return [
                'title' => $note->title,
                'route' => route('notes.index', ['note_id' => $note->id])
            ];
        })->toArray(); // Convert the collection to an array
        return view('notes.index', compact('notes', 'selectedNote', 'menuItems'));
    }

    public function togglePin($id) {
        $note = Note::findOrFail($id);
        $note->is_pinned = !$note->is_pinned;
        $note->save();
        return redirect()->back();
    }

    public function destroy($id) {
        $note = Note::findOrFail($id);
        $note->delete();
        return redirect()->route('notes.index');
    }

    public function updateColor(Request $request, $id) {
        $request->validate(['note_color' => 'required|string']);
        $note = Note::findOrFail($id);
        $note->color = $request->input('note_color');
        $note->save();
        return redirect()->back();
    }


    public function store(Request $request) {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'color' => 'nullable|in:' . implode(',', array_keys(config('settings.note_colors'))),
            'category' => 'nullable|in:' . Category::valuesAsString(),
            'is_pinned' => 'nullable|boolean',
            'is_deleted' => 'nullable|boolean',
        ]);

        $validatedData['user_id'] = auth()->id();
        $validatedData['is_pinned'] = $request->has('is_pinned');
        Note::create($validatedData);

        return redirect()->route('notes.index')->with('success', 'Note created successfully!');
    }

    public function create() {
        return view('notes.create');
    }

    public function dashStore(Request $request) {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'color' => 'nullable|in:' . implode(',', array_keys(config('settings.note_colors'))),
            'category' => 'nullable|in:' . Category::valuesAsString(),
            'is_pinned' => 'nullable|boolean',
            'is_deleted' => 'nullable|boolean',
        ]);

        $validatedData['user_id'] = auth()->id();
        $validatedData['is_pinned'] = $request->has('is_pinned');
        Note::create($validatedData);

        return redirect()->route('dashboard')->with('success', 'Note created successfully!');
    }

    public function show(Note $note) {
        return view('notes.show', compact('note'));
    }

    public function edit(Note $note) {
        return view('notes.edit', compact('note'));
    }

    public function update(Request $request, Note $note) {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'color' => 'nullable|in:' . implode(',', array_keys(config('settings.note_colors'))),
            'category' => 'nullable|in:' . Category::valuesAsString(),
            'is_pinned' => 'nullable|boolean',
            'is_deleted' => 'nullable|boolean',
        ]);

        $validatedData['is_pinned'] = $request->has('is_pinned');
        $note->update($validatedData);

        // Redirect back to the active note with the updated ID
        return redirect()->route('notes.index', ['note_id' => $note->id])
            ->with('success', 'Note updated successfully');
    }

    public function createFromMessage($messageId) {
        // Find the message by ID
        $message = Message::findOrFail($messageId);

        // Create a new note with the message content
        Note::create([
            'user_id' => auth()->id(),
            'title' => 'Note from Conversation',
            'content' => $message->message,
            'category' => $message->category,
        ]);

        // Redirect back to the conversation with a success message
        return redirect()->route('chat.showConversation', $message->conversation_id)
            ->with('success', 'Note created successfully.');
    }
//    public function destroy(Note $note) {
//        $note->delete();
//        return redirect()->route('notes.index')->with('success', 'Note deleted successfully!');
//    }
}

