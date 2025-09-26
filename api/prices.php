<?php
class PriceService {
    private static $cache = [];
    private static $cacheTime = null;
    
    public static function getPrices() {
        // Return cached prices if still valid
        if (self::$cacheTime && (time() - self::$cacheTime) < 60) {
            return self::$cache;
        }
        
        $chains = SUPPORTED_CHAINS;
        $ids = implode(',', array_column($chains, 'coingecko_id'));
        $url = "https://api.coingecko.com/api/v3/simple/price?ids={$ids}&vs_currencies=usd";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'OmnichainPortfolioTracker/1.0'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            // Return cached prices if API fails
            if (!empty(self::$cache)) {
                return self::$cache;
            }
            throw new Exception("Failed to fetch prices from CoinGecko");
        }
        
        $data = json_decode($response, true);
        
        // Map to blockchain names
        $prices = [];
        foreach ($chains as $blockchain => $info) {
            $coingeckoId = $info['coingecko_id'];
            if (isset($data[$coingeckoId]['usd'])) {
                $prices[$blockchain] = $data[$coingeckoId]['usd'];
            } else {
                $prices[$blockchain] = 0;
            }
        }
        
        self::$cache = $prices;
        self::$cacheTime = time();
        
        return $prices;
    }
}
?>