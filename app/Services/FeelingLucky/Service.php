<?php


namespace App\Services\FeelingLucky;


use App\Models\History;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Service
{
    private int $minNumber = 0;
    private int $maxNumber = 1000;
    private int $randomResultNumber = 0;
    private float $winningsTotal = 0;
    private string $resultName;

    private const HIGHEST_VALUE = 900;
    private const HIGH_VALUE = 600;
    private const MEDIUM_VALUE = 300;
    private const LOW_VALUE = 299;

    private const HIGHEST_VALUE_PERCENT = 0.7;
    private const HIGH_VALUE_PERCENT = 0.5;
    private const MEDIUM_VALUE_PERCENT = 0.3;
    private const LOW_VALUE_PERCENT = 0.1;

    private array $winPairs = [
        self::HIGHEST_VALUE => self::HIGHEST_VALUE_PERCENT,
        self::HIGH_VALUE => self::HIGH_VALUE_PERCENT,
        self::MEDIUM_VALUE => self::MEDIUM_VALUE_PERCENT,
        self::LOW_VALUE => self::LOW_VALUE_PERCENT,
    ];


    public function generateRandomValue(): JsonResponse
    {
        $this->generateNumber();
        $this->computeResult();
        $response = $this->computeWinningsTotal();
        $this->saveResultOnWin();

        return $response;
    }

    private function saveResultOnWin(): bool
    {
        try
        {
            DB::beginTransaction();

            History::create([
                'user_id' => Auth::id(),
                'random_number' => $this->randomResultNumber,
                'winning_total' => $this->winningsTotal,
                'game_result' => $this->resultName,
            ]);

            DB::commit();
            return true;
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return false;
        }

    }

    private function generateNumber(): void
    {
        $this->randomResultNumber = rand($this->minNumber, $this->maxNumber);
    }

    private function computeResult(): void
    {
        $this->resultName = $this->randomResultNumber % 2 === 0 ? 'Win' : 'Lose';
    }

    private function computeWinningsTotal(): JsonResponse
    {
        if ($this->resultName === 'Win')
        {
            switch (true)
            {
                case $this->randomResultNumber > self::HIGHEST_VALUE:
                    $this->countPercents(self::HIGHEST_VALUE);
                    break;
                case $this->randomResultNumber > self::HIGH_VALUE:
                    $this->countPercents(self::HIGH_VALUE);
                    break;
                case $this->randomResultNumber >= self::MEDIUM_VALUE:
                    $this->countPercents(self::MEDIUM_VALUE);
                    break;
                case $this->randomResultNumber <= self::LOW_VALUE:
                    $this->countPercents(self::LOW_VALUE);
                    break;
                default:
                    $this->winningsTotal = 0;
                    break;
            }
            return response()->json([
                'message' => "Random number is $this->randomResultNumber. You win $this->winningsTotal points.",
                'result_name' => $this->resultName,
            ]);
        }
        else
        {
            return response()->json(['message' => "Random number is $this->randomResultNumber. You lost."]);
        }

    }

    private function countPercents($value): void
    {
        $this->test1 = $value;
        $this->test = $this->winPairs[$value];
        $this->winningsTotal = $this->randomResultNumber * $this->winPairs[$value];
    }
}
