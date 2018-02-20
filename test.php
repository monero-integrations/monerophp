<?php

/**
 * 
 * Test ed25519 implementations using GMP vs. BCMath
 *
 */

require_once('src/cryptonote_gmp.php');
$cryptonote_gmp = new CryptonoteGMP;
$gmp_keys = $cryptonote_gmp->gen_private_keys('5909a346d4e39d49681eda0be83f443d192f8d7edb8e2fc951beff0670530b00');
$gmp_keys['publicKey'] = $cryptonote_gmp->pk_from_sk($gmp_keys['spendKey']);
$gmp_time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];

echo "GMP generated in {$gmp_time}s<br>\n";
print_r($gmp_keys);

$bcmath_start = microtime(true);
require_once('src/cryptonote_bcmath.php');
$cryptonote_bcmath = new CryptonoteBCMath;
$bcmath_keys = $cryptonote_bcmath->gen_private_keys('5909a346d4e39d49681eda0be83f443d192f8d7edb8e2fc951beff0670530b00');
$bcmath_keys['publicKey'] = $cryptonote_bcmath->pk_from_sk($bcmath_keys['spendKey']);
$bcmath_time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'] - $gmp_time;

echo "BCMath generated in {$bcmath_time}s<br>\n";
print_r($bcmath_keys);

?>