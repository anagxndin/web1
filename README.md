# veloCity

Portal de anúncios de veículos, desenvolvido para a disciplina de Desenvolvimento Web.
Integrantes: ver [integrantes.txt](integrantes.txt).

## Estrutura do projeto

```
src/               front-end (HTML, CSS, JS) — páginas públicas e área restrita
backend/
  config/          credenciais do banco (config.php)
  includes/        sessão, CSRF, conexão PDO, funções auxiliares, guard de autenticação
  api/              endpoints PHP (cadastro, login, logout, anúncios, interesses)
  sql/schema.sql    script de criação do banco de dados
  uploads/anuncios/ fotos enviadas nos anúncios
```

Páginas da área restrita (`src/pages/area-restrita/*.php`) exigem login — quem
tentar acessá-las sem sessão ativa é redirecionado para `login.php`.

## Rodando localmente

É necessário PHP 7.4+ com extensões `pdo_mysql` e `fileinfo`, além de MySQL/MariaDB.
A forma mais simples no Windows é instalar o **XAMPP** ou o **Laragon**.

1. Copie (ou clone) a pasta do projeto para `htdocs` (XAMPP) ou `www` (Laragon).
2. Crie o banco importando `backend/sql/schema.sql` (via phpMyAdmin ou
   `mysql -u root -p < backend/sql/schema.sql`).
3. Confira as credenciais em `backend/config/config.php` — os valores padrão
   (`root` sem senha, banco `velocity_db`) já funcionam com XAMPP/Laragon
   recém-instalados.
4. Acesse `http://localhost/<pasta-do-projeto>/src/pages/public/index.html`.

## Publicando em uma hospedagem gratuita (InfinityFree, Awardspace, byet.host...)

Essas hospedagens oferecem PHP + MySQL gratuitamente, o que é suficiente para
este projeto (não há dependências externas via Composer/Node no back-end).

1. Crie a conta e o banco de dados MySQL pelo painel da hospedagem (elas geram
   host, nome do banco, usuário e senha — normalmente algo como
   `sqlXXX.infinityfree.com`, `if0_XXXXXXX_velocity`, etc.).
2. Importe `backend/sql/schema.sql` pelo phpMyAdmin do painel.
3. Edite `backend/config/config.php` com os dados gerados no passo 1
   (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`). Se o site for servido em
   HTTPS (recomendado), defina `APP_HTTPS` como `'1'`.
4. Envie todo o projeto (via FTP ou o gerenciador de arquivos do painel) para
   a pasta pública do site (`htdocs`, `public_html` etc.), mantendo a mesma
   estrutura de pastas.
5. Acesse `https://seusite.exemplo/src/pages/public/index.html`.

> O GitHub Pages (usado na primeira entrega) **não roda PHP**, por isso a
> segunda entrega precisa de uma hospedagem com suporte a PHP/MySQL como as
> sugeridas acima.

## Segurança implementada

- Senhas com `password_hash`/`password_verify` (bcrypt), nunca em texto puro.
- Todas as consultas usam *prepared statements* (PDO), evitando injeção de SQL.
- Sessão de login com cookie `HttpOnly`, `SameSite=Lax` e, opcionalmente,
  `Secure`; regeneração periódica do ID de sessão contra fixação/hijacking.
- Proteção CSRF: todo formulário que altera dados envia um token de sessão
  validado no servidor.
- Bloqueio temporário de login após 5 tentativas inválidas seguidas.
- Mensagens de erro de login genéricas (não informam se o e-mail existe).
- Uploads de fotos validados pelo tipo MIME real do arquivo (não pela
  extensão), com nome aleatório e pasta sem permissão de execução de PHP.
- Toda saída de dados do usuário no HTML passa por `htmlspecialchars` (mitiga
  XSS refletido/armazenado); no front-end, o card de anúncios é montado via
  DOM em vez de `innerHTML` pelo mesmo motivo.
- `backend/config`, `backend/includes` e `backend/sql` bloqueados por
  `.htaccess` para não serem acessados diretamente pelo navegador.
