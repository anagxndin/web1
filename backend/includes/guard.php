<?php
/**
 * Inclua este arquivo no topo (antes de qualquer HTML) de toda página
 * da área restrita. Ele inicia a sessão e redireciona visitantes não
 * autenticados para o login.
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/functions.php';

iniciar_sessao();
exigir_login('../public/login.php');
