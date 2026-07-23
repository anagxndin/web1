<?php
/**
 * Proteção contra CSRF: um token aleatório é gravado na sessão e deve
 * ser reenviado em todo POST (campo oculto "csrf_token" nos formulários).
 * hash_equals evita timing attacks na comparação.
 */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_valido(?string $token): bool
{
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/** Interrompe a requisição com 403 caso o token CSRF não confira. */
function exigir_csrf(?string $token): void
{
    if (!csrf_valido($token)) {
        json_response(false, 'Token de segurança inválido ou expirado. Recarregue a página e tente novamente.', [], 403);
    }
}
