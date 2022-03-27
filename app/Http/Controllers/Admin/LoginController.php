<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function login(){
      return view('admin.login');
    }

    public function authenticate(Request $request){
      $this->validate($request, [
        'username'   => 'required',
        'password' => 'required'
      ]);
      if (Auth::guard('admin')->attempt(['username' => $request->username,'password' => $request->password]))
      {
          return redirect()->route('admin.dashboard');
      }
      return redirect()->back()->with('alert',__("Username and password don't match"));
    }

    public function loginApi(Request $request)
    {
        $this->validate($request, [
            'username'   => 'required',
            'password' => 'required'
        ]);

        if (Auth::guard('admin')->attempt(['username' => $request->username,'password' => $request->password]))
        {
            $user = Admin::where(['username' => $request->username])->firstOrFail();
            $token = Str::random(80);
            $user->api_token = $token;
            $user->save();

            return response()
                ->json(['message' => 'Hi '.$user->name.', welcome to home','access_token' => $token, 'token_type' => 'Bearer', ]);
        }
    }

    public function logout() {
      Auth::guard('admin')->logout();
      return redirect()->route('admin.login');
    }
}
