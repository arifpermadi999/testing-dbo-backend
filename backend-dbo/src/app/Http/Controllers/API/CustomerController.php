<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class CustomerController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $data = Customer::simplePaginate(10)->appends(request()->all());
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
        $validator = Validator::make($data, Customer::$rules);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 200);
        }

        $data = Customer::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
        ], 200);
    }

    public function show($id)
    {
        $data = Customer::find($id);
    
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, Customer not found.'
            ], 400);
        }
    
        return response()->json($data, 200);
    }

    public function search(Request $req)
    {
        $data = Customer::where('name', 'like', "%".$req->search."%")
                        ->simplePaginate(10)
                        ->appends(request()->all());
    
        return response()->json($data, 200);
    }

    public function edit(Customer $data)
    {
    }

    public function update(Request $request, Customer $customer)
    {

        $data = $request->all();
        //Validate data
        $validator = Validator::make($data, Customer::$rules_update);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 200);
        }

        /*$data = */
        $customer = $customer->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
            'data' => $customer
        ], 200);
    }

    public function destroy($id)
    {
        
        $data = Customer::find($id);
        if($data){
            Customer::where('id',$id)->update(['deleted_by' => $this->user->id]);
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully'
            ], 200);   
        }else{

            return response()->json([
                'success' => false,
                'error' => 'Sorry, Customer not found.'
            ], 400);
        }
    }
}
