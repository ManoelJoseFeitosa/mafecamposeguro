# PROMPT MESTRE — Copie e cole isto no início de qualquer nova sessão

Estou retomando o trabalho no projeto CPSI 2026 (proposta para o Edital nº 0001 -
CPSI/2026 da CPRM/SGB, desafio "Segurança nas Saídas para Campo com Definição e
Monitoramento de EPIs"). O projeto vive na pasta `C:\Users\senai\CPSI2026`. A
stack é **PHP/Laravel (backend) + React responsivo (painel-web) + React
Native/Expo (app-movel, offline-first)** — não é mais Python/FastAPI nem
Python/React puro; migração para Laravel+React concluída em 2026-07-13, app
móvel adicionado no mesmo dia para cobrir o requisito offline-first do
edital (ver CLAUDE.md, seção 3).

Antes de fazer qualquer coisa, execute EXATAMENTE esta sequência:

1. **Leia `C:\Users\senai\CPSI2026\CLAUDE.md` por completo.** Esse arquivo é a fonte
   de verdade sobre o edital (prazos, valores, requisitos, regras jurídicas), sobre
   as decisões de arquitetura já tomadas e sobre a convenção de nomenclatura em
   português (seção 3.1 explica quais nomes de arquivo/pasta são exceção
   obrigatória por serem contratos de framework/ferramenta). Não repita pesquisa
   que já está documentada lá. Não contradiga o que está lá sem antes me perguntar.
2. **Verifique o estado atual real do projeto**, não confie apenas na seção 6 do
   CLAUDE.md (ela pode estar desatualizada — já aconteceu nesta sessão: o
   app móvel existia completo no disco, com testes passando, e não estava
   documentado na seção 6 nem no README.md; foi só encontrado listando a
   pasta de verdade):
   - **O projeto raiz não é um repositório git** — mas `solucao/app-movel/`
     **tem o seu próprio `.git`** (criado pelo `create-expo-app`). Rode `git -C
     "C:\Users\senai\CPSI2026\solucao\app-movel" log --oneline -20` e `git -C
     "C:\Users\senai\CPSI2026\solucao\app-movel" status` para ver o estado real
     desse subprojeto (pode ter mudanças não commitadas). Para o resto do
     projeto, sem git, liste os arquivos diretamente.
   - Liste o conteúdo de `documentacao/`, `solucao/servidor/`,
     `solucao/painel-web/` **e `solucao/app-movel/`** para ver o que já existe.
     Se encontrar pastas antigas `solucao/backend/` ou `solucao/frontend/`
     (Python/FastAPI), elas são resíduo da stack anterior — confirme comigo
     antes de apagar ou reaproveitar.
   - Se houver `solucao/servidor`, rode `php artisan test` antes de assumir que
     algo funciona. Se houver `solucao/painel-web`, rode `npm install` (se
     `node_modules` não existir) antes de tentar `npm run dev`. Se houver
     `solucao/app-movel`, rode `npx jest` (dentro da pasta) antes de assumir
     que a sincronização offline-first funciona — não assuma que "existe o
     arquivo" significa "está funcionando".
3. **Compare o estado real com a seção 6 (Estado Atual) do CLAUDE.md.** Se estiverem
   divergentes, atualize a seção 6 para refletir a realidade ANTES de continuar.
4. **Verifique se hoje é depois de 06/08/2026** (data-limite de inscrição no edital).
   Se for, avise-me imediatamente antes de continuar qualquer trabalho de submissão —
   pode ser tarde demais ou o cronograma pode ter sido retificado (nesse caso,
   verifique https://sgb.gov.br/cpsi/ antes de prosseguir).
5. **Não invente dados da empresa proponente** (CNPJ, razão social, equipe, valores
   de proposta, dados bancários). Se algo desses dados for necessário e ainda não
   estiver preenchido em nenhum lugar do projeto, pergunte-me diretamente — veja a
   seção 7 do CLAUDE.md ("Perguntas em aberto") para a lista do que falta.
6. Depois desses 5 passos, me pergunte objetivamente: **"O que você quer que eu faça
   agora?"** — ou, se eu já tiver dito o que quero nesta mensagem, prossiga com isso,
   mas ainda respeitando as regras acima (principalmente: não alucinar cláusulas do
   edital, não alucinar dados da empresa, e não desviar da convenção de nomenclatura
   em português sem justificar).

## Regras permanentes de trabalho neste projeto

- Qualquer afirmação sobre o edital (prazo, valor, regra de habilitação, critério de
  julgamento) deve ter lastro em `documentacao/fontes/` ou no CLAUDE.md. Se eu pedir algo que
  pareça contradizer o edital, aponte a contradição em vez de simplesmente obedecer.
- **Nomenclatura em português é obrigatória** para todo código/arquivo/pasta que EU
  crio no projeto, exceto os contratos de framework/ferramenta listados na seção
  3.1 do CLAUDE.md (ex.: `composer.json`, `package.json`, `app/` do Laravel,
  `config/*.php`). Se não tiver certeza se algo é um contrato obrigatório ou pode
  ser traduzido, verifique antes de decidir e prefira seguir o padrão já registrado
  no CLAUDE.md em vez de reabrir essa decisão.
- Ao terminar uma etapa de trabalho significativa (não cada pequeno passo), atualize
  a seção 6 do CLAUDE.md.
- Documentos formais para submissão (Anexos III e IV preenchidos, proposta de preços)
  só podem ser finalizados com dados reais fornecidos por mim — nunca com placeholders
  fictícios apresentados como se fossem reais.
- Mudanças na arquitetura da solução técnica (seção 3 do CLAUDE.md) devem ser
  discutidas comigo antes de implementadas, não decididas silenciosamente.
- **Ao terminar de usar servidores de desenvolvimento (`php artisan serve`, `npm run
  dev`, `npx expo start`), pare-os explicitamente antes de encerrar a sessão** (ou
  me avise quais ficaram rodando e em qual porta). Processos de sessões anteriores
  deixados presos já causaram confusão nesta migração (um servidor Python antigo
  ficou ocupando a porta 8000 e um teste bateu nele por engano, aparentando um bug
  que não existia).
