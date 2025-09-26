<?php
require_once 'config.php';
require_once 'database.php';
require_once 'balance_fetcher.php';
require_once 'includes/functions.php';

$db = new Database();
$fetcher = new BalanceFetcher();
$summary = $fetcher->getPortfolioSummary();
$wallets = $db->getWallets();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZetaChain Omnichain Portfolio Tracker</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🌐 <span class="zeta-brand">ZetaChain</span> Omnichain Portfolio Tracker</h1>
            <p>Track your cryptocurrency portfolio across Bitcoin, Ethereum, BSC, Polygon, and ZetaChain</p>
        </div>

        <!-- Portfolio Overview -->
        <div class="portfolio-stats">
            <div class="stat-card">
                <div class="stat-value" id="totalValue"><?= formatUSD($summary['total_value']) ?></div>
                <div class="stat-label">Total Portfolio Value</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="walletCount"><?= $summary['wallet_count'] ?></div>
                <div class="stat-label">Tracked Wallets</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="chainCount"><?= count($summary['chains']) ?></div>
                <div class="stat-label">Active Chains</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="lastUpdate"><?= timeAgo($summary['last_update']) ?></div>
                <div class="stat-label">Last Updated</div>
            </div>
        </div>

        <!-- Add Wallet Form -->
        <div class="card">
            <h2>➕ Add New Wallet</h2>
            <form id="addWalletForm" class="add-wallet-form">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="address">Wallet Address</label>
                    <input type="text" id="address" name="address" class="form-input" 
                           placeholder="Enter wallet address" required>
                </div>
                <div class="form-group">
                    <label for="blockchain">Blockchain</label>
                    <select id="blockchain" name="blockchain" class="form-select" required>
                        <option value="">Select Chain</option>
                        <?php foreach (SUPPORTED_CHAINS as $key => $chain): ?>
                            <option value="<?= $key ?>"><?= $chain['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="label">Label (Optional)</label>
                    <input type="text" id="label" name="label" class="form-input" 
                           placeholder="e.g., Main Wallet">
                </div>
                <button type="submit" class="btn btn-primary">Add Wallet</button>
            </form>
        </div>

        <!-- Portfolio Distribution Chart -->
        <?php if (!empty($summary['chains'])): ?>
        <div class="card">
            <h2>📊 Portfolio Distribution</h2>
            <div class="chart-container">
                <canvas id="portfolioChart"></canvas>
            </div>
        </div>
        <?php endif; ?>

        <!-- Wallet List -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>💼 Your Wallets</h2>
                <button id="refreshBtn" class="btn btn-primary">🔄 Refresh Balances</button>
            </div>
            
            <?php if (empty($wallets)): ?>
                <div style="text-align: center; padding: 40px; color: #718096;">
                    <h3>No wallets added yet</h3>
                    <p>Add your first wallet using the form above to start tracking your portfolio!</p>
                </div>
            <?php else: ?>
                <div id="walletList" class="wallet-list">
                    <?php foreach ($wallets as $wallet): 
                        $balance = $db->getWalletBalance($wallet['id']);
                        $chain = SUPPORTED_CHAINS[$wallet['blockchain']];
                    ?>
                        <div class="wallet-item">
                            <div class="chain-badge chain-<?= $wallet['blockchain'] ?>">
                                <?= $chain['name'] ?>
                            </div>
                            <div class="wallet-info">
                                <div class="wallet-address">
                                    <?= htmlspecialchars($wallet['address']) ?>
                                </div>
                                <?php if ($wallet['label']): ?>
                                    <div style="font-size: 12px; color: #718096;">
                                        <?= htmlspecialchars($wallet['label']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="wallet-balance">
                                <?php if ($balance): ?>
                                    <div class="balance-crypto">
                                        <?= formatBalance($balance['balance']) ?> <?= $chain['symbol'] ?>
                                    </div>
                                    <div class="balance-usd">
                                        <?= formatUSD($balance['usd_value']) ?>
                                    </div>
                                <?php else: ?>
                                    <div style="color: #718096;">Not loaded</div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <button class="btn btn-danger delete-wallet" data-wallet-id="<?= $wallet['id'] ?>">
                                    Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ZetaChain Info -->
        <div class="card" style="background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(124, 58, 237, 0.1));">
            <h2>🚀 Powered by ZetaChain's Omnichain Vision</h2>
            <p style="margin-bottom: 16px;">
                This portfolio tracker demonstrates the power of omnichain applications. Instead of managing 
                separate wallets and bridges across different blockchains, ZetaChain enables seamless 
                cross-chain functionality.
            </p>
            <p style="margin-bottom: 0;">
                <strong>No bridges. No wrapped tokens. One unified experience.</strong> 
                Track Bitcoin, Ethereum, BSC, Polygon, and ZetaChain all in one place.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Built for the <strong>ZetaChain Vibeathon</strong> • 
                <a href="https://github.com/mianohh/zetafolio">View Source Code</a> • 
                Made with ❤️ for the omnichain future
            </p>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>