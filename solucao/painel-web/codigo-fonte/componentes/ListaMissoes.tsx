import { useEffect, useState } from "react";
import { listarMissoes, vincularColaboradorMissao } from "../cliente-api";
import { Colaborador, MissaoResumo } from "../tipos";

const corDoNivel = (nivel: number) => {
  if (nivel <= 2) return "#2e7d32";
  if (nivel <= 4) return "#f9a825";
  if (nivel <= 7) return "#ef6c00";
  return "#c62828";
};

interface PropriedadesListaMissoes {
  colaboradores: Colaborador[];
}

/**
 * Aba "Missões": lista todas as missões (independente de terem sido criadas
 * pelo painel ou pelo app de campo) e permite ao gestor VINCULAR ou TROCAR o
 * colaborador responsável a qualquer momento — não só na criação.
 */
export default function ListaMissoes({ colaboradores }: PropriedadesListaMissoes) {
  const [missoes, setMissoes] = useState<MissaoResumo[]>([]);
  const [carregando, setCarregando] = useState(true);
  const [erro, setErro] = useState<string | null>(null);
  const [salvandoId, setSalvandoId] = useState<number | null>(null);

  async function recarregar() {
    setCarregando(true);
    try {
      setMissoes(await listarMissoes());
      setErro(null);
    } catch (e) {
      setErro(e instanceof Error ? e.message : "Erro ao carregar missões.");
    } finally {
      setCarregando(false);
    }
  }

  useEffect(() => {
    recarregar();
  }, []);

  async function aoVincular(missaoId: number, valor: string) {
    const colaboradorId = valor ? Number(valor) : null;
    setSalvandoId(missaoId);
    setErro(null);
    try {
      const atualizada = await vincularColaboradorMissao(missaoId, colaboradorId);
      setMissoes((lista) =>
        lista.map((m) =>
          m.id === missaoId ? { ...m, colaboradorId: atualizada.colaboradorId, colaboradorNome: atualizada.colaboradorNome } : m,
        ),
      );
    } catch (e) {
      setErro(e instanceof Error ? e.message : "Erro ao vincular colaborador.");
    } finally {
      setSalvandoId(null);
    }
  }

  return (
    <section className="lista-missoes">
      <h2>Missões registradas ({missoes.length})</h2>
      <p className="texto-auxiliar">
        Vincule ou troque o colaborador responsável por qualquer missão — inclusive as criadas
        diretamente pelo aplicativo de campo, sem atribuição inicial.
      </p>

      {erro && <p className="mensagem-erro">{erro}</p>}
      {carregando && <p className="texto-vazio">Carregando missões...</p>}

      {!carregando && (
        <table className="tabela-usuarios">
          <thead>
            <tr>
              <th>Projeto</th>
              <th>Divisão</th>
              <th>Risco</th>
              <th>Colaborador vinculado</th>
            </tr>
          </thead>
          <tbody>
            {missoes.map((m) => (
              <tr key={m.id}>
                <td>
                  <strong>{m.projeto}</strong>
                  <br />
                  <span className="texto-auxiliar">{m.analise.atividade} · {m.analise.ambiente}</span>
                </td>
                <td>{m.divisao}</td>
                <td>
                  <span className="etiqueta-nivel etiqueta-nivel--pequena" style={{ background: corDoNivel(m.analise.nivelRisco) }}>
                    {m.analise.nivelRisco}/10
                  </span>
                </td>
                <td>
                  <select
                    value={m.colaboradorId ?? ""}
                    onChange={(e) => aoVincular(m.id, e.target.value)}
                    disabled={salvandoId === m.id}
                  >
                    <option value="">Sem atribuição</option>
                    {colaboradores.map((c) => (
                      <option key={c.id} value={c.id}>
                        {c.nome}
                        {c.matricula ? ` — ${c.matricula}` : ""}
                      </option>
                    ))}
                  </select>
                </td>
              </tr>
            ))}
            {missoes.length === 0 && (
              <tr>
                <td colSpan={4} className="texto-vazio">
                  Nenhuma missão registrada ainda.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      )}
    </section>
  );
}
