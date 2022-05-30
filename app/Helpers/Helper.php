<?php

namespace App\Helpers;

use App\Models\Properties;
use App\Models\Bookings;
use App\Models\Notification;
use Carbon\Carbon;

class Helper
{
	// GENERATE RANDOM REQUEST AND BOOKING NUMBER
	public function random_strings($length_of_string)
	{

		// String of all alphanumeric character
		$str_result = '0123456789';

		// Shuffle the $str_result and returns substring
		// of specified length
		return substr(
			str_shuffle($str_result),
			0,
			$length_of_string
		);
	}

	// CHECK PROPERTY AVAILABILITY ACCORDING DATE AND TIME
	public function check_availability($property_id, $checkin_date, $checkout_date)
	{
		$availability_status = True;
		$booking_count		= Bookings::query()
		->where(function ($q) use ($checkin_date,$checkout_date) { 
			$q->whereRaw('"' . $checkin_date . '" between `checkin_date` and `checkout_date`');
			$q->OrwhereRaw('"' . $checkout_date . '" between `checkin_date` and `checkout_date`');
			$q->OrWhereBetween('checkin_date', [$checkin_date, $checkout_date]);
			$q->OrWhereBetween('checkout_date', [$checkin_date, $checkout_date]);
		});
		$booking_count->where('property_id', $property_id)->where('booking_status', '!=', config('constant.BOOKING_STATUS.CANCELLED'));
		$booking_count  = $booking_count->count();

		if ($booking_count > 0) {
			$availability_status	= False;
		}

		return $availability_status;
	}

	// AMOUNT CALCULATION FOR STAY (MONTH, WEEK, DAY & HOURS)
	public function calculate_property_amount($property_id, $checkin_date, $checkout_date)
	{
		$property_amount	= 0;
		$property_details 	= Properties::find($property_id);
		$working_hours 		= round(abs(strtotime($property_details->checkout_time) - strtotime($property_details->checkin_time)) / 3600, 2);
		
		
		 
		if(strtotime(date('Y-m-d', strtotime($checkin_date))) == strtotime(date('Y-m-d', strtotime($checkout_date)))) {
			 $cDate = date('Y-m-d');
			 $BookedDate_1 = date('Y-m-d', strtotime($checkin_date));
			 $BookedDate_2 = date('Y-m-d', strtotime($checkout_date));
		 
			 if( ($cDate == $BookedDate_1) && ($cDate == $BookedDate_2) ) {
			 
				$hour_stay			= round(abs(strtotime($checkin_date) - strtotime(date('H:i:s', strtotime($checkout_date)))) / 3600, 2);		

			 } else {
				$hour_stay			= round(abs( strtotime(date('H:i:s', strtotime($checkout_date)) ) - strtotime(date('H:i:s', strtotime($checkin_date)) ) ) / 3600, 2);		
			 }	
			
		} else {
			$hour_stay			= round(abs(strtotime($property_details->checkout_time) - strtotime(date('H:i:s', strtotime($checkin_date)))) / 3600, 2) + round(abs(strtotime($property_details->checkin_time) - strtotime(date('H:i:s', strtotime($checkout_date)))) / 3600, 2);
		}
		
		
		
		
		
		$diff_days 			= strtotime(date('Y-m-d', strtotime($checkout_date))) - strtotime(date('Y-m-d', strtotime($checkin_date)));
		$days_stay 			= (abs(round($diff_days / 86400)) > 0 ? abs(round($diff_days / 86400)) - 1 : abs(round($diff_days / 86400)));
		$total_hours_stay	= $hour_stay + ($days_stay * $working_hours);

		$monthly_hours		= $working_hours * 30;
		$weekly_hours		= $working_hours * 7;
		
		
		 
		

		if ($total_hours_stay >= $monthly_hours && !empty($property_details->monthly_rate)) {
			$stay_months		= (int) ($total_hours_stay / $monthly_hours);
			$monthly_amount		= (int) ($total_hours_stay / $monthly_hours) * $property_details->monthly_rate;
			$property_amount	+= $monthly_amount;
			$remaining_hours	= $total_hours_stay - ((int) ($total_hours_stay / $monthly_hours)) * $monthly_hours;
		}

		if (!empty($property_details->weekly_rate) && $total_hours_stay >= $weekly_hours) {
			if (isset($remaining_hours)) {
				$stay_weeks			= (int) ($remaining_hours / $weekly_hours);
				$weekly_amount		= (int) ($remaining_hours / $weekly_hours) * $property_details->weekly_rate;
				$property_amount	+= $weekly_amount;
				$remaining_hours	= $remaining_hours - ((int) ($remaining_hours / $weekly_hours)) * $weekly_hours;
			} else {
				$stay_weeks			= (int) ($total_hours_stay / $weekly_hours);
				$weekly_amount		= (int) ($total_hours_stay / $weekly_hours) * $property_details->weekly_rate;
				$property_amount	+= $weekly_amount;
				$remaining_hours	= $total_hours_stay - ((int) ($total_hours_stay / $weekly_hours)) * $weekly_hours;
			}
		}

		if (!empty($property_details->daily_rate) && $total_hours_stay >= $working_hours) {
			if (isset($remaining_hours)) {
				$stay_days			= (int) ($remaining_hours / $working_hours);
				$daily_amount		= (int) ($remaining_hours / $working_hours) * $property_details->daily_rate;
				$property_amount	+= $daily_amount;
				$remaining_hours	= $remaining_hours - ((int) ($remaining_hours / $working_hours)) * $working_hours;
			} else {
				$stay_days			= (int) ($total_hours_stay / $working_hours);
				$daily_amount		= (int) ($total_hours_stay / $working_hours) * $property_details->daily_rate;
				$property_amount	+= $daily_amount;
				$remaining_hours	= $total_hours_stay - ((int) ($total_hours_stay / $working_hours)) * $working_hours;
			}
		}

		if (!isset($remaining_hours)) {
			
			$stay_hours			= $total_hours_stay;
			$hourly_amount 		= $total_hours_stay * $property_details->hourly_rate;
			$property_amount	+= $hourly_amount;
		} else {
			if ($remaining_hours > 0) {
				$stay_hours			= $remaining_hours;
				$hourly_amount 		= $remaining_hours * $property_details->hourly_rate;
				$property_amount	+= $hourly_amount;
			}
		}

		return [
			'property_price' => [
				'monthly_rate' 		=> $property_details->monthly_rate,
				'weekly_rate' 		=> $property_details->weekly_rate,
				'daily_rate' 		=> $property_details->daily_rate,
				'hourly_rate' 		=> $property_details->hourly_rate,
			],
			'calculate_price' => [
				'stay_months' 		=> isset($stay_months) ? $stay_months : NULL,
				'monthly_amount' 	=> isset($monthly_amount) ? $monthly_amount : NULL,
				'stay_weeks' 		=> isset($stay_weeks) ? $stay_weeks : NULL,
				'weekly_amount' 	=> isset($weekly_amount) ? $weekly_amount : NULL,
				'stay_days' 		=> isset($stay_days) ? $stay_days : NULL,
				'daily_amount' 		=> isset($daily_amount) ? $daily_amount : NULL,
				'stay_hours' 		=> isset($stay_hours) ? $stay_hours : NULL,
				'hourly_amount' 	=> isset($hourly_amount) ? $hourly_amount : NULL,
				'property_amount' 	=> $property_amount
			]
		];
	}

