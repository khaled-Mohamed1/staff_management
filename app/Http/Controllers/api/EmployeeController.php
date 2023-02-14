<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
    use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{

    public function index(){
        try {

            $users = User::where('role_id', 2)->latest()->get();

            return response()->json([
                'status' => true,
                'user' => $users,
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request){
        try {

            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                    'ID_NO' => 'required|numeric|digits:9',
                    'phone_NO' => 'required|numeric|digits:10',
                    'job' => 'required',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
                ],[
                    'name.required' => 'يجب ادخال اسم الموظف!',
                    'email.required' => 'يجب ادخال بريد الإلكتروني للموظف!',
                    'email.email' => 'البريد الإلكتروني يجب ان يحتوي على @',
                    'email.unique' => 'تم ادخال هذا البريد الإلكتروني من قبل',
                    'password' => 'يجب ادخال كلمة المرور للموظف',
                    'ID_NO.required' => 'يجب ادخال رقم هوية للموظف!',
                    'ID_NO.numeric' => 'يجب ادخال رقم الهوية بالأرقام!',
                    'ID_NO.digits' => 'رقم الهوية تحتوي على 9 ارقام!',
                    'phone_NO.required' => 'يجب ادخال رقم الجوال للموظف!',
                    'phone_NO.numeric' => 'يجب ادخال رقم الجوال بالأرقام!',
                    'phone_NO.digits' => 'رقم الجوال تحتوي على 9 ارقام',
                    'job.required' => 'يجب ادخال وظيفة الموظف',
                    'image.required' => 'يجب ادخال صورة الموظف',
                    'image.image' => 'يجب ادخال بالحقل صورة',
                    'image.mimes' => 'يجب ادخال نسخة الصورة بالشكل الصحيح',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();

            $user = User::create([
               'name' => $request->name,
               'email' => $request->email,
               'password' => bcrypt($request->password),
                'ID_NO' => $request->ID_NO,
                'phone_NO' => $request->phone_NO,
                'role_id' => '2',
                'job' => $request->job,
                'image' => 'https://testing.pal-lady.com/public/storage/app/employees' . $imageName ,
            ]);
            Storage::disk('public')->put('employees/' . $imageName, file_get_contents($request->image));

            $user->assignRole(2);

            $details = [
                'email' => $user->email,
                'password' => $request->password
            ];

            \Mail::to($request->email)->send(new \App\Mail\RegisterUserMail($details));

            return response()->json([
                'status' => true,
                'message' => 'تم اضافة الموظف بنجاح',
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request){
        try {

            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,'.$request->user_id.',id',
                    'ID_NO' => 'required|numeric|digits:9',
                    'phone_NO' => 'required|numeric|digits:10',
                    'job' => 'required',
                ],[
                    'name.required' => 'يجب ادخال اسم الموظف!',
                    'email.required' => 'يجب ادخال بريد الإلكتروني للموظف!',
                    'email.email' => 'البريد الإلكتروني يجب ان يحتوي على @',
                    'email.unique' => 'تم ادخال هذا البريد الإلكتروني من قبل',
                    'password' => 'يجب ادخال كلمة المرور للموظف',
                    'ID_NO.required' => 'يجب ادخال رقم هوية للموظف!',
                    'ID_NO.numeric' => 'يجب ادخال رقم الهوية بالأرقام!',
                    'ID_NO.digits' => 'رقم الهوية تحتوي على 9 ارقام!',
                    'phone_NO.required' => 'يجب ادخال رقم الجوال للموظف!',
                    'phone_NO.numeric' => 'يجب ادخال رقم الجوال بالأرقام!',
                    'phone_NO.digits' => 'رقم الجوال تحتوي على 9 ارقام',
                    'job.required' => 'يجب ادخال وظيفة الموظف',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::find($request->user_id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone_NO = $request->phone_NO;
            $user->ID_NO = $request->ID_NO;
            $user->job = $request->job;

            if(isset($request->password)){
                $user->password = bcrypt($request->password);

                $details = [
                    'email' => $request->email,
                    'password' => $request->password
                ];

                \Mail::to($request->email)->send(new \App\Mail\RegisterUserMail($details));
            }

            if(isset($request->image)){
                $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();
                $user->image = 'https://testing.pal-lady.com/public/storage/app/employees' . $imageName;
                Storage::disk('public')->put('employees/' . $imageName, file_get_contents($request->image));
            }

            $user->save();


            return response()->json([
                'status' => true,
                'message' => 'تم تعديل الموظف بنجاح',
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request){
        try {

            $user = User::find($request->user_id);


            return response()->json([
                'status' => true,
                'user' => $user,
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request){
        try {

            $user = User::find($request->user_id)->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف الموظف بنجاح!',
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
