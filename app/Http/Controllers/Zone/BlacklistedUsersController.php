<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use Session;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;
use Crypt;
use DB;
use File;
use Schema;
use App\blacklisted_aadhar;
use App\blacklisted_pan;
use App\blacklisted_email;
use App\blacklisted_mobile;

class BlacklistedUsersController extends Controller
{
	public function __construct()
	{
		$this->blacklisted_aadhar = new blacklisted_aadhar;
		$this->blacklisted_pan = new blacklisted_pan;
		$this->blacklisted_email = new blacklisted_email;
		$this->blacklisted_mobile = new blacklisted_mobile;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}

	public function display($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			return view('Admin.BlacklistedUsers.indexBlacklistedUsers');
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
			$aadhars = blacklisted_aadhar::select('*');
        	return \Datatables::of($aadhars)->addColumn('delete', function ($aadhars) {
            	$ret = "<a data-toggle='modal' href='#deleteAadhar" . $aadhars->id . "'>Delete</a>";
		        $ret .= "<div class='modal fade' id='deleteAadhar" . $aadhars->id . "' tabindex='-1' role='basic' aria-hidden='true' style='display: none;'>";
		        $ret .= "<div class='modal-dialog'>";
		        $ret .= "<div class='modal-content'>";
		        $ret .= "<div class='modal-header'>";
		        $ret .= "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'></button>";
		        $ret .= "<h4 class='modal-title'>Delete Aadhar ?</h4>";
		        $ret .= "</div>";
		        $ret .= "<div class='modal-body'> Do you want delete Aadhar ?</div>";
		        $ret .= "<div class='modal-footer'>";
		        $ret .= "<button type='button' class='btn dark btn-outline' data-dismiss='modal'>Close</button>";
		        $link = Session::has('zone_name') ? Session::get('zone_name') : '';
		        $ret .= "<a class='btn green' href='" . $link . "/delete/" . $aadhars->id . "'>Yes</a>";
		        $ret .= "</div>";
		        $ret .= "</div>";
		        $ret .= "</div>";
		        $ret .= "</div>";
				return $ret;
            })->make(true);
        }
		else
		{
			return "FALSE";
		}
	}

	public function renderTable2($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$pan_nos = blacklisted_pan::select('*');
        	return \Datatables::of($pan_nos)->addColumn('delete', function ($pan_nos) {
            	$ret = "<a data-toggle='modal' href='#deletePAN" . $pan_nos->id . "'>Delete</a>";
		        $ret .= "<div class='modal fade' id='deletePAN" . $pan_nos->id . "' tabindex='-1' role='basic' aria-hidden='true' style='display: none;'>";
		        $ret .= "<div class='modal-dialog'>";
		        $ret .= "<div class='modal-content'>";
		        $ret .= "<div class='modal-header'>";
		        $ret .= "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'></button>";
		        $ret .= "<h4 class='modal-title'>Delete PAN ?</h4>";
		        $ret .= "</div>";
		        $ret .= "<div class='modal-body'> Do you want delete PAN ?</div>";
		        $ret .= "<div class='modal-footer'>";
		        $ret .= "<button type='button' class='btn dark btn-outline' data-dismiss='modal'>Close</button>";
		        $link = Session::has('zone_name') ? Session::get('zone_name') : '';
		        $ret .= "<a class='btn green' href='" . $link . "/delete2/" . $pan_nos->id . "'>Yes</a>";
		        $ret .= "</div>";
		        $ret .= "</div>";
		        $ret .= "</div>";
		        $ret .= "</div>";
				return $ret;
            })->make(true);
        }
		else
		{
			return "FALSE";
		}
	}

	public function renderTable3($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$email_nos = blacklisted_email::select('*');
        	return \Datatables::of($email_nos)->addColumn('delete', function ($email_nos) {
            	$ret = "<a data-toggle='modal' href='#deleteEmail" . $email_nos->id . "'>Delete</a>";
		        $ret .= "<div class='modal fade' id='deleteEmail" . $email_nos->id . "' tabindex='-1' role='basic' aria-hidden='true' style='display: none;'>";
		        $ret .= "<div class='modal-dialog'>";
		        $ret .= "<div class='modal-content'>";
		        $ret .= "<div class='modal-header'>";
		        $ret .= "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'></button>";
		        $ret .= "<h4 class='modal-title'>Delete Email ?</h4>";
		        $ret .= "</div>";
		        $ret .= "<div class='modal-body'> Do you want delete email ?</div>";
		        $ret .= "<div class='modal-footer'>";
		        $ret .= "<button type='button' class='btn dark btn-outline' data-dismiss='modal'>Close</button>";
		        $link = Session::has('zone_name') ? Session::get('zone_name') : '';
		        $ret .= "<a class='btn green' href='" . $link . "/delete3/" . $email_nos->id . "'>Yes</a>";
		        $ret .= "</div>";
		        $ret .= "</div>";
		        $ret .= "</div>";
		        $ret .= "</div>";
				return $ret;
            })->make(true);
        }
		else
		{
			return "FALSE";
		}
	}

	public function renderTable4($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$mobile_nos = blacklisted_mobile::select('*');
        	return \Datatables::of($mobile_nos)->addColumn('delete', function ($mobile_nos) {
            	$ret = "<a data-toggle='modal' href='#deleteMobile" . $mobile_nos->id . "'>Delete</a>";
		        $ret .= "<div class='modal fade' id='deleteMobile" . $mobile_nos->id . "' tabindex='-1' role='basic' aria-hidden='true' style='display: none;'>";
		        $ret .= "<div class='modal-dialog'>";
		        $ret .= "<div class='modal-content'>";
		        $ret .= "<div class='modal-header'>";
		        $ret .= "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'></button>";
		        $ret .= "<h4 class='modal-title'>Delete Mobile ?</h4>";
		        $ret .= "</div>";
		        $ret .= "<div class='modal-body'> Do you want delete mobile ?</div>";
		        $ret .= "<div class='modal-footer'>";
		        $ret .= "<button type='button' class='btn dark btn-outline' data-dismiss='modal'>Close</button>";
		        $link = Session::has('zone_name') ? Session::get('zone_name') : '';
		        $ret .= "<a class='btn green' href='" . $link . "/delete4/" . $mobile_nos->id . "'>Yes</a>";
		        $ret .= "</div>";
		        $ret .= "</div>";
		        $ret .= "</div>";
		        $ret .= "</div>";
				return $ret;
            })->make(true);
        }
		else
		{
			return "FALSE";
		}
	}

	public function delete($zone_code, $id)
    {
		$info = $this->blacklisted_aadhar::select('*')->where('id', $id)->first();
		if($this->blacklisted_aadhar::first()->where(array('id' => $id))->delete()) {
			if(!empty($info))
			{
				$info = $info->toArray();
				$info['operation'] = 'delete';
				$this->sendAlerttoSuperAdmin($info);
			}
			return redirect()->back()->with('alert_message', 'Aadhar deleted successfully.');
		} else {
			return redirect()->back()->with('alert_danger', 'Aadhar was not deleted.');
		}
    }

    public function delete2($zone_code, $id)
    {
		$info = $this->blacklisted_pan::select('*')->where('id', $id)->first();
		if($this->blacklisted_pan::first()->where(array('id' => $id))->delete()) {
			if(!empty($info))
			{
				$info = $info->toArray();
				$info['operation'] = 'delete';
				$this->sendAlerttoSuperAdmin($info);
			}
			return redirect()->back()->with('alert_message', 'PAN deleted successfully.');
		} else {
			return redirect()->back()->with('alert_danger', 'PAN was not deleted.');
		}
    }

    public function delete3($zone_code, $id)
    {
		$info = $this->blacklisted_email::select('*')->where('id', $id)->first();
		if($this->blacklisted_email::first()->where(array('id' => $id))->delete()) {
			if(!empty($info))
			{
				$info = $info->toArray();
				$info['operation'] = 'delete';
				$this->sendAlerttoSuperAdmin($info);
			}
			return redirect()->back()->with('alert_message', 'Email deleted successfully.');
		} else {
			return redirect()->back()->with('alert_danger', 'Email was not deleted.');
		}
    }

    public function delete4($zone_code, $id)
    {
		$info = $this->blacklisted_mobile::select('*')->where('id', $id)->first();
		if($this->blacklisted_mobile::first()->where(array('id' => $id))->delete()) {
			if(!empty($info))
			{
				$info = $info->toArray();
				$info['operation'] = 'delete';
				$this->sendAlerttoSuperAdmin($info);
			}
			return redirect()->back()->with('alert_message', 'Mobile deleted successfully.');
		} else {
			return redirect()->back()->with('alert_danger', 'Mobile was not deleted.');
		}
    }

    public function savePANInBlacklistAjax(Request $request){
    	if(Session::has('is_admin'))
		{
			$valid = 0;
	    	$pan_regex = '/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/';
	    	if(!empty($request->get('pan')) and strlen($request->get('pan')) == 10 and preg_match($pan_regex, $request->get('pan')))
	    	{
	    		$valid = 1;	
	    		$posted_data['pan'] = $request->get('pan');
	    	}
	    	else
	    	{
	    		$valid = 0;
	    	}
	    	if(!empty($request->get('reason')))
	    	{
	    		$valid = 1;	
	    		$posted_data['reason'] = $request->get('reason');
	    	}
	    	else
	    	{
	    		$valid = 0;
	    	}
	    	if($valid)
	    	{
	    		$posted_data['created_by'] = (Session::has('user_name')) ? Session::get('user_name') : NULL;
	    		if (!blacklisted_pan::where('pan', '=', $posted_data['pan'])->exists()) {
	    			// dd($posted_data);
	    			$result = blacklisted_pan::create($posted_data);
	    			if(!empty($result))
	    			{
	    				$posted_data['operation'] = 'add';
	    				$this->sendAlerttoSuperAdmin($posted_data);
	    			}
		    		return (!empty($result)) ? 'success' : NULL;
				}
				else
				{
					return 'fail';
				}
	    	}
		}
		else
		{
			return redirect("/");
		}
    }

    public function saveAadharInBlacklistAjax(Request $request){
    	if(Session::has('is_admin'))
		{
			$valid = 0;
	    	$aadhar_regex = '/^\d*$/';
	    	if(!empty($request->get('aadhar')) and strlen($request->get('aadhar')) == 12 and preg_match($aadhar_regex, $request->get('aadhar')))
	    	{
	    		$valid = 1;	
	    		$posted_data['aadhar'] = $request->get('aadhar');
	    	}
	    	else
	    	{
	    		$valid = 0;
	    	}
	    	if(!empty($request->get('reason')))
	    	{
	    		$valid = 1;	
	    		$posted_data['reason'] = $request->get('reason');
	    	}
	    	else
	    	{
	    		$valid = 0;
	    	}
	    	if($valid)
	    	{
	    		$posted_data['created_by'] = (Session::has('user_name')) ? Session::get('user_name') : NULL;
	    		if (!blacklisted_aadhar::where('aadhar', '=', $posted_data['aadhar'])->exists()) 
    			{
	    			$result = blacklisted_aadhar::create($posted_data);
	    			if(!empty($result))
	    			{
	    				$posted_data['operation'] = 'add';
	    				$this->sendAlerttoSuperAdmin($posted_data);
	    			}
	    			return (!empty($result)) ? 'success' : NULL;
    			}
    			else
				{
					return 'fail';
				}
	    	}
		}
		else
		{
			return redirect("/");
		}
    }

    public function saveEmailInBlacklistAjax(Request $request){
    	if(Session::has('is_admin'))
		{
			$valid = 0;
	    	$email_regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
	    	if(!empty($request->get('email')) and preg_match($email_regex, $request->get('email')))
	    	{
	    		$valid = 1;	
	    		$posted_data['email'] = $request->get('email');
	    	}
	    	else
	    	{
	    		$valid = 0;
	    	}
	    	if(!empty($request->get('reason')))
	    	{
	    		$valid = 1;	
	    		$posted_data['reason'] = $request->get('reason');
	    	}
	    	else
	    	{
	    		$valid = 0;
	    	}
	    	if($valid)
	    	{
	    		$posted_data['created_by'] = (Session::has('user_name')) ? Session::get('user_name') : NULL;
	    		if (!blacklisted_email::where('email', '=', $posted_data['email'])->exists()) 
    			{
	    			$result = blacklisted_email::create($posted_data);
	    			if(!empty($result))
	    			{
	    				$posted_data['operation'] = 'add';
	    				$this->sendAlerttoSuperAdmin($posted_data);
	    			}
	    			return (!empty($result)) ? 'success' : NULL;
	    		}
	    		else
				{
					return 'fail';
				}
	    	}
		}
		else
		{
			return redirect("/");
		}
    }

    public function saveMobileInBlacklistAjax(Request $request){
    	if(Session::has('is_admin'))
		{
			$valid = 0;

			if(!empty($request->get('CountryCode')) and strlen($request->get('CountryCode')) >= 1 and strlen($request->get('CountryCode')) <= 3 and is_numeric($request->get('CountryCode')))
			{
				$valid = 1;
				$country_code = $request->get('CountryCode');
			}
			else
			{
				$valid = 0;
			}
			$ind_mobile_pattern = "/^0[0-9].*$/";
			if($request->get('CountryCode') == 91 and !empty($request->get('mobile')) and !preg_match($ind_mobile_pattern, $request->get('mobile')) and strlen($request->get('mobile')) == 10 and is_numeric($request->get('mobile')))
			{
				$valid = 1;
				$posted_data['mobile'] = $request->get('mobile');
			}
			else if($request->get('CountryCode') == 91 and !empty($request->get('mobile')) and preg_match($ind_mobile_pattern, $request->get('mobile')) and strlen($request->get('mobile')) == 10)
			{
				$valid = 0;
			}
			else if($request->get('CountryCode') == 91 and !empty($request->get('mobile')) and strlen($request->get('mobile')) >=6 and strlen($request->get('mobile')) <= 13)
			{
				$valid = 0;
			}
			else if($request->get('CountryCode') != 91 and !empty($request->get('mobile')) and strlen($request->get('mobile')) >=6 and strlen($request->get('mobile')) <= 13 and is_numeric($request->get('mobile')))
			{
				$valid = 1;
				$posted_data['mobile'] = $request->get('mobile');			
			}
			if(!empty($request->get('mobile')) and strlen($request->get('mobile')) >=6 and strlen($request->get('mobile')) <= 13 and is_numeric($request->get('mobile')))
			{
				$valid = 1;
				$posted_data['mobile'] = $request->get('mobile');
			}
			else
			{
				$valid = 0;
			}
			if(!empty($request->get('reason')))
	    	{
	    		$valid = 1;	
	    		$posted_data['reason'] = $request->get('reason');
	    	}
	    	else
	    	{
	    		$valid = 0;
	    	}

	    	if($valid)
	    	{
	    		if(!empty($country_code) && $posted_data['mobile'])
	    		{
	    			$posted_data['mobile'] = "+" .$country_code.$posted_data['mobile'];
	    		}
	    		$posted_data['created_by'] = (Session::has('user_name')) ? Session::get('user_name') : NULL;
	    		if (!blacklisted_mobile::where('mobile', '=', $posted_data['mobile'])->exists()) 
    			{
		    		$result = blacklisted_mobile::create($posted_data);
		    		if(!empty($result))
	    			{
	    				$posted_data['operation'] = 'add';
	    				$this->sendAlerttoSuperAdmin($posted_data);
	    			}
		    		return (!empty($result)) ? 'success' : NULL;
		    	}
	    		else
				{
					return 'fail';
				}	
	    	}
		}
		else
		{
			return redirect("/");
		}
    }

    public function sendAlerttoSuperAdmin($newData, $oldData=NULL)
    {	
    	if($newData['operation'] == 'add')
    	{
			$Message = "A new blacklisted entry is added by ".Session::get('user_name');
    	}
    	else if($newData['operation'] == 'delete')
    	{
			$Message = "A blacklisted entry has been deleted by ". Session::get('user_name');
    	}
		if(isset($newData['pan']))
		{
			$Message .= " \n PAN : ".$newData['pan'];
		}
		else if(isset($newData['aadhar']))
		{
			$Message .= " \n Aadhar : ".$newData['aadhar'];
		}
		else if(isset($newData['email']))
		{
			$Message .= " \n Email : ".$newData['email'];
		}
		else if(isset($newData['mobile']))
		{
			$Message .= " \n Mobile :".$newData['mobile'];
		}
		if(isset($newData['reason']))
		{
			$Message .= " \n Reason : ".$newData['reason'];
		}
		$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
	}
}