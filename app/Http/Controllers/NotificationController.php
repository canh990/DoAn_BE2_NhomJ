<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->thongBaos()
            ->with(['nguoiThucHien', 'baiViet.media', 'binhLuan'])
            ->latest('ngay_tao')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(ThongBao $notification)
    {
        if ($notification->nguoi_dung_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->update(['da_doc' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadThongBaos()->update(['da_doc' => true]);

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        return response()->json([
            'count' => auth()->user()->unreadThongBaos()->count()
        ]);
    }

    public function destroy(ThongBao $notification)
    {
        if ($notification->nguoi_dung_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function deleteAll()
    {
        auth()->user()->thongBaos()->delete();

        return response()->json(['success' => true]);
    }
}
