<?php
/**
 * Created by PhpStorm.
 * User: Diogenes
 * Date: 03/11/2017
 * Time: 09:27
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstituicaoPerfil extends Model
{
    use SoftDeletes;

    protected $connection = 'censo';
    protected $table = 'instituicao_perfil';
    protected $primaryKey = 'perf_id';
    protected $fillable = [
        'perf_descricao',
        'inst_codigo',
        'perf_codigo',
        'usua_tipo_id'
    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
}