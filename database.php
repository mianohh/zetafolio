<?php
require_once 'config.php';

class Database {
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = new PDO('sqlite:' . DB_PATH);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createTables();
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    private function createTables() {
        $sql = "
            CREATE TABLE IF NOT EXISTS wallets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                address TEXT NOT NULL,
                blockchain TEXT NOT NULL,
                label TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(address, blockchain)
            );
            
            CREATE TABLE IF NOT EXISTS balances (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                wallet_id INTEGER,
                balance TEXT NOT NULL,
                usd_value DECIMAL(18,8),
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (wallet_id) REFERENCES wallets (id) ON DELETE CASCADE
            );
        ";
        
        $this->pdo->exec($sql);
    }
    
    public function addWallet($address, $blockchain, $label = '') {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO wallets (address, blockchain, label) VALUES (?, ?, ?)");
            return $stmt->execute([$address, $blockchain, $label]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Unique constraint violation
                return false;
            }
            throw $e;
        }
    }
    
    public function getWallets() {
        $stmt = $this->pdo->query("SELECT * FROM wallets ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteWallet($id) {
        $stmt = $this->pdo->prepare("DELETE FROM wallets WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function updateBalance($walletId, $balance, $usdValue) {
        // Delete old balance
        $this->pdo->prepare("DELETE FROM balances WHERE wallet_id = ?")->execute([$walletId]);
        
        // Insert new balance
        $stmt = $this->pdo->prepare("INSERT INTO balances (wallet_id, balance, usd_value) VALUES (?, ?, ?)");
        return $stmt->execute([$walletId, $balance, $usdValue]);
    }
    
    public function getWalletBalance($walletId) {
        $stmt = $this->pdo->prepare("SELECT * FROM balances WHERE wallet_id = ? ORDER BY updated_at DESC LIMIT 1");
        $stmt->execute([$walletId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getLastUpdate() {
        $stmt = $this->pdo->query("SELECT MAX(updated_at) as last_update FROM balances");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['last_update'];
    }
}
?>