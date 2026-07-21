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

$email = trim($_POST['email'] ?? '');
$senha = (string)($_POST['senha'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
    json_response(false, 'Informe e-mail e senha válidos.', [], 422);
}

$pdo = db();
$stmt = $pdo->prepare(
    'SELECT id, nome, email, senha_hash, tentativas_login, bloqueado_ate FROM usuarios WHERE email = ?'
);
$stmt->execute([$email]);
$usuario = $stmt->fetch();

// Mensagem genérica em ambos os casos (usuário inexistente ou senha errada)
// para não revelar quais e-mails estão cadastrados.
$erroGenerico = 'E-mail ou senha inválidos.';

if (!$usuario) {
    json_response(false, $erroGenerico, [], 401);
}

if (!empty($usuario['bloqueado_ate']) && strtotime($usuario['bloqueado_ate']) > time()) {
    json_response(false, 'Conta temporariamente bloqueada por excesso de tentativas. Tente novamente em alguns minutos.', [], 429);
}

if (!password_verify($senha, $usuario['senha_hash'])) {
    $tentativas = (int)$usuario['tentativas_login'] + 1;
    $bloqueadoAte = null;

    if ($tentativas >= 5) {
        $bloqueadoAte = date('Y-m-d H:i:s', time() + 300); // bloqueia 5 minutos
        $tentativas = 0;
    }

    $stmt = $pdo->prepare('UPDATE usuarios SET tentativas_login = ?, bloqueado_ate = ? WHERE id = ?');
    $stmt->execute([$tentativas, $bloqueadoAte, $usuario['id']]);

    json_response(false, $erroGenerico, [], 401);
}

$stmt = $pdo->prepare('UPDATE usuarios SET tentativas_login = 0, bloqueado_ate = NULL WHERE id = ?');
$stmt->execute([$usuario['id']]);

login_usuario($usuario);

json_response(true, 'Login realizado com sucesso!', ['redirect' => '../area-restrita/principalRestrita.php']);
