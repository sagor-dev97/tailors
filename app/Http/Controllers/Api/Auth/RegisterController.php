<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Carbon\Carbon;
use App\Traits\SMS;
use App\Models\User;
use App\Mail\OtpMail;
use App\Helpers\Helper;
use App\Helpers\PhoneHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Events\RegistrationNotificationEvent;
use App\Notifications\RegistrationNotification;

class RegisterController extends Controller
{

    use SMS;

    public $select;
    public $showSelect;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'username', 'phone_number', 'email', 'otp', 'avatar', 'otp_verified_at', 'last_activity_at'];
        $this->showSelect = ['id', 'name', 'username', 'phone_number', 'email', 'avatar'];
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'address'      => 'required|string|max:255',
            'password'     => 'required|string|min:6|confirmed',
            'phone_number' => 'required|string|max:15|unique:users,phone_number',
        ]);

        try {
            DB::beginTransaction();

            // Generate unique slug
            do {
                $slug = 'user_' . rand(1000000000, 9999999999);
            } while (User::where('slug', $slug)->exists());

            // Generate username
            function randomAlphaNum($length = 4)
            {
                return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
            }

            $username = '@user_' . randomAlphaNum(4);

            // Create user (AUTO VERIFIED, NO OTP)
            $user = User::create([
                'username'           => $username,
                'slug'               => $slug,
                'name'               => $request->name,
                'address'            => $request->address,
                'phone_number'       => $request->phone_number,
                'password'           => Hash::make($request->password),

                // auto verified
                'otp'                => null,
                'otp_verified_at'    => Carbon::now(),

                'status'             => 'active',
                'last_activity_at'   => Carbon::now(),
            ]);

            // Assign role (user)
            DB::table('model_has_roles')->insert([
                'role_id'    => 4,
                'model_type' => 'App\Models\User',
                'model_id'   => $user->id,
            ]);

            // Notify admins
            $notiData = [
                'user_id' => $user->id,
                'title'   => 'New user registered',
                'body'    => 'A new user has registered successfully.',
            ];

            $admins = User::role('admin', 'web')->get();
            foreach ($admins as $admin) {
                $admin->notify(new RegistrationNotification($notiData));

                if (config('settings.reverb') === 'on') {
                    broadcast(
                        new RegistrationNotificationEvent($notiData, $admin->id)
                    )->toOthers();
                }
            }

            DB::commit();

            // Login user instantly
            $token = auth('api')->login($user);

            return response()->json([
                'status'  => true,
                'message' => 'User registered successfully.',
                'token'   => $token,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse(
                'User registration failed',
                500,
                [$e->getMessage()]
            );
        }
    }




    public function VerifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|digits:4',
        ]);
        try {
            $user = User::with('inAppPurchase')->where('email', $request->input('email'))
                ->first();

            //! Check if email has already been verified
            if (!empty($user->otp_verified_at)) {
                return  Helper::jsonErrorResponse('Email already verified.', 409);
            }

            if ((string)$user->otp !== (string)$request->input('otp')) {
                return Helper::jsonErrorResponse('Invalid OTP code', 422);
            }

            //* Check if OTP has expired
            if (Carbon::parse($user->otp_expires_at)->isPast()) {
                return Helper::jsonErrorResponse('OTP has expired. Please request a new OTP.', 422);
            }

            //* Verify the email
            $user->otp_verified_at   = now();
            $user->otp               = null;
            $user->otp_expires_at    = null;
            $user->save();

            $isVerified = true;
            // Check if name, last_name or dob is empty
            if (empty($user->name) || empty($user->last_name) || empty($user->dob)) {
                $isVerified = false;
            }


            $token = auth('api')->login($user);

            // Merge in-app purchase into user
            $purchaseData = [
                'product_id'        => null,
                'purchase_id'       => null,
                'purchase_status'   => null,
                'purchase_date'     => null,
                'verification_data' => null
            ];

            // If purchase exists, overwrite nulls
            if ($user->inAppPurchase) {
                $purchaseData = [
                    'product_id'        => $user->inAppPurchase->product_id,
                    'purchase_id'       => $user->inAppPurchase->purchase_id,
                    'purchase_status'   => $user->inAppPurchase->status,
                    'purchase_date'     => $user->inAppPurchase->purchase_date,
                    'verification_data'     => $user->inAppPurchase->verification_data ?? null,
                ];
            }

            $userData = array_merge(
                $user->only($this->showSelect),
                $purchaseData
            );
            return response()->json([
                'status'  => true,
                'message' => 'Email verified successfully.',
                'code'    => 200,
                'is_verified' => $isVerified,
                'token'   => $token,
                'user'    => $userData,
            ], 200);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function ResendOtp(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $user = User::where('email', $request->input('email'))->first();

            if (!$user) {
                return Helper::jsonErrorResponse('User not found.', 404);
            }

            if ($user->otp_verified_at) {
                return Helper::jsonErrorResponse('Email already verified.', 409);
            }

            $newOtp               = rand(1000, 9999);
            $otpExpiresAt         = Carbon::now()->addMinutes(60);
            $user->otp            = $newOtp;
            $user->otp_expires_at = $otpExpiresAt;
            $user->save();

            //* Send the new OTP to the user's email
            //  Mail::to($user->email)->send(new OtpMail($newOtp, $user, 'Verify Your Email Address'));

            return response()->json([
                'status'  => true,
                'message' => 'A new OTP has been sent to your email address.',
                'code'    => 200,
                'otp'     => $newOtp // Remove this line in production
            ], 200);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 200);
        }
    }
}
