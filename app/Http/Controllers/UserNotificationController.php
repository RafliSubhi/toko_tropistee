<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class UserNotificationController extends Controller
{
    public function markAsRead(UserNotification $notification)
    {
        // Ensure the user can only mark their own notifications as read
        if ($notification->user_id !== Auth::guard('pengunjung')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json(['success' => 'Notification marked as read.']);
    }

    public function markAllAsRead(Request $request)
    {
        UserNotification::where('user_id', Auth::guard('pengunjung')->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => 'All notifications marked as read.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserNotification $notification)
    {
        // Ensure the user can only delete their own notifications
        if ($notification->user_id !== Auth::guard('pengunjung')->id()) {
            abort(403, 'Unauthorized action.');
        }

        $notification->delete();

        return redirect()->back()->with('success', 'Notifikasi telah dihapus.');
    }

    /**
     * Remove all notifications for the authenticated user.
     */
    public function destroyAll()
    {
        UserNotification::where('user_id', Auth::guard('pengunjung')->id())->delete();

        return response()->json(['success' => 'All notifications deleted.']);
    }
}