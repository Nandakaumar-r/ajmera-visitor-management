<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use App\Http\Requests\NoteRequest;
use Illuminate\Http\JsonResponse;

class NoteController extends Controller
{
    public function index(string $employeeId): JsonResponse
    {
        $notes = Note::where('employee_id', $employeeId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($note) => [
                'id' => $note->id,
                'employee_id' => $note->employee_id,
                'text' => $note->note_text,
                'date' => $note->created_at,
                'lastEdited' => $note->last_edited
            ]);

        return response()->json($notes);
    }

    public function store(NoteRequest $request): JsonResponse
    {
        $note = Note::create([
            'employee_id' => $request->employee_id,
            'note_text' => $request->text,
        ]);

        return response()->json([
            'id' => $note->id,
            'message' => 'Note added successfully'
        ], 201);
    }

    public function update(NoteRequest $request, Note $note): JsonResponse
    {
        $note->update(['note_text' => $request->text]);

        return response()->json([
            'message' => 'Note updated successfully'
        ]);
    }

    public function destroy(Note $note): JsonResponse
    {
        $note->delete();

        return response()->json([
            'message' => 'Note deleted successfully'
        ]);
    }
}
