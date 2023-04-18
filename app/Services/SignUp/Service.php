<?php


namespace App\Services\SignUp;


use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class Service
{
    private mixed $data;
    private ?int $userId;

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

    private function isPhoneExists(): bool
    {
        return User::wherePhoneNumber($this->data['phone_number'])->exists();
    }

    private function isUsernameExists(): bool
    {
        return User::whereName($this->data['user_name'])->exists();
    }

    private function generateAccessURL($personal_access_token): string
    {
        return env('APP_URL') . "/access-code/" . $personal_access_token;
    }
}
