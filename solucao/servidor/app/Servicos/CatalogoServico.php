<?php

namespace App\Servicos;

/**
 * Catálogo de referência: divisões do SGB, atividades, ambientes e EPIs.
 *
 * Fonte dos dados de domínio: Documento de Descrição do Desafio (SEI nº 2727608),
 * itens 8 e 13.2.2. Este serviço é o ponto único de verdade dos dados de negócio
 * do protótipo — não duplicar estas listas em outras classes.
 */
class CatalogoServico
{
    public const DIVISOES = [
        'DIGEOB' => 'Divisão de Geologia Básica',
        'DISEGE' => 'Divisão de Sensoriamento Remoto e Geofísica',
        'DIBASE' => 'Divisão de Bacias Sedimentares',
        'DIGEOD' => 'Divisão de Geodinâmica',
        'DIGEOM' => 'Divisão de Geologia Marinha',
        'DIGECO' => 'Divisão de Geologia Econômica',
        'DIEMGE' => 'Divisão de Economia Mineral e Geologia Exploratória',
        'DIPEME' => 'Divisão de Projetos Especiais e Minerais Estratégicos',
        'DIMINI' => 'Divisão de Rochas e Minerais Industriais',
        'DIGEOQ' => 'Divisão de Geoquímica',
        'DEGET' => 'Divisão de Gestão Territorial',
        'DIGEAP' => 'Divisão de Geologia Aplicada',
        'DIHIBA' => 'Divisão de Hidrologia Básica',
        'DIHAPI' => 'Divisão de Hidrologia Aplicada',
        'DIHEXP' => 'Divisão de Hidrogeologia e Exploração',
        'LAMIN' => 'Laboratório de Análises Minerais',
    ];

    public const ATIVIDADES = [
        'caminhamento_geologico',
        'coleta_amostras_solo',
        'sondagem',
        'hidrometria_rio',
        'mergulho_geologia_marinha',
        'sensoriamento_aereo_drone',
        'trabalho_laboratorio_campo',
        'operacao_veiculo_4x4',
    ];

    public const AMBIENTES = [
        'mata_fechada',
        'area_urbana',
        'area_rural',
        'montanha_alto_risco',
        'rio_lago',
        'litoral',
        'area_conflito',
    ];

    /** Informações de uso/manutenção de cada EPI do catálogo. */
    public const INFORMACOES_EPI = [
        'capacete' => [
            'nome' => 'Capacete de segurança classe B',
            'uso' => 'Obrigatório em áreas com risco de queda de objetos ou impacto na cabeça.',
            'manutencao' => 'Inspecionar rachaduras a cada uso; substituir a cada 5 anos ou após impacto.',
        ],
        'bota_seguranca' => [
            'nome' => 'Bota de segurança com biqueira composite',
            'uso' => 'Obrigatória em qualquer atividade de campo com risco de perfuração ou impacto no pé.',
            'manutencao' => 'Verificar sola e biqueira antes de cada saída; higienizar após uso em áreas úmidas.',
        ],
        'colete_flutuacao' => [
            'nome' => 'Colete de flutuação (salva-vidas)',
            'uso' => 'Obrigatório em atividades próximas a rios, lagos ou mar com profundidade > 1m.',
            'manutencao' => 'Verificar integridade das câmaras de flutuação e fivelas antes do uso.',
        ],
        'protetor_solar_uv' => [
            'nome' => 'Protetor solar FPS 50+ e vestimenta com proteção UV',
            'uso' => 'Recomendado em exposição solar prolongada (> 2h) em área aberta.',
            'manutencao' => 'Reaplicar a cada 2h de exposição; verificar validade do produto.',
        ],
        'repelente' => [
            'nome' => 'Repelente de insetos (Icaridina ou DEET)',
            'uso' => 'Obrigatório em mata fechada e áreas com risco de doenças transmitidas por vetores.',
            'manutencao' => 'Reaplicar conforme bula; não aplicar sob roupas.',
        ],
        'luva_seguranca' => [
            'nome' => 'Luva de segurança (nitrílica ou de raspa, conforme atividade)',
            'uso' => 'Obrigatória em manuseio de amostras, sondagem e coleta de solo.',
            'manutencao' => 'Descartar se rasgada ou perfurada; higienizar entre coletas.',
        ],
        'oculos_protecao' => [
            'nome' => 'Óculos de proteção contra impacto e UV',
            'uso' => 'Obrigatório em sondagem, uso de ferramentas e exposição solar intensa.',
            'manutencao' => 'Limpar com pano macio; substituir se riscado, comprometendo a visão.',
        ],
        'protetor_auricular' => [
            'nome' => 'Protetor auricular (plug ou concha)',
            'uso' => 'Obrigatório próximo a equipamentos de sondagem ou motores.',
            'manutencao' => 'Plugs descartáveis: uso único; conchas: higienizar após uso.',
        ],
        'corda_epi_altura' => [
            'nome' => 'Sistema de proteção contra quedas (cinto tipo paraquedista + talabarte)',
            'uso' => 'Obrigatório em atividades em encostas, escarpas ou desníveis > 2m.',
            'manutencao' => 'Inspeção antes de cada uso; recolher de uso se houver queda registrada.',
        ],
        'mascara_pff2' => [
            'nome' => 'Máscara PFF2 ou equivalente',
            'uso' => 'Recomendado em ambientes com poeira mineral (sondagem, laboratório de campo).',
            'manutencao' => 'Descartável; trocar quando comprometer a respiração ou molhar.',
        ],
        'gps_comunicador_satelital' => [
            'nome' => 'Comunicador satelital com GPS (ex.: rastreador de emergência)',
            'uso' => 'Obrigatório em áreas sem cobertura de celular (mata fechada, montanha, litoral remoto).',
            'manutencao' => 'Testar sinal e bateria antes da saída.',
        ],
    ];

