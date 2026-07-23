<?php
require_once __DIR__ . '/Database.php';

class AnuncioFoto
{
    /** Insere o caminho de uma foto no banco. */
    public static function inserir(int $anuncioId, string $caminho): void
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare('INSERT INTO anuncio_fotos (anuncio_id, caminho) VALUES (?, ?)');
        $stmt->execute([$anuncioId, $caminho]);
    }

    /** Retorna todas as fotos de um anúncio. */
    public static function listarPorAnuncio(int $anuncioId): array
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare('SELECT id, caminho FROM anuncio_fotos WHERE anuncio_id = ? ORDER BY id ASC');
        $stmt->execute([$anuncioId]);
        return $stmt->fetchAll();
    }

    /** Exclui todas as fotos de um anúncio do banco. */
    public static function excluirPorAnuncio(int $anuncioId): void
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare('DELETE FROM anuncio_fotos WHERE anuncio_id = ?');
        $stmt->execute([$anuncioId]);
    }
}
