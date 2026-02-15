<?php
/**
 * Created by PhpStorm.
 * User: Diogenes
 * Date: 10/11/2017
 * Time: 14:06
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class IdUsuarios extends Model
{
    protected $connection = 'compartilhados';
    protected $table = 'id_usuarios';
    protected $primaryKey = 'usua_id';
    protected $fillable = [ 'usua_email' ];
    public $timestamps = false;

    public function instituicao_usuarios() {
        return $this->hasMany('App\Model\InstituicaoUsuarios', 'usua_id', 'usua_id');
    }

    public function instituicao_usuario($inst_codigo) {
        return $this->instituicao_usuarios()->where('inst_codigo', $inst_codigo);
    }

    public function emBlackList () {
        return $this->hasOne('App\Model\BlackList', 'black_list_mail', 'usua_email');
    }
}
