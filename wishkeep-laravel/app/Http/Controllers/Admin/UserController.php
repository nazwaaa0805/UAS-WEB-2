<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('wishlistItems as total_items')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.users', ['users' => $users]);
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.users')->with('error', 'Tidak dapat menghapus akun Admin.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users')->with('success', "Pengguna \"$name\" berhasil dihapus.");
    }
}
