<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    // Semua: Lihat daftar pengumuman terbaru
    public function index()
    {
        $announcements = Announcement::with('user')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Daftar pengumuman',
            'data'    => $announcements,
        ]);
    }

    // Semua: Lihat detail satu pengumuman
    public function show($id)
    {
        $announcement = Announcement::with('user')->find($id);

        if (!$announcement) {
            return response()->json(['message' => 'Pengumuman tidak ditemukan.'], 404);
        }

        return response()->json([
            'message' => 'Detail pengumuman',
            'data'    => $announcement,
        ]);
    }

    // Admin: Buat pengumuman baru
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:150',
            'content' => 'required|string',
        ]);

        $announcement = Announcement::create([
            'user_id' => auth()->id(),
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Pengumuman berhasil dibuat.',
            'data'    => $announcement->load('user'),
        ], 201);
    }

    // Admin: Update pengumuman
    public function update(Request $request, $id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json(['message' => 'Pengumuman tidak ditemukan.'], 404);
        }

        $request->validate([
            'title'   => 'sometimes|string|max:150',
            'content' => 'sometimes|string',
        ]);

        $announcement->update($request->only(['title', 'content']));

        return response()->json([
            'message' => 'Pengumuman berhasil diupdate.',
            'data'    => $announcement->load('user'),
        ]);
    }

    // Admin: Hapus pengumuman
    public function destroy($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json(['message' => 'Pengumuman tidak ditemukan.'], 404);
        }

        $announcement->delete();

        return response()->json(['message' => 'Pengumuman berhasil dihapus.']);
    }
}