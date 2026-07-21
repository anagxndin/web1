<?php
require_once __DIR__ . '/Database.php';

class Usuario
{
    /** Cadastra um novo usuário. Retorna o ID inserido. */
    public static function cadastrar(array $dados): int
    {
        $pdo = Database::conectar();
        $hash = password_hash($dados['senha'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO usuarios (nome, cpf, email, telefone, senha_hash) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $dados['nome'],
            $dados['cpf'],
            $dados['email'],
            $dados['telefone'],
            $hash,
        ]);
        return (int)$pdo->lastInsertId();
    }

    /** Busca usuário por e-mail (para login). */
    public static function buscarPorEmail(string $email): ?array
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare(
            'SELECT id, nome, email, senha_hash, tentativas_login, bloqueado_ate FROM usuarios WHERE email = ?'
        );
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    /** Busca usuário por ID. */
    public static function buscarPorId(int $id): ?array
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare('SELECT id, nome, email FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    /** Verifica se e-mail ou CPF já existem. */
    public static function existeEmailOuCpf(string $email, string $cpf): bool
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? OR cpf = ?');
        $stmt->execute([$email, $cpf]);
        return (bool)$stmt->fetch();
    }

    /** Atualiza contagem de tentativas de login. Se exceder 5, bloqueia. */
    public static function registrarTentativaFalha(int $usuarioId, int $tentativas): void
    {
        $pdo = Database::conectar();
        $bloqueadoAte = null;
        if ($tentativas >= 5) {
            $bloqueadoAte = date('Y-m-d H:i:s', time() + 300);
            $tentativas = 0;
        }
        $stmt = $pdo->prepare('UPDATE usuarios SET tentativas_login = ?, bloqueado_ate = ? WHERE id = ?');
        $stmt->execute([$tentativas, $bloqueadoAte, $usuarioId]);
    }

    /** Reseta tentativas de login após sucesso. */
    public static function resetarTentativas(int $usuarioId): void
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare('UPDATE usuarios SET tentativas_login = 0, bloqueado_ate = NULL WHERE id = ?');
        $stmt->execute([$usuarioId]);
    }

    /** Verifica se a conta está bloqueada. */
    public static function estaBloqueado(?array $usuario): bool
    {
        return $usuario && !empty($usuario['bloqueado_ate'])
            && strtotime($usuario['bloqueado_ate']) > time();
    }
}
