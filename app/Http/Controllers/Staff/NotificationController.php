<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function go($id)
    {
        $user = auth()->user();

        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            $data = $notification->data ?? [];
            $url = $data['action_url'] ?? url('/staff');
            return redirect($url);
        }

        return back();
    }
}
