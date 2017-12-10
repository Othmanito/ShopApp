<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Shop extends Model
{
        protected $table='shops';
        protected $fillable = array('id', 'name', 'ville','adresse','distance','liked','created_at','updated_at');
}
