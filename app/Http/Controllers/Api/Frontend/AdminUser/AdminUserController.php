<?php

namespace App\Http\Controllers\Api\Frontend\AdminUser;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminUserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function getManagerUsers(Request $request)
    {
        // Fetch all users with the 'manager' role
        $managers = User::role('manager')->get();

        // Return as a resource collection
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Manager users fetched successfully',
            'data' => AdminUserResource::collection($managers),
        ]);
    }
}
