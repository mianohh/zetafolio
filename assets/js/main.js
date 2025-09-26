class PortfolioTracker {
    constructor() {
        this.chart = null;
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadPortfolio();
        
        // Auto-refresh every 5 minutes
        setInterval(() => {
            this.refreshBalances();
        }, 300000);
    }
    
    bindEvents() {
        // Add wallet form
        const addForm = document.getElementById('addWalletForm');
        if (addForm) {
            addForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.addWallet();
            });
        }
        
        // Refresh button
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.refreshBalances();
            });
        }
        
        // Delete wallet buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-wallet')) {
                const walletId = e.target.dataset.walletId;
                this.deleteWallet(walletId);
            }
        });
    }
    
    async addWallet() {
        const form = document.getElementById('addWalletForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('api_handler.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showAlert('Wallet added successfully!', 'success');
                form.reset();
                this.loadPortfolio();
            } else {
                this.showAlert(result.message || 'Failed to add wallet', 'error');
            }
        } catch (error) {
            this.showAlert('Network error occurred', 'error');
        }
    }
    
    async deleteWallet(walletId) {
        if (!confirm('Are you sure you want to delete this wallet?')) {
            return;
        }
        
        try {
            const response = await fetch('api_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&wallet_id=${walletId}`
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showAlert('Wallet deleted successfully!', 'success');
                this.loadPortfolio();
            } else {
                this.showAlert(result.message || 'Failed to delete wallet', 'error');
            }
        } catch (error) {
            this.showAlert('Network error occurred', 'error');
        }
    }
    
    async refreshBalances() {
        const refreshBtn = document.getElementById('refreshBtn');
        const originalText = refreshBtn.innerHTML;
        
        refreshBtn.innerHTML = '<div class="loading"></div> Refreshing...';
        refreshBtn.disabled = true;
        
        try {
            const response = await fetch('api_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=refresh'
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showAlert('Balances updated successfully!', 'success');
                this.loadPortfolio();
            } else {
                this.showAlert('Some balances failed to update', 'error');
            }
        } catch (error) {
            this.showAlert('Failed to refresh balances', 'error');
        } finally {
            refreshBtn.innerHTML = originalText;
            refreshBtn.disabled = false;
        }
    }
    
    async loadPortfolio() {
        try {
            const response = await fetch('api_handler.php?action=portfolio');
            const data = await response.json();
            
            this.updatePortfolioStats(data.summary);
            this.updateWalletList(data.wallets);
            this.updateChart(data.summary.chains);
        } catch (error) {
            console.error('Failed to load portfolio:', error);
        }
    }
    
    updatePortfolioStats(summary) {
        document.getElementById('totalValue').textContent = this.formatUSD(summary.total_value);
        document.getElementById('walletCount').textContent = summary.wallet_count;
        document.getElementById('lastUpdate').textContent = this.timeAgo(summary.last_update);
    }
    
    updateWalletList(wallets) {
        const container = document.getElementById('walletList');
        container.innerHTML = '';
        
        wallets.forEach(wallet => {
            const item = document.createElement('div');
            item.className = 'wallet-item';
            item.innerHTML = `
                <div class="chain-badge chain-${wallet.blockchain}">
                    ${this.getChainName(wallet.blockchain)}
                </div>
                <div class="wallet-info">
                    <div class="wallet-address">${this.truncateAddress(wallet.address)}</div>
                    ${wallet.label ? `<div class="wallet-label">${wallet.label}</div>` : ''}
                </div>
                <div class="wallet-balance">
                    <div class="balance-crypto">
                        ${this.formatBalance(wallet.balance)} ${this.getChainSymbol(wallet.blockchain)}
                    </div>
                    <div class="balance-usd">${this.formatUSD(wallet.usd_value)}</div>
                </div>
                <button class="btn btn-danger delete-wallet" data-wallet-id="${wallet.id}">
                    Delete
                </button>
            `;
            container.appendChild(item);
        });
    }
    
    updateChart(chains) {
        const ctx = document.getElementById('portfolioChart');
        if (!ctx) return;
        
        if (this.chart) {
            this.chart.destroy();
        }
        
        const labels = [];
        const data = [];
        const colors = [];
        
        const chainColors = {
            bitcoin: '#f7931a',
            ethereum: '#627eea',
            bsc: '#f3ba2f',
            polygon: '#8247e5',
            zetachain: '#00d4ff'
        };
        
        Object.keys(chains).forEach(chain => {
            labels.push(chains[chain].name);
            data.push(chains[chain].value);
            colors.push(chainColors[chain] || '#gray');
        });
        
        this.chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }
    
    // Utility methods
    formatUSD(value) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(value || 0);
    }
    
    formatBalance(balance) {
        return parseFloat(balance || 0).toFixed(6);
    }
    
    truncateAddress(address) {
        if (address.length <= 20) return address;
        return `${address.slice(0, 10)}...${address.slice(-8)}`;
    }
    
    getChainName(blockchain) {
        const chains = {
            bitcoin: 'Bitcoin',
            ethereum: 'Ethereum',
            bsc: 'BSC',
            polygon: 'Polygon',
            zetachain: 'ZetaChain'
        };
        return chains[blockchain] || blockchain;
    }
    
    getChainSymbol(blockchain) {
        const symbols = {
            bitcoin: 'BTC',
            ethereum: 'ETH',
            bsc: 'BNB',
            polygon: 'MATIC',
            zetachain: 'ZETA'
        };
        return symbols[blockchain] || blockchain.toUpperCase();
    }
    
    timeAgo(datetime) {
        if (!datetime) return 'Never';
        
        const now = new Date();
        const past = new Date(datetime);
        const diffMs = now - past;
        const diffMins = Math.floor(diffMs / 60000);
        
        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins} min ago`;
        
        const diffHours = Math.floor(diffMins / 60);
        if (diffHours < 24) return `${diffHours}h ago`;
        
        const diffDays = Math.floor(diffHours / 24);
        return `${diffDays}d ago`;
    }
    
    showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        
        const container = document.querySelector('.container');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new PortfolioTracker();
});