<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class ProductController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $data = Product::simplePaginate(10)->appends(request()->all());
        return response()->json($data, 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->all();
        //Validate data
        $validator = Validator::make($data, Product::$rules);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 200);
        }

        $data = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
        ], 200);
    }

    public function show($id)
    {
        $data = Product::find($id);
    
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, Product not found.'
            ], 400);
        }
    
        return response()->json($data, 200);
    }

    public function search(Request $req)
    {
        $data = Product::where('name', 'like', "%".$req->search."%")
                        ->simplePaginate(10)
                        ->appends(request()->all());
    
        return response()->json($data, 200);
    }

    public function edit(Product $data)
    {
    }

    public function update(Request $request, Product $Product)
    {

        $data = $request->all();
        //Validate data
        $validator = Validator::make($data, Product::$rules);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 200);
        }

        /*$data = */
        $Product = $Product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully'
        ], 200);
    }

    public function destroy($id)
    {
        
        $data = Product::find($id);
        if($data){
            Product::where('id',$id)->update(['deleted_by' => $this->user->id]);
            $data = $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ], 200);   
        }else{

            return response()->json([
                'success' => false,
                'error' => 'Sorry, Product not found.'
            ], 400);
        }
    }
}
