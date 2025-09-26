<?php
class BitcoinAPI {
    public static function getBalance($address) {
        $url = "https://api.blockcypher.com/v1/btc/main/addrs/{$address}/balance";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'OmnichainPortfolioTracker/1.0'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception("Failed to fetch Bitcoin balance");
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['balance'])) {
            throw new Exception("Invalid Bitcoin API response");
        }
        
        // Convert from satoshis to BTC
        return $data['balance'] / 100000000;
    }
}
?>