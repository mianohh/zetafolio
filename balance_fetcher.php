<?php
require_once 'config.php';
require_once 'database.php';
require_once 'api/bitcoin.php';
require_once 'api/ethereum.php';
require_once 'api/bsc.php';
require_once 'api/polygon.php';
require_once 'api/zetachain.php';
require_once 'api/prices.php';

class BalanceFetcher {
    private $db;
    private $prices;
    
    public function __construct() {
        $this->db = new Database();
        $this->prices = PriceService::getPrices();
    }
    
    public function fetchAllBalances() {
        $wallets = $this->db->getWallets();
        $results = [];
        
        foreach ($wallets as $wallet) {
            try {
                $balance = $this->fetchBalance($wallet['address'], $wallet['blockchain']);
                $usdValue = $balance * $this->prices[$wallet['blockchain']];
                
                $this->db->updateBalance($wallet['id'], $balance, $usdValue);
                
                $results[] = [
                    'wallet' => $wallet,
                    'balance' => $balance,
                    'usd_value' => $usdValue,
                    'success' => true
                ];
            } catch (Exception $e) {
                $results[] = [
                    'wallet' => $wallet,
                    'error' => $e->getMessage(),
                    'success' => false
                ];
            }
        }
        
        return $results;
    }
    
    private function fetchBalance($address, $blockchain) {
        switch ($blockchain) {
            case 'bitcoin':
                return BitcoinAPI::getBalance($address);
            case 'ethereum':
                return EthereumAPI::getBalance($address);
            case 'bsc':
                return BSCAPI::getBalance($address);
            case 'polygon':
                return PolygonAPI::getBalance($address);
            case 'zetachain':
                return ZetaChainAPI::getBalance($address);
            default:
                throw new Exception("Unsupported blockchain: {$blockchain}");
        }
    }
    
    public function getPortfolioSummary() {
        $wallets = $this->db->getWallets();
        $summary = [
            'total_value' => 0,
            'chains' => [],
            'wallet_count' => count($wallets),
            'last_update' => $this->db->getLastUpdate()
        ];
        
        foreach ($wallets as $wallet) {
            $balance = $this->db->getWalletBalance($wallet['id']);
            
            if ($balance) {
                $blockchain = $wallet['blockchain'];
                $usdValue = (float) $balance['usd_value'];
                
                $summary['total_value'] += $usdValue;
                
                if (!isset($summary['chains'][$blockchain])) {
                    $summary['chains'][$blockchain] = [
                        'name' => SUPPORTED_CHAINS[$blockchain]['name'],
                        'symbol' => SUPPORTED_CHAINS[$blockchain]['symbol'],
                        'value' => 0,
                        'wallets' => 0
                    ];
                }
                
                $summary['chains'][$blockchain]['value'] += $usdValue;
                $summary['chains'][$blockchain]['wallets']++;
            }
        }
        
        return $summary;
    }
}
?>