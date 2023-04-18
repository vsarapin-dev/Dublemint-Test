<?php


namespace App\Services\History;


use App\Http\Resources\History\HistoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class Service
{
    private int $historyRowLimit = 3;

    public function showHistory(): JsonResponse
    {
        return response()->json([
            'history' => HistoryResource::collection(Auth::user()
                ->history()
                ->orderBy('id', 'desc')
                ->take($this->historyRowLimit)
                ->get()),
        ]);
    }
}
