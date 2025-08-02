<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $note = Note::all();
        return view('notes.index', [
            'note' => $note
        ]);
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

        $log = new Log();
        $log->action = 'CREAR';
        $log->objeto = 'Notes';
        $log->objeto_id =  $note->id;
        $log->detail = $note->toJson();
        $log->ip = '3333';
        $log->user_id = auth()->user()->id;
        $log->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Nota creada con exito.',
        ]);
    }

    public function viewNotes()
    {
        return view('notes.view_notes');
    }

    public function listNotes(): JsonResponse
    {
        $notas = Note::all();
        $notes = [];
        foreach ($notas as $note) {
            $notes[] = [
                'id' => $note->id,
                'title' => $note->title,
                'contents' => $note->contents,
                'completed' => (bool)$note->completed,
                'url_detail' => route('notes.show', $note->id),
            ];
        }
        return response()->json([
            'status' => 'success',
            'data' =>  $notes,
        ]);
    }

    public function show($id)
    {
        $note = Note::find($id);
        return view('notes.show', [
            'note' => $note
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

        return response()->json([
                'status' => 'success',
                'message' => 'Nota actualizada con exito.',
            ]);

        //return redirect()->route('notes.index')->with('success', 'Note created successfully.');
    }

    public function destroy($id)
    {
        $note = Note::find($id);

        $log = new Log();
        $log->action = 'ELIMINAR';
        $log->objeto = 'Notes';
        $log->objeto_id =  $note->id;
        $log->detail = $note->toJson();
        $log->ip = '3333';
        $log->user_id = auth()->user()->id;
        $log->save();

        $note->delete();

        return response()->json([
                'status' => 'success',
                'message' => 'Nota eliminada con exito.',
            ]);

        //return redirect()->route('notes.index')->with('success', 'Note deleted successfully.');
    }

    private function middleware(string $string)
    {
    }
}
