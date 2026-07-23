<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Anuncio.php';
require_once __DIR__ . '/../models/Interesse.php';

iniciar_sessao();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(false, 'Método não permitido.', [], 405);

exigir_csrf($_POST['csrf_token'] ?? null);

$anuncioId = (int)($_POST['anuncio_id'] ?? 0);
$nome      = trim($_POST['nome'] ?? '');
$telefone  = apenas_digitos($_POST['telefone'] ?? '');
$mensagem  = trim($_POST['mensagem'] ?? '');

$erros = [];
if (mb_strlen($nome) < 3)               $erros[] = 'Informe seu nome completo.';
if (strlen($telefone) < 10 || strlen($telefone) > 11) $erros[] = 'Telefone inválido.';
if (mb_strlen($mensagem) < 5)           $erros[] = 'Escreva uma mensagem de interesse.';

if (!Anuncio::buscarPorId($anuncioId)) {
    $erros[] = 'Anúncio não encontrado.';
}

if ($erros) json_response(false, implode(' ', $erros), [], 422);

Interesse::criar([
    'anuncio_id' => $anuncioId,
    'nome' => $nome,
    'telefone' => $telefone,
    'mensagem' => $mensagem,
]);

json_response(true, 'Interesse registrado com sucesso! O anunciante entrará em contato em breve.');
