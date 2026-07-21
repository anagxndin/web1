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

$anuncioId = (int)($_POST['anuncio_id'] ?? 0);
$pdo = db();

$stmt = $pdo->prepare('SELECT usuario_id FROM anuncios WHERE id = ?');
$stmt->execute([$anuncioId]);
$anuncio = $stmt->fetch();

// Verifica posse: só o dono do anúncio pode excluí-lo.
if (!$anuncio || (int)$anuncio['usuario_id'] !== (int)$_SESSION['usuario_id']) {
    json_response(false, 'Anúncio não encontrado.', [], 404);
}

$stmt = $pdo->prepare('SELECT caminho FROM anuncio_fotos WHERE anuncio_id = ?');
$stmt->execute([$anuncioId]);
foreach ($stmt->fetchAll() as $foto) {
    $arquivo = __DIR__ . '/../../' . $foto['caminho'];
    if (is_file($arquivo)) {
        unlink($arquivo);
    }
}

$stmt = $pdo->prepare('DELETE FROM anuncios WHERE id = ?');
$stmt->execute([$anuncioId]);

json_response(true, 'Anúncio excluído com sucesso.');
