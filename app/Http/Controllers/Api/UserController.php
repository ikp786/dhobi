<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use App\Http\Resources\UserProfileCollection;
use App\Http\Resources\CompanyCollection;
use App\Http\Resources\DriverProfile;
use App\Http\Resources\EducationCollection;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\SaleNowCollection;
use App\Models\Company;
use App\Models\Education;
use App\Models\SaleNow;

class UserController extends BaseController
{
	public function getUserProfile()
	{
		return $this->sendSuccess('USER DATA GET SUCCESSFULLY', new UserProfileCollection(auth()->user()));
	}

	public function getDriverProfile()
	{
		return $this->sendSuccess('DRIVER DATA GET SUCCESSFULLY', new DriverProfile(auth()->user()));
	}

	// UPDATE PROFILE
	public function update_profile(Request $request)
	{
		$user_details = auth()->user();
		$error_message =     [
			'first_name.required'             => 'First name should be required',
			'last_name.max'                   => 'Last name max length 32 character',
			'email.required'                  => 'Email address should be required',
			'email.unique'                    => 'Email address has been taken',
			'asthetician_license_id.required' => 'Asthetician License Id should be required',
			'asthetician_license_id.unique'   => 'Asthetician License Id has been taken',
			'profile_pic.mimes'               => 'Profile photo format jpg,jpeg,png',
			'profile_pic.max'                 => 'Profile photo max size 2 MB',
		];
		$rules = [
			'first_name'               => 'required|max:20',
			'last_name'                => 'required|max:20',
			'email'                    => 'required|email|unique:users,email,' . $user_details->id . ',id',
			'asthetician_license_id'   => 'required|unique:users,asthetician_license_id,' . $user_details->id,
			'profile_pic'              => 'required',
		];
		if (!empty($request->profile_pic)) {
			$rules['profile_pic'] = 'mimes:jpg,jpeg,png|max:2000';
		}

		$validator = Validator::make($request->all(), $rules, $error_message);

		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}
		try {
			$input = $request->all();
			if (!empty($request->file('profile_pic'))) {
				if (Storage::disk('public')->exists('user_images/' . $user_details->profile_pic)) {
					Storage::disk('public')->delete('user_images/' . $user_details->profile_pic);
				}
				$user_pic = time() . '_' . rand(1111, 9999) . '.' . $request->file('profile_pic')->getClientOriginalExtension();
				$request->file('profile_pic')->storeAs('user_images', $user_pic, 'public');
				$input['profile_pic'] = $user_pic;
			}
			\DB::beginTransaction();
			$user_details->fill($input)->save();
			\DB::commit();
			return $this->sendSuccess('PROFILE UPDATED SUCCESSFULLY', new UserProfileCollection($user_details));
		} catch (\Throwable $e) {
			\DB::rollback();
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	function getNotification()
	{
		$notification      = auth()->user()->notification;
		if (!isset($notification)) {
			return $this->sendFailed('NO NOTIFACATION YET NOW', 200);
		}
		return $this->sendSuccess('NOTIFACATION DATA GET SUCCESSFULLY', NotificationResource::collection($notification));
	}
}
