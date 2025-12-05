<?php
// inc/functions/auth.php

if (!function_exists('gerarTokenPublico')) {
    function gerarTokenPublico($length = 64) {
        // Gera um token hex seguro, ex: 64 caracteres
        return bin2hex(random_bytes($length / 2));
    }
}
