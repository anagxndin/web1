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

$nome     = trim($_POST['nome'] ?? '');
$cpf      = apenas_digitos($_POST['cpf'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telefone = apenas_digitos($_POST['telefone'] ?? '');
$senha    = (string)($_POST['senha'] ?? '');

$erros = [];
if (mb_strlen($nome) < 3)                   $erros[] = 'Informe o nome completo.';
if (!validar_cpf($cpf))                     $erros[] = 'CPF inválido.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
if (strlen($telefone) < 10 || strlen($telefone) > 11) $erros[] = 'Telefone inválido.';
if (mb_strlen($senha) < 8)                  $erros[] = 'A senha deve ter ao menos 8 caracteres.';

if ($erros) {
    json_response(false, implode(' ', $erros), [], 422);
}

if (Usuario::existeEmailOuCpf($email, $cpf)) {
    json_response(false, 'Já existe uma conta cadastrada com este e-mail ou CPF.', [], 409);
}

Usuario::cadastrar([
    'nome' => $nome, 'cpf' => $cpf, 'email' => $email,
    'telefone' => $telefone, 'senha' => $senha,
]);

json_response(true, 'Cadastro realizado com sucesso! Faça login para continuar.', ['redirect' => 'login.php']);
