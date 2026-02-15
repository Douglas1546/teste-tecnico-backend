<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class BlackList extends Model
{
    protected $connection = "email";
    protected $table = "em_black_list";
    protected $primaryKey = "black_list_id";
}
