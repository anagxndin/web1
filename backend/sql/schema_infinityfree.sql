-- Mesmo schema de schema.sql, mas sem CREATE DATABASE/USE.
-- Use este arquivo em hospedagens compartilhadas (InfinityFree, Awardspace,
-- byet.host etc.), onde o usuário do banco só tem permissão dentro do banco
-- que o próprio painel já criou (não pode criar/trocar de banco).
-- Selecione o banco no phpMyAdmin antes de importar este arquivo.

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    cpf CHAR(11) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    tentativas_login TINYINT UNSIGNED NOT NULL DEFAULT 0,
    bloqueado_ate DATETIME NULL DEFAULT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_usuarios_email (email),
    UNIQUE KEY uq_usuarios_cpf (cpf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS anuncios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    marca VARCHAR(60) NOT NULL,
    modelo VARCHAR(60) NOT NULL,
    ano_fabricacao SMALLINT UNSIGNED NOT NULL,
    cor VARCHAR(40) NOT NULL,
    quilometragem INT UNSIGNED NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    estado CHAR(2) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_anuncios_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios (id) ON DELETE CASCADE,
    KEY idx_anuncios_marca (marca),
    KEY idx_anuncios_modelo (modelo),
    KEY idx_anuncios_cidade (cidade)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS anuncio_fotos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    anuncio_id INT UNSIGNED NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_fotos_anuncio FOREIGN KEY (anuncio_id)
        REFERENCES anuncios (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS interesses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    anuncio_id INT UNSIGNED NOT NULL,
    nome VARCHAR(150) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    mensagem TEXT NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_interesses_anuncio FOREIGN KEY (anuncio_id)
        REFERENCES anuncios (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
