-- Logik Mastermind – tabulky pro výsledky a online hry
-- Spusť jednou proti MySQL databázi.

CREATE TABLE IF NOT EXISTS results (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  player_name VARCHAR(64) NOT NULL,
  mode VARCHAR(16) NOT NULL DEFAULT '1p',
  won TINYINT(1) NOT NULL DEFAULT 0,
  attempts TINYINT UNSIGNED NOT NULL,
  difficulty VARCHAR(16) NULL,
  game_code VARCHAR(12) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_player (player_name),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS games (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  game_code VARCHAR(12) NOT NULL,
  player1_name VARCHAR(64) NOT NULL,
  player2_name VARCHAR(64) NULL,
  secret JSON NULL,
  status ENUM('waiting','secret_entered','playing','won','lost') NOT NULL DEFAULT 'waiting',
  history JSON NULL,
  max_attempts TINYINT UNSIGNED NOT NULL DEFAULT 10,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_game_code (game_code),
  INDEX idx_status (status),
  INDEX idx_updated (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
