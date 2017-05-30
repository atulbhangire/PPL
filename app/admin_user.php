<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class admin_user extends Model
{
	protected $table = 'admin_user';
	protected $fillable = [];    
	public $timestamps = true;
	public static $rules = array();
}