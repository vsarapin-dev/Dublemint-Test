<?php


namespace App\Services\SignUp;


use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Service class for sign up users
 */
class Service
{
    /**
     * @var mixed $data Data needed for user creation
     */
    private mixed $data;


    /**
     * Creates a new user with the given data
     *
     * @param mixed $data Data needed for user creation
     * @return JsonResponse Response JSON containing either the access code or an error message
     */
    public function signUp($data): JsonResponse
    {
        $this->data = $data;

        if ($this->isPhoneExists() &&
            $this->isUsernameExists())
        {
            return response()->json([
                'codeString' => User::wherePhoneNumber($data['phone_number'])->first()->personal_access_token
            ]);
        }

        $response = $this->createUser();

        return $response;
    }

    /**
     * Attempts to create a new user and returns the response JSON
     *
     * @return JsonResponse Response JSON containing either the access code or an error message
     */
    private function createUser(): JsonResponse
    {
        try {
            Db::beginTransaction();
            $user = User::create([
                'role_id' => Role::adminRole(),
                'name' => $this->data['user_name'],
                'phone_number' => $this->data['phone_number'],
                'personal_access_token' => bin2hex(random_bytes(20)),
                'expires_at' => Carbon::now()->addDays(7),
            ]);
            $this->userId = $user->id;
            Db::commit();
            return response()->json(['codeString' => $this->generateAccessURL($user->personal_access_token)]);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(['error' => 'Oops, something went wrong.']);
        }
    }

    /**
     * Checks if the given phone number already exists in the database
     *
     * @return bool True if phone number exists, false otherwise
     */
    private function isPhoneExists(): bool
    {
        return User::wherePhoneNumber($this->data['phone_number'])->exists();
    }

    /**
     * Checks if the given username already exists in the database
     *
     * @return bool True if username exists, false otherwise
     */
    private function isUsernameExists(): bool
    {
        return User::whereName($this->data['user_name'])->exists();
    }

    /**
     * Generates the access URL for the newly created user
     *
     * @param string $personal_access_token The user's personal access token
     * @return string The access URL
     */
    private function generateAccessURL($personal_access_token): string
    {
        return env('APP_URL') . "/access-code/" . $personal_access_token;
    }
}
