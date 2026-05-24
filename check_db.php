<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

echo "=== CHECK THEO_DOI TABLE ===\n";
$rows = DB::table('theo_doi')->get();
echo "Total theo_doi rows: " . $rows->count() . "\n";
foreach ($rows as $r) {
    echo "nguoi_theo_doi_id={$r->nguoi_theo_doi_id} nguoi_duoc_theo_doi_id={$r->nguoi_duoc_theo_doi_id} trang_thai={$r->trang_thai}\n";
}

echo "\n=== CHECK USERS (con_hoat_dong) ===\n";
$users = DB::table('nguoi_dung')->select('id','ten_dang_nhap','con_hoat_dong','ngay_xoa')->get();
foreach ($users as $u) {
    echo "id={$u->id} ten_dang_nhap={$u->ten_dang_nhap} con_hoat_dong={$u->con_hoat_dong} ngay_xoa={$u->ngay_xoa}\n";
}

echo "\n=== TEST @all query for user_id=1 ===\n";
$senderId = $users->first()->id ?? 1;
$connected = DB::table('theo_doi')
    ->where('trang_thai', 'da_chap_nhan')
    ->where(function($q) use ($senderId) {
        $q->where('nguoi_theo_doi_id', $senderId)
          ->orWhere('nguoi_duoc_theo_doi_id', $senderId);
    })
    ->get();
echo "Connected rows for user_id={$senderId}: " . $connected->count() . "\n";
foreach ($connected as $c) {
    $otherId = ($c->nguoi_theo_doi_id == $senderId) ? $c->nguoi_duoc_theo_doi_id : $c->nguoi_theo_doi_id;
    echo "  => other user_id={$otherId}\n";
}

echo "\n=== RECENT THONG_BAO (loai=tag) ===\n";
$nots = DB::table('thong_bao')->where('loai', 'tag')->orderByDesc('id')->take(10)->get();
echo "Tag notifications count: " . $nots->count() . "\n";
foreach ($nots as $n) {
    echo "id={$n->id} nguoi_dung_id={$n->nguoi_dung_id} nguoi_thuc_hien_id={$n->nguoi_thuc_hien_id} bai_viet_id={$n->bai_viet_id} binh_luan_id={$n->binh_luan_id}\n";
}
