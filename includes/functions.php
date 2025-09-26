<?php
function validateWalletAddress($address, $blockchain) {
    switch ($blockchain) {
        case 'bitcoin':
            return preg_match('/^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$/', $address) ||
                   preg_match('/^bc1[a-z0-9]{39,59}$/', $address);
        case 'ethereum':
        case 'bsc':
        case 'polygon':
            return preg_match('/^0x[a-fA-F0-9]{40}$/', $address);
        case 'zetachain':
            return preg_match('/^zeta[a-z0-9]{39}$/', $address) || 
                   preg_match('/^0x[a-fA-F0-9]{40}$/', $address); // ZetaChain supports both formats
        default:
            return false;
    }
}

function formatBalance($balance, $decimals = 6) {
    return number_format($balance, $decimals);
}

function formatUSD($value) {
    return '$' . number_format($value, 2);
}

function timeAgo($datetime) {
    if (!$datetime) return 'Never';
    
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->d > 0) {
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    } elseif ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    } elseif ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    } else {
        return 'Just now';
    }
}
?>