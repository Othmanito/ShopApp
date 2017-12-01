<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
        protected $fillable = array('id', 'name', 'ville','adresse','distance','liked','created_at','updated_at');
}
