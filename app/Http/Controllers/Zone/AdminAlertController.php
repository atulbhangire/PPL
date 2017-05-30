<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\admin_alerts;
use Session;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;

class AdminAlertController extends ZoneBaseClass
{
	public function __construct()
	{
		$this->admin_alerts = new admin_alerts;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}

    public function display($zone_code)
    {
        if(Session::get('is_admin'))
        {
            Session::set('zone_name', $zone_code);
            return view('Admin.Alert.indexAdminAlerts');
        }
        else
        {
            return redirect('/');
        }
    }

    public function getAdminAlertData()
    {
    	$admin_alerts = $this->admin_alerts::select('*')->orderBy('created_at', 'DESC')->get();
		$zone_name = Session::has('zone_name') ? Session::get('zone_name') : NULL;
		foreach($admin_alerts as $admin_alert)
		{
			// create active/inactive link. change its text using jquery and in backend do it over ajax
			// create delete link, remove row on delete's click and do operation over ajax
			$status = $admin_alert->status = ($admin_alert['is_active'] == 1)? 'Active' : 'inactive';
			if($admin_alert['alert_is_active'])
			{
				$admin_alert->status = "<a id='a_" . $admin_alert['id'] . "' class='edit' onclick=\"toggleAdminAlertStatus(" . $admin_alert['id'] .", 0, this.id);\"> Active </a>";
			}
			else
			{
				$admin_alert->status = "<a id='a_" . $admin_alert['id'] . "' class='edit' onclick=\"toggleAdminAlertStatus(" . $admin_alert['id'] .", 1, this.id)\"> Inactive </a>";
			}
			$delete = "<a data-toggle='modal' href='#deleteAlert" . $admin_alert['id'] . "'>Delete</a>";
			$delete .= "<div class='modal fade' id='deleteAlert" . $admin_alert['id'] . "' tabindex='-1' role='basic' aria-hidden='true' style='display: none;'>";
			$delete .= "<div class='modal-dialog'>";
			$delete .= "<div class='modal-content'>";
			$delete .= "<div class='modal-header'>";
			$delete .= "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'></button>";
			$delete .= "<h4 class='modal-title'>Delete Alert</h4>";
			$delete .= "</div>";
			$delete .= "<div class='modal-body'> Do you want delete " . $admin_alert['stock_unique_identifier'] . "? </div>";
			$delete .= "<div class='modal-footer'>";
			$delete .= " <button type='button' class='btn dark btn-outline' data-dismiss='modal'>Close</button>";
			$delete .= "<button id='delete_" . $admin_alert['id'] . "' type='button' class='btn green' onclick=\"deleteAdminAlert(" . $admin_alert['id'] .", this.id);\" data-dismiss='modal'>Yes</button>";
			$delete .= "</div>";
			$delete .= "</div>";
			$delete .= "</div>";
			$delete .= "</div>";
			$admin_alert->delete = $delete;
		}
		$adminAlert = '{ "data":'.json_encode($admin_alerts). '}';
		return $adminAlert;
    }

    public function toggleAdminAlertStatus($request)
    {
    	if(!empty($request['id']))
    	{
    		$result = $this->admin_alerts::where('id', $request['id'])->update(['alert_is_active' => $request['status']]);
    		if($result)
    		{
    			if($request['status'])
				{
					return "<a id='a_" . $request['id'] . "' class='edit' onclick=\"toggleAdminAlertStatus(" . $request['id'] .", 0, this.id);\"> Active </a>";
				}
				else
				{
					return "<a id='a_" . $request['id'] . "' class='edit' onclick=\"toggleAdminAlertStatus(" . $request['id'] .", 1, this.id)\"> Inactive </a>";
				}
    		}
    		else
    		{
    			return "FALSE";
    		}
    	}
    	else
    	{
    		return "FALSE";
    	}
    }

    public function deleteAdminAlert($request)
    {
    	if(!empty($request['id']))
    	{
    		$result = $this->admin_alerts->where('id', $request['id'])->delete();
    		if($result)
    		{
    			return "TRUE";
    		}
    		else
    		{
    			return "FALSE";
    		}
    	}
    	else
    	{
    		return "FALSE";
    	}
    }

    public function addAdminAlert($stock_unique_identifier, $alert_price, $alert_condition, $alert_message)
    {
    	$this->admin_alerts->stock_unique_identifier = $stock_unique_identifier;
    	$this->admin_alerts->alert_price = $alert_price;
    	$this->admin_alerts->alert_condition = $alert_condition;
    	$this->admin_alerts->alert_message = $alert_message;
		if($this->admin_alerts->save())
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
    }

    public function removeAdminAlert($stock_unique_identifier, $alert_price, $alert_condition, $alert_message)
    {
    	$data = array(
    		'stock_unique_identifier' => $stock_unique_identifier, 
    		'alert_price' => $alert_price, 
    		'alert_condition' => $alert_condition, 
    		'alert_message' => $alert_message, 
    	);
    	if($this->admin_alerts->where($data)->delete())
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }
}
