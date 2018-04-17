<?php

namespace App\Http\Controllers\V1;

use App\Models\Note;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoteController extends Controller
{
    use ApiActions;

    public function __construct()
    {
        $this->middleware('auth.jwt:api');
    }

    /**
     * Displays a listing of notes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notes = Note::all(['title', 'body']);

        return $this->respond($notes);
    }


    /**
     * Stores a newly created noted
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestBody = $request->only(['title', 'body']);
        $validator = Validator::make($requestBody, Note::$rules);

        if ($validator->fails()) {
            return $this->respond($validator->messages());
        }

        $newNote = auth()->user()->notes()->create($requestBody);

        return $this->respond($newNote);
    }

    /**
     * Displays the specified note.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $note = Note::find($id);

        if (!$note) {
            return $this->respond('Note not found', 404);
        }
        return $this->respond($note);
    }

    /**
     * Updates the specified note.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return $this->respond('Invalid note', 404);
        }

        $result = $note->update($request->all());

        if (!$result) {
            return $this->respond('Couldn\'t update note. Try again', 500);
        }

        return $this->respond('Note updated successfully');
    }

    /**
     * Removes the specified note.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $note = Note::find($id);

        if (!$note) {
            return $this->respond('Note not found', 404);
        }

        $result = $note->delete();

        if (!$result) {
            return $this->respond('Couldn\'t delete note. Try again', 500);
        }
        return $this->respond('Note deleted successfully');
    }
}