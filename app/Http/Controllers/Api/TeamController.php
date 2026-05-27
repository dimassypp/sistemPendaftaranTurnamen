<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    // READ ALL (Admin only - via route middleware)
    public function index()
    {
        try {
            $teams = Team::with(['user', 'tournament'])->get();
            return response()->json($teams);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // CREATE - Fixed: use $request->user()->id instead of auth()->id()
    public function store(Request $request)
    {
        $request->validate([
            'team_name'     => 'required|string|max:100',
            'tournament_id' => 'required|exists:tournaments,id'
        ]);

        // Cek duplikat
        $sudahDaftar = Team::where('user_id', $request->user()->id)
            ->where('tournament_id', $request->tournament_id)
            ->exists();

        if ($sudahDaftar) {
            return response()->json([
                'message' => 'Tim kamu sudah terdaftar di turnamen ini.'
            ], 422);
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('teams', 'public');
        }

        $team = Team::create([
            'user_id'       => $request->user()->id,
            'tournament_id' => $request->tournament_id,
            'team_name'     => $request->team_name,
            'logo'          => $logoPath
        ]);

        return response()->json($team, 201);
    }

    public function my(Request $request)
    {
        return Team::where('user_id', $request->user()->id)
            ->with('tournament')
            ->get();
    }

    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        if ($request->user()->id !== $team->user_id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($team->logo) {
                Storage::disk('public')->delete($team->logo);
            }
            $team->logo = $request->file('logo')->store('teams', 'public');
        }

        $team->team_name = $request->team_name;
        $team->save();

        return response()->json($team);
    }

    public function destroy(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        if ($request->user()->id !== $team->user_id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($team->logo) {
            Storage::disk('public')->delete($team->logo);
        }

        $team->delete();
        
        return response()->json(['message' => 'Deleted']);
    }
}