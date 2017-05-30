<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\support_incoming;
use App\support_replies;
use App\support_reply_templates;
use App\order_details;
use App\user_profiles;
use Session;
use Config;
use URL;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;
define("SITE_ADDRESS1",config('config_path_vars.site_address'));

class CustomerSupportTemplatesController extends ZoneBaseClass
{
	public function __construct()
	{
		$this->support_incoming = new support_incoming;
		$this->support_replies = new support_replies;
		$this->support_reply_templates = new support_reply_templates;
		$this->aws = new CustomAwsController;
		// $this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}
	public function display($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$category_arr = $this->support_reply_templates::select('category')->groupBy('category')->pluck('category')->toArray();
			$support_reply_templates = [];
			foreach($category_arr as $category) {
				
				$support_reply_templates[$category] = $this->support_reply_templates::select('*')->where('category',$category)->get()->toArray();
			}
			return view('Admin.CustomerSupportTemplates.index', compact('support_reply_templates'));
		}
		else
		{
			return redirect('/');
		}
	}
    public function saveTemplate($request){
		
		$inputData = $request->all();
		$this->support_reply_templates->category = $inputData['category'];
		$this->support_reply_templates->title = trim($inputData['title']);
		$this->support_reply_templates->template_data = trim($inputData['template_data']);
		if($this->support_reply_templates->save()) {
			return $this->support_reply_templates->id;
		} else {
			return 0;
		}
    }
    public function delete($zone_code, $id)
    {
		if($this->support_reply_templates::first()->where(array('id' => $id))->delete()) {
			return 1;
		} else {
			return 0;
		}
    }
	
}