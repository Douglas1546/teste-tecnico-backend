<?php
/**
 * Created by PhpStorm.
 * User: Diogenes
 * Date: 03/11/2017
 * Time: 14:18
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioPerfil extends Model
{
    use SoftDeletes;

    protected $connection = 'censo';
    protected $table = 'usuario_perfil';
    protected $primaryKey = 'usua_perf_id';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'inst_usua_id',
        'perf_id',
        'ano_leti_id',
        'usua_perf_cadastro_automatico'
    ];

    public function instituicaoPerfil() {
        return $this->hasOne('App\Model\InstituicaoPerfil', 'perf_id', 'perf_id');
    }
}
