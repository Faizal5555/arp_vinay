<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Communication;
use Illuminate\Support\Facades\Auth;


class CommunicationController extends Controller
{
    //
    
    public function index()
    {
         $communications = Communication::where('user_id', auth()->id())->get();
    return view('communication.index', compact('communications'));
    }


    public function store(Request $request)
    {
        foreach ($request->communications as $data) {
        Communication::updateOrCreate(
            ['id' => $data['id'] ?? null],
            [
                'user_id' => auth()->id(),
                'subject' => $data['subject'],
                'message' => $data['message'],
            ]
        );
    }

        return response()->json(['success' => 'Communication saved successfully!']);
    }

    public function destroy($id)
    {
        $comm = Communication::where('user_id', auth()->id())->find($id);

        if ($comm) {
            $comm->delete();
            return response()->json(['success' => 'Deleted successfully']);
        }

        return response()->json(['error' => 'Not found'], 404);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $results = Communication::where('user_id', auth()->id())
                    ->where(function($query) use ($keyword) {
                        $query->where('subject', 'like', "%{$keyword}%")
                            ->orWhere('message', 'like', "%{$keyword}%");
                    })
                    ->get(['subject', 'message']); // return only necessary fields

        return response()->json($results);
    }

}
