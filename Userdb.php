<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Userdb extends Model
{
    //
	protected $table='user';
	protected $primaryKey='email';
	protected $fillable = array('email','password','dni_customer');
	protected $hidden=['password'];
	public function user(){
			return $this->beLongsToMany('App\Customer');
	}
}