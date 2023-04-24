<?php


namespace App\Services\User;


use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

/**
 * The Service class handles CRUD operations for User entities
 */
class Service
{
    use AuthorizesRequests;

    /**
     * Retrieve all users with associated roles
     *
     * @return JsonResponse
     */
    public function getAllUsers(): JsonResponse
    {
        return response()->json([
            'users' => User::with('role')->get(),
        ]);
    }

    /**
     * Create a new user
     *
     * @param Request $data The user's data
     * @return JsonResponse
     * @throws Exception
     */
    public function createUser(Request $data): JsonResponse
    {
        $this->authorize('create', auth()->user());

        try {
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
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred.',
            ], 500);
        }
    }

    /**
     * Edit an existing user
     *
     * @param Request $data The user's data
     * @return JsonResponse|RedirectResponse
     * @throws Exception
     */
    public function editUser(Request $data): JsonResponse|RedirectResponse
    {
        $this->authorize('update', auth()->user());

        $user = User::whereId($data['id'])->first();

        try {
            DB::beginTransaction();

            $user->role_id = Role::whereName($data['role'])->first()->id;
            $user->name = $data['name'];
            $user->phone_number = $data['phone_number'];
            $user->is_active = $data['is_active'];

            $user->save();

            DB::commit();

            if (Auth::id() === $data['id'] &&
                ($data['is_active'] == false ||
                    $user->hasRole('admin') === false)) {
                return response()->json([
                    'redirect_to' => '/home',
                ]);
            }

            return response()->json([
                'message' => 'User updated successfully.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred.',
            ], 500);
        }
    }

    /**
     * Delete an existing user
     *
     * @param Request $data The user's data
     * @return JsonResponse
     * @throws Exception
     */
    public function deleteUser(Request $data): JsonResponse
    {
        $this->authorize('delete', auth()->user());

        try {
            DB::beginTransaction();

            User::whereId($data['id'])->delete();

            DB::commit();

            if (Auth::id() === $data['id']) {
                Auth::logout();
                return response()->json([
                    'redirect_to' => '/',
                ]);
            }

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred.',
            ], 500);
        }


        return response()->json([
            'message' => 'User deleted.'
        ]);
    }

    /**
     * Log in as another user
     *
     * @param Request $data The user's data
     * @return JsonResponse
     */
    public function loginAs(Request $data): JsonResponse
    {
        $user = User::whereId($data['id'])->first();
        if ($user->is_active == true) {
            Auth::login($user);
            return response()->json([
                'redirect_to' => '/home',
            ]);
        }
    }
}
