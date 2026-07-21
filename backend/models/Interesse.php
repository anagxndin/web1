<?php
require_once __DIR__ . '/Database.php';

class Interesse
{
    /** Registra um novo interesse. */
    public static function criar(array $dados): void
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare(
            'INSERT INTO interesses (anuncio_id, nome, telefone, mensagem) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $dados['anuncio_id'],
            $dados['nome'],
            $dados['telefone'],
            $dados['mensagem'],
        ]);
    }

    /** Lista interesses de um anúncio. */
    public static function listarPorAnuncio(int $anuncioId): array
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare(
            'SELECT id, nome, telefone, mensagem, criado_em FROM interesses WHERE anuncio_id = ? ORDER BY criado_em DESC'
        );
        $stmt->execute([$anuncioId]);
        return $stmt->fetchAll();
    }

    /** Exclui um interesse. */
    public static function excluir(int $id): bool
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare('DELETE FROM interesses WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /** Verifica se o interesse pertence a um anúncio do usuário. */
    public static function verificarDono(int $interesseId, int $usuarioId): bool
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare(
            'SELECT i.id FROM interesses i
             JOIN anuncios a ON a.id = i.anuncio_id
             WHERE i.id = ? AND a.usuario_id = ?'
        );
        $stmt->execute([$interesseId, $usuarioId]);
        return (bool)$stmt->fetch();
    }
}
