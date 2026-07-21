<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

iniciar_sessao();

if (!usuario_logado()) {
    json_response(false, 'É necessário estar logado.', [], 401);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Método não permitido.', [], 405);
}

exigir_csrf($_POST['csrf_token'] ?? null);

$interesseId = (int)($_POST['interesse_id'] ?? 0);
$pdo = db();

// Só o dono do anúncio relacionado pode excluir a mensagem de interesse.
$stmt = $pdo->prepare(
    'SELECT i.id, a.usuario_id FROM interesses i
     JOIN anuncios a ON a.id = i.anuncio_id
     WHERE i.id = ?'
);
$stmt->execute([$interesseId]);
$linha = $stmt->fetch();

if (!$linha || (int)$linha['usuario_id'] !== (int)$_SESSION['usuario_id']) {
    json_response(false, 'Mensagem não encontrada.', [], 404);
}

$stmt = $pdo->prepare('DELETE FROM interesses WHERE id = ?');
$stmt->execute([$interesseId]);

json_response(true, 'Mensagem excluída.');
