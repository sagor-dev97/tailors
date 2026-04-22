<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use App\Helpers\Helper;
use App\Models\FirebaseTokens;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogoutController extends Controller
{
    public $select;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar'];   
    }
    // public function logout()
    // {
    //     try {
    //         if (Auth::check('api')) {

    //             $firebaseTokens = FirebaseTokens::where('user_id', Auth::guard('api')->id())->get();
    //             if ($firebaseTokens) {
    //                 $firebaseTokens->each->delete();
    //             }

    //             Auth::logout('api');

    //             return Helper::jsonResponse(true, 'Logged out successfully. Token revoked.', 200);
    //         } else {
    //             return Helper::jsonErrorResponse( 'User not authenticated', 401);
    //         }
    //     } catch (Exception $e) {
    //         return Helper::jsonErrorResponse($e->getMessage(), 500);
    //     }
    // }

    public function logout(Request $request)
    {
        try {
            // First get user before logout
            $user = auth('api')->user();

            // Log koro debugging er jonno
            Log::info('Logout attempt', [
                'user_id' => $user ? $user->id : null,
                'device_id' => $request->device_id ?? null
            ]);

            // Validate device_id - optional banao (jodi frontend theke na ashe)
            if ($request->has('device_id')) {
                $validator = Validator::make($request->all(), [
                    'device_id' => 'required|string'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status'  => false,
                        'code'    => 422,
                        'message' => 'Validation error',
                        'errors'  => $validator->errors()
                    ], 422);
                }

                // Firebase token delete - only if user exists
                if ($user) {
                    $deleted = FirebaseTokens::where('user_id', $user->id)
                        ->where('device_id', $request->device_id)
                        ->delete();

                    Log::info('Token deleted', ['count' => $deleted]);
                }
            }

            // Logout
            auth('api')->logout();

            return Helper::jsonResponse(true, 'Logged out successfully. Token revoked.', 200);
        } catch (Exception $e) {
            Log::error('Logout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }
}
