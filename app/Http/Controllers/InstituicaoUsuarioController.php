<?php

/**
 * Created by PhpStorm.
 * User: Diogenes
 * Date: 03/11/2017
 * Time: 10:41
 */

namespace App\Http\Controllers;

use App\Model\InstituicaoUsuarios;
use App\Repository\InstituicaoUsuarioRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InstituicaoUsuarioController extends Controller
{

    public function byId(Request $request)
    {
        try {
            $usuario = InstituicaoUsuarios::where('inst_usua_id', '=', $request->input('inst_usua_id'))
                ->first();
            $dados_usuario = $usuario->idUsuario;
            $usuario_perfil = $usuario->usuarioPerfis;

            $usuario_retorno = array();
            $usuario_retorno['inst_usua_id'] = $request->input('inst_usua_id');
            $usuario_retorno['usua_id'] = strval($dados_usuario->usua_id);
            $usuario_retorno['usuario_nome'] = $dados_usuario->usua_nome;
            $usuario_retorno['usuario_email'] = $dados_usuario->usua_email;
            $usuario_retorno['usuario_codigo'] = $usuario->inst_usua_codigo;
            $usuario_retorno['usuario_cpf'] = $dados_usuario->usua_cpf;
            $usuario_retorno['usuario_funcao'] = $usuario->inst_usua_funcao;
            $usuario_retorno['usuario_sexo'] = $dados_usuario->usua_sexo;
            $usuario_retorno['usuario_idioma'] = $usuario->inst_usua_idioma;
            $usuario_retorno['email_blacklist'] = (!empty($dados_usuario->usua_email) && !empty($dados_usuario->emBlackList) ? 1 : 0);
            $usuario_retorno['usua_foto'] = $dados_usuario['usua_foto'];
            $usuario_retorno['usua_foto_miniatura'] = $dados_usuario['usua_foto_miniatura'];

            if (!empty($dados_usuario->usua_data_nascimento) && $this->validar_data($dados_usuario->usua_data_nascimento)) {
                $data_nascimento = date_create($dados_usuario->usua_data_nascimento);
                $usuario_retorno['data_nascimento'] = $data_nascimento->format('d/m/Y');
            } else {
                $usuario_retorno['data_nascimento'] = '';
            }
            $usuario_retorno['usuario_telefones'] = array();

            $usuario_retorno['usuario_perfil'] = array();
            foreach ($usuario_perfil as $perfil) {
                $instituicao_perfil = $perfil->instituicaoPerfil;
                $perfil_array = array();
                $perfil_array['perf_id'] = $perfil->perf_id;
                $perfil_array['perf_descricao'] = $instituicao_perfil->perf_descricao;
                $perfil_array['usua_tipo_id'] = $instituicao_perfil->usua_tipo_id;
                $usuario_retorno['usuario_perfil'][] = $perfil_array;
            }

            return new JsonResponse(['success' => true, 'data' => $usuario_retorno, 'message' => ''], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'data' => '', 'message' => 'Ocorreu um erro no processamento da requisição'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function listar(Request $request)
    {
        try {
            $inst_codigo = $request->header('inst_codigo');

            if (!empty($request->input('offset'))) {
                $offset = " LIMIT " . config('constants.SEGUNDO_LIMIT') . " OFFSET " . $request->input('offset');
                $dados = InstituicaoUsuarioRepository::listar($inst_codigo, $offset);
            } else {
                $dados = InstituicaoUsuarioRepository::listar($inst_codigo, " LIMIT " . config('constants.PRIMEIRO_LIMIT'));
            }

            return new JsonResponse(['success' => true, 'data' => $dados, 'message' => ''], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'data' => '', 'message' => 'Ocorreu um erro no processamento da requisição'], Response::HTTP_BAD_REQUEST);
        }
    }

    // Função responsável por saber se a data é valida ou não, porque a data estava retornando do Eloquent "0000-00-00"
    private function validar_data($dat)
    {
        $data = explode("-", "$dat"); // fatia a string $dat em pedados, usando - como referência
        $d = $data[2];
        $m = $data[1];
        $y = $data[0];

        $res = checkdate($m, $d, $y);
        if ($res == 1) {
            return true;
        } else {
            return false;
        }
    }
}
