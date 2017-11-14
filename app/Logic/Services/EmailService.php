<?php

namespace App\Logic\Services;

use Log;
use Mail;
use App\User;

class EmailService
{

	public function SendRegisterEmailToAdmin ($user_id)
	{
		$user = User::findOrFail($user_id);
		if (isset($user))
		{
	        $data = array(
	            'organization' => $user->organization,
	            'name' => $user->name,
	            'user_id' => $user->id
	        );

			Mail::send('emails.confirm_user', $data, function ($message) use ($user) {

				$message->from( \Config::get('constants.ADMIN_EMAIL'), \Config::get('constants.WEBSITE_NAME') );

				$message->to( \Config::get('constants.ADMIN_EMAIL'))->subject( \Config::get('constants.WEBSITE_NAME').': Confirm new user');

			});
		}
	}

	public function SendConfirmationEmail ($user_id)
	{
		$user = User::findOrFail($user_id);
		if (isset($user))
		{
	        $data = array(
	            'name' => $user->name
	        );

			Mail::send('emails.user_confirmation', $data, function ($message) use ($user) {

				$message->from( \Config::get('constants.ADMIN_EMAIL'), \Config::get('constants.WEBSITE_NAME') );

				$message->to($user->email)->subject( \Config::get('constants.WEBSITE_NAME').': Your account has been activated');

			});
		}
	}

	public function SendEmailWithMatlabOutput ($user_id, $file_path)
	{
		// Log::info('[USER_ID SEND_EMAIL]: '.$user_id);
		$user = User::findOrFail($user_id);
		if (isset($user))
		{
	        $data = array(
	            'name' => $user->name
	        );

			Mail::send('emails.matlab_output', $data, function ($message) use ($user, $file_path) {

				$message->from( \Config::get('constants.ADMIN_EMAIL'), \Config::get('constants.WEBSITE_NAME') );

				$message->to($user->email)->subject( \Config::get('constants.WEBSITE_NAME').': Matlab result of your Project.');

				$message->attach($file_path);

			});
		}
	}

}