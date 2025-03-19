<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class AdminController extends Controller
{
    //
    public function AdminDashboard()
    {

        return view('admin.index');
    }

    public function AdminLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('admin/login');
    }
    public function AdminLogin()
    {

        return view('admin.admin_login');
    }

    public function AdminProfile()
    {
        $id = Auth::user()->id;
        $Profiledata = User::find($id);
        return view('admin.admin_profile_view', compact('Profiledata'));
        // var_dump($Profiledata);
        // die;
    }

    public function AdminProfileStore(Request $request)
    {


        $id = Auth::user()->id;
        $data = User::find($id);
        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        //image 
        if ($request->file('photo')) {
            $file = $request->file('photo');
            $filename = date('YmdHi') . $file->getClientOriginalName(); //555.ouarda.png
            @unlink(public_path('uploads/admin_images/' . $data->photo));
            $file->move(public_path('uploads/admin_images'), $filename);
            $data['photo'] = $filename;
        }
        // var_dump($data->save());
        // die;
        if ($data->save()) {
            $notification = array(
                'message' => 'Admin Profile Updated successfully',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => ' Error while updating Admin Profile ',
                'alert-type' => 'warning'
            );
        }

        return redirect()->back()->with($notification);
    }
}
