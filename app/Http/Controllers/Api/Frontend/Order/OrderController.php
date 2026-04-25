<?php

namespace App\Http\Controllers\Api\Frontend\Order;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'code'    => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }
            $user = User::where('phone_number', $request->phone_number)->first();

            if (!$user) {

                $username = Str::slug($request->name) . rand(100, 999);
                $slug     = Str::slug($request->name . '-' . uniqid());

                $user = User::create([
                    'username'         => $username,
                    'slug'             => $slug,
                    'name'             => $request->name,
                    'address'          => $request->address,
                    'phone_number'     => $request->phone_number,
                    'password'         => Hash::make($request->password ?? '12345678'),

                    // auto verified
                    'otp'              => null,
                    'otp_verified_at'  => Carbon::now(),
                    'status'           => 'active',
                    'last_activity_at' => Carbon::now(),
                ]);

                // Assign role (user)
                DB::table('model_has_roles')->insert([
                    'role_id'    => 4,
                    'model_type' => 'App\Models\User',
                    'model_id'   => $user->id,
                ]);

                // Notify admins (optional)
                $notiData = [
                    'user_id' => $user->id,
                    'title'   => 'New user registered',
                    'body'    => 'A new user has registered successfully.',
                ];
            }

            $customer = Customer::create([
                'name'     => $request->name,
                'address'  => $request->address,
                'phone'    => $request->phone_number,
                'receiver' => $request->receiver,
            ]);



            $order = Order::create([
                'user_id'       => $user->id,
                'customer_id'   => $customer->id,
                'receiver'      => $request->receiver,
                'order_number'  => 'ORD-' . time(),
                'order_date'    => $request->order_date,
                'delivery_date' => $request->delivery_date,
                'status'        => 'pending',
            ]);


            $orderDetail = OrderDetail::create([

                'order_id' => $order->id,

                /* ===== garments ===== */
                'single_hand_punjabi' => $request->single_hand_punjabi ?? false,
                'double_hand_punjabi' => $request->double_hand_punjabi ?? false,
                'punjabi'             => $request->punjabi ?? false,
                'arabian_jubba'       => $request->arabian_jubba ?? false,
                'kabli'               => $request->kabli ?? false,
                'fatwa'               => $request->fatwa ?? false,
                'salwar'              => $request->salwar ?? false,
                'pajama'              => $request->pajama ?? false,
                'punjabi_pajama'      => $request->punjabi_pajama ?? false,

                /* ===== upper features ===== */
                'chest_pocket'  => $request->chest_pocket ?? false,
                'collar_button' => $request->collar_button ?? false,
                'double_stitch' => $request->double_stitch ?? false,
                'front_button'  => $request->front_button ?? false,
                'side_cut'      => $request->side_cut ?? false,

                /* ===== bottom features ===== */
                'back_pocket'          => $request->back_pocket ?? false,
                'front_button_pocket'  => $request->front_button_pocket ?? false,
                'single_pocket_design' => $request->single_pocket_design ?? false,
                'double_pocket_design' => $request->double_pocket_design ?? false,

                /* ===== upper measurements ===== */
                'length'   => $request->length,
                'body'     => $request->body,
                'belly'    => $request->belly,
                'sleeves'  => $request->sleeves,
                'neck'     => $request->neck,
                'shoulder' => $request->shoulder,
                'cuff'     => $request->cuff,
                'hip'      => $request->hip,

                /* ===== bottom measurements ===== */
                'bottom_length' => $request->bottom_length,
                'natural'       => $request->natural,
                'waist'         => $request->waist,
                'hi'            => $request->hi,
                'run'           => $request->run,

                /* ===== cost ===== */
                'fabric_qty'       => $request->fabric_qty,
                'fabric_price'     => $request->fabric_price,
                'labor_qty'        => $request->labor_qty,
                'labor_price'      => $request->labor_price,
                'design_qty'       => $request->design_qty,
                'design_price'     => $request->design_price,
                'button_qty'       => $request->button_qty,
                'button_price'     => $request->button_price,
                'embroidery_qty'   => $request->embroidery_qty,
                'embroidery_price' => $request->embroidery_price,
                'courier_qty'      => $request->courier_qty,
                'courier_price'    => $request->courier_price,

                /* ===== money ===== */
                'total'   => $request->total ?? 0,
                'advance' => $request->advance ?? 0,
                'due'     => $request->due ?? 0,

                /* ===== note ===== */
                'note' => $request->note,

                /* ===== new fields ===== */
                'botam_no'                 => $request->botam_no,
                'metal_botam_no'           => $request->metal_botam_no,
                'isnaf_botam_no'           => $request->isnaf_botam_no,
                'tira'                     => $request->tira,
                'serowani_kolar'           => $request->serowani_kolar,
                'band_kolar'               => $request->band_kolar,
                'shirt_kolar'              => $request->shirt_kolar,

                'book_pocket'              => $request->book_pocket ?? false,
                'book_pocket_sticker'      => $request->book_pocket_sticker ?? false,
                'two_pack_ring'            => $request->two_pack_ring ?? false,
                'kof_hand'                 => $request->kof_hand ?? false,
                'koflin_hand'              => $request->koflin_hand ?? false,
                'kolar_black_sticker'      => $request->kolar_black_sticker ?? false,
                'koflin_hand_pocket'       => $request->koflin_hand_pocket ?? false,
                'koflin_hand_pocket_sticker' => $request->koflin_hand_pocket_sticker ?? false,
                'koflin_hand_kolar'        => $request->koflin_hand_kolar ?? false,

            ]);


            DB::commit();

            return response()->json([
                'status'  => true,
                'code'   => 200,
                'message' => 'Order created successfully',
                'order_id'=> $order->id
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $authUser = auth('api')->user();

            if (!$authUser) {
                return response()->json([
                    'status'  => false,
                    'code'    => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }


            $order = Order::with(['customer', 'orderDetail'])->find($id);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ]);
            }


            $customerData = array_filter([
                'name'     => $request->name,
                'address'  => $request->address,
                'phone'    => $request->phone_number,
                'receiver' => $request->receiver,
            ], fn($value) => !is_null($value));

            if (!empty($customerData) && $order->customer) {
                $order->customer->update($customerData);
            }

            $orderData = array_filter([
                'receiver'      => $request->receiver,
                'order_date'    => $request->order_date,
                'delivery_date' => $request->delivery_date,
                'status'        => $request->status,
            ], fn($value) => !is_null($value));

            if (!empty($orderData)) {
                $order->update($orderData);
            }


            $orderDetailsData = array_filter([

                // garments
                'single_hand_punjabi' => $request->single_hand_punjabi,
                'double_hand_punjabi' => $request->double_hand_punjabi,
                'punjabi'             => $request->punjabi,
                'arabian_jubba'       => $request->arabian_jubba,
                'kabli'               => $request->kabli,
                'fatwa'               => $request->fatwa,
                'salwar'              => $request->salwar,
                'pajama'              => $request->pajama,
                'punjabi_pajama'      => $request->punjabi_pajama,

                // upper features
                'chest_pocket'  => $request->chest_pocket,
                'collar_button' => $request->collar_button,
                'double_stitch' => $request->double_stitch,
                'front_button'  => $request->front_button,
                'side_cut'      => $request->side_cut,

                // bottom features
                'back_pocket'          => $request->back_pocket,
                'front_button_pocket'  => $request->front_button_pocket,
                'single_pocket_design' => $request->single_pocket_design,
                'double_pocket_design' => $request->double_pocket_design,

                // measurements
                'length'   => $request->length,
                'body'     => $request->body,
                'belly'    => $request->belly,
                'sleeves'  => $request->sleeves,
                'neck'     => $request->neck,
                'shoulder' => $request->shoulder,
                'cuff'     => $request->cuff,
                'hip'      => $request->hip,

                'bottom_length' => $request->bottom_length,
                'natural'       => $request->natural,
                'waist'         => $request->waist,
                'hi'            => $request->hi,
                'run'           => $request->run,

                // cost
                'fabric_qty'       => $request->fabric_qty,
                'fabric_price'     => $request->fabric_price,
                'labor_qty'        => $request->labor_qty,
                'labor_price'      => $request->labor_price,
                'design_qty'       => $request->design_qty,
                'design_price'     => $request->design_price,
                'button_qty'       => $request->button_qty,
                'button_price'     => $request->button_price,
                'embroidery_qty'   => $request->embroidery_qty,
                'embroidery_price' => $request->embroidery_price,
                'courier_qty'      => $request->courier_qty,
                'courier_price'    => $request->courier_price,

                // money
                'total'   => $request->total,
                'advance' => $request->advance,
                'due'     => $request->due,

                'note' => $request->note,

                /* ===== new fields ===== */
                'botam_no'                 => $request->botam_no,
                'metal_botam_no'           => $request->metal_botam_no,
                'isnaf_botam_no'           => $request->isnaf_botam_no,
                'tira'                     => $request->tira,
                'serowani_kolar'           => $request->serowani_kolar,
                'band_kolar'               => $request->band_kolar,
                'shirt_kolar'              => $request->shirt_kolar,

                'book_pocket'              => $request->book_pocket,
                'book_pocket_sticker'      => $request->book_pocket_sticker,
                'two_pack_ring'            => $request->two_pack_ring,
                'kof_hand'                 => $request->kof_hand,
                'koflin_hand'              => $request->koflin_hand,
                'kolar_black_sticker'      => $request->kolar_black_sticker,
                'koflin_hand_pocket'       => $request->koflin_hand_pocket,
                'koflin_hand_pocket_sticker' => $request->koflin_hand_pocket_sticker,
                'koflin_hand_kolar'        => $request->koflin_hand_kolar,

            ], fn($value) => !is_null($value));

            if (!empty($orderDetailsData)) {

                if ($order->orderDetail) {
                    // ✅ update existing
                    $order->orderDetail->update($orderDetailsData);
                } else {
                    // ✅ create new if not exists
                    $order->orderDetail()->create(
                        $orderDetailsData + ['order_id' => $order->id]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'code'    => 200,
                'message' => 'Order updated successfully',
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'code'    => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }




    public function reOrder($orderId)
    {
        DB::beginTransaction();

        try {
            $authUser = auth('api')->user();

            if (!$authUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Get old order with relations
            $oldOrder = Order::with(['detail', 'customer'])
                ->where('id', $orderId)
                ->where('user_id', $authUser->id) // सुरक्षा: only own order
                ->first();

            if (!$oldOrder) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            /*
        |----------------------------------------
        | 1️⃣ Duplicate Customer
        |----------------------------------------
        */
            $customer = Customer::create([
                'name'     => $oldOrder->customer->name,
                'address'  => $oldOrder->customer->address,
                'phone'    => $oldOrder->customer->phone,
                'receiver' => $oldOrder->customer->receiver,
            ]);

            /*
        |----------------------------------------
        | 2️⃣ Create New Order
        |----------------------------------------
        */
            $newOrder = Order::create([
                'user_id'       => $authUser->id,
                'customer_id'   => $customer->id,
                'receiver'      => $oldOrder->receiver,
                'order_number'  => 'ORD-' . time(),
                'order_date'    => now(),
                'delivery_date' => $oldOrder->delivery_date,
                'status'        => 'pending',
            ]);

            /*
        |----------------------------------------
        | 3️⃣ Copy Order Details
        |----------------------------------------
        */
            $oldDetail = $oldOrder->detail;

            $newDetail = $oldDetail->replicate(); // 🔥 easiest way
            $newDetail->order_id = $newOrder->id;
            $newDetail->save();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Reorder created successfully',
                'data'    => $newOrder
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function getOrderData()
    {
        try {

            $orders = Order::with(['detail', 'user', 'customer'])->latest()->get();

            return response()->json([
                'status' => true,
                'code'   => 200,
                'data'   => $orders
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ]);
        }
    }



    public function UsersOrderData()
    {

        try {
            $userId = auth('api')->id();
            if (!$userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                    'code'    => 401
                ], 401);
            }

            $orders = Order::with(['detail', 'customer'])
                ->where('user_id', $userId) // filter by logged-in user
                ->latest()
                ->get();

            return response()->json([
                'status' => true,
                'code'   => 200,
                'data'   => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function UsersOrderDetails($id)
    {

        try {
            $userId = auth('api')->id();
            if (!$userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                    'code'    => 401
                ], 401);
            }

            $orders = Order::with(['detail', 'customer'])
                ->where('user_id', $userId)->where('id', $id) // filter by logged-in user
                ->latest()
                ->get();

            if (!$orders) {
                return response()->json([
                    'status'  => false,
                    'code'    => 404,
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'code'   => 200,
                'data'   => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ]);
        }
    }



    public function showDetails($id)
    {
        try {


            $order = Order::with(['detail', 'user', 'customer'])
                ->where('id', $id)
                ->first();

            if (!$order) {
                return response()->json([
                    'status'  => false,
                    'code'    => 404,
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'code'   => 200,
                'data'   => $order
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


    public function UpdateOrderStatus(Request $request, $id)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'code'    => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $request->validate([
            'status' => 'required|string'
        ]);

        try {

            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'status'  => false,
                    'code'    => 404,
                    'message' => 'Order not found',
                ], 404);
            }

            $order->update([
                'status' => $request->status
            ]);

            return response()->json([
                'status'  => true,
                'code'    => 200,
                'message' => 'Order status updated successfully',
                'data'    => $order
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'code'    => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // auth user order data

}
