<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

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

        // var_dump($request);
        // die;
        $id = Auth::user()->id;
        $data = User::find($id);
        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        //image 
        // var_dump('here');
        // die;
        if ($request->file('photo')) {

            $file = $request->file('photo');
            $filename = date('YmdHi') . $file->getClientOriginalName(); //555.ouarda.png
            @unlink(public_path('uploads/admin_images/' . $data->photo));
            $file->move(public_path('uploads/admin_images'), $filename);
            $data['photo'] = $filename;
        }

        // $data->save();
        if ($data->save()) {
            $notification = array(
                'message' => 'Admin Profile Updated successfully.',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Failed to update profile. Please try again.',
                'alert-type' => 'warning'
            );
        }

        return redirect()->back()->with($notification);
    }
    public function AdminChangePassword()
    {
        $id = Auth::user()->id;
        $Profiledata = User::find($id);
        return view("admin.admin_change_password", compact('Profiledata'));
    }
    public function AdminUpdatePassword(Request $request)
    {
        //validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);
        //match the old password 
        if (!Hash::check($request->old_password, auth::user()->password)) {
            $notification = array(
                'message' => 'Old Password Does not Match !',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }
        //update now the new pasword
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        $notification = array(
            'message' => 'Password change successfully.',
            'alert-type' => 'success'
        );


        return back()->with($notification);
    }
}
