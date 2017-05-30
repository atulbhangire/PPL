<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\user_profiles;
use Session;
use Config;

use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;
use Crypt;

class UserProfilesController extends ZoneBaseClass
{
    public function __construct()
	{
		$this->user_profiles = new user_profiles;
		$this->aws = new CustomAwsController;
		$this->Alert_Admin = Config::get('config_path_vars.Alert_Admin');
	}
	public function display($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$user_profiles = $this->user_profiles::select('*')->orderBy('usr_id','DESC')->paginate(1000);
			//$user_profiles = $user_profiles->toArray();
			return view('Admin.UserProfiles.indexUserProfiles', compact('user_profiles'));
		}
		else
		{
			return redirect('/');
		}
	}

	public function renderTable($zone_code){
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			
			 
			// Array of database columns which should be read and sent back to DataTables.
			// The `db` parameter represents the column name in the database, while the `dt`
			// parameter represents the DataTables column identifier. In this case simple
			// indexes
			$columns = array(
			    array( 'db' => 'usr_id', 'dt' => 0 ),
			    array( 'db' => 'usr_username',  'dt' => 1 ),
			    array( 'db' => 'usr_name',   'dt' => 2 ),
			    array( 'db' => 'usr_email_id',     'dt' => 3 ),
			    array( 'db' => 'usr_mobile_country_code',     'dt' => 4 ),
			    array( 'db' => 'usr_status',     'dt' => 5 ),
			    array( 'db' => 'usr_email_id_temp',     'dt' => 6 ),
			    array( 'db' => 'usr_verify_email_code',     'dt' => 7 ),
			    array( 'db' => 'usr_change_password',     'dt' => 8 ),
			    array( 'db' => 'usr_last_change_password',     'dt' => 9 ),
			    array( 'db' => 'usr_address',     'dt' => 10 ),
			    array( 'db' => 'usr_city',     'dt' => 11 ),
			    array( 'db' => 'usr_state',     'dt' => 12 ),
			    array( 'db' => 'usr_country',     'dt' => 13 ),
			    array( 'db' => 'usr_postalcode',     'dt' => 14 ),
			    array( 'db' => 'usr_pan_number',     'dt' => 15 ),
			    array( 'db' => 'usr_registered_at',     'dt' => 16 ),
			    array( 'db' => 'usr_registered_ip',     'dt' => 17 ),
			    array( 'db' => 'usr_last_login_at',     'dt' => 18 ),
			    array( 'db' => 'usr_last_login_ip',     'dt' => 19 ),
			    array( 'db' => 'usr_last_logout_time',     'dt' => 20 ),
			    array( 'db' => 'usr_last_active_order_id',     'dt' => 21 ),
			    array( 'db' => 'created_at',     'dt' => 22 ),
			    array( 'db' => 'updated_at',     'dt' => 23 )
			   // array( 'db' => 'pan_card_path_in_s3',     'dt' => 24 )
			    
			);

			$users = user_profiles::select('*');//->orderBy('usr_id','DESC');

        	return \Datatables::of($users)->editColumn('usr_username', function ($users) {
                return '<a href="/Admin/'.Session::get('zone_name').'/edit/'.$users->usr_id.'">'.$users->usr_username.'</a>';
            })->make(true);/*->editColumn('pan_card_path_in_s3', function ($users) {
            	if(!empty($users->pan_card_path_in_s3)){
                	return '<a href="'.$users->pan_card_path_in_s3.'">Click here to open</a>';
            	}else{
            		return 'No file available';
            	}
            })*/
			 
		}
		else
		{
			return "FALSE";
		}
	}

	public function addNew($zone_code){

	}

	public function save($request){

	}

	public function edit($zone_code, $id)
    {
    	if(Session::get('is_admin'))
		{
			$profile_obj = user_profiles::select('*')->where('usr_id', $id)->first();

			/*echo $profile_obj->usr_change_password;
			exit();*/
			Session::flash('edit_profile', TRUE);
			Session::flash('usr_id', $id);
			Session::flash('usr_username', $profile_obj->usr_username);
			Session::flash('usr_password', Crypt::decrypt($profile_obj->usr_password));
			//Session::flash('usr_password', $password);
			Session::flash('usr_change_password', $profile_obj->usr_change_password);
			Session::flash('usr_name', $profile_obj->usr_name);

			Session::flash('usr_email_id', $profile_obj->usr_email_id);
			Session::flash('usr_email_id_temp', $profile_obj->usr_email_id_temp);
			Session::flash('usr_mobile_country_code', $profile_obj->usr_mobile_country_code);
			Session::flash('usr_mobile_number', $profile_obj->usr_mobile_number);
			Session::flash('usr_address', $profile_obj->usr_address);
			Session::flash('usr_city', $profile_obj->usr_city);
			Session::flash('usr_state', $profile_obj->usr_state);
			Session::flash('usr_country', $profile_obj->usr_country);
			Session::flash('usr_postalcode', $profile_obj->usr_postalcode);
			Session::flash('usr_pan_number', $profile_obj->usr_pan_number);
			Session::flash('usr_is_blacklisted', $profile_obj->usr_is_blacklisted);

			Session::flash('created_at', $profile_obj->created_at);
			Session::flash('usr_registered_ip', $profile_obj->usr_registered_ip);
			Session::flash('usr_last_login_at', $profile_obj->usr_last_login_at);
			Session::flash('usr_last_login_ip', $profile_obj->usr_last_login_ip);
			Session::flash('usr_last_logout_time', $profile_obj->usr_last_logout_time);
			Session::flash('usr_last_active_order_id', $profile_obj->usr_last_active_order_id);
			Session::flash('usr_status', $profile_obj->usr_status);
			Session::flash('updated_at', $profile_obj->updated_at);
			Session::flash('usr_registered_at', $profile_obj->usr_registered_at);
			Session::flash('usr_last_change_password', $profile_obj->usr_last_change_password);

			Session::flash('fcm_token_android', $profile_obj->fcm_token_android);
			Session::flash('fcm_android_device_id', $profile_obj->fcm_android_device_id);
			Session::flash('fcm_token_ios', $profile_obj->fcm_token_ios);
			Session::flash('fcm_ios_device_id', $profile_obj->fcm_ios_device_id);
			Session::flash('gcm_browser_token', $profile_obj->gcm_browser_token);
			Session::flash('safari_browser_token', $profile_obj->safari_browser_token);
			Session::flash('creation_method', $profile_obj->creation_method);
			Session::flash('my_alert_limit', $profile_obj->my_alert_limit);
			Session::flash('send_email', $profile_obj->send_email);
			Session::flash('send_sms', $profile_obj->send_sms);
			Session::flash('send_mobile_app_notifications', $profile_obj->send_mobile_app_notifications);
			Session::flash('send_browser_notifications', $profile_obj->send_browser_notifications);

			Session::flash('m0_alerts', $profile_obj->m0_alerts);
			Session::flash('m1_alerts', $profile_obj->m1_alerts);
			Session::flash('m2_alerts', $profile_obj->m2_alerts);
			Session::flash('m3_alerts', $profile_obj->m3_alerts);
			Session::flash('m4_alerts', $profile_obj->m4_alerts);
			Session::flash('m5_alerts', $profile_obj->m5_alerts);
			Session::flash('m6_alerts', $profile_obj->m6_alerts);
			Session::flash('m7_alerts', $profile_obj->m7_alerts);
			Session::flash('m8_alerts', $profile_obj->m8_alerts);
			Session::flash('m9_alerts', $profile_obj->m9_alerts);
			Session::flash('f1_alerts', $profile_obj->f1_alerts);
			Session::flash('f2_alerts', $profile_obj->f2_alerts);
			Session::flash('f3_alerts', $profile_obj->f3_alerts);
			Session::flash('f4_alerts', $profile_obj->f4_alerts);
			Session::flash('f5_alerts', $profile_obj->f5_alerts);
			Session::flash('f6_alerts', $profile_obj->f6_alerts);
			Session::flash('f8_alerts', $profile_obj->f8_alerts);
			Session::flash('f9_alerts', $profile_obj->f9_alerts);

			Session::flash('is_allowed_for_cheque_payment', $profile_obj->is_allowed_for_cheque_payment);
			Session::flash('usr_followers_count', $profile_obj->usr_followers_count);
			Session::flash('usr_followers_info', $profile_obj->usr_followers_info);
			Session::flash('usr_following_count', $profile_obj->usr_following_count);
			Session::flash('usr_following_info', $profile_obj->usr_following_info);
			Session::flash('trusted_device_access_token', $profile_obj->trusted_device_access_token);
			Session::flash('last_used_access_token', $profile_obj->last_used_access_token);
			Session::flash('usr_is_require_otp', $profile_obj->usr_is_require_otp);
			Session::flash('trusted_mobile_device', $profile_obj->trusted_mobile_device);
			//Session::flash('pan_card_path_in_s3', $profile_obj->pan_card_path_in_s3);
			
			Session::flash('usr_dob', $profile_obj->usr_dob);
			Session::flash('notification_sound_level_file_name', $profile_obj->notification_sound_level_file_name);
			Session::flash('usr_last_activity_at', $profile_obj->usr_last_activity_at);
			Session::flash('user_score', $profile_obj->user_score);
			Session::flash('safari_browser_token', $profile_obj->safari_browser_token);
			Session::flash('usr_unsubscribe_code', $profile_obj->usr_unsubscribe_code);
			
			Session::flash('usr_OTP', $profile_obj->usr_OTP);
			Session::flash('usr_OTP_set_at', $profile_obj->usr_OTP_set_at);
			
			Session::flash('kyc_verified', $profile_obj->kyc_verified);

			Session::flash('usr_aadhar_number', $profile_obj->usr_aadhar_number);
			Session::flash('usr_uploaded_pan', $profile_obj->usr_uploaded_pan);
			Session::flash('usr_uploaded_aadhar', $profile_obj->usr_uploaded_aadhar);
			Session::flash('usr_uploaded_pan_verified', $profile_obj->usr_uploaded_pan_verified);
			Session::flash('usr_uploaded_aadhar_verified', $profile_obj->usr_uploaded_aadhar_verified);
			Session::flash('usr_ckyc_number', $profile_obj->usr_ckyc_number);
			Session::flash('usr_kra_number', $profile_obj->usr_kra_number);
			Session::flash('kyc_ckyc_verified', $profile_obj->kyc_ckyc_verified);
			Session::flash('kyc_kra_verified', $profile_obj->kyc_kra_verified);
			Session::flash('kyc_aadhar_verified', $profile_obj->kyc_aadhar_verified);

			Session::flash('usr_name_verified', $profile_obj->usr_name_verified);
			Session::flash('usr_address_verified', $profile_obj->usr_address_verified);
			Session::flash('usr_city_verified', $profile_obj->usr_city_verified);
			Session::flash('usr_state_verified', $profile_obj->usr_state_verified);
			Session::flash('usr_country_verified', $profile_obj->usr_country_verified);
			Session::flash('usr_postalcode_verified', $profile_obj->usr_postalcode_verified);
			Session::flash('usr_mobile_number_verified', $profile_obj->usr_mobile_number_verified);
			Session::flash('usr_email_id_verified', $profile_obj->usr_email_id_verified);
			Session::flash('usr_pan_number_verified', $profile_obj->usr_pan_number_verified);
			Session::flash('usr_aadhar_number_verified', $profile_obj->usr_aadhar_number_verified);
			Session::flash('usr_dob_verified', $profile_obj->usr_dob_verified);

			return view('Admin.UserProfiles.editUserProfiles');
		}
		else
		{
			return redirect('/');
		}
	}

	public function update($request)
	{
		if($request->session()->get('is_admin'))
		{

			$data = array(

				'usr_change_password' => $request->input('usr_change_password'),
				'usr_is_blacklisted' => $request->input('usr_is_blacklisted'),
				'send_email' => $request->input('send_email'),
				'send_sms' => $request->input('send_sms'),
				'send_mobile_app_notifications' => $request->input('send_mobile_app_notifications'),
				'send_browser_notifications' => $request->input('send_browser_notifications'),
				'is_allowed_for_cheque_payment' => $request->input('is_allowed_for_cheque_payment'),
				'usr_is_require_otp' => $request->input('usr_is_require_otp')

			);
			$user_id = $request->input('usr_id');

			$profile_obj = user_profiles::select('*')->where('usr_id', $user_id)->first();

				$profile_obj_new = user_profiles::first()->where(array('usr_id' => $user_id));
				$updateNow = $profile_obj_new->update($data);
				if($updateNow){
						
						Session::flash('error_message', 'User Profile Updated successfully!');
						
						$Message = "User Profile Info Changed From Admin \n\nUser Profile : " . $request->input('usr_username') . " updated successfully.";
						$Message .= " \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
						$Message .= "\nFields Changes : \n Username : \n Old Value : ".$profile_obj->usr_username." \n New Value : ".$request->input('usr_username');
						$Message .= "\n \n Change Password (days) : \n Old Value : ".$profile_obj->usr_change_password." \n New Value : ".$request->input('usr_change_password');
						$Message .= "\n\n Name : \n Old Value : ".$profile_obj->usr_name." \n New Value : ".$request->input('usr_name');
						$Message .= "\n\n Email ID : \n Old Value : ".$profile_obj->usr_email_id." \n New Value : ".$request->input('usr_email_id');
						$Message .= "\n\n Temp Email ID : \n Old Value : ".$profile_obj->usr_email_id_temp." \n New Value : ".$request->input('usr_email_id_temp');
						$Message .= "\n\n Mobile Country Code : \n Old Value : ".$profile_obj->usr_mobile_country_code." \n New Value : ".$request->input('usr_mobile_country_code');
						$Message .= "\n\n Mobile Number : \n Old Value : ".$profile_obj->usr_mobile_number." \n New Value : ".$request->input('usr_mobile_number');
						$Message .= "\n\n Address : \n Old Value : ".$profile_obj->usr_address." \n New Value : ".$request->input('usr_address');
						$Message .= "\n\n City : \n Old Value : ".$profile_obj->usr_city." \n New Value : ".$request->input('usr_city');
						$Message .= "\n\n State : \n Old Value : ".$profile_obj->usr_state." \n New Value : ".$request->input('usr_state');
						$Message .= "\n\n Country : \n Old Value : ".$profile_obj->usr_country." \n New Value : ".$request->input('usr_country');
						$Message .= "\n\n Postal Code : \n Old Value : ".$profile_obj->usr_postalcode." \n New Value : ".$request->input('usr_postalcode');
						$Message .= "\n\n PAN : \n Old Value : ".$profile_obj->usr_pan_number." \n New Value : ".$request->input('usr_pan_number');
						$Message .= "\n\n Blacklisted : \n Old Value : ".$profile_obj->usr_is_blacklisted." \n New Value : ".$request->input('usr_is_blacklisted');

						//$this->aws->send_admin_alerts($this->Alert_Admin,$Message);
				}else{
					Session::flash('error_message_danger', 'Unable to edit Gateway!');
				}

			$zoneCode = Session::get('zone_name');
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/";
			return redirect($zone_name);		
		}
		else
		{
			return redirect('/');
		}
	}


	public function delete($zone_code, $id){

	}
}
