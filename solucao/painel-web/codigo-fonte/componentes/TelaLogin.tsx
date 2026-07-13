import { useState } from "react";
import { login, UsuarioAutenticado } from "../cliente-api";

interface PropriedadesTelaLogin {
  aoAutenticar: (usuario: UsuarioAutenticado) => void;
}

export default function TelaLogin({ aoAutenticar }: PropriedadesTelaLogin) {
  const [email, setEmail] = useState("");
  const [senha, setSenha] = useState("");
  const [erro, setErro] = useState<string | null>(null);
  const [entrando, setEntrando] = useState(false);

  async function aoEnviar(evento: React.FormEvent) {
    evento.preventDefault();
    setErro(null);
    setEntrando(true);
    try {
      const usuario = await login(email, senha);
      aoAutenticar(usuario);
    } catch (err) {
      setErro(err instanceof Error ? err.message : "Erro ao entrar.");
    } finally {
      setEntrando(false);
    }
  }

  return (
    <div className="pagina pagina-login">
      <div className="cartao-login">
        <h1>CPSI 2026 — Painel do Gestor</h1>
        <p>Entre com sua conta de gestor para acessar os indicadores da missão.</p>
        <form onSubmit={aoEnviar} className="formulario-login">
          <label>
            E-mail
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              autoComplete="username"
            />
          </label>
          <label>
            Senha
            <input
              type="password"
              value={senha}
              onChange={(e) => setSenha(e.target.value)}
              required
              autoComplete="current-password"
            />
          </label>
          {erro && <p className="mensagem-erro">{erro}</p>}
          <button type="submit" disabled={entrando}>
            {entrando ? "Entrando..." : "Entrar"}
          </button>
        </form>
      </div>
    </div>
  );
}
