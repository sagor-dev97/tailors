<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Mail\OtpMail;
use App\Models\SmsSetting;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public $select;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'phone_number', 'avatar'];
    }
    // public function forgotPassword(Request $request)
    // {
    //     $request->validate([
    //         'phone_number' => [
    //             'required',
    //             'exists:users,phone_number',
    //             'regex:/^(?:\+8801|01)[3-9]\d{8}$/'
    //         ],
    //     ]);
    //     try {
    //         $phone = $request->input('phone_number');
    //         $otp   = rand(1000, 9999);
    //         $user  = User::where('phone_number', $phone)->first();
    //         // dd($user);

    //         if ($user) {
    //             //  Mail::to($phone)->send(new ForgotPasswordMail($otp, $user, 'Reset Your Password'));

    //             $user->otp            = $otp;
    //             $user->otp_expires_at = Carbon::now()->addMinutes(60);
    //             $user->save();

    //             return response()->json([
    //                 'status'  => true,
    //                 'message' => 'OTP sent to your email.',
    //                 'code'    => 200,
    //                 'otp'    => $otp,
    //             ]);
    //         } else {
    //             return Helper::jsonErrorResponse('Invalid Email Address', 404);
    //         }
    //     } catch (Exception $e) {
    //         return Helper::jsonErrorResponse($e->getMessage(), 500);
    //     }
    // }
public function forgotPassword(Request $request)
{
    $request->validate([
        'phone_number' => 'required|exists:users,phone_number',
    ]);

    try {

        $phone = $request->phone_number;
        $otp   = rand(1000, 9999);

        $user = User::where('phone_number', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'code' => 404,
            ]);
        }

        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        $message = "Your OTP for password reset is: {$otp}. Valid for 10 minutes.";

        $smsSent = $this->sendSms(
            $user->phone_number,
            $message,
            'OTP-' . $user->id
        );

        if (!$smsSent) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send OTP SMS',
                'code' => 400,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully.',
            'code' => 200,
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
            'code' => 500,
        ]);
    }
}

 private function sendSms($phone, $message, $orderId)
    {
        try {
            // ফোন নাম্বার ক্লিন করা
            $phone = preg_replace('/[^0-9]/', '', $phone);

            // ডাটাবেজ থেকে API সেটিংস নেওয়া
            $smsSetting = SmsSetting::first();

            $apiKey = $smsSetting->api_key ?? env('SMS_API_KEY');
            $senderId = $smsSetting->sender_id ?? env('SMS_SENDER_ID');

            $response = Http::timeout(10)->get('https://api.automas.com.bd/smsapiv3', [
                'apikey'  => $apiKey,
                'sender'  => $senderId,
                'msisdn'  => $phone,
                'smstext' => $message,
            ]);

            // রেসপন্স চেক করা
            if (!$response->successful()) {
                Log::warning('SMS API failed', ['order_id' => $orderId]);
                return false;
            }

            $data = $response->json();

            // স্ট্যাটাস চেক করা
            $status = $data['response'][0]['status'] ?? null;

            if ($status === null) {
                Log::error('Invalid SMS response', ['order_id' => $orderId, 'data' => $data]);
                return false;
            }

            Log::info('SMS RESPONSE', [
                'order_id' => $orderId,
                'status' => $status,
            ]);

            return $status === 0;
        } catch (\Exception $e) {
            Log::error('SMS ERROR', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    public function MakeOtpToken(Request $request)
    {
        $request->validate([
            'phone_number' => [
                'required',
                'exists:users,phone_number',
                'regex:/^(?:\+8801|01)[3-9]\d{8}$/'
            ],
            'otp'   => 'required|digits:4',
        ]);

        try {
            $phone_number = $request->input('phone_number');
            $otp   = $request->input('otp');
            $user = User::where('phone_number', $phone_number)->first();

            if (!$user) {
                return Helper::jsonErrorResponse('User not found', 404);
            }

            if (Carbon::parse($user->otp_expires_at)->isPast()) {
                return Helper::jsonErrorResponse('OTP has expired.', 400);
            }

            if ($user->otp !== $otp) {
                return Helper::jsonErrorResponse('Invalid OTP', 400);
            }
            $token = Str::random(60);

            $user->otp = null;
            $user->otp_expires_at = null;
            $user->reset_password_token = $token;
            $user->reset_password_token_expire_at = Carbon::now()->addHour();

            $user->save();

            return response()->json([
                'status'     => true,
                'message'    => 'OTP verified successfully.',
                'code'       => 200,
                'token'      => $token,
            ]);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }


    public function ResetPassword(Request $request)
    {
        $request->validate([
            'phone_number'    => [
                'required',
                'exists:users,phone_number',
                'regex:/^(?:\+8801|01)[3-9]\d{8}$/'
            ],
            'token'    => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);
        try {
            $phone_number       = $request->input('phone_number');
            $newPassword = $request->input('password');

            $user = User::where('phone_number', $phone_number)->first();
            if (!$user) {
                return Helper::jsonErrorResponse('User not found', 404);
            }

            if (!empty($user->reset_password_token) && $user->reset_password_token === $request->token && $user->reset_password_token_expire_at >= Carbon::now()) {

                $user->password = Hash::make($newPassword);
                $user->reset_password_token = null;
                $user->reset_password_token_expire_at = null;

                $user->save();

                return Helper::jsonResponse(true, 'Password reset successfully.', 200);
            } else {
                return Helper::jsonErrorResponse('Invalid Token', 419);
            }
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }
}
