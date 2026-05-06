<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\CamXuc;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    protected array $allowedTypes = [
        'thich',
        'tim',
        'haha',
        'buon',
        'phan_no',
        'wow',
    ];

    public function store(Request $request, BaiViet $post)
    {
        $validated = $request->validate([
            'loai_cam_xuc' => ['required', 'string', 'in:' . implode(',', $this->allowedTypes)],
        ]);

        $user = $request->user();
        $currentReaction = CamXuc::where('nguoi_dung_id', $user->id)
            ->where('bai_viet_id', $post->id)
            ->first();

        $removed = false;
        $message = 'Bạn đã thả cảm xúc.';

        if ($currentReaction && $currentReaction->loai_cam_xuc === $validated['loai_cam_xuc']) {
            $currentReaction->delete();
            $removed = true;
            $message = 'Bạn đã gỡ cảm xúc.';
        } else {
            CamXuc::updateOrCreate(
                [
                    'nguoi_dung_id' => $user->id,
                    'bai_viet_id' => $post->id,
                ],
                [
                    'loai_cam_xuc' => $validated['loai_cam_xuc'],
                ]
            );
        }

        $reactionsCount = $post->reactions()->count();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'removed' => $removed,
                'reaction' => $validated['loai_cam_xuc'],
                'reactions_count' => $reactionsCount,
            ]);
        }

        return back()->with('success', $message);
    }
}
