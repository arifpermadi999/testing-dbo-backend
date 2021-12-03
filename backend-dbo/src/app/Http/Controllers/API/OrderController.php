<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use JWTAuth;
use DB;
use Tymon\JWTAuth\Exceptions\JWTException;
class OrderController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $data = Order::with(['customer','order_details','order_details.products'])->simplePaginate(10)->appends(request()->all());
        return response()->json($data, 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        DB::beginTransaction();
        try {
            $validator = Validator::make($data, Order::$rules,Order::$messages_rules);
            if ($validator->fails()) {
                DB::rollback();
                return response()->json(['success' => false,'message' => $validator->messages()], 200);
            }
            $order = Order::create($data);
            foreach($data['detail'] as $detail){
                $validator = Validator::make($detail, OrderDetail::$rules);
                if ($validator->fails()) {
                    DB::rollback();
                    return response()->json(['success' => false,'message' => $validator->messages()], 200);
                }

                $orderDetail = OrderDetail::where('product_id',$detail['product_id'])->where('order_id',$order->id)->first();
                if($orderDetail){
                    DB::rollback();
                    return response()->json(['success' => false,'message' => "can't add duplicate product in one order"], 200);   
                }else{
                    $product = Product::where("id",$detail['product_id'])->first();
                    $detail['product_price'] = $product->price;
                    $detail['order_id'] = $order->id;
                    $orderDetail = OrderDetail::create($detail);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false,'message' => $e->getMessage()], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['success' => false,'message' => $e->getMessage()], 200);
        }
            

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
        ], 200);

        

    }

    public function show($id)
    {
        $data = Order::with(['customer','order_details','order_details.products'])->find($id);
    
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, Order not found.'
            ], 400);
        }
    
        return response()->json($data, 200);
    }

    public function search(Request $req)
    {
        $data = Order::with(['customer','order_details','order_details.products'])
                        ->where('invoice_no', 'like', "%".$req->search."%")
                        ->simplePaginate(10)
                        ->appends(request()->all());
    
        return response()->json($data, 200);
    }

    public function edit(Order $data)
    {
    }

    public function update(Request $request, Order $order)
    {

        $data = json_decode($request->getContent(), true);
        
        DB::beginTransaction();
        try {
            $validator = Validator::make($data, Order::$rules,Order::$messages_rules);
            if ($validator->fails()) {
                DB::rollback();
                return response()->json(['success' => false,'message' => $validator->messages()], 200);
            }
            $order->update($data);
        
            foreach($data['detail'] as $detail){
                
                $validator = Validator::make($detail, OrderDetail::$rules);
                if ($validator->fails()) {
                    DB::rollback();
                    return response()->json(['success' => false,'message' => $validator->messages()], 200);
                }

                $orderDetail = OrderDetail::where('product_id',$detail['product_id'])->where('order_id',$order->id)->first();
                $product = Product::where("id",$detail['product_id'])->first();

                if($orderDetail){
                    $orderDetail->product_price = $product->price;
                    $orderDetail->product_qty = $detail['product_qty'];
                    $orderDetail->update();
                }else{
                    $detail['product_price'] = $product->price;
                    $detail['order_id'] = $order->id;
                    $orderDetail = OrderDetail::create($detail);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $error = sprintf('[%s],[%d] ERROR:[%s]', __METHOD__, __LINE__, json_encode($e->getMessage(), true));
            return response()->json(['success' => false,'message' => $error], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            $error = sprintf('[%s],[%d] ERROR:[%s]', __METHOD__, __LINE__, json_encode($e->getMessage(), true));
            return response()->json(['success' => false,'message' => $error], 200);
        }
            

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
        ], 200);
    }

    public function destroy($id)
    {
        
        $data = Order::find($id);
        $data_detail = OrderDetail::where('order_id',$id);
        if($data){
            $data_detail->delete();
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ], 200);   
        }else{

            return response()->json([
                'success' => false,
                'error' => 'Sorry, Order not found.'
            ], 400);
        }
    }
}
