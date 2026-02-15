<?php

/**
 * Created by PhpStorm.
 * User: Diogenes
 * Date: 03/11/2017
 * Time: 10:39
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstituicaoUsuarios extends Model
{
    use SoftDeletes;

    protected $connection = 'censo';
    protected $table = 'instituicao_usuarios';
    protected $primaryKey = 'inst_usua_id';
    protected $fillable = [
        'inst_codigo',
        'inst_usua_codigo',
        'inst_usua_idioma',
        'usua_id',
        'inst_usua_cadastro_automatico',
        'inst_usua_funcao'
    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function idUsuario()
    {
        return $this->hasOne('App\Model\IdUsuarios', 'usua_id', 'usua_id');
    }


    public function usuarioPerfis()
    {
        return $this->hasMany('App\Model\UsuarioPerfil', 'inst_usua_id', 'inst_usua_id');
    }
}
