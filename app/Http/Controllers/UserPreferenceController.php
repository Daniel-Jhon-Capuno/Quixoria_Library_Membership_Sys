<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    public function updateSidebar(Request $request)
    {
        $request->validate([
            'sidebar_collapsed' => ['required', 'boolean'],
        ]);

        $user = Auth::user();
        $user->sidebar_collapsed = (bool) $request->input('sidebar_collapsed');
        $user->save();

        return response()->json(['ok' => true]);
    }
}
