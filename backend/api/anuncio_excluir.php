<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Anuncio.php';
require_once __DIR__ . '/../models/AnuncioFoto.php';

iniciar_sessao();

if (!usuario_logado())     json_response(false, 'É necessário estar logado.', [], 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(false, 'Método não permitido.', [], 405);

exigir_csrf($_POST['csrf_token'] ?? null);

$anuncioId = (int)($_POST['anuncio_id'] ?? 0);

if (!Anuncio::verificarDono($anuncioId, $_SESSION['usuario_id'])) {
    json_response(false, 'Anúncio não encontrado.', [], 404);
}

// Exclui arquivos de foto do disco
$fotos = AnuncioFoto::listarPorAnuncio($anuncioId);
foreach ($fotos as $foto) {
    $arquivo = dirname(__DIR__, 2) . '/' . $foto['caminho'];
    if (is_file($arquivo)) unlink($arquivo);
}

// Exclui do banco (CASCADE remove fotos e interesses)
Anuncio::excluir($anuncioId);

json_response(true, 'Anúncio excluído com sucesso.');
