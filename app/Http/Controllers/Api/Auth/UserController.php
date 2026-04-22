<?php

namespace App\Http\Controllers\Api\Auth;

use Stripe\Stripe;
use Stripe\Account;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\Contact;
use App\Models\ReedmeCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public $select;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'phone_number', 'username', 'slug', 'avatar'];
    }

    public function me()
    {
        $usrData = auth('api')->user();

        if (!$usrData) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized',
                'code'    => 401
            ]);
        }

        $user = User::select($this->select)->find(auth('api')->id());

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found',
            ], 404);
        }

        // Get role name
        $role = $user->getRoleNames()->first();

        // Convert to array & remove roles relation
        $userData = $user->toArray();
        unset($userData['roles']); // remove roles array

        // Add single role
        $userData['role'] = $role;

        return response()->json([
            'status'  => true,
            'message' => 'User details fetched successfully',
            'data'    => $userData
        ], 200);
    }

// public function me(Request $request)
// {
//     $usrData = auth('api')->user();

//     if (!$usrData) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'Unauthorized',
//             'code'    => 401
//         ]);
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | 🔐 Password Change Section (FIXED)
//     |--------------------------------------------------------------------------
//     */
//     if (
//         $request->filled('old_password') &&
//         $request->filled('new_password') &&
//         $request->filled('new_password_confirmation')
//     ) {

//         $request->validate([
//             'old_password' => 'required',
//             'new_password' => 'required|min:6|confirmed',
//         ]);

//         // Check old password
//         if (!Hash::check($request->old_password, $usrData->password)) {
//             return response()->json([
//                 'status'  => false,
//                 'message' => 'Old password is incorrect',
//             ], 400);
//         }

//         // Update password
//         $usrData->password = Hash::make($request->new_password);
//         $usrData->save();
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | 👤 User Fetch
//     |--------------------------------------------------------------------------
//     */
//     $user = User::select($this->select)->find(auth('api')->id());

//     if (!$user) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'User not found',
//         ], 404);
//     }

//     // Get role name
//     $role = $user->getRoleNames()->first();

//     // Convert to array & remove roles relation
//     $userData = $user->toArray();
//     unset($userData['roles']);

//     // Add single role
//     $userData['role'] = $role;

//     return response()->json([
//         'status'  => true,
//         'message' => 'User details fetched successfully',
//         'data'    => $userData
//     ], 200);
// }


