# Guia de Deploy — MAFE Campo Seguro (CPSI 2026)

> Passo a passo para colocar a solução no ar: **backend Laravel + painel web React**
> hospedados na **Hostinger**, e o **aplicativo Android (APK)** disponibilizado para
> download no mesmo domínio.
>
> ⚠️ **Sobre "hospedar o app na Hostinger":** um aplicativo React Native/Expo **não
> é um site** — ele vira um arquivo `.apk` que se instala no celular. A Hostinger
> hospeda o **backend + painel web** (que são web) e serve o **arquivo `.apk` para
> download**. O celular baixa e instala o app; depois ele conversa com o backend
> pela API. Não existe "rodar o app dentro do site" — o que fica no site é o
> instalador. (Este foi o caminho escolhido pelo proponente; a alternativa de
> publicar na Play Store fica registrada no fim deste guia.)

---

## Visão geral da arquitetura em produção

```
                 Internet
                    │
        ┌───────────┴────────────┐
        │      Hostinger          │
        │                         │
        │  seudominio.com         │  ← Painel web (React, arquivos estáticos)
        │  api.seudominio.com     │  ← Backend Laravel (PHP 8.2) + banco MySQL
        │  seudominio.com/app.apk │  ← Instalador do app Android para download
        └─────────────────────────┘
                    ▲
                    │ HTTPS (API REST /api/...)
                    │
     ┌──────────────┴───────────────┐
     │  Celular do colaborador       │
     │  App Android (offline-first)  │  ← funciona sem internet; sincroniza depois
     └───────────────────────────────┘
```

**Requisito de plano na Hostinger:** o backend é Laravel (PHP 8.2), então é preciso
um plano que ofereça **PHP 8.2, acesso SSH, Composer e banco MySQL** — na prática, os
planos **Business** / **Cloud** (hospedagem compartilhada com SSH) ou um **VPS**. O
plano mais básico sem SSH dificulta o `composer install` e os comandos `artisan`.

---

## Parte 1 — Backend Laravel (`solucao/servidor`)

### 1.1. Preparar o banco de dados (MySQL)

No hPanel da Hostinger → **Bancos de Dados → MySQL**:

1. Crie um banco (ex.: `u123_cpsi`), um usuário e uma senha. **Anote os três.**
2. O protótipo usa SQLite; em produção troque para MySQL **só pelo `.env`** — nenhum
   código de domínio muda (essa portabilidade foi uma decisão de arquitetura, ver
   CLAUDE.md §3).

### 1.2. Subir o código

Opção recomendada (com SSH):

```bash
# No seu computador, gere o pacote sem as dependências (elas são instaladas no servidor)
cd solucao/servidor
# Suba a pasta via Git ou SFTP para, por ex., /home/uXXXX/dominios/api.seudominio.com/

# No servidor (SSH), dentro da pasta do projeto:
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate
```

### 1.3. Configurar o `.env` de produção

Edite o `.env` no servidor:

```env
APP_NAME="MAFE Campo Seguro"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.seudominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u123_cpsi
DB_USERNAME=u123_cpsi_user
DB_PASSWORD=a-senha-que-voce-anotou

# Domínio do painel web, para o Sanctum/CORS aceitarem as chamadas dele
SANCTUM_STATEFUL_DOMAINS=seudominio.com
SESSION_DOMAIN=.seudominio.com
```

### 1.4. Migrar e semear

```bash
php artisan migrate --force
php artisan db:seed --force     # cria o super admin, o gestor e colaboradores demo
php artisan config:cache
php artisan route:cache
```

> ⚠️ **Segurança:** as senhas do seeder (`cpsi2026-admin`, `cpsi2026-demo`,
> `colaborador-demo`) são **só de demonstração**. Em produção, entre no painel como
> super admin e **troque a senha** (ou crie usuários reais e remova os demo) antes de
> divulgar o endereço.

### 1.5. Apontar o subdomínio para a pasta `public/`

O Laravel serve a partir de `public/`. No hPanel → **Domínios/Subdomínios**, crie
`api.seudominio.com` e defina a **raiz do documento** como a pasta `public/` do
projeto (ou use o `.htaccess` padrão do Laravel, que já acompanha a pasta `public/`).
Confirme que há **HTTPS/SSL** ativo (Hostinger oferece SSL grátis via hPanel).

**Teste:** acesse `https://api.seudominio.com/up` — deve responder o health-check do
Laravel. E `https://api.seudominio.com/api/colaboradores` deve devolver a lista (JSON).

---

## Parte 2 — Painel web React (`solucao/painel-web`)

O painel é um **site estático** (HTML/JS/CSS) depois de compilado — leve de hospedar.

### 2.1. Apontar o painel para a API de produção

Em produção o painel e a API ficam em domínios diferentes, então o proxy do Vite
(usado só em desenvolvimento) não vale. Ajuste o `cliente-api.ts` para usar a URL
absoluta da API, por variável de ambiente do Vite:

