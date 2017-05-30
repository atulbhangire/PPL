<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\user_profiles;
use Session;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;
use Crypt;
use DB;
use File;
use Schema;

class KYCZoneController extends Controller
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
			return view('Admin.KYC.indexKYC');
		}
		else
		{
			return redirect('/');
		}
	}

	public function renderTable($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$users = user_profiles::select(DB::raw('*, usr_name_verified + usr_address_verified + usr_city_verified + usr_state_verified + usr_country_verified + usr_postalcode_verified + usr_mobile_number_verified + usr_email_id_verified + usr_pan_number_verified + usr_aadhar_number_verified + usr_dob_verified + usr_uploaded_pan_verified + usr_uploaded_aadhar_verified as verified_datas'))->where('usr_status', "Subscribed");
        	return \Datatables::of($users)->editColumn('usr_username', function ($users) {
                return '<a href="/Admin/'.Session::get('zone_name').'/edit/'.$users->usr_id.'">'.$users->usr_username.'</a>';
            })->addColumn('usr_redirect_to_info_page', function($users) {
            	return $users->usr_redirect_to_info_page;
            })->addColumn('verified_datas', function($users) {
            	return ($users->usr_name_verified + $users->usr_address_verified + $users->usr_city_verified + $users->usr_state_verified + $users->usr_country_verified + $users->usr_postalcode_verified + $users->usr_mobile_number_verified + $users->usr_email_id_verified + $users->usr_pan_number_verified + $users->usr_aadhar_number_verified + $users->usr_dob_verified + $users->usr_uploaded_pan_verified + $users->usr_uploaded_aadhar_verified);
            })->make(true);
        }
		else
		{
			return "FALSE";
		}
	}

	public function save($request)
	{
		if(Session::has('is_admin'))
		{
			if((!empty($request['record_id'])) && (!empty($request['username'])))
			{
				$verification_col = $request['column'] . '_verified';
				$data = NULL;
				if($request['verify'] == 1)
				{
					if($request['button'] == "mobile_no_btn")
					{
						$data = array(
							'usr_mobile_country_code' => "+" . $request['country_code_value'],
							$request['column'] => $request['value'],
							$verification_col => $request['verify']
						);
					}
					else if(($request['value'] == "Pan file") || ($request['value'] == "Aadhar file"))
					{
						$data = array(
							$verification_col => $request['verify']
						);
					}
					else
					{
						$data = array(
							$request['column'] => $request['value'],
							$verification_col => $request['verify']
						);
					}
				}
				else if($request['verify'] == 0)
				{
					$data = array(
						$verification_col => $request['verify']
					);
				}
				else if($request['verify'] == 2) // reset
				{
					$request['verify'] = 0;
					if($request['button'] == "mobile_no_btn")
					{
						$data = array(
							'usr_mobile_country_code' => "",
							$request['column'] => $request['value'],
							$verification_col => $request['verify']
						);
					}
					else if($request['value'] == "Pan file")
					{
						$data = array(
							'usr_uploaded_pan' => $request['verify'],
							'usr_uploaded_pan_verified' => $request['verify'],
						);
					}
					else if($request['value'] == "Aadhar file")
					{
						$data = array(
							'usr_uploaded_aadhar' => $request['verify'],
							'usr_uploaded_aadhar_verified' => $request['verify'],
						);
					}
					else
					{
						$data = array(
							$request['column'] => $request['value'],
							$verification_col => $request['verify']
						);
					}
				}
				user_profiles::where(['usr_id' => $request['record_id']])->update($data);
				// Upload member info to S3 --- 10/04/2017 (Tejas)
				$username_enc = base64_encode($request['username']);
				exec('php /var/www/CRONS/user_info_to_s3.php '.$username_enc.' > /dev/null 2> /dev/null &');
				return "1";
			}
			else
			{
				return "0";
			}
		}
		else
		{
			return redirect("/");
		}
	}

	public function edit($zone_code, $id)
    {
    	if(Session::get('is_admin'))
		{
			$profile_obj = user_profiles::select('*')->where('usr_id', $id)->first();

			Session::flash('edit_profile', TRUE);
			Session::flash('usr_id', $id);

			Session::flash('usr_username', $profile_obj->usr_username);

			Session::flash('usr_name', $profile_obj->usr_name);
			Session::flash('usr_name_verified', $profile_obj->usr_name_verified);

			Session::flash('usr_email_id', $profile_obj->usr_email_id);
			Session::flash('usr_email_id_verified', $profile_obj->usr_email_id_verified);

			Session::flash('usr_mobile_country_code', ltrim($profile_obj->usr_mobile_country_code, "+"));
			Session::flash('usr_mobile_number', $profile_obj->usr_mobile_number);
			Session::flash('usr_mobile_number_verified', $profile_obj->usr_mobile_number_verified);

			Session::flash('usr_address', $profile_obj->usr_address);
			Session::flash('usr_address_verified', $profile_obj->usr_address_verified);

			Session::flash('usr_city', $profile_obj->usr_city);
			Session::flash('usr_city_verified', $profile_obj->usr_city_verified);

			Session::flash('usr_state', $profile_obj->usr_state);
			Session::flash('usr_state_verified', $profile_obj->usr_state_verified);

			Session::flash('usr_country', $profile_obj->usr_country);
			Session::flash('usr_country_verified', $profile_obj->usr_country_verified);

			Session::flash('usr_postalcode', $profile_obj->usr_postalcode);
			Session::flash('usr_postalcode_verified', $profile_obj->usr_postalcode_verified);

			Session::flash('usr_pan_number', $profile_obj->usr_pan_number);
			Session::flash('usr_pan_number_verified', $profile_obj->usr_pan_number_verified);

			Session::flash('usr_uploaded_pan', $profile_obj->usr_uploaded_pan);
			if($profile_obj->usr_uploaded_pan)
			{
				$this->downloadFiles(1, $id);
			}
			Session::flash('usr_uploaded_pan_verified', $profile_obj->usr_uploaded_pan_verified);
			Session::flash('cams_kra_pan_info_free', json_decode($profile_obj->cams_kra_pan_info_free, true));

			Session::flash('usr_aadhar_number', $profile_obj->usr_aadhar_number);
			Session::flash('usr_aadhar_number_verified', $profile_obj->usr_aadhar_number_verified);
			
			Session::flash('usr_uploaded_aadhar', $profile_obj->usr_uploaded_aadhar);
			if($profile_obj->usr_uploaded_aadhar)
			{
				$this->downloadFiles(2, $id);
			}
			Session::flash('usr_uploaded_aadhar_verified', $profile_obj->usr_uploaded_aadhar_verified);
			Session::flash('cams_kra_aadhar_info_free', json_decode($profile_obj->cams_kra_aadhar_info_free, true));

			Session::flash('usr_dob', $profile_obj->usr_dob);
			Session::flash('usr_dob_verified', $profile_obj->usr_dob_verified);

			Session::flash('usr_last_active_order_id', $profile_obj->usr_last_active_order_id);
			
			Session::flash('usr_redirect_to_info_page', $profile_obj->usr_redirect_to_info_page);

			header("Content-type: application/pdf");
			return view('Admin.KYC.editKYC');
		}
		else
		{
			return redirect('/');
		}
	}

	public function downloadFiles($flag, $id)
	{
		if(Session::has('is_admin'))
		{
			$info = user_profiles::select('usr_username', 'usr_uploaded_pan', 'usr_uploaded_aadhar')->where('usr_id', $id)->first();
			if(!empty($info))
			{
				$username = base64_encode($info->usr_username);
				$bucket = base64_encode('spt-dev-member-records');
				$filename = "";
				if($flag == 1 && $info->usr_uploaded_pan)
				{
					$pan_filename = base64_encode($info->usr_username . "-pan.pdf");
					$pan_cmd = "php /var/www/CRONS/download_file_from_s3.php " . $username . " " . $bucket . " " . $pan_filename;
					$filename = exec($pan_cmd);
					sleep(1);
					return 1;
				}
				if($flag == 2 && $info->usr_uploaded_pan)
				{
					$aadhar_filename = base64_encode($info->usr_username . "-aadhar.pdf");
					$aadhar_cmd = "php /var/www/CRONS/download_file_from_s3.php " . $username . " " . $bucket . " " . $aadhar_filename;
					$filename = exec($aadhar_cmd);
					sleep(1);
					return 1;
				}
			}
	    }
	    return response()->view('errors.404', [], 404);
	}

	public function downloadActiveUsersInfo(Request $request)
	{
		if(Session::has('is_admin'))
		{
			$column_names = array(
				"usr_id",
				"usr_username",
				"usr_change_password",
				"usr_last_change_password",
				"usr_name",
				"usr_email_id",
				"usr_email_id_temp",
				"usr_mobile_country_code",
				"usr_mobile_number",
				"usr_address",
				"usr_city",
				"usr_state",
				"usr_country",
				"usr_postalcode",
				"usr_registered_at",
				"usr_registered_ip",
				"usr_last_login_at",
				"usr_last_login_ip",
				"usr_last_logout_time",
				"usr_is_blacklisted",
				"usr_last_active_order_id",
				"usr_status",
				"creation_method",
				"created_at",
				"updated_at",
				"my_alert_limit",
				"send_email",
				"send_sms",
				"send_mobile_app_notifications",
				"send_browser_notifications",
				"m0_alerts",
				"m1_alerts",
				"m2_alerts",
				"m3_alerts",
				"m4_alerts",
				"m5_alerts",
				"m6_alerts",
				"m7_alerts",
				"m8_alerts",
				"m9_alerts",
				"f1_alerts",
				"f2_alerts",
				"f3_alerts",
				"f4_alerts",
				"f5_alerts",
				"f6_alerts",
				"f8_alerts",
				"f9_alerts",
				"is_allowed_for_cheque_payment",
				"user_score",
				"usr_followers_count",
				"usr_following_count",
				"usr_is_require_otp",
				"usr_last_activity_at",
				"notification_sound_level_file_name",
				"usr_pan_number",
				"usr_dob",
				"usr_unsubscribe_code",
				"usr_OTP",
				"usr_OTP_set_at",
				"kyc_verified",
				"usr_aadhar_number",
				"usr_uploaded_pan",
				"usr_uploaded_aadhar",
				"usr_uploaded_pan_verified",
				"usr_uploaded_aadhar_verified",
				"usr_ckyc_number",
				"usr_kra_number",
				"kyc_ckyc_verified",
				"kyc_kra_verified",
				"kyc_aadhar_verified",
				"usr_name_verified",
				"usr_address_verified",
				"usr_city_verified",
				"usr_state_verified",
				"usr_country_verified",
				"usr_postalcode_verified",
				"usr_mobile_number_verified",
				"usr_email_id_verified",
				"usr_pan_number_verified",
				"usr_dob_verified",
				"cams_kra_pan_info_free",
				"cams_kra_pan_info_free_verified",
				"cams_kra_aadhar_info_free",
				"cams_kra_aadhar_info_free_verified"
			);
			$user_profiles = user_profiles::select($column_names)->where('usr_status', 'Subscribed')->orderBy('usr_last_active_order_id')->get()->toArray();
			if(!empty($user_profiles))
			{
				$csv_file_path = '/tmp/user_profiles_' . date("Y-m-d_H-i-s") . '.csv';
				$file = fopen($csv_file_path, 'w+');
				fputcsv($file, $column_names);
				foreach($user_profiles as $fields)
				{
					fputcsv($file, $fields);
				}
				fclose($file);
				if(!File::exists($csv_file_path))
				{
					return response()->view('errors.404', [], 404);
				}
				return response()->download($csv_file_path)->deleteFileAfterSend(true);
			}
		}
		else
		{
			return redirect("/");
		}
	}

	public function blockUser(Request $request)
	{
		if(Session::has('is_admin'))
		{
			$username = $request['username'];
			$value = $request['value'];
			if((!empty($username)) && (isSet($value)))
			{
				$data = array(
					'usr_redirect_to_info_page' => $value
				);
				user_profiles::where('usr_username', $username)->update($data);
				// Upload member info to S3 --- 25/04/2017 (Tejas)
				$username_enc = base64_encode($username);
				exec('php /var/www/CRONS/user_info_to_s3.php '.$username_enc.' > /dev/null 2> /dev/null &');
				return $value;
			}
			else
			{
				return "FALSE";
			}
		}
		else
		{
			return redirect("/");
		}
	}

	public function showVerifiedUsers(Request $request)
	{
		if(Session::has('is_admin'))
		{
			$user_profiles = user_profiles::select('usr_id')->where('usr_status', 'Subscribed')->where('usr_redirect_to_info_page', 1)->inRandomOrder()->first();
			if(!empty($user_profiles['usr_id']))
			{
				echo $user_profiles['usr_id'];
			}
			else
			{
				echo "FALSE";
			}
			exit;
		}
		else
		{
			return redirect("/");
		}
	}
}