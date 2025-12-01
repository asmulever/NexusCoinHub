START TRANSACTION;

CREATE DATABASE IF NOT EXISTS finhub
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE finhub;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email          VARCHAR(255) NOT NULL UNIQUE,
    password_hash  VARCHAR(255) NOT NULL,
    created_at     DATETIME NOT NULL,
    updated_at     DATETIME NULL
) ENGINE=InnoDB;

-- Tokens de sesión/refresh
CREATE TABLE IF NOT EXISTS refresh_tokens (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       BIGINT UNSIGNED NOT NULL,
    token         CHAR(64) NOT NULL UNIQUE,
    expires_at    DATETIME NOT NULL,
    revoked_at    DATETIME NULL,
    created_at    DATETIME NOT NULL,
    CONSTRAINT fk_refresh_tokens_user
      FOREIGN KEY (user_id) REFERENCES users(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de instrumentos
CREATE TABLE IF NOT EXISTS instruments (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    symbol     VARCHAR(50) NOT NULL UNIQUE,
    name       VARCHAR(255) NOT NULL,
    currency   CHAR(3) NOT NULL,
    exchange   VARCHAR(100) NULL,
    type       VARCHAR(50) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB;

-- Tabla de portafolios
CREATE TABLE IF NOT EXISTS portfolios (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    BIGINT UNSIGNED NOT NULL,
    name       VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    CONSTRAINT fk_portfolios_user
      FOREIGN KEY (user_id) REFERENCES users(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de holdings
CREATE TABLE IF NOT EXISTS holdings (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    portfolio_id  BIGINT UNSIGNED NOT NULL,
    instrument_id BIGINT UNSIGNED NOT NULL,
    quantity      DECIMAL(20,8) NOT NULL DEFAULT 0,
    average_cost  DECIMAL(20,8) NOT NULL DEFAULT 0,
    created_at    DATETIME NOT NULL,
    updated_at    DATETIME NULL,
    CONSTRAINT fk_holdings_portfolio
      FOREIGN KEY (portfolio_id) REFERENCES portfolios(id)
      ON DELETE CASCADE,
    CONSTRAINT fk_holdings_instrument
      FOREIGN KEY (instrument_id) REFERENCES instruments(id)
      ON DELETE RESTRICT,
    CONSTRAINT uq_holdings_portfolio_instrument
      UNIQUE (portfolio_id, instrument_id)
) ENGINE=InnoDB;

-- Tabla de precios diarios
CREATE TABLE IF NOT EXISTS price_daily (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    instrument_id BIGINT UNSIGNED NOT NULL,
    price_date    DATE NOT NULL,
    open_price    DECIMAL(20,8) NOT NULL,
    high_price    DECIMAL(20,8) NOT NULL,
    low_price     DECIMAL(20,8) NOT NULL,
    close_price   DECIMAL(20,8) NOT NULL,
    volume        DECIMAL(20,4) NOT NULL DEFAULT 0,
    created_at    DATETIME NOT NULL,
    CONSTRAINT fk_price_daily_instrument
      FOREIGN KEY (instrument_id) REFERENCES instruments(id)
      ON DELETE CASCADE,
    CONSTRAINT uq_price_daily_instrument_date
      UNIQUE (instrument_id, price_date)
) ENGINE=InnoDB;

-- Tabla de logs de llamadas a APIs externas
CREATE TABLE IF NOT EXISTS api_logs (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    source           VARCHAR(100) NOT NULL,
    endpoint         VARCHAR(255) NOT NULL,
    status_code      INT NOT NULL,
    request_payload  JSON NULL,
    response_payload JSON NULL,
    started_at       DATETIME NOT NULL,
    finished_at      DATETIME NULL
) ENGINE=InnoDB;

COMMIT;
