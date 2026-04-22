<?php

namespace App\Http\Controllers\Web\Backend\InappPurchase;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\InAppPurchase;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rules\In;
use Yajra\DataTables\Facades\DataTables;

class InappPurchaseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = InAppPurchase::with('user')->get();
                
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($data) {
                    if ($data->image) {
                        $url = asset($data->image);
                        return '<img src="' . $url . '" alt="image" width="50px" height="50px" style="margin-left:20px;">';
                    } else {
                        return '<img src="' . asset('default/logo.svg') . '" alt="image" width="50px" height="50px" style="margin-left:20px;">';
                    }
                })
                ->addColumn('user_name', function ($data) {
                    return $data->user ? $data->user->user_name : 'N/A';
                })
                ->addColumn('name', function ($data) {
                    return $data->user ? $data->user->name : 'N/A';
                })
                ->addColumn('email', function ($data) {
                    return $data->user ? $data->user->email : 'N/A';
                })

                ->addColumn('status', function ($data) {
                    $backgroundColor = $data->status == "active" ? '#4CAF50' : '#ccc';
                    $sliderTranslateX = $data->status == "active" ? '26px' : '2px';

                    $status = '<div class="d-flex justify-content-center align-items-center">';
                    $status .= '<div class="form-check form-switch" style="position: relative; width: 50px; height: 24px; background-color: ' . $backgroundColor . '; border-radius: 12px; transition: background-color 0.3s ease; cursor: pointer;">';
                    $status .= '<input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status" style="position: absolute; width: 100%; height: 100%; opacity: 0; z-index: 2; cursor: pointer;">';
                    $status .= '<span style="position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; background-color: white; border-radius: 50%; transition: transform 0.3s ease; transform: translateX(' . $sliderTranslateX . ');"></span>';
                    $status .= '<label for="customSwitch' . $data->id . '" class="form-check-label" style="margin-left: 10px;"></label>';
                    $status .= '</div>';
                    $status .= '</div>';

                    return $status;
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                                <a href="#" type="button" onclick="goToOpen(' . $data->id . ')" class="btn btn-success fs-14 text-white delete-icn" title="View">
                                    <i class="fe fe-eye"></i>
                                </a>

                               
                            </div>';
                })
                ->addColumn('purchase_status', function ($data) {
                    return $data
                        ? '<span class="badge bg-success">Purchased</span>'
                        : '<span class="badge bg-danger">Not Purchased</span>';
                })
                ->addColumn('purchase_date', function ($data) {
                    if ($data) {
                        return $data->created_at->format('j F Y');
                        // example: 2 December 2026
                    }

                    return '<span class="badge bg-danger">Not Purchased</span>';
                })

                ->rawColumns(['image', 'status', 'action', 'purchase_status', 'purchase_date', 'user_name', 'name', 'email'])
                ->make();
        }
        return view("backend.layouts.orderList.index");
    }


    public function show(int $id)
    {
        $data = InAppPurchase::with('user')->where('id', $id)->first();
        
        return view('backend.layouts.orderList.show', compact('data'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = InAppPurchase::findOrFail($id);
            $user->delete();
            return response()->json([
                'status' => 't-success',
                'message' => 'Your action was successful!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 't-error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
