import { useEffect, useState } from "react";
import {
  buscarIndicadores,
  criarMissao,
  listarCatalogo,
  listarColaboradores,
  logout,
  UsuarioAutenticado,
  usuarioAtual,
  urlRelatorioDocx,
  urlRelatorioPdf,
} from "./cliente-api";
import GerenciarUsuarios from "./componentes/GerenciarUsuarios";
import ListaMissoes from "./componentes/ListaMissoes";
import PainelIndicadores from "./componentes/PainelIndicadores";
import TelaLogin from "./componentes/TelaLogin";
import { Colaborador, Indicadores, RespostaMissao } from "./tipos";

type Aba = "nova-missao" | "missoes" | "usuarios";

const corDoNivel = (nivel: number) => {
  if (nivel <= 2) return "#2e7d32";
  if (nivel <= 4) return "#f9a825";
  if (nivel <= 7) return "#ef6c00";
  return "#c62828";
};

export default function Aplicativo() {
  const [usuario, setUsuario] = useState<UsuarioAutenticado | null>(() => usuarioAtual());
  const [aba, setAba] = useState<Aba>("nova-missao");
  const [divisoes, setDivisoes] = useState<string[]>([]);
  const [atividades, setAtividades] = useState<string[]>([]);
  const [ambientes, setAmbientes] = useState<string[]>([]);
  const [colaboradores, setColaboradores] = useState<Colaborador[]>([]);
  const [indicadores, setIndicadores] = useState<Indicadores | null>(null);
  const [resultado, setResultado] = useState<RespostaMissao | null>(null);
  const [erro, setErro] = useState<string | null>(null);
  const [carregando, setCarregando] = useState(false);

  const [formulario, setFormulario] = useState({
    divisao: "",
    projeto: "",
    atividade: "",
    ambiente: "",
    latitude: -15.79,
    longitude: -47.88,
    tempoExposicaoHoras: 4,
    climaSevero: false,
    historicoAcidentesLocal: 0,
    colaboradorId: null as number | null,
  });

  useEffect(() => {
    listarCatalogo("divisoes").then((lista) => {
      setDivisoes(lista);
      setFormulario((f) => ({ ...f, divisao: lista[0] ?? "" }));
    });
    listarCatalogo("atividades").then((lista) => {
      setAtividades(lista);
      setFormulario((f) => ({ ...f, atividade: lista[0] ?? "" }));
    });
    listarCatalogo("ambientes").then((lista) => {
      setAmbientes(lista);
      setFormulario((f) => ({ ...f, ambiente: lista[0] ?? "" }));
    });
  }, []);

  useEffect(() => {
    if (usuario) {
      recarregarIndicadores();
      listarColaboradores().then(setColaboradores).catch(() => setColaboradores([]));
    }
  }, [usuario]);

  function recarregarIndicadores() {
    buscarIndicadores()
      .then(setIndicadores)
      .catch(() => setUsuario(usuarioAtual()));
  }

  async function aoSair() {
    await logout();
    // Sair leva de volta ao site (não à tela de login do painel) — a tela de
    // login só deve aparecer quando o gestor clica em "Painel do Gestor" no
    // site, não como destino automático após encerrar a sessão.
    window.location.href = "/";
  }

  async function aoEnviar(evento: React.FormEvent) {
    evento.preventDefault();
    setErro(null);
    setCarregando(true);
    try {
      const resposta = await criarMissao(formulario);
      setResultado(resposta);
      recarregarIndicadores();
    } catch (err) {
      setErro(err instanceof Error ? err.message : "Erro desconhecido");
    } finally {
      setCarregando(false);
    }
  }

  if (!usuario) {
    return <TelaLogin aoAutenticar={setUsuario} />;
  }

  return (
    <div className="pagina">
      <header className="cabecalho">
        <div className="cabecalho__linha-topo">
          <div>
            <h1>MAFE Campo Seguro</h1>
            <p>Painel de gestão de risco ocupacional e definição de EPIs para saídas de campo.</p>
          </div>
          <div className="cabecalho__usuario">
            <span>{usuario.nome}</span>
            <button type="button" className="botao-sair" onClick={aoSair}>
              Sair
            </button>
          </div>
        </div>
        <nav className="abas">
          <button
            type="button"
            className={aba === "nova-missao" ? "aba aba--ativa" : "aba"}
            onClick={() => setAba("nova-missao")}
          >
            Nova missão
          </button>
          <button
            type="button"
            className={aba === "missoes" ? "aba aba--ativa" : "aba"}
            onClick={() => setAba("missoes")}
          >
            Missões
          </button>
          <button
            type="button"
            className={aba === "usuarios" ? "aba aba--ativa" : "aba"}
            onClick={() => setAba("usuarios")}
          >
            Usuários
          </button>
        </nav>
      </header>

      {aba === "usuarios" ? (
        <main className="conteudo-principal conteudo-principal--unico">
          <GerenciarUsuarios perfilAtual={usuario.perfil} />
        </main>
      ) : aba === "missoes" ? (
        <main className="conteudo-principal conteudo-principal--unico">
          <ListaMissoes colaboradores={colaboradores} />
        </main>
      ) : (
        <>
          {indicadores && <PainelIndicadores indicadores={indicadores} />}

          <main className="conteudo-principal">
        <form onSubmit={aoEnviar} className="formulario-missao">
          <h2>Nova missão de campo</h2>

          <label>
            Divisão
            <select
              value={formulario.divisao}
              onChange={(e) => setFormulario({ ...formulario, divisao: e.target.value })}
            >
              {divisoes.map((d) => (
                <option key={d} value={d}>
                  {d}
                </option>
              ))}
            </select>
          </label>

          <label>
            Projeto / missão
            <input
              value={formulario.projeto}
              onChange={(e) => setFormulario({ ...formulario, projeto: e.target.value })}
              placeholder="Ex.: Mapeamento geológico - Serra do Espinhaço"
              required
            />
          </label>

          <label>
            Colaborador responsável (campo)
            <select
              value={formulario.colaboradorId ?? ""}
              onChange={(e) =>
                setFormulario({
                  ...formulario,
                  colaboradorId: e.target.value ? Number(e.target.value) : null,
                })
              }
            >
              <option value="">Sem atribuição (definir depois)</option>
              {colaboradores.map((c) => (
                <option key={c.id} value={c.id}>
                  {c.nome}
                  {c.matricula ? ` — ${c.matricula}` : ""}
                  {c.cargo ? ` (${c.cargo})` : ""}
                </option>
              ))}
            </select>
          </label>

          <label>
            Atividade
            <select
              value={formulario.atividade}
              onChange={(e) => setFormulario({ ...formulario, atividade: e.target.value })}
            >
              {atividades.map((a) => (
                <option key={a} value={a}>
                  {a}
                </option>
              ))}
            </select>
          </label>

          <label>
            Ambiente
            <select
              value={formulario.ambiente}
              onChange={(e) => setFormulario({ ...formulario, ambiente: e.target.value })}
            >
              {ambientes.map((a) => (
                <option key={a} value={a}>
                  {a}
                </option>
              ))}
            </select>
          </label>

          <label>
            Tempo de exposição estimado (horas)
            <input
              type="number"
              min={1}
              max={24}
              value={formulario.tempoExposicaoHoras}
              onChange={(e) => setFormulario({ ...formulario, tempoExposicaoHoras: Number(e.target.value) })}
            />
          </label>

          <label className="rotulo-checkbox">
            <input
              type="checkbox"
              checked={formulario.climaSevero}
              onChange={(e) => setFormulario({ ...formulario, climaSevero: e.target.checked })}
            />
            Alerta meteorológico severo previsto (INMET/Defesa Civil)
          </label>

          <button type="submit" disabled={carregando}>
            {carregando ? "Analisando..." : "Gerar plano de risco"}
          </button>

          {erro && <p className="mensagem-erro">{erro}</p>}
        </form>

        <div className="painel-resultado">
          <h2>Resultado da análise</h2>
          {!resultado && <p className="texto-vazio">Preencha o formulário para gerar o plano de risco.</p>}
          {resultado && (
            <div className="cartao-resultado">
              <p>
                <strong>Projeto:</strong> {resultado.projeto}
                {resultado.colaboradorNome && (
                  <>
                    {" — "}
                    <strong>Responsável:</strong> {resultado.colaboradorNome}
                  </>
                )}
              </p>
              <p>
                <strong>Nível de risco:</strong>{" "}
                <span
                  className="etiqueta-nivel"
                  style={{ background: corDoNivel(resultado.analise.nivelRisco) }}
                >
                  {resultado.analise.nivelRisco}/10 — {resultado.analise.classificacao}
                </span>
              </p>

              <h3>Riscos identificados</h3>
              <ul>
                {resultado.analise.riscosIdentificados.map((r) => (
                  <li key={r}>{r}</li>
                ))}
              </ul>

              <h3>EPIs recomendados</h3>
              <ul>
                {resultado.analise.episRecomendados.map((epi) => (
                  <li key={epi.codigo}>
                    <strong>{epi.nome}</strong> — {epi.uso}
                  </li>
                ))}
              </ul>

              <h3>Medidas administrativas</h3>
              <ul>
                {resultado.analise.medidasAdministrativas.map((m) => (
                  <li key={m}>{m}</li>
                ))}
              </ul>

              <h3>Pontos de apoio próximos</h3>
              <ul>
                {resultado.pontosDeApoio.map((p) => (
                  <li key={p.nome}>
                    {p.nome} ({p.tipo}) — {p.distanciaKm} km ao {p.direcaoCardinal} ({p.direcaoGraus}°) — tel: {p.telefone}
                    {" — "}
                    <a
                      href={`https://www.google.com/maps/dir/?api=1&destination=${p.latitude},${p.longitude}&travelmode=driving`}
                      target="_blank"
                      rel="noreferrer"
                    >
                      ver no mapa
                    </a>
                  </li>
                ))}
              </ul>

              <div className="acoes-relatorio">
                <a href={urlRelatorioPdf(resultado.id)} target="_blank" rel="noreferrer">
                  Baixar PDF
                </a>
                <a href={urlRelatorioDocx(resultado.id)} target="_blank" rel="noreferrer">
                  Baixar DOCX
                </a>
              </div>
            </div>
          )}
        </div>
          </main>
        </>
      )}
    </div>
  );
}
