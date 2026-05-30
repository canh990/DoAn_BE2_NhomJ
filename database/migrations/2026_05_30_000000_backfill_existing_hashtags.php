<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\BaiViet;
use App\Models\Hashtag;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Scan and extract hashtags from existing posts
        BaiViet::whereNotNull('noi_dung')
            ->where('da_xoa', false)
            ->chunkById(100, function ($posts) {
                foreach ($posts as $post) {
                    $content = $post->noi_dung;
                    preg_match_all('/(?<=^|(?<=[^a-zA-Z0-9_\.]))#([\p{L}\p{N}_]+)/u', $content, $matches);
                    
                    if (!empty($matches[1])) {
                        $tags = array_unique(array_map('mb_strtolower', $matches[1]));
                        $tagIds = [];
                        foreach ($tags as $tagName) {
                            $hashtag = Hashtag::firstOrCreate(
                                ['ten' => $tagName],
                                ['so_bai_viet' => 0]
                            );
                            $tagIds[] = $hashtag->id;
                        }
                        $post->hashtags()->sync($tagIds);
                    }
                }
            });

        // Recalculate and update the count of posts for all hashtags
        Hashtag::chunkById(100, function ($hashtags) {
            foreach ($hashtags as $hashtag) {
                $count = $hashtag->posts()->count();
                if ($count === 0) {
                    $hashtag->delete();
                } else {
                    $hashtag->so_bai_viet = $count;
                    $hashtag->save();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Truncate intermediate table, reset count, but keep hashtags if they exist
        // Since we are reversing, we can detach all hashtags and clear count.
        // But normally it's not strictly necessary. Let's make it safe.
        \Illuminate\Support\Facades\DB::table('bai_viet_hashtag')->delete();
        Hashtag::query()->update(['so_bai_viet' => 0]);
    }
};
