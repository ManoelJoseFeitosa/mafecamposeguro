<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MAFE Campo Seguro — Segurança nas Saídas de Campo</title>
        <meta name="description" content="MAFE Campo Seguro — gerenciamento de risco ocupacional e definição automatizada de EPIs para saídas de campo.">

        <style>
            :root {
                --azul-noturno: #0b1220;
                --azul-painel: #101b30;
                --azul-borda: #22314d;
                --texto-claro: #e7ecf5;
                --texto-suave: #9aa7bd;
                --ambar: #f5a524;
                --ambar-forte: #ffb84d;
                --verde-ok: #34d399;
            }

            * { box-sizing: border-box; }

            html, body {
                margin: 0;
                padding: 0;
                min-height: 100vh;
                font-family: -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                background:
                    radial-gradient(circle at 15% -10%, rgba(245, 165, 36, 0.12), transparent 45%),
                    radial-gradient(circle at 110% 10%, rgba(52, 211, 153, 0.08), transparent 40%),
                    var(--azul-noturno);
                color: var(--texto-claro);
            }

            .faixa-perigo {
                height: 6px;
                width: 100%;
                background: repeating-linear-gradient(
                    135deg,
                    var(--ambar) 0 18px,
                    #0b1220 18px 36px
                );
            }

            .container {
                max-width: 1080px;
                margin: 0 auto;
                padding: 28px 20px 60px;
            }

            header.topo {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                flex-wrap: wrap;
                margin-bottom: 56px;
            }

            .marca {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .marca svg { flex-shrink: 0; }

            .marca-texto {
                display: flex;
                flex-direction: column;
                line-height: 1.15;
            }

            .marca-texto strong {
                font-size: 18px;
                letter-spacing: 0.02em;
            }

            .marca-texto span {
                font-size: 12px;
                color: var(--texto-suave);
            }

            .topo nav a {
                display: inline-block;
                margin-left: 10px;
                padding: 9px 18px;
                border-radius: 8px;
                text-decoration: none;
                font-size: 14px;
                font-weight: 600;
                transition: all .15s ease;
            }

            .botao-secundario {
                color: var(--texto-claro);
                border: 1px solid var(--azul-borda);
            }
            .botao-secundario:hover { border-color: var(--ambar); color: var(--ambar-forte); }

            .botao-primario {
                background: var(--ambar);
                color: #1a1204;
            }
            .botao-primario:hover { background: var(--ambar-forte); }

            .botao-desabilitado {
                opacity: 0.5;
                cursor: default;
                pointer-events: none;
            }

            .hero {
                display: grid;
                grid-template-columns: 1.1fr 0.9fr;
                gap: 48px;
                align-items: center;
                margin-bottom: 72px;
            }

            .selo {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                color: var(--ambar-forte);
                background: rgba(245, 165, 36, 0.1);
                border: 1px solid rgba(245, 165, 36, 0.35);
                padding: 6px 12px;
                border-radius: 999px;
                margin-bottom: 20px;
            }

            .hero h1 {
                font-size: clamp(30px, 4.4vw, 46px);
                line-height: 1.15;
                margin: 0 0 18px;
                letter-spacing: -0.01em;
            }

            .hero h1 em {
                font-style: normal;
                color: var(--ambar-forte);
            }

            .hero p.lead {
                font-size: 17px;
                color: var(--texto-suave);
                line-height: 1.6;
                margin: 0 0 28px;
                max-width: 52ch;
            }

            .hero-acoes { display: flex; gap: 14px; flex-wrap: wrap; }
            .hero-acoes a {
                padding: 13px 24px;
                border-radius: 10px;
                text-decoration: none;
                font-weight: 700;
                font-size: 15px;
            }

            .cartao-escudo {
                background: linear-gradient(160deg, var(--azul-painel), #0c1526);
                border: 1px solid var(--azul-borda);
                border-radius: 20px;
                padding: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                overflow: hidden;
            }
            .cartao-escudo::before {
                content: "";
                position: absolute;
                inset: -40%;
                background: radial-gradient(circle, rgba(245,165,36,0.14), transparent 60%);
            }
            .cartao-escudo svg { position: relative; width: 100%; max-width: 260px; }

            .grade-recursos {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin-bottom: 64px;
            }

            .cartao-recurso {
                background: var(--azul-painel);
                border: 1px solid var(--azul-borda);
                border-radius: 14px;
                padding: 22px;
            }
            .cartao-recurso .icone {
                width: 38px; height: 38px;
                border-radius: 10px;
                display: flex; align-items: center; justify-content: center;
                background: rgba(245, 165, 36, 0.12);
                margin-bottom: 14px;
            }
            .cartao-recurso h3 { font-size: 15px; margin: 0 0 8px; }
            .cartao-recurso p { font-size: 13.5px; color: var(--texto-suave); line-height: 1.5; margin: 0; }

            .baixar-app {
                display: grid;
                grid-template-columns: 1.4fr 0.6fr;
                gap: 40px;
                align-items: center;
                background: linear-gradient(160deg, var(--azul-painel), #0c1526);
                border: 1px solid var(--azul-borda);
                border-radius: 20px;
                padding: 40px;
                margin-bottom: 48px;
            }
            .baixar-app h2 { font-size: clamp(22px, 3vw, 30px); margin: 0 0 14px; }
            .baixar-app p { color: var(--texto-suave); font-size: 15px; line-height: 1.6; margin: 0 0 18px; max-width: 52ch; }
            .passos-instalar {
                margin: 0 0 22px;
                padding-left: 20px;
                color: var(--texto-suave);
                font-size: 14px;
                line-height: 1.7;
            }
            .passos-instalar strong { color: var(--texto-claro); }
            .passos-instalar code, .baixar-app code {
                background: rgba(245,165,36,0.12);
                color: var(--ambar-forte);
                padding: 1px 6px;
                border-radius: 5px;
                font-size: 12.5px;
            }
            .nota-app { font-size: 12.5px; margin-top: 14px !important; }
            .baixar-app__celular { display: flex; justify-content: center; }
            .baixar-app__celular svg { width: 100%; max-width: 150px; }

            footer {
                border-top: 1px solid var(--azul-borda);
                padding-top: 24px;
                font-size: 12.5px;
                color: var(--texto-suave);
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 10px;
            }

            @media (max-width: 820px) {
                .hero { grid-template-columns: 1fr; }
                .grade-recursos { grid-template-columns: 1fr; }
                .cartao-escudo { order: -1; padding: 26px; }
                .baixar-app { grid-template-columns: 1fr; padding: 28px; }
                .baixar-app__celular { order: -1; }
                .baixar-app__celular svg { max-width: 110px; }
            }
        </style>
    </head>
    <body>
        <div class="faixa-perigo"></div>

        <div class="container">
            <header class="topo">
                <div class="marca">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 2 L36 8 V19 C36 29 29.5 35.5 20 38 C10.5 35.5 4 29 4 19 V8 Z" fill="#101b30" stroke="#f5a524" stroke-width="1.6"/>
                        <path d="M20 8 L30 12 V19.5 C30 26.5 25.8 31 20 33 C14.2 31 10 26.5 10 19.5 V12 Z" fill="#f5a524" opacity="0.14"/>
                        <path d="M14 20 L18 24 L27 14" stroke="#f5a524" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div class="marca-texto">
                        <strong>MAFE Campo Seguro</strong>
                        <span></span>
                    </div>
                </div>
                <nav>
                    <a class="botao-secundario" href="{{ config('mafe.url_painel_gestor') }}">Painel do Gestor</a>
                    <a class="botao-primario" href="{{ config('mafe.url_app_android') }}">Baixar (Android)</a>
                    @if (config('mafe.url_app_ios'))
                        <a class="botao-secundario" href="{{ config('mafe.url_app_ios') }}">Baixar (iOS)</a>
                    @else
                        <span class="botao-secundario botao-desabilitado">iOS (em breve)</span>
                    @endif
                </nav>
            </header>

            <section class="hero">
                <div>
                    <span class="selo">Gerenciamento de risco ocupacional</span>
                    <h1>Segurança nas saídas de campo, <em>antes</em> de sair a campo.</h1>
                    <p class="lead">
                        O MAFE Campo Seguro analisa automaticamente o risco de cada missão —
                        atividade, ambiente, tempo de exposição e clima — recomenda os EPIs
                        corretos, localiza pontos de apoio de emergência próximos e gera o
                        relatório pronto para assinatura, tudo antes de a equipe deixar a base.
                    </p>
                    <div class="hero-acoes">
                        <a class="botao-primario" href="{{ config('mafe.url_app_android') }}">↓ Baixar para Android</a>
                        @if (config('mafe.url_app_ios'))
                            <a class="botao-primario" href="{{ config('mafe.url_app_ios') }}">↓ Baixar para iOS</a>
                        @else
                            <span class="botao-secundario botao-desabilitado">iOS (em breve)</span>
                        @endif
                    </div>
                </div>
                <div class="cartao-escudo">
                    <svg viewBox="0 0 200 220" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 8 L182 38 V102 C182 150 148 188 100 208 C52 188 18 150 18 102 V38 Z" fill="#0c1526" stroke="#f5a524" stroke-width="2.5"/>
                        <path d="M100 26 L164 50 V102 C164 140 140 168 100 184 C60 168 36 140 36 102 V50 Z" fill="#f5a524" opacity="0.10"/>
                        <path d="M70 108 L92 130 L134 78" stroke="#f5a524" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="100" cy="102" r="70" stroke="#f5a524" stroke-opacity="0.25" stroke-width="1"/>
                    </svg>
                </div>
            </section>

            <section class="grade-recursos">
                <div class="cartao-recurso">
                    <div class="icone">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 2 21 6v6c0 5.5-3.8 9.7-9 11-5.2-1.3-9-5.5-9-11V6l9-4Z" stroke="#f5a524" stroke-width="1.6"/><path d="M8.5 12.5l2.5 2.5 5-5.5" stroke="#f5a524" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <h3>Análise de risco automatizada</h3>
                    <p>Motor determinístico e auditável — não é caixa-preta de IA — cruza atividade, ambiente e condições para classificar o risco e recomendar EPIs.</p>
                </div>
                <div class="cartao-recurso">
                    <div class="icone">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-6.1 7-11.5A7 7 0 0 0 5 9.5C5 14.9 12 21 12 21Z" stroke="#f5a524" stroke-width="1.6"/><circle cx="12" cy="9.5" r="2.4" stroke="#f5a524" stroke-width="1.6"/></svg>
                    </div>
                    <h3>Pontos de apoio próximos</h3>
                    <p>Localiza hospitais, delegacias e UBS mais próximos de cada missão para resposta rápida em emergências de campo.</p>
                </div>
                <div class="cartao-recurso">
                    <div class="icone">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M4 20V10M12 20V4M20 20v-7" stroke="#f5a524" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </div>
                    <h3>Painel do gestor</h3>
                    <p>Indicadores em tempo real: % de planejamento concluído, histórico de alertas, RQAs e classificação de risco por missão.</p>
                </div>
                <div class="cartao-recurso">
                    <div class="icone">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="7" y="2" width="10" height="20" rx="2" stroke="#f5a524" stroke-width="1.6"/><path d="M11 18h2" stroke="#f5a524" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </div>
                    <h3>App de campo offline-first</h3>
                    <p>Registra a missão direto no aparelho, sem internet, com GPS automático, e sincroniza sozinho quando a conexão volta.</p>
                </div>
                <div class="cartao-recurso">
                    <div class="icone">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M14 3v5h5M6 3h8l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" stroke="#f5a524" stroke-width="1.6"/></svg>
                    </div>
                    <h3>Relatório pronto para assinar</h3>
                    <p>Exportação em PDF e DOCX, com análise de risco, EPIs e pontos de apoio — pronta para assinatura digital.</p>
                </div>
                <div class="cartao-recurso">
                    <div class="icone">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="3" y="11" width="18" height="10" rx="2" stroke="#f5a524" stroke-width="1.6"/><path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="#f5a524" stroke-width="1.6"/></svg>
                    </div>
                    <h3>Acesso seguro do gestor</h3>
                    <p>Painel do gestor protegido por login (token), enquanto o app de campo permanece livre de fricção para o colaborador.</p>
                </div>
            </section>

            <section class="baixar-app" id="baixar">
                <div class="baixar-app__texto">
                    <span class="selo">Aplicativo de campo · Android e iOS</span>
                    <h2>Leve o MAFE Campo Seguro para o campo</h2>
                    <p>
                        Instale o aplicativo no celular da equipe: registre missões com o GPS,
                        consulte EPIs e pontos de apoio e trabalhe <strong>mesmo sem internet</strong> —
                        a sincronização acontece sozinha quando a conexão volta.
                    </p>
                    <ol class="passos-instalar">
                        <li>Toque no botão da sua plataforma para baixar o aplicativo.</li>
                        <li>No Android, pode ser preciso <strong>permitir instalar de esta fonte</strong> — confirme.</li>
                        <li>Conclua a instalação e abra o app; encontre seu cadastro para começar.</li>
                    </ol>
                    <div class="hero-acoes">
                        <a class="botao-primario" href="{{ config('mafe.url_app_android') }}">↓ Baixar para Android</a>
                        @if (config('mafe.url_app_ios'))
                            <a class="botao-primario" href="{{ config('mafe.url_app_ios') }}">↓ Baixar para iOS</a>
                        @else
                            <span class="botao-secundario botao-desabilitado">iOS (em breve)</span>
                        @endif
                    </div>
                    @unless (config('mafe.url_app_ios'))
                        <p class="nota-app">A versão iOS estará disponível em breve. No iPhone, use enquanto isso o Painel do Gestor pelo navegador.</p>
                    @endunless
                </div>
                <div class="baixar-app__celular" aria-hidden="true">
                    <svg viewBox="0 0 150 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="12" y="6" width="126" height="288" rx="22" fill="#0c1526" stroke="#22314d" stroke-width="2"/>
                        <rect x="20" y="26" width="110" height="248" rx="10" fill="#101b30"/>
                        <circle cx="75" cy="16" r="2.4" fill="#22314d"/>
                        <path d="M55 150 L70 165 L98 122" stroke="#34d399" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="75" cy="143" r="42" stroke="#f5a524" stroke-opacity="0.35" stroke-width="2"/>
                        <rect x="34" y="210" width="82" height="9" rx="4.5" fill="#22314d"/>
                        <rect x="34" y="228" width="60" height="9" rx="4.5" fill="#22314d"/>
                    </svg>
                </div>
            </section>

            <footer>
                <span>MAFE Campo Seguro — Segurança nas Saídas de Campo </span>
                <span>MaFe Sistemas</span>
            </footer>
        </div>
    </body>
</html>
