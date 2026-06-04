<?php

namespace App\Services;

use App\Models\User;
use App\Models\ThongBao;
use App\Models\BaiViet;
use App\Models\BinhLuan;
use Illuminate\Support\Facades\Log;
class MentionService
{
    /**
     * Parse mentions from content and create tag/mention notifications.
     * Supports tagging all friends/followers using @all.
     *
     * @param string $content
     * @param User $sender
     * @param BaiViet $post
     * @param BinhLuan|null $comment
     * @return array List of user IDs that were mentioned and notified
     */
    public function processMentions(string $content, User $sender, BaiViet $post, ?BinhLuan $comment = null): array
    {
        if (empty(trim($content))) {
            return [];
        }

        $notifiedUserIds = [];

        // Lấy danh sách ID người dùng có quan hệ chặn với người gửi
        $blockedUserIds = \Illuminate\Support\Facades\DB::table('chan')
            ->where('nguoi_chan_id', $sender->id)
            ->orWhere('nguoi_bi_chan_id', $sender->id)
            ->get()
            ->map(function($row) use ($sender) {
                return $row->nguoi_chan_id == $sender->id ? $row->nguoi_bi_chan_id : $row->nguoi_chan_id;
            })
            ->toArray();

        // 1. Handle @all (notify all connected users: followers and following, must be accepted)
        if (preg_match('/(?<=^|(?<=[^a-zA-Z0-9_\.]))@all/iu', $content)) {
            Log::info('MentionService: @all detected', ['content' => $content]);
            $connectedUserIds = \Illuminate\Support\Facades\DB::table('theo_doi')
                ->where('trang_thai', 'da_chap_nhan')
                ->where(function($q) use ($sender) {
                    $q->where('nguoi_theo_doi_id', $sender->id)
                      ->orWhere('nguoi_duoc_theo_doi_id', $sender->id);
                })
                ->get()
                ->flatMap(function ($row) use ($sender) {
                    return [$row->nguoi_theo_doi_id, $row->nguoi_duoc_theo_doi_id];
                })
                ->unique()
                ->reject(fn($id) => $id === $sender->id || in_array($id, $blockedUserIds))
                ->toArray();

            if (!empty($connectedUserIds)) {
                $connectedUsers = User::whereIn('id', $connectedUserIds)
                    ->where('con_hoat_dong', true)
                    ->get();

                foreach ($connectedUsers as $user) {
                    ThongBao::create([
                        'nguoi_dung_id' => $user->id,
                        'nguoi_thuc_hien_id' => $sender->id,
                        'loai' => 'tag_all',
                        'bai_viet_id' => $post->id,
                        'binh_luan_id' => $comment ? $comment->id : null,
                        'da_doc' => false,
                        'ngay_tao' => now(),
                    ]);
                    $notifiedUserIds[] = $user->id;
                }
            }
        }

        // 2. Handle individual @username tags
        preg_match_all('/(?<=^|(?<=[^a-zA-Z0-9_\.]))@([a-zA-Z0-9_]+)/u', $content, $matches);

        if (!empty($matches[1])) {
            $usernames = array_unique($matches[1]);
            // Exclude 'all' which is already processed above
            $usernames = array_filter($usernames, fn($u) => strtolower($u) !== 'all');

            if (!empty($usernames)) {
                $taggedUsers = User::whereIn('ten_dang_nhap', $usernames)
                    ->where('con_hoat_dong', true)
                    ->where('id', '!=', $sender->id)
                    ->whereNotIn('id', $blockedUserIds) // Không gửi cho những người có quan hệ chặn
                    ->whereNotIn('id', $notifiedUserIds) // Avoid duplicate notifications
                    ->get();

                foreach ($taggedUsers as $taggedUser) {
                    ThongBao::create([
                        'nguoi_dung_id' => $taggedUser->id,
                        'nguoi_thuc_hien_id' => $sender->id,
                        'loai' => 'tag',
                        'bai_viet_id' => $post->id,
                        'binh_luan_id' => $comment ? $comment->id : null,
                        'da_doc' => false,
                        'ngay_tao' => now(),
                    ]);

                    $notifiedUserIds[] = $taggedUser->id;
                }
            }
        }

        return $notifiedUserIds;
    }

    /**
     * Convert @username and @all in content into clickable/highlighted links.
     *
     * @param string|null $content
     * @return string
     */
    public function highlightMentions(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        // Convert raw text into safe HTML first to prevent XSS
        $safeContent = e($content);

        // Highlight @all first
        $safeContent = preg_replace('/(?<=^|(?<=[^a-zA-Z0-9_\.]))@all/iu', '<span class="text-sky-400 font-bold">@all</span>', $safeContent);

        // Replace @username with clickable blue links
        $safeContent = preg_replace_callback('/(?<=^|(?<=[^a-zA-Z0-9_\.]))@([a-zA-Z0-9_]+)/u', function ($matches) {
            $username = $matches[1];
            if (strtolower($username) === 'all') {
                return '@' . $username; // already handled
            }
            // Check if user exists to ensure we only highlight actual users
            $userExists = User::where('ten_dang_nhap', $username)->where('con_hoat_dong', true)->exists();
            if ($userExists) {
                $url = route('profile.public', ['username' => $username]);
                return '<a href="' . $url . '" class="text-sky-400 font-bold hover:underline" onclick="event.stopPropagation();">@' . $username . '</a>';
            }
            return '@' . $username;
        }, $safeContent);

        // Replace #hashtag with clickable blue links
        return preg_replace_callback('/(?<=^|(?<=[^a-zA-Z0-9_\.]))#([\p{L}\p{N}_]+)/u', function ($matches) {
            $tagName = $matches[1];
            $url = route('explore', ['search' => '#' . $tagName, 'type' => 'hashtag']);
            return '<a href="' . $url . '" class="text-sky-400 font-bold hover:underline" onclick="event.stopPropagation();">#' . $tagName . '</a>';
        }, $safeContent);
    }
}
