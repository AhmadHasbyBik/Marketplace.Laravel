<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markRead(Request $request): JsonResponse
    {
        $ids = collect($request->input('ids', []))
            ->map(fn ($value) => (string) $value)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return response()->json(['updated' => 0]);
        }

        $seen = collect(session('admin_seen_notifications', []))
            ->merge($ids)
            ->unique()
            ->values()
            ->all();

        session(['admin_seen_notifications' => $seen]);

        return response()->json(['updated' => count($ids)]);
    }
}
