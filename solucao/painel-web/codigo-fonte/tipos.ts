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
  telefone: string;
}

export interface RespostaMissao {
  id: number;
  projeto: string;
  divisao: string;
  analise: ResultadoAnaliseRisco;
  pontosDeApoio: PontoDeApoio[];
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
