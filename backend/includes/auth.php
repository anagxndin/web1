<?php
require_once __DIR__ . '/../config/config.php';

/**
 * Inicia a sessão com cookies protegidos contra roubo/fixação:
 * - HttpOnly: impede acesso via JavaScript (mitiga XSS lendo o cookie)
 * - SameSite=Lax: mitiga CSRF em requisições cross-site
 * - Secure (quando APP_HTTPS): cookie só trafega em HTTPS
 * - strict_mode + regeneração periódica: mitiga session fixation/hijacking
 */
function iniciar_sessao(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => APP_HTTPS,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    ini_set('session.use_strict_mode', '1');
    session_name('velocity_sid');
    session_start();

    if (empty($_SESSION['sessao_criada_em'])) {
        $_SESSION['sessao_criada_em'] = time();
    } elseif (time() - $_SESSION['sessao_criada_em'] > 900) {
        session_regenerate_id(true);
        $_SESSION['sessao_criada_em'] = time();
    }
}

function usuario_logado(): bool
{
    return !empty($_SESSION['usuario_id']);
}

function usuario_atual(): ?array
{
    if (!usuario_logado()) {
        return null;
    }

    return [
        'id' => $_SESSION['usuario_id'],
        'nome' => $_SESSION['usuario_nome'],
        'email' => $_SESSION['usuario_email'],
    ];
}

/** Marca a sessão como autenticada para o usuário informado. */
function login_usuario(array $usuario): void
{
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int)$usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['sessao_criada_em'] = time();
    unset($_SESSION['csrf_token']); // força um novo token após elevar privilégio
}

function logout_usuario(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

/**
 * Garante que existe um usuário autenticado; caso contrário redireciona
 * para a tela de login. Use nas páginas da área restrita.
 */
function exigir_login(string $login_url = '../public/login.php'): void
{
    if (!usuario_logado()) {
        header('Location: ' . $login_url);
        exit;
    }
}
