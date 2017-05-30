<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\emails_bounce_complaint;
use Session;

class EmailsBounceComplaintController extends ZoneBaseClass
{
	public function __construct()
	{
		$this->emails_bounce_complaint = new emails_bounce_complaint;
	}

	public function display($zone_code)
    {
        if(Session::get('is_admin'))
        {
            Session::set('zone_name', $zone_code);
            return view('Admin.EmailsBounceComplaint.indexEmailsBounceComplaint');
        }
        else
        {
            return redirect('/');
        }
    }

    public function getInvalidEmailComplaintsData()
    {
        if(Session::get('is_admin'))
        {
            $columns = array(
                array( 'db' => 'email_id', 'dt' => 0 ),
                array( 'db' => 'notification_type',  'dt' => 1 ),
                array( 'db' => 'notification_details',   'dt' => 2 ),
                array( 'db' => 'removed_from_db',     'dt' => 3 ),
                array( 'db' => 'informed_user',     'dt' => 4 ),
                array( 'db' => 'allow_in_future',     'dt' => 5 ),
                array( 'db' => 'original_mail_dump',     'dt' => 6 ),
                array( 'db' => 'report_object_dump',     'dt' => 7 ),
                array( 'db' => 'created_at',     'dt' => 8 ),
                array( 'db' => 'updated_at',     'dt' => 9 )                
            );

            $complaintData = emails_bounce_complaint::select('*');//->orderBy('created_at', 'DESC');

            return \Datatables::of($complaintData)->make(true);
        }
        else
        {
            return redirect('/');
        }
    }
}
