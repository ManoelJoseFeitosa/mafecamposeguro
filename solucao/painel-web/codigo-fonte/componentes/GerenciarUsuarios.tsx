import { useEffect, useState } from "react";
import {
  atualizarUsuario,
  criarUsuario,
  DadosUsuario,
  listarUsuarios,
  removerUsuario,
} from "../cliente-api";
import { PerfilUsuario, Usuario } from "../tipos";

const ROTULO_PERFIL: Record<PerfilUsuario, string> = {
  superadmin: "Administrador geral",
  gestor: "Gestor",
  colaborador: "Colaborador de campo",
};

interface PropriedadesGerenciarUsuarios {
  perfilAtual: PerfilUsuario;
}

const FORMULARIO_VAZIO = {
  nome: "",
  email: "",
  senha: "",
  perfil: "colaborador" as PerfilUsuario,
  matricula: "",
  cargo: "",
  telefone: "",
};

export default function GerenciarUsuarios({ perfilAtual }: PropriedadesGerenciarUsuarios) {
  const [usuarios, setUsuarios] = useState<Usuario[]>([]);
  const [formulario, setFormulario] = useState(FORMULARIO_VAZIO);
  const [erro, setErro] = useState<string | null>(null);
  const [aviso, setAviso] = useState<string | null>(null);
  const [salvando, setSalvando] = useState(false);

  // Um gestor só pode cadastrar colaboradores; o super admin pode criar os três perfis.
  const perfisDisponiveis: PerfilUsuario[] =
    perfilAtual === "superadmin" ? ["colaborador", "gestor", "superadmin"] : ["colaborador"];

  async function recarregar() {
    try {
      setUsuarios(await listarUsuarios());
    } catch (e) {
      setErro(e instanceof Error ? e.message : "Erro ao carregar usuários.");
    }
  }

  useEffect(() => {
    recarregar();
  }, []);

  async function aoCriar(evento: React.FormEvent) {
    evento.preventDefault();
    setErro(null);
    setAviso(null);
    setSalvando(true);
    try {
      const dados: DadosUsuario = {
        nome: formulario.nome,
        email: formulario.email,
        senha: formulario.senha,
        perfil: formulario.perfil,
        matricula: formulario.matricula || null,
        cargo: formulario.cargo || null,
        telefone: formulario.telefone || null,
      };
      const criado = await criarUsuario(dados);
      setAviso(`Usuário "${criado.nome}" cadastrado.`);
      setFormulario(FORMULARIO_VAZIO);
      recarregar();
    } catch (e) {
      setErro(e instanceof Error ? e.message : "Erro ao cadastrar usuário.");
    } finally {
      setSalvando(false);
    }
  }

  async function alternarAtivo(usuario: Usuario) {
    setErro(null);
    try {
      await atualizarUsuario(usuario.id, { ativo: !usuario.ativo });
      recarregar();
    } catch (e) {
      setErro(e instanceof Error ? e.message : "Erro ao atualizar usuário.");
    }
  }

  async function remover(usuario: Usuario) {
    if (!window.confirm(`Remover o usuário "${usuario.nome}"? As missões dele ficam no histórico, sem responsável.`)) {
      return;
    }
    setErro(null);
    try {
      await removerUsuario(usuario.id);
      recarregar();
    } catch (e) {
      setErro(e instanceof Error ? e.message : "Erro ao remover usuário.");
    }
  }

  return (
    <section className="gestao-usuarios">
      <form onSubmit={aoCriar} className="formulario-usuario">
        <h2>Cadastrar usuário</h2>
        <p className="texto-auxiliar">
          {perfilAtual === "superadmin"
            ? "Como administrador geral, você cadastra colaboradores de campo, gestores e outros administradores."
            : "Cadastre os colaboradores de campo (usuários do aplicativo móvel)."}
        </p>

        <label>
          Nome completo
          <input
            value={formulario.nome}
            onChange={(e) => setFormulario({ ...formulario, nome: e.target.value })}
            required
          />
        </label>

        <label>
          Perfil
          <select
            value={formulario.perfil}
            onChange={(e) => setFormulario({ ...formulario, perfil: e.target.value as PerfilUsuario })}
          >
            {perfisDisponiveis.map((p) => (
              <option key={p} value={p}>
                {ROTULO_PERFIL[p]}
              </option>
            ))}
          </select>
        </label>

        <label>
          E-mail (login){formulario.perfil === "colaborador" ? " — só para cadastro, o app não faz login" : ""}
          <input
            type="email"
            value={formulario.email}
            onChange={(e) => setFormulario({ ...formulario, email: e.target.value })}
            required
          />
        </label>

        <label>
          Senha inicial
          <input
            type="password"
            value={formulario.senha}
            onChange={(e) => setFormulario({ ...formulario, senha: e.target.value })}
            minLength={6}
            required
          />
        </label>

        {formulario.perfil === "colaborador" && (
          <div className="linha-dupla">
            <label>
              Matrícula *
              <input
                value={formulario.matricula}
                onChange={(e) => setFormulario({ ...formulario, matricula: e.target.value })}
                placeholder="Ex.: SGB-0001"
                required
              />
            </label>
            <label>
              Cargo
              <input
                value={formulario.cargo}
                onChange={(e) => setFormulario({ ...formulario, cargo: e.target.value })}
                placeholder="Ex.: Geólogo(a)"
              />
            </label>
          </div>
        )}

        <label>
          Telefone (opcional)
          <input
            value={formulario.telefone}
            onChange={(e) => setFormulario({ ...formulario, telefone: e.target.value })}
            placeholder="(86) 90000-0000"
          />
        </label>

        <button type="submit" disabled={salvando}>
          {salvando ? "Cadastrando..." : "Cadastrar usuário"}
        </button>

        {erro && <p className="mensagem-erro">{erro}</p>}
        {aviso && <p className="mensagem-sucesso">{aviso}</p>}
      </form>

      <div className="lista-usuarios">
        <h2>Usuários cadastrados ({usuarios.length})</h2>
        <table className="tabela-usuarios">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Perfil</th>
              <th>Matrícula</th>
              <th>Situação</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            {usuarios.map((u) => (
              <tr key={u.id} className={u.ativo ? "" : "linha-inativa"}>
                <td>
                  <strong>{u.nome}</strong>
                  <br />
                  <span className="texto-auxiliar">{u.email}</span>
                </td>
                <td>{ROTULO_PERFIL[u.perfil]}</td>
                <td>{u.matricula ?? "—"}</td>
                <td>{u.ativo ? "Ativo" : "Inativo"}</td>
                <td className="celula-acoes">
                  <button type="button" className="botao-secundario" onClick={() => alternarAtivo(u)}>
                    {u.ativo ? "Desativar" : "Reativar"}
                  </button>
                  <button type="button" className="botao-perigo" onClick={() => remover(u)}>
                    Remover
                  </button>
                </td>
              </tr>
            ))}
            {usuarios.length === 0 && (
              <tr>
                <td colSpan={5} className="texto-vazio">
                  Nenhum usuário cadastrado ainda.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </section>
  );
}
