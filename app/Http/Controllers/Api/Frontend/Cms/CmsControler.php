<?php

namespace App\Http\Controllers\Api\Frontend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CMS;
use Illuminate\Http\Request;

class CmsControler extends Controller
{
    public function CmsData()
    {
        $homeIntro = CMS::all();
        return response()->json([
            'status' => 'success',
            'data' => $homeIntro,
        ]);
    }
}
