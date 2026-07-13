import { Indicadores } from "../tipos";
import Cartao from "./Cartao";

interface PropriedadesPainelIndicadores {
  indicadores: Indicadores;
}

const corDaClassificacao: Record<string, string> = {
  Baixo: "#2e7d32",
  Moderado: "#f9a825",
  Alto: "#ef6c00",
  Crítico: "#c62828",
};

function formatarData(iso: string) {
  return new Date(iso).toLocaleString("pt-BR", { dateStyle: "short", timeStyle: "short" });
}

export default function PainelIndicadores({ indicadores }: PropriedadesPainelIndicadores) {
  const classificacoes = Object.entries(indicadores.missoesPorClassificacaoRisco);

  return (
    <section className="painel-indicadores">
      <div className="grade-indicadores">
        <Cartao titulo="Missões planejadas" valor={String(indicadores.totalMissoes)} />
        <Cartao
          titulo="% Planejamento de risco concluído"
          valor={`${indicadores.percentualPlanejamentoRiscoConcluido}%`}
        />
        <Cartao titulo="RQAs registrados" valor={String(indicadores.totalRqaRegistrados)} />
        <Cartao
          titulo="% Missões com alerta climático"
          valor={`${indicadores.percentualMissoesComAlertaClimatico}%`}
        />
      </div>

      {classificacoes.length > 0 && (
        <div className="bloco-indicador">
          <h3>Missões por classificação de risco</h3>
          <ul className="lista-classificacao">
            {classificacoes.map(([classificacao, total]) => (
              <li key={classificacao}>
                <span
                  className="etiqueta-nivel etiqueta-nivel--pequena"
                  style={{ background: corDaClassificacao[classificacao] ?? "#616161" }}
                >
                  {classificacao}
                </span>
                <span>{total} missão(ões)</span>
              </li>
            ))}
          </ul>
        </div>
      )}

      <div className="bloco-indicador">
        <h3>Histórico de alertas (risco alto/crítico ou clima severo)</h3>
        {indicadores.historicoAlertas.length === 0 && (
          <p className="texto-vazio">Nenhum alerta registrado até o momento.</p>
        )}
        <ul className="lista-alertas">
          {indicadores.historicoAlertas.map((alerta) => (
            <li key={alerta.missaoId}>
              <strong>{alerta.projeto}</strong> — {alerta.atividade} em {alerta.ambiente}
              {alerta.classificacaoRisco && (
                <>
                  {" "}
                  (
                  <span
                    className="etiqueta-nivel etiqueta-nivel--pequena"
                    style={{ background: corDaClassificacao[alerta.classificacaoRisco] ?? "#616161" }}
                  >
                    {alerta.classificacaoRisco}
                  </span>
                  )
                </>
              )}
              {alerta.climaSevero && " ⚠️ clima severo"} — {formatarData(alerta.criadoEm)}
            </li>
          ))}
        </ul>
      </div>

      <div className="bloco-indicador">
        <h3>Relatórios de quase acidente (RQA) recentes</h3>
        {indicadores.rqasRecentes.length === 0 && (
          <p className="texto-vazio">Nenhum RQA registrado até o momento.</p>
        )}
        <ul className="lista-alertas">
          {indicadores.rqasRecentes.map((rqa) => (
            <li key={rqa.id}>
              <strong>{rqa.projetoMissao ?? `Missão #${rqa.missaoId}`}</strong> — {rqa.descricao} —{" "}
              {formatarData(rqa.criadoEm)}
            </li>
          ))}
        </ul>
      </div>
    </section>
  );
}
