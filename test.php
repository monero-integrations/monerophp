<?php

require_once('src/cryptonote.php');
$cryptonote = new Cryptonote;
$keys = $cryptonote->gen_private_keys('5909a346d4e39d49681eda0be83f443d192f8d7edb8e2fc951beff0670530b00');
$keys['publicKey'] = $cryptonote->pk_from_sk($keys['spendKey']);
$time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];

echo "Generated in {$time}s<br>\n";
print_r($keys);

?>