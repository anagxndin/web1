<?php
/** Envia uma resposta JSON padronizada e encerra o script. */
function json_response(bool $sucesso, string $mensagem, array $dados = [], int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge(['sucesso' => $sucesso, 'mensagem' => $mensagem], $dados), JSON_UNESCAPED_UNICODE);
    exit;
}

/** Valida CPF (formato + dígitos verificadores). Espera apenas dígitos. */
function validar_cpf(string $cpf): bool
{
    $cpf = preg_replace('/\D/', '', $cpf);

    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += (int)$cpf[$i] * (($t + 1) - $i);
        }
        $digito = ((10 * $soma) % 11) % 10;
        if ((int)$cpf[$t] !== $digito) {
            return false;
        }
    }

    return true;
}

/** Remove tudo que não for dígito (telefone, cpf, etc.). */
function apenas_digitos(string $valor): string
{
    return preg_replace('/\D/', '', $valor);
}

/** Escapa texto para saída segura em HTML (mitiga XSS refletido/armazenado). */
function h(?string $valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
