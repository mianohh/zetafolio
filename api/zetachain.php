<?php
class ZetaChainAPI {
    public static function getBalance($address) {
        // ZetaChain API integration - using mock data for demo
        // In production, replace with actual ZetaChain API calls
        
        // Mock balance for demonstration
        $mockBalances = [
            'zeta1abc123' => 1250.75,
            'zeta1def456' => 500.25,
            'zeta1ghi789' => 2000.50
        ];
        
        // Return mock balance or random value for demo
        if (isset($mockBalances[$address])) {
            return $mockBalances[$address];
        }
        
        // Generate realistic mock balance
        return rand(100, 5000) + (rand(0, 99) / 100);
    }
}
?>