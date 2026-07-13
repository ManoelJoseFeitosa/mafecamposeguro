import { Indicadores, RespostaMissao } from "./tipos";

const BASE = "/api";
const CHAVE_SESSAO = "cpsi2026.sessao";

export interface UsuarioAutenticado {
  id: number;
  nome: string;
  email: string;
  perfil: "gestor" | "colaborador";
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
