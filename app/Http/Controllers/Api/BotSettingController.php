<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BotSetting;
use Illuminate\Http\JsonResponse;

class BotSettingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            BotSetting::query()->pluck('value', 'key')->all()
        );
    }
}
