<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Usuario.php';

iniciar_sessao();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Método não permitido.', [], 405);
}

exigir_csrf($_POST['csrf_token'] ?? null);

$email = trim($_POST['email'] ?? '');
$senha = (string)($_POST['senha'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
    json_response(false, 'Informe e-mail e senha válidos.', [], 422);
}

$usuario = Usuario::buscarPorEmail($email);
$erroGenerico = 'E-mail ou senha inválidos.';

if (!$usuario) {
    json_response(false, $erroGenerico, [], 401);
}

if (Usuario::estaBloqueado($usuario)) {
    json_response(false, 'Conta temporariamente bloqueada por excesso de tentativas. Tente novamente em alguns minutos.', [], 429);
}

if (!password_verify($senha, $usuario['senha_hash'])) {
    Usuario::registrarTentativaFalha($usuario['id'], (int)$usuario['tentativas_login'] + 1);
    json_response(false, $erroGenerico, [], 401);
}

Usuario::resetarTentativas($usuario['id']);
login_usuario($usuario);

json_response(true, 'Login realizado com sucesso!', ['redirect' => '../area-restrita/principalRestrita.php']);
