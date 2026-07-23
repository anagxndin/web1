<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Interesse.php';

iniciar_sessao();

if (!usuario_logado())     json_response(false, 'É necessário estar logado.', [], 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(false, 'Método não permitido.', [], 405);

exigir_csrf($_POST['csrf_token'] ?? null);

$interesseId = (int)($_POST['interesse_id'] ?? 0);

if (!Interesse::verificarDono($interesseId, $_SESSION['usuario_id'])) {
    json_response(false, 'Mensagem não encontrada.', [], 404);
}

Interesse::excluir($interesseId);

json_response(true, 'Mensagem excluída.');
