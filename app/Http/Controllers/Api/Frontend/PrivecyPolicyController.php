<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PrivacyTerms;
use App\Models\PrivecyAndTerms;
use Illuminate\Http\Request;

class PrivecyPolicyController extends Controller
{
    public function index()
    {
        $data = PrivacyTerms::whereIn('type', ['terms', 'privacy'])->get();

        $formatted = $data->map(function ($item) {

            if ($item->type === 'terms') {
                $description = $item->terms_conditions;
            } elseif ($item->type === 'privacy') {
                $description = $item->privacy_policy;
            } else {
                $description = null;
            }

            return [
                'id' => $item->id,
                'type' => $item->type,
                'description' => $description
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Privacy and Terms fetched successfully',
            'data' => $formatted
        ]);
    }



    public function privacyPolicy()
    {
        $data = PrivacyTerms::select('privacy_policy')
            ->whereNotNull('privacy_policy')
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Privacy policy fetched successfully',
            'data' => $data
        ]);
    }

    public function termsConditions()
    {
        $data = PrivacyTerms::select('terms_conditions')
            ->whereNotNull('terms_conditions')
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Terms & conditions fetched successfully',
            'data' => $data
        ]);
    }
}
