<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class SearchController extends Controller
{
    //
    public function searchUsers(Request $request)
    {
        $keyword = trim($request->q);

        if (!$keyword) {
            return response()->json([]);
        }

        $users = User::query()
            ->where(function ($query) use ($keyword) {
                $query->where('ten_dang_nhap', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('so_dien_thoai', 'LIKE', "%{$keyword}%");
            })
            ->limit(10)
            ->get([
                'id',
                'ten_dang_nhap',
                'anh_dai_dien',
                'tieu_su',
            ]);

        return response()->json($users);
    }
}
