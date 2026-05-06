<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersonalSettingsController extends Controller
{
    // return the settings view (you can add more personal endpoints here)
    public function index(Request $request)
    {
        return view('settings');
    }

    // Persist theme choice (light|dark) into session
    public function setTheme(Request $request)
    {
        $data = $request->validate([
            'theme' => 'required|string|in:light,dark'
        ]);

        session(['personal_theme' => $data['theme']]);

        return response()->json(['status' => 'ok', 'theme' => $data['theme']]);
    }
}
