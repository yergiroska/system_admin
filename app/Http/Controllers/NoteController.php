<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::all();
        return view('notes.index', compact('notes'));
    }

    public function create()
    {
        return view('notes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'contents' => 'required',
            'completed' => 'required',
        ]);

        $note = new Note();
        $note->title = $request->title;
        $note->contents = $request->contents;
        //$note->completed = $request->completed;
        $note->completed =  $request->completed === '1' ? 1 : 0;
        $note->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Note created successfully.',
        ]);
    }

    public function viewNotes()
    {
        return view('notes.view_notes');
    }

    public function listNotes(): JsonResponse
    {
        $notes = Note::all();
        return response()->json([
            'status' => 'success',
            'data' => $notes,
        ]);
    }

    public function edit($id)
    {
        $note = Note::find($id);
        return view('notes.edit', [
            'note' => $note
        ]);
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'title' => 'required',
            'contents' => 'required',
            'completed' => 'required',
        ]);

        $note= Note::find($id);
        $note->title = $request->title;
        $note->contents = $request->contents;
        $note->completed = $request->completed;
        $note->save();

        return redirect()->route('notes.index')->with('success', 'Note created successfully.');
    }

    public function destroy($id)
    {
        $note = Note::find($id);
        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Note deleted successfully.');
    }
}
