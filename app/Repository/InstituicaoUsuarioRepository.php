<?php

namespace App\Repository;


class InstituicaoUsuarioRepository
{

    public static function listar($inst_codigo, $offset)
    {
        $sql = "SELECT
                    " . config('database.censo_schema')  .  ".instituicao_usuarios.inst_usua_id,
                    " . config('database.censo_schema')  .  ".instituicao_usuarios.inst_usua_codigo,
                    " . config('database.compartilhados_schema')  .  ".id_usuarios.usua_nome,
                    " . config('database.compartilhados_schema')  .  ".id_usuarios.usua_email,
                    GROUP_CONCAT(" . config('database.censo_schema')  .  ".instituicao_perfil.perf_descricao SEPARATOR ', ') AS usuario_perfil,
                    IF(" . config('database.email_schema')  .  ".em_black_list.black_list_id IS NULL, 0, 1) AS email_blacklist
                FROM " . config('database.censo_schema')  .  ".instituicao_usuarios
                INNER JOIN " . config('database.compartilhados_schema')  .  ".id_usuarios ON " . config('database.censo_schema')  .  ".instituicao_usuarios.usua_id = " . config('database.compartilhados_schema')  .  ".id_usuarios.usua_id
                INNER JOIN " . config('database.censo_schema')  .  ".usuario_perfil ON " . config('database.censo_schema')  .  ".instituicao_usuarios.inst_usua_id = " . config('database.censo_schema')  .  ".usuario_perfil.inst_usua_id
                INNER JOIN " . config('database.censo_schema')  .  ".instituicao_perfil ON " . config('database.censo_schema')  .  ".usuario_perfil.perf_id = " . config('database.censo_schema')  .  ".instituicao_perfil.perf_id
                LEFT JOIN " . config('database.email_schema')  .  ".em_black_list ON " . config('database.email_schema')  .  ".em_black_list.black_list_mail = " . config('database.compartilhados_schema')  .  ".id_usuarios.usua_email
                    AND " . config('database.compartilhados_schema')  .  ".id_usuarios.usua_email <> ''
                    AND " . config('database.compartilhados_schema')  .  ".id_usuarios.usua_email IS NOT NULL
                WHERE " . config('database.censo_schema')  .  ".instituicao_usuarios.deleted_at IS NULL
                    AND " . config('database.censo_schema')  .  ".usuario_perfil.deleted_at IS NULL
                    AND " . config('database.censo_schema')  .  ".instituicao_perfil.deleted_at IS NULL
                    AND " . config('database.censo_schema')  .  ".instituicao_usuarios.inst_codigo = :inst_codigo
                    
                GROUP BY " . config('database.censo_schema')  .  ".instituicao_usuarios.inst_usua_id
                ORDER BY " . config('database.compartilhados_schema')  .  ".id_usuarios.usua_nome
                $offset
                ";
                return \DB::select($sql, ['inst_codigo' => $inst_codigo]);
    }
}
