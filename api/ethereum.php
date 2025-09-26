<?php
class EthereumAPI {
    public static function getBalance($address) {
        $apiKey = ETHERSCAN_API_KEY;
        $url = "https://api.etherscan.io/api?module=account&action=balance&address={$address}&tag=latest&apikey={$apiKey}";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'OmnichainPortfolioTracker/1.0'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception("Failed to fetch Ethereum balance");
        }
        
        $data = json_decode($response, true);
        
        if ($data['status'] !== '1') {
            throw new Exception("Ethereum API error: " . $data['message']);
        }
        
        // Convert from wei to ETH
        return $data['result'] / 1000000000000000000;
    }
}
?>