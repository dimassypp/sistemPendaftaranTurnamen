<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    // CREATE
    public function store(Request $req)
    {
        $req->validate([
            'team_name' => 'required',
            'tournament_id' => 'required|exists:tournaments,id'
        ]);

        $logoPath = null;

        if ($req->hasFile('logo')) {
            $logoPath = $req->file('logo')->store('teams','public');
        }

        $team = Team::create([
            'user_id' => auth()->id(),
            'tournament_id' => $req->tournament_id,
            'team_name' => $req->team_name,
            'logo' => $logoPath
        ]);

        return response()->json($team);
    }

    // READ (punya sendiri)
    public function my()
    {
        return Team::where('user_id', auth()->id())
            ->with('tournament')
            ->get();
    }

    // READ (admin semua)
    public function index()
    {
        return Team::with(['user','tournament'])->get();
    }

    // UPDATE
    public function update(Request $req, $id)
    {
        $team = Team::findOrFail($id);

        if ($req->hasFile('logo')) {
            $team->logo = $req->file('logo')->store('teams','public');
        }

        $team->team_name = $req->team_name;
        $team->save();

        return response()->json($team);
    }

    // DELETE
    public function destroy($id)
    {
        Team::destroy($id);
        return response()->json(['message'=>'Deleted']);
    }
}
