<?php


namespace App\Services\FeelingLucky;


use App\Models\History;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Service
{
    /**
     * The minimum number that can be generated
     *
     * @var int $minNumber
     */
    private int $minNumber = 0;

    /**
     * The maximum number that can be generated
     *
     * @var int $maxNumber
     */
    private int $maxNumber = 1000;

    /**
     * The random number generated
     *
     * @var int $randomResultNumber
     */
    private int $randomResultNumber = 0;

    /**
     * The total amount of winnings
     *
     * @var float $winningsTotal
     */
    private float $winningsTotal = 0;

    /**
     * The result of the game
     *
     * @var string $resultName
     */
    private string $resultName;

    /**
     * The highest possible value for the game result
     *
     * @var int
     */
    private const HIGHEST_VALUE = 900;

    /**
     * The high possible value for the game result
     *
     * @var int
     */
    private const HIGH_VALUE = 600;

    /**
     * The medium possible value for the game result
     *
     * @var int
     */
    private const MEDIUM_VALUE = 300;

    /**
     * The lowest possible value for the game result
     *
     * @var int
     */
    private const LOW_VALUE = 299;

    /**
     * The percentage value for the highest win.
     *
     * @var float
     */
    private const HIGHEST_VALUE_PERCENT = 0.7;

    /**
     * The percentage value for a high win.
     *
     * @var float
     */
    private const HIGH_VALUE_PERCENT = 0.5;

    /**
     * The percentage value for a medium win.
     *
     * @var float
     */
    private const MEDIUM_VALUE_PERCENT = 0.3;

    /**
     * The percentage value for a low win.
     *
     * @var float
     */
    private const LOW_VALUE_PERCENT = 0.1;

    /**
     *The array of win pairs.
     *
     * @var array
     */
    private array $winPairs = [
        self::HIGHEST_VALUE => self::HIGHEST_VALUE_PERCENT,
        self::HIGH_VALUE => self::HIGH_VALUE_PERCENT,
        self::MEDIUM_VALUE => self::MEDIUM_VALUE_PERCENT,
        self::LOW_VALUE => self::LOW_VALUE_PERCENT,
    ];


    /**
     * Generates a random number and calculates the user's winnings based on the generated number.
     *
     * @return JsonResponse The response containing the message and result name, or a response with an error message.
     * @throws Exception if there was an error saving the user's game result in the database.
     */
    public function generateRandomValue(): JsonResponse
    {
        $this->generateNumber();
        $this->computeResult();
        $response = $this->computeWinningsTotal();
        $this->saveResultOnWin();

        return $response;
    }

    /**
     * Saves the user's game result in the database.
     *
     * @return bool True if the game result was saved successfully, false otherwise.
     * @throws Exception if there was an error saving the user's game result in the database.
     */
    private function saveResultOnWin(): bool
    {
        try {
            DB::beginTransaction();

            History::create([
                'user_id' => Auth::id(),
                'random_number' => $this->randomResultNumber,
                'winning_total' => $this->winningsTotal,
                'game_result' => $this->resultName,
            ]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

    }

    /**
     * Generates a random number between `$minNumber` and `$maxNumber` and sets `$randomResultNumber` to that value.
     *
     * @return void
     */
    private function generateNumber(): void
    {
        $this->randomResultNumber = rand($this->minNumber, $this->maxNumber);
    }

    /**
     * Computes the result name based on whether `$randomResultNumber` is even or odd.
     *
     * @return void
     */
    private function computeResult(): void
    {
        $this->resultName = $this->randomResultNumber % 2 === 0 ? 'Win' : 'Lose';
    }

    /**
     * Computes the user's winnings total and returns a response with the message and result name.
     *
     * @return \Illuminate\Http\JsonResponse The response containing the message and result name.
     */
    private function computeWinningsTotal(): JsonResponse
    {
        if ($this->resultName === 'Win') {
            switch (true) {
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
        } else {
            return response()->json(['message' => "Random number is $this->randomResultNumber. You lost."]);
        }

    }

    /**
     * Computes the user's winnings total based on `$randomResultNumber` and sets `$winningsTotal` to that value.
     *
     * @param int $value The value to use for the computation.
     *
     * @return void
     */
    private function countPercents($value): void
    {
        $this->winningsTotal = $this->randomResultNumber * $this->winPairs[$value];
    }
}
