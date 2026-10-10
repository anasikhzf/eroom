<?php

if (!function_exists('encode_id')) {
    function encode_id($id) {
        if (empty($id)) return '';
        if (!is_numeric($id)) return $id;
        // XOR obfuscation with a secret number
        $obfuscated = (int)$id ^ 987654321;
        // Convert to base64, remove padding, and make url-safe
        return rtrim(strtr(base64_encode((string)$obfuscated), '+/', '-_'), '=');
    }
}

if (!function_exists('decode_id')) {
    function decode_id($encoded) {
        if (empty($encoded)) return 0;
        if (is_numeric($encoded)) return (int)$encoded; // fallback if raw integer is used
        // Decode from url-safe base64
        $decoded = base64_decode(strtr($encoded, '-_', '+/'));
        if ($decoded === false) return 0;
        $obfuscated = (int)$decoded;
        return $obfuscated ^ 987654321;
    }
}
