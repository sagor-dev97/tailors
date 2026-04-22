<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public $select;
    public $showSelect;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'username', 'avatar', 'otp_verified_at', 'last_activity_at'];
        $this->showSelect = ['id', 'name','address', 'username','phone_number', 'avatar'];
    }

  public function login(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'password'     => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Phone number check
        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'No account found with this phone number',
            ], 422);
        }

        // Status check
        if ($user->status !== 'active') {
            return response()->json([
                'status'  => false,
                'message' => 'User is not active',
            ], 403);
        }

        // Password check
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Your password is invalid',
            ], 422);
        }

        // Verification check
        if (!$user->otp_verified_at) {
            return response()->json([
                'status'           => false,
                'message'          => 'Account not verified',
                'is_otp_verified'  => false,
            ], 403);
        }

        // Cleanup
        $user->update([
            'otp'                             => null,
            'otp_expires_at'                  => null,
            'reset_password_token'            => null,
            'reset_password_token_expire_at'  => null,
            'last_activity_at'                => now(),
        ]);

        $is_verified = !empty($user->is_verified);

        // Generate token
        $token = auth('api')->tokenById($user->id);

        $userData = $user->only($this->showSelect);
        $role = $user->getRoleNames()->first(); 

        return response()->json([
            'status'      => true,
            'message'     => 'Login successful',
            'token_type'  => 'bearer',
            'token'       => $token,
            'expires_in'  => auth('api')->factory()->getTTL() * 60,
            'role'        => $role,
            'user_id'     => $user->id,
            'user'        => $userData,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'An error occurred during login.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}



    public function refreshToken()
    {
        $refreshToken = auth('api')->refresh();

        if (empty($refreshToken)) {
            return Helper::jsonErrorResponse('Failed to refresh the token.', 401);
        }

        return response()->json([
            'status'     => true,
            'message'    => 'Access token refreshed successfully.',
            'code'       => 200,
            'token_type' => 'bearer',
            'token'      => $refreshToken,
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'data' => auth('api')->user()
        ]);
    }

}
