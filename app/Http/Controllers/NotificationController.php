<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function go($id)
    {
        $user = auth()->user();

        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            $data = $notification->data ?? [];
            $url = $data['action_url'] ?? url('/');
            return redirect($url);
        }

        return back();
    }
}