public function allUsers()
{
    $authUser = auth('api')->user();

    if (!$authUser) {
        return Helper::jsonResponse(false, 'Unauthorized', 401, null);
    }

    // ✅ Only manager can access
    if (!$authUser->hasRole('manager')) {
        return Helper::jsonResponse(false, 'Forbidden: Only manager can access', 403, null);
    }

    // ✅ Get only users with role "user"
    $users = User::role('user')
        ->select($this->select)
        ->get();

    return response()->json([
        'status'  => true,
        'message' => 'User list fetched successfully',
        'data'    => $users
    ], 200);
}







    // public function updateProfile(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'nullable|string|max:255',
    //         'address' => 'nullable|string|max:255',
    //         'phone_number' => 'nullable|string|max:2000',
    //         'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',

    //     ]);

    //     $user = auth('api')->user();
    //     if (!$user) {
    //         return Helper::jsonResponse(false, 'Unauthorized', 404, null);
    //     }


    //     try {
    //         DB::beginTransaction();

    //         if ($request->hasFile('avatar')) {
    //             if (!empty($user->avatar)) {
    //                 Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
    //             }
    //             $validatedData['avatar'] = Helper::fileUpload(
    //                 $request->file('avatar'),
    //                 'user/avatar',
    //                 getFileName($request->file('avatar'))
    //             );
    //         } else {
    //             $validatedData['avatar'] = $user->avatar;
    //         }

    //         $user->update($validatedData);
    //         DB::commit();
    //         $data = User::select($this->select)->find($user->id);

    //         return Helper::jsonResponse(true, 'Profile updated successfully', 200, $data);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         if (!empty($validatedData['avatar']) && $validatedData['avatar'] !== $user->avatar) {
    //             Helper::fileDelete(public_path($validatedData['avatar']));
    //         }

    //         return Helper::jsonResponse(false, 'Profile update failed: ' . $e->getMessage(), 500);
    //     }
    // }

    public function updateProfile(Request $request)
{
    $validatedData = $request->validate([
        'name'         => 'nullable|string|max:255',
        'address'      => 'nullable|string|max:255',
        'phone_number' => 'nullable|string|max:2000',
        'avatar'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',

        // Password fields (optional)
        'old_password' => 'nullable',
        'new_password' => 'nullable|min:6|confirmed',
        // new_password_confirmation required if new_password given
    ]);

    $user = auth('api')->user();

    if (!$user) {
        return Helper::jsonResponse(false, 'Unauthorized', 404, null);
    }

    try {
        DB::beginTransaction();

        /*
        |--------------------------------------------------------------------------
        | 🔐 PASSWORD CHANGE
        |--------------------------------------------------------------------------
        */
        if ($request->filled('old_password') && $request->filled('new_password')) {

            // Check old password
            if (!Hash::check($request->old_password, $user->password)) {
                return Helper::jsonResponse(false, 'Old password is incorrect', 400);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
        }

        /*
        |--------------------------------------------------------------------------
        | 🖼️ AVATAR UPLOAD
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('avatar')) {

            if (!empty($user->avatar)) {
                Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
            }

            $validatedData['avatar'] = Helper::fileUpload(
                $request->file('avatar'),
                'user/avatar',
                getFileName($request->file('avatar'))
            );

        } else {
            $validatedData['avatar'] = $user->avatar;
        }

        /*
        |--------------------------------------------------------------------------
        | 👤 REMOVE PASSWORD FIELDS FROM UPDATE ARRAY
        |--------------------------------------------------------------------------
        */
        unset(
            $validatedData['old_password'],
            $validatedData['new_password'],
            $validatedData['new_password_confirmation']
        );

        /*
        |--------------------------------------------------------------------------
        | 💾 UPDATE USER
        |--------------------------------------------------------------------------
        */
        $user->update($validatedData);

        // Save password if changed
        if ($request->filled('new_password')) {
            $user->save();
        }

        DB::commit();

        $data = User::select($this->select)->find($user->id);

        return Helper::jsonResponse(true, 'Profile updated successfully', 200, $data);

    } catch (\Exception $e) {

        DB::rollBack();

        if (!empty($validatedData['avatar']) && $validatedData['avatar'] !== $user->avatar) {
            Helper::fileDelete(public_path($validatedData['avatar']));
        }

        return Helper::jsonResponse(false, 'Profile update failed: ' . $e->getMessage(), 500);
    }
}

    private function randomAlphaNum($length = 4)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }


    public function updateAvatar(Request $request)
    {
        $validatedData = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);
        $user = auth('api')->user();
        if (!empty($user->avatar)) {
            Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
        }
        $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar', getFileName($request->file('avatar')));
        $user->update($validatedData);
        $data = User::select($this->select)->find($user->id);
        return Helper::jsonResponse(true, 'Avatar updated successfully', 200, $data);
    }

    public function delete()
    {
        $user = User::findOrFail(auth('api')->id());
        if (!empty($user->avatar) && file_exists(public_path($user->avatar))) {
            Helper::fileDelete(public_path($user->avatar));
        }
        Auth::logout('api');
        $user->delete();
        return Helper::jsonResponse(true, 'Profile deleted successfully', 200);
    }

    public function destroy()
    {
        $user = User::findOrFail(auth('api')->id());
        if (!empty($user->avatar) && file_exists(public_path($user->avatar))) {
            Helper::fileDelete(public_path($user->avatar));
        }
        Auth::logout('api');
        $user->forceDelete();
        return Helper::jsonResponse(true, 'Profile deleted successfully', 200);
    }



    public function changePassword(Request $request)
    {
        $user = auth()->guard('api')->user();

        if (!$user) {
            return Helper::jsonResponse(false, 'User not found', 404, null);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'old_password'      => 'required',
            'new_password'      => 'required|min:6',
            'confirm_password'  => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return Helper::jsonResponse(false, 'Validation failed', 422, $validator->errors());
        }

        // Check if old password is correct
        if (!Hash::check($request->old_password, $user->password)) {
            return Helper::jsonResponse(false, 'Old password does not match', 400, null);
        }

        // Update with new password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return Helper::jsonResponse(true, 'Password changed successfully', 200, null);
    }

    // support

    public function support(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Store in the database
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'status' => 'active',
        ]);

        // Send email to support
        // Mail::send([], [], function ($mail) use ($request) {
        //     $mail->to('support@example.com') // Replace with your support email
        //         ->subject('New Support Request')
        //         ->from($request->email, $request->name)
        //         ->setBody(
        //             "Name: {$request->name}\n" .
        //                 "Email: {$request->email}\n\n" .
        //                 "Message:\n{$request->message}"
        //         );
        // });

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Support request submitted successfully'
        ]);
    }

    //  update manager profile

    public function managerProfile(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return Helper::jsonResponse(false, 'User not found', 404, null);
        }
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string|email|max:255',
            'phone_number' => 'nullable|string|max:2000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        if ($request->hasFile('avatar')) {
            if (!empty($user->avatar)) {
                Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
            }
            $validatedData['avatar'] = Helper::fileUpload(
                $request->file('avatar'),
                'manager/avatar',
                getFileName($request->file('avatar'))
            );
        } else {
            $validatedData['avatar'] = $user->avatar;
        }
        $user->update($validatedData);

        return Helper::jsonResponse(true, 'Profile updated successfully', 200, $user);
    }
}
