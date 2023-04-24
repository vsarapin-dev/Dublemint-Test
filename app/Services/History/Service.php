<?php


namespace App\Services\History;


use App\Http\Resources\History\HistoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Service class for managing user history.
 */
class Service
{
    /**
     * The limit of history rows to retrieve.
     *
     * @var int
     */
    private int $historyRowLimit = 3;

    /**
     * Get the user's history and return it as a JSON response.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the user's history.
     */
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
