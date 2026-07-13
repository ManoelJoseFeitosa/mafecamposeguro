<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Gestão de Risco de Campo</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #1a1a1a; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 13px; color: #0d47a1; margin-top: 18px; margin-bottom: 6px; }
        .rotulo-nivel { color: #fff; padding: 2px 8px; border-radius: 8px; background: #c62828; }
        ul { margin: 4px 0; padding-left: 18px; }
        .assinatura { margin-top: 40px; }
    </style>
</head>
<body>
    <h1>Relatório de Gestão de Risco de Campo</h1>
    <p><strong>Projeto/Missão:</strong> {{ $projeto }}</p>
    <p><strong>Atividade:</strong> {{ $analise['atividade'] }} &nbsp;|&nbsp; <strong>Ambiente:</strong> {{ $analise['ambiente'] }}</p>
    <p><strong>Nível de risco:</strong> <span class="rotulo-nivel">{{ $analise['nivelRisco'] }}/10 — {{ $analise['classificacao'] }}</span></p>

    <h2>Riscos identificados</h2>
    <ul>
        @foreach ($analise['riscosIdentificados'] as $risco)
            <li>{{ $risco }}</li>
        @endforeach
    </ul>

    <h2>EPIs recomendados</h2>
    <ul>
        @foreach ($analise['episRecomendados'] as $epi)
            <li><strong>{{ $epi['nome'] }}</strong> — {{ $epi['uso'] }}</li>
        @endforeach
    </ul>

    <h2>Medidas administrativas</h2>
    <ul>
        @foreach ($analise['medidasAdministrativas'] as $medida)
            <li>{{ $medida }}</li>
        @endforeach
    </ul>

    <p class="assinatura">Assinatura do responsável técnico: ____________________________</p>
</body>
</html>