	public function SendNotification($device_token, $title, $body, $user_id, $booking = NULL, $user_type = null, $notification_count = null,$is_save=null)
	{
		$url = 'https://fcm.googleapis.com/fcm/send';
		$headers = array(
			'Authorization: key=AAAAUOgfqs0:APA91bGXNEadxR-8RtcDVMe6_F8YdLN6ZKeluqT8LXYixDSEPdhpb6ZTed9SSD7FU93uf_AhGYRzW0HwaJmQpiRe1hLODUwYOJaqAoY8MnH8W32h-5OvfkBDEtNv8O8_QRNbD8v34lOJ',
			'Content-Type: application/json',
		);

		$data = array(
			"to" => $device_token,
			"notification" =>
			array(
				"title" 			=> $title,
				"body"  			=> $body,
				"request_number"  	=> $booking,
				"sound" 			=> 'default',
				'badge'             => '1',
				'action_type'       => 'transfer',
			),
			"data" =>
			array(
				"title" 			=> $title,
				"body"  			=> $body,
				"request_number"  	=> $booking,
				"sound" 			=> 'default',
				'badge'             => '1',
				'action_type'       => 'transfer',
			)
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		$result = curl_exec($ch);
		curl_close($ch);
		if ($notification_count != '') {
			$notification = new Notification;
			//$notification->user_id 				= 1;
			$notification->notification_title 	= $title . ' ' . $booking;
			$notification->notification_details = $body;
			$notification->user_type = $user_type;
			$notification->usercount = $notification_count;
			$notification->save();
		} else {
			if($is_save != 1){
			$notification = new Notification;
			$notification->user_id 				= $user_id;
			$notification->notification_title 	= $title . ' ' . $booking;
			$notification->notification_details = $body;
			$notification->user_type = $user_type;
			$notification->save();
			}
		}
		return $result;
	}

	static function agoDate($date)
	{
		return Carbon::parse($date)->diffForHumans(null, true, true, 2) . ' ago';
		// return Carbon::parse($date)->diffForHumans();
	}
}
