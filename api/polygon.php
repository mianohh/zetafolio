<?php
class PolygonAPI {
    public static function getBalance($address) {
        $apiKey = POLYGONSCAN_API_KEY;
        $url = "https://api.polygonscan.com/api?module=account&action=balance&address={$address}&tag=latest&apikey={$apiKey}";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'OmnichainPortfolioTracker/1.0'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception("Failed to fetch Polygon balance");
        }
        
        $data = json_decode($response, true);
        
        if ($data['status'] !== '1') {
            throw new Exception("Polygon API error: " . $data['message']);
        }
        
        // Convert from wei to MATIC
        return $data['result'] / 1000000000000000000;
    }
}
?>