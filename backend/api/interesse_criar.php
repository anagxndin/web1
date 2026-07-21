<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

iniciar_sessao();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Método não permitido.', [], 405);
}

exigir_csrf($_POST['csrf_token'] ?? null);

$anuncioId = (int)($_POST['anuncio_id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$telefone = apenas_digitos($_POST['telefone'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

$erros = [];
if (mb_strlen($nome) < 3) $erros[] = 'Informe seu nome completo.';
if (strlen($telefone) < 10 || strlen($telefone) > 11) $erros[] = 'Telefone inválido.';
if (mb_strlen($mensagem) < 5) $erros[] = 'Escreva uma mensagem de interesse.';

$pdo = db();
$stmt = $pdo->prepare('SELECT id FROM anuncios WHERE id = ?');
$stmt->execute([$anuncioId]);
if (!$stmt->fetch()) {
    $erros[] = 'Anúncio não encontrado.';
}

if ($erros) {
    json_response(false, implode(' ', $erros), [], 422);
}

$stmt = $pdo->prepare(
    'INSERT INTO interesses (anuncio_id, nome, telefone, mensagem) VALUES (?, ?, ?, ?)'
);
$stmt->execute([$anuncioId, $nome, $telefone, $mensagem]);

json_response(true, 'Interesse registrado com sucesso! O anunciante entrará em contato em breve.');
