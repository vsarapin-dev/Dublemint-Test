<?php


namespace App\Services\Token;


use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Service class for managing user access tokens
 *
 * @package App\Services\Token
 */
class Service
{
    /**
     * Retrieve the current user's access token
     *
     * @return JsonResponse
     */
    public function getToken(): JsonResponse
    {
        return response()->json([
            'token' => $this->generateAccessURL(Auth::user()->personal_access_token),
        ]);
    }

    /**
     * Regenerate a new access token for the current user
     *
     * @return JsonResponse
     */
    public function regenerate(): JsonResponse
    {
        try {
            Db::beginTransaction();

            $newToken = bin2hex(random_bytes(20));
            $user = Auth::user();
            $user->personal_access_token = $newToken;
            $user->expires_at = Carbon::now()->addDays(7);
            $user->save();
            Db::commit();

            return response()->json([
                'message' => 'Regenerated successfully.',
                'token' => $this->generateAccessURL($newToken),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Not regenerated.']);
        }
    }

    /**
     * Deactivate the current user's account and log them out
     *
     * @return JsonResponse|Response
     */
    public function deactivate(): JsonResponse|Response
    {
        try {
            Db::beginTransaction();

            $user = Auth::user();
            $user->is_active = false;
            $user->save();

            Db::commit();

            Auth::logout();

            return response()->json(['redirect_to' => '/']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Not deactivated.']);
        }
    }

    /**
     * Generate the URL for an access token
     *
     * @param string $personal_access_token
     * @return string
     */
    private function generateAccessURL($personal_access_token): string
    {
        return env('APP_URL') . "/access-code/" . $personal_access_token;
    }
}
