<?php

/*
|--------------------------------------------------------------------------
| Configurações do produto MAFE Campo Seguro
|--------------------------------------------------------------------------
| Lidas em tempo de carga da config (sobrevivem ao `php artisan config:cache`,
| ao contrário de env() usado direto nas views). Ajuste os valores pelo .env
| em produção — ver documentacao/03_guia_deploy_hostinger.md.
*/

return [
    // Caminho/URL do instalador Android (.apk) hospedado para download.
    'url_app_android' => env('URL_APP_ANDROID', '/mafe-campo-seguro.apk'),

    // URL do painel web do gestor (site React). Em produção, o endereço real
    // do painel (ex.: https://mafecamposeguro.com.br/painel).
    'url_painel_gestor' => env('URL_PAINEL_GESTOR', '/painel'),
];
