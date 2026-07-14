import { Colaborador, Indicadores, MissaoResumo, PerfilUsuario, RespostaMissao, Usuario } from "./tipos";

const BASE = "/api";
const CHAVE_SESSAO = "cpsi2026.sessao";

export interface UsuarioAutenticado {
  id: number;
  nome: string;
  email: string;
  perfil: PerfilUsuario;
}

interface SessaoArmazenada {
  token: string;
  usuario: UsuarioAutenticado;
}

function obterSessao(): SessaoArmazenada | null {
  const bruto = localStorage.getItem(CHAVE_SESSAO);
  if (!bruto) return null;
  try {
    return JSON.parse(bruto) as SessaoArmazenada;
  } catch {
    return null;
  }
}

function definirSessao(sessao: SessaoArmazenada): void {
  localStorage.setItem(CHAVE_SESSAO, JSON.stringify(sessao));
}

function limparSessao(): void {
  localStorage.removeItem(CHAVE_SESSAO);
}

export function usuarioAtual(): UsuarioAutenticado | null {
  return obterSessao()?.usuario ?? null;
}

function cabecalhosAutenticados(): HeadersInit {
  const sessao = obterSessao();
  return sessao ? { Authorization: `Bearer ${sessao.token}` } : {};
}

export async function login(email: string, senha: string): Promise<UsuarioAutenticado> {
  const res = await fetch(`${BASE}/login`, {
    method: "POST",
    headers: { "Content-Type": "application/json", Accept: "application/json" },
    body: JSON.stringify({ email, senha }),
  });
  if (!res.ok) throw new Error("E-mail ou senha inválidos.");
  const dados: SessaoArmazenada = await res.json();
  definirSessao(dados);
  return dados.usuario;
}

export async function logout(): Promise<void> {
  try {
    await fetch(`${BASE}/logout`, {
      method: "POST",
      headers: { ...cabecalhosAutenticados(), Accept: "application/json" },
    });
  } finally {
    limparSessao();
  }
}

export async function listarCatalogo(tipo: "divisoes" | "atividades" | "ambientes"): Promise<string[]> {
  const res = await fetch(`${BASE}/catalogo/${tipo}`);
  return res.json();
}

export interface NovaMissaoPayload {
  divisao: string;
  projeto: string;
  atividade: string;
  ambiente: string;
  latitude: number;
  longitude: number;
  tempoExposicaoHoras: number;
  climaSevero: boolean;
  historicoAcidentesLocal: number;
  colaboradorId: number | null;
}

export async function criarMissao(payload: NovaMissaoPayload): Promise<RespostaMissao> {
  const res = await fetch(`${BASE}/missoes`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  if (!res.ok) throw new Error(`Falha ao criar missão: ${res.status}`);
  return res.json();
}

export async function buscarIndicadores(): Promise<Indicadores> {
  const res = await fetch(`${BASE}/indicadores`, {
    headers: { ...cabecalhosAutenticados(), Accept: "application/json" },
  });
  if (res.status === 401) {
    limparSessao();
    throw new Error("Sessão expirada. Faça login novamente.");
  }
  if (!res.ok) throw new Error(`Falha ao buscar indicadores: ${res.status}`);
  return res.json();
}

export async function registrarRqa(missaoId: number, descricao: string): Promise<void> {
  await fetch(`${BASE}/rqa`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ missaoId, descricao }),
  });
}

export function urlRelatorioPdf(missaoId: number): string {
  return `${BASE}/missoes/${missaoId}/relatorio.pdf`;
}

export function urlRelatorioDocx(missaoId: number): string {
  return `${BASE}/missoes/${missaoId}/relatorio.docx`;
}

// ---------------------------------------------------------------------------
// Gestão de usuários (super admin / gestor) e seletor de colaboradores.
// ---------------------------------------------------------------------------

