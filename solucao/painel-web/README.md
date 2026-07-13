# Painel Web — Frontend responsivo do protótipo CPSI 2026

Painel React + TypeScript, responsivo (funciona em desktop e no navegador do
celular), que consome a API Laravel (`solucao/servidor`).

## Convenção de nomenclatura

Diferente do Laravel, aqui não há contratos de framework que exijam nomes em
inglês para o nosso próprio código — por isso a pasta de código-fonte é
`codigo-fonte/` (não `src/`) e os componentes/arquivos têm nomes em português
(`Aplicativo.tsx`, `principal.tsx`, `cliente-api.ts`, `tipos.ts`,
`estilos.css`). As exceções são arquivos que são contratos de ferramenta:
`package.json` (contrato do npm), `tsconfig.json` (convenção do TypeScript),
`vite.config.ts` (Vite procura esse nome por padrão) e `index.html` (entrada
padrão do Vite).

## Rodar localmente

```bash
cd solucao/painel-web
npm install
npm run dev   # http://localhost:5173 (ou a porta que o Vite escolher)
```

Espera o backend Laravel rodando (por padrão em `http://127.0.0.1:8000` — ver
`vite.config.ts`, seção `server.proxy`).

## Responsividade

O layout usa CSS Grid com breakpoint em `720px` (ver `codigo-fonte/estilos.css`):
acima disso, formulário e resultado ficam lado a lado; abaixo, empilham em uma
coluna. Inputs usam `font-size: 16px` para evitar zoom automático no iOS.
Testado em viewport de 375×812 (celular) sem rolagem horizontal.

## Estrutura

```
codigo-fonte/
├── principal.tsx          <- ponto de entrada (equivalente a main.tsx)
├── Aplicativo.tsx          <- componente raiz
├── cliente-api.ts          <- chamadas à API Laravel
├── tipos.ts                <- tipos TypeScript (contrato com a API)
├── estilos.css              <- CSS responsivo
└── componentes/
    └── Cartao.tsx           <- card de indicador reutilizável
```
