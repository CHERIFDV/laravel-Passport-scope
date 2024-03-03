<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request ->all(),[
            'name'=> 'required',
            'email'=> 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()){
            $arr = array('status' => false, 'message'=>$validator->errors()->all());
           }else{
             $obj = new User();
             $obj->name = $request->name;
             $obj->email = $request->email;
             $obj->password = Hash::make($request->password);
             $obj->save();
             $arr = array('status' => true, 'message'=>' Query Successfully Send');
           }
           echo json_encode($arr);
    }
    public function login(Request $request){
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);
       
        if ($request->email == "" || $request->password == ""){
            $arr = array('status' =>'false', 'message'=>'Email or Password not much');
           }else{
            $check_status = User::where('email', '=', $request->email)->first();
            if (!auth()->attempt($data)) {
                $arr = array('status' => false, 'message' => 'Email or Password not correct', 'type'=>'warning');
            }
    
            if ($check_status){
                if(Hash::check($request->password, $check_status->password)){
                    $scope =['user'];
                    $token = auth()->user()->createToken('API Token', $scope)->accessToken;
                    $arr = array('status' => true, 'message'=>'success', 'data'=>$check_status, 'type'=>'success','token'=>$token);
                }else{
                    $arr = array('status' => false, 'message' => 'Email or Password not correct', 'type'=>'warning');
                }
            }else{
                $arr = array('status' => false, 'message' => 'Email or Password not correct', 'type'=>'warning');
            }

           }

           echo json_encode($arr);

    }
}
