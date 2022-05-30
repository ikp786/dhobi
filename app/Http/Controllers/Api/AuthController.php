<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\DriverProfile;
use App\Http\Resources\UserProfileCollection;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// use App\Http\Resources\UserProfileCollection;
use App\Models\User;
use Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Validator;

class AuthController extends BaseController
{

	public function __construct()
	{
		$this->Helper   = new Helper;
	}
	// UNAUTHORIZED ACCESS
	public function unauthorized_access()
	{
		return $this->sendFailed('YOU ARE NOT UNAUTHORIZED TO ACCESS THIS URL, PLEASE LOGIN AGAIN', 401);
	}

	public function registerOtpVerify(Request $request)
	{
		$error_message = 	[
			// 'mobile.unique'  	              => 'mobile has been already taken',
			'mobile.required'            	  => 'Mobile should be required',
			'user_id.required'			 	  => 'User Id should be required',
			'otp.required'					  => 'OTP should be required',
		];
		$rules = [
			'mobile'                          => 'required|min:10|max:10',
			'user_id'						  => 'required|integer|exists:users,id',
			'otp'						      => 'required|integer',
			'device_token'					  => 'required'
		];
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}
		try {
			\DB::beginTransaction();

			$user_detail = User::find($request->user_id);
			if ($user_detail->mobile != $request->mobile) {
				return $this->sendFailed("Mobile number does not exist", 200);
			}
			if ($request->otp != $user_detail->otp) {
				return $this->sendFailed("wrong otp", 200);
			}
			// $user = User::updateOrCreate(
			// 	$request->only('mobile', 'role')
			// );
			Auth::loginUsingId($user_detail->id);
			$access_token = auth()->user()->createToken(auth()->user()->mobile)->accessToken;
			auth()->user()->fill($request->only(['device_token']))->save();
			\DB::commit();
			return $this->sendSuccess('LOGGED IN SUCCESSFULLY', ['access_token' => $access_token, 'profile_data' => new UserProfileCollection(auth()->user())]);
		} catch (\Throwable $e) {
			\DB::rollback();
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	// CREATE ACCOUNT API
	public function userRegister(Request $request)
	{
		$error_message = 	[
			// 'mobile.unique'  	              => 'mobile has been already taken',
			'mobile.required'            	  => 'Mobile should be required',
			'role.required'			 	  	  => 'User Type should be required',
			'role.In'						  => 'User Type should be 1 OR 2',
		];
		$rules = [
			'mobile'                          => 'required|min:10|max:10',
			'role'						      => 'required|In:2,3'
		];
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}

		try {
			\DB::beginTransaction();
			// $user = new User();
			// $user->fill($request->all());
			// $user->password = Hash::make($request->password);
			// $user->save();
			// echo config('constant.ROLE.USER');die;
			// echo $request->user_type;die;		

			// $data      = $request->only('mobile','role');
			// $data['role'] = $user_type;
			$user = User::updateOrCreate(
				$request->only('mobile', 'role')
			);
			// print_r($user->id);die;
			$verifaction_otp = rand(1000, 9999);
			$user->otp = $verifaction_otp;
			if ($user->role == 3 && $user->unique_id == '') {
				$user->unique_id = $user->id . rand(1000, 9999);
			}
			$user->save();
			Self::send_sms_otp($request->mobile, $verifaction_otp);
			\DB::commit();
			return $this->sendSuccess('OTP SENT SUCCESSFULLY', ['user_id' => $user->id, 'otp' => $verifaction_otp]);
		} catch (\Throwable $e) {
			\DB::rollback();
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	public function getUserProfile()
	{
		return $this->sendSuccess('LOGGED IN SUCCESSFULLY', ['profile_data' => new UserProfileCollection(auth()->user())]);
	}

	public function getDriverProfile()
	{
		$delivery_charge                = Setting::value('deliver_charge');
		return $this->sendSuccess('LOGGED IN SUCCESSFULLY', ['profile_data' => new DriverProfile(auth()->user())]);
	}

	public function sentRegisterOtp(Request $request)
	{
		$error_message = 	[
			'mobile.required'  	=> 'Mobile address should be required',
		];
		$rules = [
			'mobile'       		=> 'required',
		];
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}
		try {
			$verifaction_otp = rand(1000, 9999);
			$email_data = ['otp' => $verifaction_otp];
			// \Mail::to($request->email_address)->send(new \App\Mail\LoginOtp($email_data));
			return $this->sendSuccess('OTP SENT SUCCESSFULLY', ['verifaction_otp' => $verifaction_otp, 'mobile' => $request->mobile]);
		} catch (\Throwable $e) {
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	public function reSentOtpMobileUpdate(Request $request)
	{
		$error_message = 	[
			'mobile.required'  	=> 'Mobile address should be required',
		];
		$rules = [
			'mobile' => 'required|unique:users,mobile,' . auth()->user()->id,
		];
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}
		$mobileExist = User::where('id', '!=', auth()->user()->id)->where(['role' => auth()->user()->role, 'mobile' => $request->mobile])->count();
		if ($mobileExist > 0) {
			return $this->sendFailed("Mobile number has been already taken", 200);
		}
		try {
			$verifaction_otp = rand(1000, 9999);
			Self::send_sms_otp($request->mobile, $verifaction_otp);
			$user = auth()->user();
			$user->otp = $verifaction_otp;
			$user->save();
			return $this->sendSuccess('OTP SENT SUCCESSFULLY', ['verifaction_otp' => $verifaction_otp, 'mobile' => $request->mobile]);
		} catch (\Throwable $e) {
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	public function registerReSentOtp(Request $request)
	{
		$error_message = 	[
			// 'mobile.unique'  	              => 'mobile has been already taken',
			'mobile.required'            	  => 'Mobile should be required',
			'user_id.required'			 	  => 'User Id should be required',
		];
		$rules = [
			'mobile'                          => 'required|min:10|max:10|exists:users,mobile',
			'user_id'						  => 'required|integer|exists:users,id',
		];
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}
		$user_detail = User::find($request->user_id);
		if ($user_detail->mobile != $request->mobile) {
			return $this->sendFailed("Mobile number does not exist", 200);
		}
		try {
			$verifaction_otp = rand(1000, 9999);
			// Self::send_sms_otp($request->mobile, $verifaction_otp);
			$user_detail->otp = $verifaction_otp;
			$user_detail->save();
			Self::send_sms_otp($request->mobile, $verifaction_otp);
			\DB::commit();
			return $this->sendSuccess('OTP SENT SUCCESSFULLY', ['user_id' => $user_detail->id, 'otp' => $verifaction_otp]);
		} catch (\Throwable $e) {
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	public function updateMobile(Request $request)
	{
		$error_message = 	[
			'mobile.required'  	=> 'Mobile should be required',
			'OTP.required'  	=> 'OTP  should be required',
		];
		$rules = [
			'mobile'            => 'required|min:10|max:10',
			'otp'               => 'required|min:4|max:4',
		];
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}
		$mobileExist = User::where('id', '!=', auth()->user()->id)->where(['role' => auth()->user()->role, 'mobile' => $request->mobile])->count();
		if ($mobileExist > 0) {
			return $this->sendFailed("Mobile number has been already taken", 200);
		}

		if (auth()->user()->otp != $request->otp) {
			return $this->sendFailed("wrong otp", 200);
		}

		try {
			\DB::beginTransaction();
			$user_details = auth()->user();
			$user_details->mobile = $request->mobile;
			$user_details->otp = null;
			$user_details->save();
			\DB::commit();
			return $this->sendSuccess('MOBILE NUMBER UPDATE SUCCESSFULLY');
		} catch (\Throwable $e) {
			\DB::rollback();
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	// UPDATE PROFILE
	public function updateUserProfile(Request $request)
	{
		$error_message = 	[
			'name.required'    	 => 'Name should be required',
			'email.required'	 => 'Email address should be required',
			'address.required'	 => 'Address should be required',
			'email.unique'  	 => 'Email address has been taken',
			'profile_pic.mimes'  => 'Profile photo format jpg,jpeg,png',
			'profile_pic.max'    => 'Profile photo max size 2 MB',
			'dob.required'		 => 'Date Of Birth should be required.',
			'dob.required'		 => 'Date Of Birth should be valid date format.'
		];
		$rules = [
			'name'            => 'required|max:20',
			'email' 		  => 'required|email',
			'mobile'          => 'required|min:10|max:10',
			'dob'			  => 'required|date',
			'address'		  => 'required',
		];
		if (!empty($request->profile_pic)) {
			$rules['profile_pic'] = 'mimes:jpg,jpeg,png|max:2000';
		}
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}

		$mobileExist = User::where('id', '!=', auth()->user()->id)->where(['role' => auth()->user()->role, 'mobile' => $request->mobile])->count();
		if ($mobileExist > 0) {
			return $this->sendFailed("Mobile number has been already taken", 200);
		}

		$emailExist = User::where('id', '!=', auth()->user()->id)->where(['role' => auth()->user()->role, 'email' => $request->email])->count();
		if ($emailExist > 0) {
			return $this->sendFailed("Email address has been already taken", 200);
		}

		try {
			\DB::beginTransaction();
			$user_details = auth()->user();
			$user_details->fill($request->only('name', 'email', 'address', 'dob', 'address'));
			if (!empty($request->file('profile_pic'))) {
				if (Storage::disk('public')->exists('user_images/' . $user_details->user_pic_name)) {
					Storage::disk('public')->delete('user_images/' . $user_details->user_pic_name);
				}
				$user_pic = time() . '_' . rand(1111, 9999) . '.' . $request->file('profile_pic')->getClientOriginalExtension();
				$request->file('profile_pic')->storeAs('user_images', $user_pic, 'public');
				$request['profile_pic'] = $user_pic;
				$user_details->profile_pic = $user_pic;
			}
			$user_details->save();
			\DB::commit();
			if ($request->mobile == auth()->user()->mobile) {
				$mobileVerify = array('mobile_verify_status' => 1);
				return $this->sendSuccess('PROFILE UPDATE SUCCESSFULLY', $mobileVerify);
			} else {
				$otp = rand(1000, 9999);
				Self::send_sms_otp($request->mobile, $otp);
				$user = auth()->user();
				$verifaction_otp = $otp;
				$user->otp = $verifaction_otp;
				$user->save();
				$mobileVerify = array('mobile_verify_status' => 0, 'otp' => $otp);
				return $this->sendSuccess('PROFILE UPDATE AND OTP SENT SUCCESSFULLY YOUR MOBILE', $mobileVerify);
			}
		} catch (\Throwable $e) {
			\DB::rollback();
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	public function forgot_password(Request $request)
	{
		$error_message = 	[
			'email.required'    => 'Email address should be required',
			'email.exists'      => 'WE COULD NOT FOUND ANY EMAIL'
		];
		$rules = [
			'email'       		=> 'required|email|exists:users,email',
		];
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}
		try {
			$user_detail = User::where('email', $request->email)->first();
			if (!isset($user_detail)) {
				return $this->sendFailed('WE COULD NOT FOUND ANY ACCOUNT', 200);
			}
			$verifaction_otp = rand(1000, 9999);
			$email_data = ['user_name' => $user_detail->first_name, 'verifaction_otp' => $verifaction_otp];
			\Mail::to($user_detail->email)->send(new \App\Mail\ForgotPassword($email_data));
			return $this->sendSuccess('OTP SENT SUCCESSFULLY', ['user_id' => $user_detail->id, 'verifaction_otp' => $verifaction_otp, 'email' => $user_detail->email]);
		} catch (\Throwable $e) {
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	public function reset_password(Request $request)
	{
		$error_message = 	[
			'id.required'  		=> 'Id should be required',
			'password.required' => 'Password should be required',
		];
		$rules = [
			'id'        		=> 'required|numeric|exists:users,id',
			'password'      	=> 'required',
		];
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}
		try {
			$user_detail = User::find($request->id);
			if (!isset($user_detail)) {
				return $this->sendFailed('WE COULD NOT FOUND ANY ACCOUNT', 200);
			}
			\DB::beginTransaction();
			$user_detail->password = Hash::make($request->user_password);
			$user_detail->save();
			\DB::commit();
			return $this->sendSuccess('PASSWORD UPDATED SUCCESSFULLY');
		} catch (\Throwable $e) {
			\DB::rollback();
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	public function send_sms_otp($mobile_number, $verification_otp)
	{
// 		return;
// die;
		// $opt_url = "https://2factor.in/API/V1/fd9c6a99-19d7-11ec-a13b-0200cd936042/SMS/" . $mobile_number . "/" . $verification_otp . "/OTP_TAMPLATE";
		//$opt_url = "https://2factor.in/API/V1/786547ea-bbc8-11ec-9c12-0200cd936042/SMS/" . $mobile_number . "/" . $verification_otp . "/OTP_TAMPLATE";
		$opt_url = "https://2factor.in/API/V1/eaf9b2b6-d5b4-11ec-9c12-0200cd936042/SMS/" . $mobile_number . "/" . $verification_otp . "/OTP_TAMPLATE";
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $opt_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_PROXYPORT, "80");
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($curl);
		// echo $result;die;
		return;
	}


	public function sendsms2factorotp($numbers, $otp)
    {
       
      /*   phone = '+918949529301';
        $otp = '7777'; */

        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => 'https://2factor.in/API/V1/786547ea-bbc8-11ec-9c12-0200cd936042/SMS/+' . $numbers . '/' . $otp . '',

            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",

            CURLOPT_MAXREDIRS => 10,

            CURLOPT_TIMEOUT => 30,

            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "{}",

        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        $respons = json_decode($response, true);
        // print_r($respons); exit;
        return $respons;
        // echo "<pre>";
        // print_r($respons['Status']);

        curl_close($curl);
    }


	public function changeOnlineStatus()
	{
		try {
			\DB::beginTransaction();
			$change = User::find(auth()->user()->id)->update(['online_status' => auth()->user()->online_status == 'Online' ? 'Offline' : 'Online']);
			\DB::commit();
			return $this->sendSuccess('Online Status change succssfully');
		} catch (\Throwable $e) {
			\DB::rollback();
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}
}
