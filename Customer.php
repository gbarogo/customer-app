<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
	protected $table='customer';
	protected $primaryKey='dni';
	protected $fillable = array('dni','phone_number','name','second_surname','surname');
	public function user(){
			return $this->hasMany('App\Userdb');
	}
}
