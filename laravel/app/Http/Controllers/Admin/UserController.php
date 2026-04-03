<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function show(int $id)
    {
        $userDetail = User::findOrFail($id);
        $userBookings = Booking::where('user_id', $id)->latest()->take(10)->get();
        $userActivity = ActivityLog::where('user_id', $id)->latest()->take(20)->get();

        return view('admin.user-detail', compact('userDetail', 'userBookings', 'userActivity'));
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role'   => 'required|in:customer,agent,admin,superadmin',
            'status' => 'required|in:active,suspended,pending',
        ]);

        $user->update($validated);

        return redirect("/admin/users/{$id}")->with('success', 'User updated successfully.');
    }
}