1. Crie `solucao/painel-web/.env.production`:
   ```env
   VITE_URL_API=https://api.seudominio.com/api
   ```
2. Em `codigo-fonte/cliente-api.ts`, troque a constante `BASE`:
   ```ts
   const BASE = import.meta.env.VITE_URL_API ?? "/api";
   ```
   (Em desenvolvimento continua caindo no `/api` do proxy; em produção usa a URL real.)

### 2.2. Compilar e subir

```bash
cd solucao/painel-web
npm install
npm run build          # gera a pasta dist/
```

Suba **o conteúdo da pasta `dist/`** para a raiz do domínio principal
(`seudominio.com`) via SFTP ou Gerenciador de Arquivos do hPanel.

**Teste:** acesse `https://seudominio.com` — deve abrir a tela de login. Entre como
super admin e confirme que o painel carrega os indicadores (isso prova que ele está
falando com `api.seudominio.com`).

### 2.3. Liberar CORS no backend (se necessário)

Se o navegador bloquear as chamadas por CORS, edite `config/cors.php` no Laravel para
permitir `https://seudominio.com` em `allowed_origins`, e rode
`php artisan config:cache` de novo.

---

## Parte 3 — Aplicativo Android (`solucao/app-movel`)

### 3.1. Apontar o app para a API de produção

Antes de gerar o APK, edite `solucao/app-movel/codigo-fonte/configuracao.ts`:

```ts
export const URL_BASE_API = 'https://api.seudominio.com/api';
```

(É por aqui que o app sabe onde sincronizar. Sem isso ele tenta o `127.0.0.1` de
desenvolvimento e nunca conecta no celular do usuário.)

### 3.2. Gerar o APK com o EAS Build (recomendado)

O jeito mais simples de gerar um `.apk` instalável, sem instalar o Android Studio:

```bash
cd solucao/app-movel
npm install -g eas-cli
eas login                       # exige conta gratuita Expo
eas build:configure
# Perfil que gera APK (e não .aab de loja):
eas build -p android --profile preview
```

No `eas.json`, garanta um perfil que produza APK:

```json
{
  "build": {
    "preview": {
      "android": { "buildType": "apk" }
    }
  }
}
```

Ao terminar, o EAS fornece um **link de download do `.apk`**. Baixe esse arquivo.

> Alternativa sem EAS (build local): exige Android Studio/SDK — `npx expo prebuild`
> seguido de `cd android && ./gradlew assembleRelease`, gerando o `.apk` em
> `android/app/build/outputs/apk/release/`. Mais trabalhoso; use o EAS se puder.

### 3.3. Disponibilizar o APK para download na Hostinger

1. Renomeie o arquivo para algo claro, ex.: `mafe-campo-seguro.apk`.
2. Suba-o para a raiz do site (`seudominio.com`) via Gerenciador de Arquivos.
3. Divulgue o link: `https://seudominio.com/mafe-campo-seguro.apk`.

### 3.4. Instalar no celular

O usuário abre esse link no navegador do Android, baixa o `.apk` e instala. Como não
vem da Play Store, o Android pede para **permitir instalação de "fontes
desconhecidas"** naquele navegador (uma vez). Depois é só abrir o app.

> Em iPhone (iOS) **não** dá para instalar `.apk` — se precisar de iOS, o caminho é
> publicar na App Store (ver abaixo). O escopo escolhido agora é Android por download.

---

## Parte 4 — Ordem de subida e checklist final

Faça nesta ordem (cada etapa depende da anterior estar no ar):

- [ ] 1. Banco MySQL criado e credenciais anotadas.
- [ ] 2. Backend Laravel no ar em `api.seudominio.com` (`/up` responde, SSL ativo).
- [ ] 3. `migrate --force` + `db:seed --force` rodados; **senhas demo trocadas**.
- [ ] 4. Painel web compilado apontando para a API e subido em `seudominio.com`.
- [ ] 5. Login do super admin funcionando no painel em produção.
- [ ] 6. Colaboradores de campo reais cadastrados pelo super admin/gestor.
- [ ] 7. `configuracao.ts` do app apontando para `api.seudominio.com`.
- [ ] 8. APK gerado (EAS) e subido para `seudominio.com/mafe-campo-seguro.apk`.
- [ ] 9. Instalado num celular de teste; seleção de colaborador lista os cadastrados.
- [ ] 10. Missão criada no painel → aparece no app após sincronizar (e vice-versa).

---

## Anexo — Publicar nas lojas (opção futura, fora da Hostinger)

Se no futuro quiser distribuição profissional (atualização automática, iOS incluso):

- **Google Play:** `eas build -p android --profile production` (gera `.aab`), conta de
  desenvolvedor Google (US$ 25, pagamento único).
- **App Store (iOS):** `eas build -p ios`, conta Apple Developer (US$ 99/ano) e um Mac
  ou o serviço de build do EAS.

Isso **não usa a Hostinger** para o app — a Hostinger continua hospedando só o backend
e o painel web. Decisão registrada para retomar quando/se fizer sentido.
