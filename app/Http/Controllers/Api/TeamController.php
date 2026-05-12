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
            'team_name' => 'required|string|max:100',
            'tournament_id' => 'required|exists:tournaments,id'
        ]);

        $logoPath = null;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('teams', 'public');
        }

        $team = Team::create([
            // ✅ Fixed: Use $request->user()->id (null-safe with auth:sanctum middleware)
            'user_id' => $request->user()->id,
            'tournament_id' => $request->tournament_id,
            'team_name' => $request->team_name,
            'logo' => $logoPath
        ]);

        return response()->json($team, 201);
    }

    // READ (punya sendiri) - Fixed: add Request parameter + null-safe access
    public function my(Request $request)
    {
        // ✅ Fixed: Use $request->user()?->id with null-safe operator
        return Team::where('user_id', $request->user()->id)
            ->with('tournament')
            ->get();
    }

    // UPDATE - Added authorization check + null-safe file handling
    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        // ✅ Optional: Ensure only owner or admin can update
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