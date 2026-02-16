<?php

namespace App\Http\Controllers;

use App\Repository\BlacklistRepository;
use Illuminate\Http\Request;

class BlacklistController extends Controller
{
    /**
     * Remove um email da blacklist
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function remover(Request $request)
    {
        // Validar email
        $this->validate($request, [
            'email' => 'required|email'
        ]);

        $email = $request->input('email');

        // Verificar e remover
        $resultado = BlacklistRepository::remover($email);

        if ($resultado['sucesso']) {
            return response()->json([
                'success' => true,
                'data' => '',
                'message' => 'E-mail removido da blacklist com sucesso.'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'data' => '',
                'message' => $resultado['mensagem']
            ], 400);
        }
    }
}
