# 🌐 ZetaChain Omnichain Portfolio Tracker

A comprehensive cross-chain portfolio tracker built for the ZetaChain Vibeathon hackathon. Track your cryptocurrency holdings across Bitcoin, Ethereum, BSC, Polygon, and ZetaChain in one unified dashboard.

## 🎯 Problem Statement

Managing cryptocurrency portfolios across multiple blockchains is fragmented and cumbersome. Users need to:
- Check multiple block explorers
- Manage different wallet interfaces  
- Calculate total portfolio value manually
- Track balances across various chains

## 💡 Solution

Our Omnichain Portfolio Tracker provides:
- **Unified Dashboard**: One interface for all your wallets
- **Real-time Balances**: Automatic fetching from multiple chains
- **USD Valuation**: Live price conversion using CoinGecko
- **Visual Analytics**: Portfolio distribution charts
- **ZetaChain Integration**: Showcasing omnichain capabilities

## 🚀 Features

### Core Functionality
- ✅ Multi-wallet management across 5 blockchains
- ✅ Real-time balance fetching with caching
- ✅ USD value calculation and portfolio totals
- ✅ Interactive charts showing distribution
- ✅ Responsive web interface
- ✅ SQLite database for local storage

### Supported Blockchains
- 🟠 **Bitcoin** - Native BTC balances
- 🔵 **Ethereum** - Native ETH balances  
- 🟡 **Binance Smart Chain** - Native BNB balances
- 🟣 **Polygon** - Native MATIC balances
- 🌊 **ZetaChain** - Native ZETA balances

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+ with SQLite
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Charts**: Chart.js for portfolio visualization
- **APIs**: Free public APIs (no premium subscriptions)
- **Database**: File-based SQLite (no server setup)

## 📦 Installation

### Prerequisites
- PHP 7.4+ with SQLite extension
- Web server (Apache, Nginx, or PHP built-in)
- Internet connection for API calls

### Quick Start with PHP Built-in Server

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/zetachain-portfolio-tracker.git
   cd zetachain-portfolio-tracker