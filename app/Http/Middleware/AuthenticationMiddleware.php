<?php

namespace App\Http\Middleware;

use App\Repository\UsuarioAutenticador;
use App\Service\PluriIDAuthenticator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Closure;
use App\Service\ServiceRotas;
use App\Service\ParceiroService;

class AuthenticationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        return $response;
    }
}
