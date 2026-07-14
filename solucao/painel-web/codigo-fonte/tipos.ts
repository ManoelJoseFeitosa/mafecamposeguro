export interface ItemEpi {
  codigo: string;
  nome: string;
  uso: string;
  manutencao: string;
}

export interface ResultadoAnaliseRisco {
  atividade: string;
  ambiente: string;
  nivelRisco: number;
  classificacao: string;
  riscosIdentificados: string[];
  episRecomendados: ItemEpi[];
  medidasAdministrativas: string[];
}

export interface PontoDeApoio {
  nome: string;
  tipo: string;
  distanciaKm: number;
  direcaoGraus: number;
  direcaoCardinal: string;
  latitude: number;
  longitude: number;
  telefone: string;
}

export interface RespostaMissao {
  id: number;
  projeto: string;
  divisao: string;
  colaboradorId: number | null;
  colaboradorNome: string | null;
  analise: ResultadoAnaliseRisco;
  pontosDeApoio: PontoDeApoio[];
}

/** Item da listagem de missões (aba "Missões" — vínculo de colaborador). */
export interface MissaoResumo {
  id: number;
  projeto: string;
  divisao: string;
  latitude: number;
  longitude: number;
  colaboradorId: number | null;
  colaboradorNome: string | null;
  analise: ResultadoAnaliseRisco;
  atualizadoEm: string | null;
}

export type PerfilUsuario = "superadmin" | "gestor" | "colaborador";

/** Usuário completo, como o painel de gestão de usuários enxerga. */
export interface Usuario {
  id: number;
  nome: string;
  email: string;
  perfil: PerfilUsuario;
  matricula: string | null;
  cargo: string | null;
  telefone: string | null;
  ativo: boolean;
}

/** Colaborador enxuto, usado no seletor de atribuição de missão. */
export interface Colaborador {
  id: number;
  nome: string;
  matricula: string | null;
  cargo: string | null;
}

export interface AlertaHistorico {
  missaoId: number;
  projeto: string;
  atividade: string;
  ambiente: string;
  classificacaoRisco: string | null;
  climaSevero: boolean;
  criadoEm: string;
}

export interface RqaRecente {
  id: number;
  missaoId: number;
  projetoMissao: string | null;
  descricao: string;
  criadoEm: string;
}

export interface Indicadores {
  totalMissoes: number;
  percentualPlanejamentoRiscoConcluido: number;
  totalRqaRegistrados: number;
  missoesPorClassificacaoRisco: Record<string, number>;
  percentualMissoesComAlertaClimatico: number;
  historicoAlertas: AlertaHistorico[];
  rqasRecentes: RqaRecente[];
}
