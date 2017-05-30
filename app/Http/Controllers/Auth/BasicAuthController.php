<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Config;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthController extends Controller
{
    protected $username;

	protected $encryption_key;

	protected $admin;

	protected $adminData;

	protected $loggedOut;

	public function basic()
	{
		// if ($this->check()) return;

		if ($this->attemptBasic($this->getRequest())) return;

		return $this->getBasicResponse();
	}

	public function getRequest()
	{
		// if (!Session::has('httpAuthSuperUser'))
		{	
			return Request::createFromGlobals();
		}	
		// return false;
	}

	protected function attemptBasic(Request $request)
	{
		// if ( ! $request->getUser()) return false;
		return $this->checkCredentials($this->getBasicCredentials($request));
	}

	protected function getBasicCredentials(Request $request)
	{
		return ['username' => $request->getUser(), 'password' => $request->getPassword()];
	}

	public function checkCredentials(array $credentials = [])
	{
		// dd($credentials);
		// $encrypted_pass = $this->encrypt($credentials['password']);
		// $pass = ($this->decrypt($encrypted_pass));
		$admin_http_username=Config::get('config_path_vars.admin_http_username');
		$admin_http_password=Config::get('config_path_vars.admin_http_password');
		if($credentials['username'] == $admin_http_username && $credentials['password'] == $admin_http_password)
		{
			return true;
		}
		return false;
	}

	protected function getBasicResponse()
	{
		$headers = ['WWW-Authenticate' => 'Basic'];

		return new Response('Invalid credentials.', 401, $headers);
	}

	public static function check()
	{
		$value = AdminAuth::getAdminData();
		return ! is_null($value);
	}

	/**
	 * Determine if the current user is a guest.
	 *
	 * @return bool
	 */
	public static function guest()
	{
		return ! AdminAuth::check();
	}

	/**
	 * Get list of zones.
	 *
	 * @return bool
	 */
	public static function getZones()
	{
		return TPZone::get();
	}

	public static function getNavbarMenus()
	{
		return TPDynamicMenu::where('parent_menu_id', 0)->where('is_visible', 0)->orderBy('menu_order', 'asc')->get();
	}

	public static function getNavbarSubMenus($parent_menu_id)
	{
		return TPDynamicMenu::where('parent_menu_id', $parent_menu_id)->where('is_visible', 0)->orderBy('menu_order', 'asc')->get();
	}

	public static function getAdminData()
	{
		$value = Session::get('adminData');
		return $value;
	}


	public static function checkZoneAllow($zone)
	{
		$adminData = Session::get('adminData');
		if($adminData->$zone)
		{
			return true;
		}
		return false;
	}

	public static function checkZoneAllowInMiddleware($zone_name)
	{
		$zone_id = TPZone::where('zone_name',$zone_name)->pluck('id');
		$adminData = Session::get('adminData');
		$zone = 'is_allowed_zone'.$zone_id;
		if($adminData->$zone)
		{
			return true;
		}
		return false;
	}

	
}
