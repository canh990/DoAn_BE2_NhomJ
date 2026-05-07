<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\BinhLuan;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, BaiViet $post)
    {
        $validated = $request->validate([
            'noi_dung' => ['required', 'string', 'max:1000'],
            'binh_luan_cha_id' => ['nullable', 'integer', 'exists:binh_luan,id'],
        ]);

        if (!empty($validated['binh_luan_cha_id'])) {
            $parentComment = BinhLuan::find($validated['binh_luan_cha_id']);
            if (!$parentComment || $parentComment->bai_viet_id !== $post->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bình luận cha không hợp lệ.',
                ], 422);
            }
        }

        $comment = BinhLuan::create([
            'bai_viet_id' => $post->id,
            'nguoi_dung_id' => $request->user()->id,
            'binh_luan_cha_id' => $validated['binh_luan_cha_id'] ?? null,
            'noi_dung' => $validated['noi_dung'],
            'da_xoa' => false,
        ]);

        $comment->load('user');
        $commentCount = $post->comments()->count();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bình luận đã được gửi.',
                'comments_count' => $commentCount,
                'comment' => [
                    'id' => $comment->id,
                    'parent_id' => $comment->binh_luan_cha_id,
                    'content' => $comment->noi_dung,
                    'created_at' => $comment->ngay_tao->diffForHumans(),
                    'user_name' => $comment->user?->name ?? 'Người dùng',
                    'user_avatar' => $comment->user && $comment->user->anh_dai_dien ? asset('storage/' . $comment->user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png'),
                ],
            ]);
        }

        return back()->with('success', 'Bình luận đã được gửi.');
    }
}
