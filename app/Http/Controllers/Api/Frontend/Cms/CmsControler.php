<?php

namespace App\Http\Controllers\Api\Frontend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CMS;
use Illuminate\Http\Request;

class CmsControler extends Controller
{
    public function CmsData()
    {
        $cmsBySection = CMS::query()
            ->get()
            ->groupBy('section');
        return response()->json([
            'status' => 'success',
            'data' => $cmsBySection,
        ]);
    }
}
