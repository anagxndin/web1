<?php
/**
 * Configuração do banco de dados e do app.
 *
 * Em produção (InfinityFree, Awardspace, byet.host etc.), defina estes valores
 * com as credenciais fornecidas pelo painel de hospedagem. É possível usar
 * variáveis de ambiente (quando o host suportar) ou simplesmente editar os
 * valores padrão abaixo antes do upload.
 */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'velocity_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Defina como '1' (via variável de ambiente APP_HTTPS) quando o site estiver
// servido em HTTPS, para que o cookie de sessão exija conexão segura.
define('APP_HTTPS', filter_var(getenv('APP_HTTPS') ?: '0', FILTER_VALIDATE_BOOLEAN));

// Timezone usada para os timestamps exibidos (datas de anúncios, mensagens etc.)
date_default_timezone_set('America/Sao_Paulo');
