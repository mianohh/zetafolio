<?php
require_once 'config.php';
require_once 'database.php';
require_once 'balance_fetcher.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

$db = new Database();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            $address = trim($_POST['address'] ?? '');
            $blockchain = $_POST['blockchain'] ?? '';
            $label = trim($_POST['label'] ?? '');
            
            if (empty($address) || empty($blockchain)) {
                throw new Exception('Address and blockchain are required');
            }
            
            if (!validateWalletAddress($address, $blockchain)) {
                throw new Exception('Invalid wallet address format');
            }
            
            if (!array_key_exists($blockchain, SUPPORTED_CHAINS)) {
                throw new Exception('Unsupported blockchain');
            }
            
            $success = $db->addWallet($address, $blockchain, $label);
            
            if (!$success) {
                throw new Exception('Wallet already exists');
            }
            
            echo json_encode(['success' => true, 'message' => 'Wallet added successfully']);
            break;
            
        case 'delete':
            $walletId = (int)($_POST['wallet_id'] ?? 0);
            
            if ($walletId <= 0) {
                throw new Exception('Invalid wallet ID');
            }
            
            $success = $db->deleteWallet($walletId);
            
            if (!$success) {
                throw new Exception('Failed to delete wallet');
            }
            
            echo json_encode(['success' => true, 'message' => 'Wallet deleted successfully']);
            break;
            
        case 'refresh':
            $fetcher = new BalanceFetcher();
            $results = $fetcher->fetchAllBalances();
            
            $successCount = count(array_filter($results, fn($r) => $r['success']));
            $totalCount = count($results);
            
            echo json_encode([
                'success' => $successCount > 0,
                'message' => "Updated {$successCount}/{$totalCount} wallets",
                'results' => $results
            ]);
            break;
            
        case 'portfolio':
            $fetcher = new BalanceFetcher();
            $summary = $fetcher->getPortfolioSummary();
            
            // Get detailed wallet info
            $wallets = $db->getWallets();
            $walletsWithBalances = [];
            
            foreach ($wallets as $wallet) {
                $balance = $db->getWalletBalance($wallet['id']);
                $walletsWithBalances[] = [
                    'id' => $wallet['id'],
                    'address' => $wallet['address'],
                    'blockchain' => $wallet['blockchain'],
                    'label' => $wallet['label'],
                    'balance' => $balance ? $balance['balance'] : '0',
                    'usd_value' => $balance ? $balance['usd_value'] : 0,
                    'updated_at' => $balance ? $balance['updated_at'] : null
                ];
            }
            
            echo json_encode([
                'success' => true,
                'summary' => $summary,
                'wallets' => $walletsWithBalances
            ]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>