    /**
     * Matriz determinística atividade x ambiente -> riscos, EPIs, nível de criticidade
     * base. Cada regra é auditável e versionável (requisito de defensabilidade
     * perante o SGB) — ver App\Servicos\MotorDeRiscoServico.
     */
    public const MATRIZ_RISCO = [
        [
            'atividade' => 'caminhamento_geologico',
            'ambiente' => 'mata_fechada',
            'riscos' => [
                'Picada de animais peçonhentos',
                'Desorientação/perda em área de mata densa',
                'Quedas em terreno irregular',
                'Doenças transmitidas por vetores (malária, dengue)',
            ],
            'epis' => ['bota_seguranca', 'luva_seguranca', 'repelente', 'gps_comunicador_satelital', 'capacete'],
            'nivel_base' => 3,
        ],
        [
            'atividade' => 'sondagem',
            'ambiente' => 'area_rural',
            'riscos' => [
                'Ruído excessivo de equipamentos',
                'Poeira mineral em suspensão',
                'Prensamento de membros em partes móveis',
                'Projeção de fragmentos',
            ],
            'epis' => ['capacete', 'protetor_auricular', 'mascara_pff2', 'luva_seguranca', 'oculos_protecao', 'bota_seguranca'],
            'nivel_base' => 4,
        ],
        [
            'atividade' => 'hidrometria_rio',
            'ambiente' => 'rio_lago',
            'riscos' => [
                'Afogamento',
                'Correnteza forte / variação de nível do rio',
                'Fauna aquática de risco',
            ],
            'epis' => ['colete_flutuacao', 'bota_seguranca', 'gps_comunicador_satelital'],
            'nivel_base' => 4,
        ],
        [
            'atividade' => 'mergulho_geologia_marinha',
            'ambiente' => 'litoral',
            'riscos' => [
                'Doença descompressiva',
                'Corrente marítima e ressaca',
                'Fauna marinha perigosa',
                'Hipotermia',
            ],
            'epis' => ['colete_flutuacao', 'gps_comunicador_satelital'],
            'nivel_base' => 5,
        ],
        [
            'atividade' => 'coleta_amostras_solo',
            'ambiente' => 'area_rural',
            'riscos' => [
                'Exposição solar prolongada',
                'Contato com substâncias químicas do solo',
                'Ferimentos por ferramentas manuais',
            ],
            'epis' => ['luva_seguranca', 'protetor_solar_uv', 'bota_seguranca', 'oculos_protecao'],
            'nivel_base' => 2,
        ],
        [
            'atividade' => 'sensoriamento_aereo_drone',
            'ambiente' => 'area_urbana',
            'riscos' => [
                'Colisão com estruturas/pessoas',
                'Interferência em espaço aéreo controlado',
            ],
            'epis' => ['oculos_protecao'],
            'nivel_base' => 1,
        ],
        [
            'atividade' => 'trabalho_laboratorio_campo',
            'ambiente' => 'area_rural',
            'riscos' => [
                'Exposição a reagentes químicos',
                'Poeira mineral',
            ],
            'epis' => ['luva_seguranca', 'mascara_pff2', 'oculos_protecao'],
            'nivel_base' => 2,
        ],
        [
            'atividade' => 'operacao_veiculo_4x4',
            'ambiente' => 'montanha_alto_risco',
            'riscos' => [
                'Capotamento em terreno íngreme',
                'Isolamento em caso de pane',
                'Deslizamento de encosta',
            ],
            'epis' => ['capacete', 'corda_epi_altura', 'gps_comunicador_satelital', 'bota_seguranca'],
            'nivel_base' => 4,
        ],
    ];

    /**
     * Pontos de apoio de REFERÊNCIA (delegacias, hospitais, UBS, bombeiros).
     *
     * Cada ponto é posicionado por um deslocamento (delta) de latitude/longitude
     * relativo à coordenada da missão — assim a distância exibida é REALMENTE
     * calculada por geolocalização (Haversine) a partir de onde a equipe está,
     * não um número fixo. Ver App\Servicos\PontosDeApoioServico.
     *
     * Os telefones são os números nacionais reais de emergência (192 SAMU, 190
     * PM, 193 Bombeiros). Os NOMES são genéricos de propósito: no protótipo não
     * inventamos a identidade de um hospital específico — em produção este
     * dataset é substituído pela consulta por coordenada às bases públicas
     * (CNES/DATASUS para saúde, SSP estaduais para segurança).
     *
     * @var array<int, array{nome: string, tipo: string, delta_lat: float, delta_lng: float, telefone: string}>
     */
    public const PONTOS_DE_APOIO_MOCK = [
        ['nome' => 'UBS de referência mais próxima', 'tipo' => 'ubs', 'delta_lat' => 0.035, 'delta_lng' => 0.020, 'telefone' => '136'],
        ['nome' => 'Delegacia de Polícia de referência', 'tipo' => 'delegacia', 'delta_lat' => -0.045, 'delta_lng' => 0.055, 'telefone' => '190'],
        ['nome' => 'Hospital de referência com pronto-socorro', 'tipo' => 'hospital', 'delta_lat' => 0.070, 'delta_lng' => -0.060, 'telefone' => '192'],
        ['nome' => 'Corpo de Bombeiros de referência', 'tipo' => 'bombeiros', 'delta_lat' => -0.090, 'delta_lng' => -0.085, 'telefone' => '193'],
    ];
}
