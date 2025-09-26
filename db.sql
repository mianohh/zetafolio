-- Wallets table
CREATE TABLE wallets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    address TEXT NOT NULL,
    blockchain TEXT NOT NULL,
    label TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(address, blockchain)
);

-- Balances cache table
CREATE TABLE balances (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    wallet_id INTEGER,
    balance TEXT NOT NULL,
    usd_value DECIMAL(18,8),
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (wallet_id) REFERENCES wallets (id)
);