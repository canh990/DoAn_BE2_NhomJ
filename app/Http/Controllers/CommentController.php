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
            'noi_dung' => ['required_without:media', 'nullable', 'string', 'max:1000'],
            'binh_luan_cha_id' => ['nullable', 'integer', 'exists:binh_luan,id'],
            'media' => ['nullable', 'array', 'max:10'],
            'media.*' => ['file', 'mimes:jpeg,png,jpg,gif,webp,bmp,svg,heic,heif,mp4,mov,webm,avi,mkv,wmv', 'max:51200'],
        ]);

        // KIỂM TRA CHẶN: Tránh lỗi bình luận chéo khi người dùng đã chặn nhau
        if ($request->user()->hasAnyBlockRelationship($post->nguoi_dung_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể bình luận trên bài viết này.',
            ], 403);
        }

        // XÁC THỰC BÌNH LUẬN CHA: Kiểm tra bình luận cha có tồn tại và thuộc cùng bài viết hay không để tránh lỗi logic
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
            'noi_dung' => $validated['noi_dung'] ?? '',
            'da_xoa' => false,
        ]);

        // XỬ LÝ ĐÍNH KÈM MEDIA: Lưu file vật lý và tạo bản ghi liên kết, phân biệt video/hình ảnh
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('comments', 'public');
                $mimeType = $file->getMimeType();
                $loai = str_starts_with($mimeType, 'video/') ? 'video' : 'hinh_anh';
                
                \App\Models\MediaBinhLuan::create([
                    'binh_luan_id' => $comment->id,
                    'loai' => $loai,
                    'duong_dan' => $path,
                ]);
            }
        }

        // Tăng dung lượng bộ nhớ đệm
        $cacheSizeToAdd = 0.1;
        if ($request->hasFile('media')) {
            $cacheSizeToAdd += count($request->file('media')) * 1.5;
        }
        \DB::table('cai_dat_nguoi_dung')
            ->where('nguoi_dung_id', $currentUser->id)
            ->increment('dung_luong_cache', $cacheSizeToAdd);

        // --- QUÉT MENTION/TAG VÀ TẠO THÔNG BÁO ---
        // XỬ LÝ NHẮC TÊN (@username): Phân tích nội dung và gửi thông báo cho những người được tag
        $currentUser = $request->user();
        $mentionService = resolve(\App\Services\MentionService::class);
        $taggedUserIds = $mentionService->processMentions($comment->noi_dung ?? '', $currentUser, $post, $comment);

        // --- TẠO THÔNG BÁO TƯƠNG TÁC ---
        // 1. Thông báo cho chủ bài viết (chỉ gửi khi chủ bài viết không phải là người bình luận và chưa được tag tên)
        if ($post->nguoi_dung_id !== $currentUser->id && !in_array($post->nguoi_dung_id, $taggedUserIds)) {
            \App\Models\ThongBao::create([
                'nguoi_dung_id' => $post->nguoi_dung_id,
                'nguoi_thuc_hien_id' => $currentUser->id,
                'loai' => 'binh_luan',
                'bai_viet_id' => $post->id,
                'binh_luan_id' => $comment->id,
                'ngay_tao' => now(),
            ]);
        }

        // 2. Thông báo cho chủ bình luận cha (khi có phản hồi/reply)
        if (!empty($validated['binh_luan_cha_id'])) {
            $parentComment = BinhLuan::find($validated['binh_luan_cha_id']);
            // Tránh gửi trùng nếu chủ bình luận cha cũng là chủ bài viết (đã gửi ở trên), tự trả lời chính mình, hoặc đã nhận thông báo tag tên
            if ($parentComment && 
                $parentComment->nguoi_dung_id !== $currentUser->id && 
                $parentComment->nguoi_dung_id !== $post->nguoi_dung_id &&
                !in_array($parentComment->nguoi_dung_id, $taggedUserIds)) {
                
                \App\Models\ThongBao::create([
                    'nguoi_dung_id' => $parentComment->nguoi_dung_id,
                    'nguoi_thuc_hien_id' => $currentUser->id,
                    'loai' => 'binh_luan',
                    'bai_viet_id' => $post->id,
                    'binh_luan_id' => $comment->id,
                    'ngay_tao' => now(),
                ]);
            }
        }
        // ----------------------

        $comment->load(['user', 'media']);
        $commentCount = $post->comments()->count();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bình luận đã được gửi.',
                'comments_count' => $commentCount,
                'comment' => [
                    'id' => $comment->id,
                    'parent_id' => $comment->binh_luan_cha_id,
                    'content' => $comment->formatted_content,
                    'created_at' => $comment->ngay_tao->diffForHumans(),
                    'user_name' => $comment->user?->name ?? 'Người dùng',
                    'user_avatar' => $comment->user && $comment->user->anh_dai_dien ? asset('storage/' . $comment->user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png'),
                    'media' => $comment->media->map(function($m) {
                        return [
                            'id' => $m->id,
                            'loai' => $m->loai,
                            'url' => asset('storage/' . $m->duong_dan),
                        ];
                    })->toArray(),
                ],
            ]);
        }

        return back()->with('success', 'Bình luận đã được gửi.');
    }

    public function destroy(BinhLuan $comment)
    {
        // Check authorization (người viết bình luận hoặc chủ bài viết)
        $isAuthorized = auth()->id() === $comment->nguoi_dung_id || 
                        (isset($comment->post) && auth()->id() === $comment->post->nguoi_dung_id);

        if (!$isAuthorized) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền xoá bình luận này.'], 403);
            }
            return back()->with('error', 'Bạn không có quyền xoá bình luận này.');
        }

        // BinhLuan Model has 'deleting' event to delete physical files, and DB has cascadeOnDelete
        $comment->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Bình luận đã được xoá.'
            ]);
        }

        return back()->with('success', 'Bình luận đã được xoá.');
    }
}
