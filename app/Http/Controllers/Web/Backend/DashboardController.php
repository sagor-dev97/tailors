<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\Transaction;
use App\Models\InAppPurchase;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use function App\Helpers\parseTemplate;

class DashboardController extends Controller
{
    public function index()
    {

        return view('backend.layouts.dashboard');
    }
}
