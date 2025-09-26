<?php
class BSCAPI {
    public static function getBalance($address) {
        $apiKey = BSCSCAN_API_KEY;
        $url = "https://api.bscscan.com/api?module=account&action=balance&address={$address}&tag=latest&apikey={$apiKey}";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'OmnichainPortfolioTracker/1.0'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception("Failed to fetch BSC balance");
        }
        
        $data = json_decode($response, true);
        
        if ($data['status'] !== '1') {
            throw new Exception("BSC API error: " . $data['message']);
        }
        
        // Convert from wei to BNB
        return $data['result'] / 1000000000000000000;
    }
}
?>