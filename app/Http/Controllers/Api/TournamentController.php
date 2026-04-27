<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TournamentController extends Controller
{
    // Semua: Lihat daftar turnamen
    public function index()
    {
        $tournaments = Tournament::latest()->get();

        return response()->json([
            'message' => 'Daftar turnamen',
            'data'    => $tournaments,
        ]);
    }

    // Semua: Lihat detail satu turnamen
    public function show($id)
    {
        $tournament = Tournament::find($id);

        if (!$tournament) {
            return response()->json(['message' => 'Turnamen tidak ditemukan.'], 404);
        }

        return response()->json([
            'message' => 'Detail turnamen',
            'data'    => $tournament,
        ]);
    }

    // Admin: Buat turnamen baru (+ upload poster)
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'schedule'     => 'required|date',
            'description'  => 'nullable|string',
            'poster_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $posterPath = null;
        if ($request->hasFile('poster_image')) {
            $posterPath = $request->file('poster_image')->store('posters', 'public');
        }

        $tournament = Tournament::create([
            'name'         => $request->name,
            'schedule'     => $request->schedule,
            'description'  => $request->description,
            'poster_image' => $posterPath,
        ]);

        return response()->json([
            'message' => 'Turnamen berhasil dibuat.',
            'data'    => $tournament,
        ], 201);
    }

    // Admin: Update turnamen
    public function update(Request $request, $id)
    {
        $tournament = Tournament::find($id);

        if (!$tournament) {
            return response()->json(['message' => 'Turnamen tidak ditemukan.'], 404);
        }

        $request->validate([
            'name'         => 'sometimes|string|max:100',
            'schedule'     => 'sometimes|date',
            'description'  => 'nullable|string',
            'poster_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Ganti poster jika ada file baru
        if ($request->hasFile('poster_image')) {
            if ($tournament->poster_image) {
                Storage::disk('public')->delete($tournament->poster_image);
            }
            $tournament->poster_image = $request->file('poster_image')->store('posters', 'public');
        }

        $tournament->update($request->only(['name', 'schedule', 'description']));

        return response()->json([
            'message' => 'Turnamen berhasil diupdate.',
            'data'    => $tournament,
        ]);
    }

    // Admin: Hapus turnamen
    public function destroy($id)
    {
        $tournament = Tournament::find($id);

        if (!$tournament) {
            return response()->json(['message' => 'Turnamen tidak ditemukan.'], 404);
        }

        if ($tournament->poster_image) {
            Storage::disk('public')->delete($tournament->poster_image);
        }

        $tournament->delete();

        return response()->json(['message' => 'Turnamen berhasil dihapus.']);
    }
}