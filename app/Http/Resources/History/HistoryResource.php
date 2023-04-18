<?php

namespace App\Http\Resources\History;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resultString = '';
        if ($this->game_result === 'Win')
        {
            $resultString = "{$this->user->name}: Random number is $this->random_number. You win $this->winning_total points.";
        }
        else
        {
            $resultString = "{$this->user->name}: Random number is $this->random_number. You lost.";
        }
        return [
            'message' => $resultString,
            'game_result' => $this->game_result,
        ];
    }
}
