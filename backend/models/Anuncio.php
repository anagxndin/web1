<?php
require_once __DIR__ . '/Database.php';

class Anuncio
{
    /** Cria um novo anúncio. Retorna o ID inserido. */
    public static function criar(array $dados, int $usuarioId): int
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare(
            'INSERT INTO anuncios (usuario_id, marca, modelo, ano_fabricacao, cor, quilometragem, valor, estado, cidade, descricao)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $usuarioId,
            $dados['marca'],
            $dados['modelo'],
            $dados['ano'],
            $dados['cor'],
            $dados['km'],
            $dados['valor'],
            $dados['estado'],
            $dados['cidade'],
            $dados['descricao'],
        ]);
        return (int)$pdo->lastInsertId();
    }

    /** Lista anúncios com filtros opcionais (marca, modelo, cidade). */
    public static function listar(array $filtros = []): array
    {
        $pdo = Database::conectar();
        $condicoes = [];
        $parametros = [];

        foreach (['marca', 'modelo', 'cidade'] as $campo) {
            if (!empty($filtros[$campo])) {
                $condicoes[] = "a.$campo LIKE ?";
                $parametros[] = '%' . $filtros[$campo] . '%';
            }
        }

        $sql = "SELECT a.id, a.marca, a.modelo, a.ano_fabricacao, a.cidade, a.estado, a.valor,
                       (SELECT f.caminho FROM anuncio_fotos f WHERE f.anuncio_id = a.id ORDER BY f.id ASC LIMIT 1) AS foto
                FROM anuncios a";
        if ($condicoes) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }
        $sql .= ' ORDER BY a.criado_em DESC LIMIT 20';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }

    /** Lista anúncios de um usuário específico. */
    public static function listarPorUsuario(int $usuarioId): array
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare(
            'SELECT a.id, a.marca, a.modelo, a.ano_fabricacao, a.valor,
                    (SELECT f.caminho FROM anuncio_fotos f WHERE f.anuncio_id = a.id ORDER BY f.id ASC LIMIT 1) AS foto
             FROM anuncios a
             WHERE a.usuario_id = ?
             ORDER BY a.criado_em DESC'
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    /** Busca um anúncio por ID. */
    public static function buscarPorId(int $id): ?array
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare('SELECT * FROM anuncios WHERE id = ?');
        $stmt->execute([$id]);
        $anuncio = $stmt->fetch();
        return $anuncio ?: null;
    }

    /** Verifica se o usuário é o dono do anúncio. */
    public static function verificarDono(int $anuncioId, int $usuarioId): bool
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare('SELECT usuario_id FROM anuncios WHERE id = ?');
        $stmt->execute([$anuncioId]);
        $anuncio = $stmt->fetch();
        return $anuncio && (int)$anuncio['usuario_id'] === $usuarioId;
    }

    /** Exclui um anúncio (fotos são excluídas separadamente). Retorna true se excluiu. */
    public static function excluir(int $id): bool
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare('DELETE FROM anuncios WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
