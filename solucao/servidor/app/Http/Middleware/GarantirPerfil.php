<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garante que o usuário autenticado (via Sanctum) tenha um dos perfis
 * permitidos. Usado como `perfil:superadmin` ou `perfil:superadmin,gestor`
 * nas rotas de gestão de usuários — ver routes/api.php.
 */
class GarantirPerfil
{
    public function handle(Request $requisicao, Closure $proximo, string ...$perfisPermitidos): Response
    {
        $usuario = $requisicao->user();

        if (! $usuario || ! in_array($usuario->perfil, $perfisPermitidos, true)) {
            return response()->json(
                ['mensagem' => 'Você não tem permissão para acessar este recurso.'],
                Response::HTTP_FORBIDDEN,
            );
        }

        return $proximo($requisicao);
    }
}