/** Extrai a primeira mensagem de erro de validação do Laravel, se houver. */
async function extrairMensagemErro(res: Response, padrao: string): Promise<string> {
  try {
    const corpo = await res.json();
    if (corpo?.errors) {
      const primeiro = Object.values(corpo.errors)[0];
      if (Array.isArray(primeiro) && primeiro[0]) return String(primeiro[0]);
    }
    if (corpo?.mensagem) return String(corpo.mensagem);
  } catch {
    // corpo não-JSON: cai no padrão
  }
  return padrao;
}

export interface DadosUsuario {
  nome: string;
  email: string;
  senha?: string;
  perfil: PerfilUsuario;
  matricula?: string | null;
  cargo?: string | null;
  telefone?: string | null;
  ativo?: boolean;
}

export async function listarUsuarios(): Promise<Usuario[]> {
  const res = await fetch(`${BASE}/usuarios`, {
    headers: { ...cabecalhosAutenticados(), Accept: "application/json" },
  });
  if (res.status === 401) {
    limparSessao();
    throw new Error("Sessão expirada. Faça login novamente.");
  }
  if (!res.ok) throw new Error(await extrairMensagemErro(res, "Falha ao listar usuários."));
  return res.json();
}

export async function criarUsuario(dados: DadosUsuario): Promise<Usuario> {
  const res = await fetch(`${BASE}/usuarios`, {
    method: "POST",
    headers: { ...cabecalhosAutenticados(), "Content-Type": "application/json", Accept: "application/json" },
    body: JSON.stringify(dados),
  });
  if (!res.ok) throw new Error(await extrairMensagemErro(res, "Falha ao criar usuário."));
  return res.json();
}

export async function atualizarUsuario(id: number, dados: Partial<DadosUsuario>): Promise<Usuario> {
  const res = await fetch(`${BASE}/usuarios/${id}`, {
    method: "PUT",
    headers: { ...cabecalhosAutenticados(), "Content-Type": "application/json", Accept: "application/json" },
    body: JSON.stringify(dados),
  });
  if (!res.ok) throw new Error(await extrairMensagemErro(res, "Falha ao atualizar usuário."));
  return res.json();
}

export async function removerUsuario(id: number): Promise<void> {
  const res = await fetch(`${BASE}/usuarios/${id}`, {
    method: "DELETE",
    headers: { ...cabecalhosAutenticados(), Accept: "application/json" },
  });
  if (!res.ok) throw new Error(await extrairMensagemErro(res, "Falha ao remover usuário."));
}

/**
 * Lista de colaboradores para os SELETORES do painel (atribuir responsável na
 * missão, vincular na aba Missões). Usa a rota AUTENTICADA de usuários — a
 * rota pública /api/colaboradores é só para o app de campo identificar A SI
 * MESMO por matrícula exata (nunca lista todos, ver ColaboradorControlador).
 */
export async function listarColaboradores(): Promise<Colaborador[]> {
  const usuarios = await listarUsuarios();
  return usuarios
    .filter((u) => u.perfil === "colaborador" && u.ativo)
    .map((u) => ({ id: u.id, nome: u.nome, matricula: u.matricula, cargo: u.cargo }));
}

// ---------------------------------------------------------------------------
// Missões — listagem e vínculo de colaborador (aba "Missões").
// ---------------------------------------------------------------------------

export async function listarMissoes(): Promise<MissaoResumo[]> {
  const res = await fetch(`${BASE}/missoes`, { headers: { Accept: "application/json" } });
  if (!res.ok) throw new Error("Falha ao listar missões.");
  return res.json();
}

export async function vincularColaboradorMissao(
  missaoId: number,
  colaboradorId: number | null,
): Promise<MissaoResumo> {
  const res = await fetch(`${BASE}/missoes/${missaoId}/colaborador`, {
    method: "PUT",
    headers: { ...cabecalhosAutenticados(), "Content-Type": "application/json", Accept: "application/json" },
    body: JSON.stringify({ colaboradorId }),
  });
  if (!res.ok) throw new Error(await extrairMensagemErro(res, "Falha ao vincular colaborador à missão."));
  return res.json();
}
