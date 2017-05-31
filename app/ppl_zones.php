<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ppl_zones extends Model
{
	protected $table = 'ppl_zones';
	// protected $fillable = array('zn_id','zn_zone_code', 'zn_name', 'zn_description', 'zn_controller');    
	protected $fillable = [];    
	public static $rules = array();
}