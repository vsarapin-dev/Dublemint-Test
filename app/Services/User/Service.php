<?php


namespace App\Services\User;


use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class Service
{
    use AuthorizesRequests;

    public function getAllUsers(): JsonResponse
    {
        return response()->json([
            'users' => User::with('role')->get(),
        ]);
    }

    public function createUser($data): JsonResponse
    {
        $this->authorize('create', auth()->user());

        try
        {
            DB::beginTransaction();
            User::create([
                'role_id' => Role::whereName($data['role'])->first()->id,
                'name' => $data['name'],
                'phone_number' => $data['phone_number'],
                'personal_access_token' => bin2hex(random_bytes(20)),
                'expires_at' => Carbon::now()->addDays(7),
                'is_active' => $data['is_active'],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'User created successfully.'
            ]);
        }
        catch (Exception $e)
        {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred.',
            ], 500);
        }
    }

    public function editUser($data): JsonResponse|RedirectResponse
    {
        $this->authorize('update', auth()->user());

        $user = User::whereId($data['id'])->first();

        try
        {
            DB::beginTransaction();

            $user->role_id = Role::whereName($data['role'])->first()->id;
            $user->name = $data['name'];
            $user->phone_number = $data['phone_number'];
            $user->is_active = $data['is_active'];

            $user->save();

            DB::commit();

            if (Auth::id() === $data['id'] &&
                ($data['is_active'] == false ||
                 $user->hasRole('admin') === false))
            {
                return response()->json([
                    'redirect_to' => '/home',
                ]);
            }

            return response()->json([
                'message' => 'User updated successfully.',
            ]);
        }
        catch (Exception $e)
        {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred.',
            ], 500);
        }
    }

    public function deleteUser($data)
    {
        $this->authorize('delete', auth()->user());

        try
        {
            DB::beginTransaction();

            User::whereId($data['id'])->delete();

            DB::commit();

            if (Auth::id() === $data['id']) {
                Auth::logout();
                return redirect()->route('index');
            }

        }
        catch (Exception $e)
        {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred.',
            ], 500);
        }


        return response()->json([
            'message' => 'User deleted.'
        ]);
    }
}
