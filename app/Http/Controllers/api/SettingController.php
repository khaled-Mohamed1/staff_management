<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function update(Request $request){
        try {

            $validateUser = Validator::make(
                $request->all(),
                [
                    'company_name' => 'required',
                    'company_email' => 'required|email|unique:settings,company_email,'.$request->setting_id.',id',
                    'company_phone_NO' => 'required|numeric|digits:10',
                ],[
                    'company_name.required' => 'يجب ادخال اسم الشركة!',
                    'company_email.required' => 'يجب ادخال بريد الإلكتروني للشركة!',
                    'company_email.email' => 'البريد الإلكتروني يجب ان يحتوي على @',
                    'company_email.unique' => 'تم ادخال هذا البريد الإلكتروني من قبل',
                    'company_phone_NO.required' => 'يجب ادخال رقم الجوال للشركة!',
                    'company_phone_NO.numeric' => 'يجب ادخال رقم الجوال بالأرقام!',
                    'company_phone_NO.digits' => 'رقم الجوال تحتوي على 10 ارقام'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $setting = Setting::find($request->setting_id);
            $setting->company_name = $request->company_name;
            $setting->company_email = $request->company_email;
            $setting->company_phone_NO = $request->company_phone_NO;

            if(isset($request->company_logo)){
                $imageName = Str::random(32) . "." . $request->company_logo->getClientOriginalExtension();
                $setting->company_logo = 'https://testing.pal-lady.com/storage/app/public/setting' . $imageName;
                Storage::disk('public')->put('setting/' . $imageName, file_get_contents($request->company_logo));
            }

            $setting->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تعديل بيانات الشركة بنجاح!',
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

            $setting = Setting::find($request->setting_id);

            return response()->json([
                'status' => true,
                'setting' => $setting,
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
