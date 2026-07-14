<?php

namespace App\Http\Controladores;

use App\Modelos\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as RespostaHttp;

/**
 * Gestão de usuários (cadastro de campo + gestores).
 *
 * Regras de alçada (verificadas em cada ação, não só na rota):
 * - superadmin: cria/edita/remove qualquer perfil (colaborador, gestor,
 *   superadmin) e enxerga todos os usuários.
 * - gestor: cria/edita/remove apenas colaboradores (usuários de campo) e só
 *   enxerga colaboradores. Não pode criar outro gestor nem superadmin.
 * - colaborador: não tem acesso a este controlador (bloqueado no middleware
 *   `perfil` da rota).
 */
class UsuarioControlador extends Controlador
{
    /** Perfis que o solicitante tem permissão de criar/editar/remover. */
    private function perfisGerenciaveis(Usuario $solicitante): array
    {
        return $solicitante->ehSuperadmin()
            ? ['superadmin', 'gestor', 'colaborador']
            : ['colaborador'];
    }

    public function listar(Request $requisicao): JsonResponse
    {
        $solicitante = $requisicao->user();

        $consulta = Usuario::query()->orderBy('nome');
        if (! $solicitante->ehSuperadmin()) {
            $consulta->where('perfil', 'colaborador');
        }

        return response()->json(
            $consulta->get()->map(fn (Usuario $u) => $this->formatar($u))->all(),
        );
    }

    public function armazenar(Request $requisicao): JsonResponse
    {
        $solicitante = $requisicao->user();
        $gerenciaveis = $this->perfisGerenciaveis($solicitante);

        $dados = $requisicao->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:usuarios,email'],
            'senha' => ['required', 'string', 'min:6'],
            'perfil' => ['required', 'string', Rule::in($gerenciaveis)],
            // Matrícula é OBRIGATÓRIA para colaborador (é a chave de identificação
            // no app, que é por matrícula/nome completo). Opcional p/ gestor/admin.
            'matricula' => ['required_if:perfil,colaborador', 'nullable', 'string', 'max:50', 'unique:usuarios,matricula'],
            'cargo' => ['nullable', 'string', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:30'],
        ]);

        $usuario = Usuario::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha' => $dados['senha'],
            'perfil' => $dados['perfil'],
            'matricula' => $dados['matricula'] ?? null,
            'cargo' => $dados['cargo'] ?? null,
            'telefone' => $dados['telefone'] ?? null,
            'ativo' => true,
        ]);

        return response()->json($this->formatar($usuario), RespostaHttp::HTTP_CREATED);
    }

    public function atualizar(Request $requisicao, Usuario $usuario): JsonResponse
    {
        $solicitante = $requisicao->user();
        $this->garantirAlcada($solicitante, $usuario);

        $dados = $requisicao->validate([
            'nome' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('usuarios', 'email')->ignore($usuario->id)],
            'senha' => ['nullable', 'string', 'min:6'],
            'perfil' => ['sometimes', 'string', Rule::in($this->perfisGerenciaveis($solicitante))],
            'matricula' => ['required_if:perfil,colaborador', 'nullable', 'string', 'max:50', Rule::unique('usuarios', 'matricula')->ignore($usuario->id)],
            'cargo' => ['nullable', 'string', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'ativo' => ['sometimes', 'boolean'],
        ]);

        // Senha só é trocada se vier preenchida (o cast 'hashed' cuida do hash).
        if (empty($dados['senha'])) {
            unset($dados['senha']);
        }

        $usuario->update($dados);

        return response()->json($this->formatar($usuario->fresh()));
    }

    public function remover(Request $requisicao, Usuario $usuario): JsonResponse
    {
        $solicitante = $requisicao->user();
        $this->garantirAlcada($solicitante, $usuario);

        if ($solicitante->id === $usuario->id) {
            throw ValidationException::withMessages([
                'usuario' => ['Você não pode remover a si mesmo.'],
            ]);
        }

        // As missões do colaborador não são apagadas (colaborador_id fica nulo,
        // ver migration com nullOnDelete) — o histórico de risco é preservado.
        $usuario->delete();

        return response()->json(['mensagem' => 'Usuário removido.']);
    }

    /** Bloqueia gestor de mexer em gestor/superadmin. */
    private function garantirAlcada(Usuario $solicitante, Usuario $alvo): void
    {
        if (! in_array($alvo->perfil, $this->perfisGerenciaveis($solicitante), true)) {
            abort(RespostaHttp::HTTP_FORBIDDEN, 'Você não tem permissão sobre este usuário.');
        }
    }

    private function formatar(Usuario $usuario): array
    {
        return [
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'email' => $usuario->email,
            'perfil' => $usuario->perfil,
            'matricula' => $usuario->matricula,
            'cargo' => $usuario->cargo,
            'telefone' => $usuario->telefone,
            'ativo' => $usuario->ativo,
        ];
    }
}
