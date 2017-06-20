<?php

/* Example of Monero Payment  */
require_once('jsonRPCClient.php');
require_once('Monero_Payments.php');

/* Edit it with your ip and your port of Monero RPC */
$monero_daemon = new Monero_Payments('127.0.0.1','18083');

?>
<html>
  <body>
    <h1>Example of Monero Library</h1>
    <h3>Monero Address</h3>
    <?php $monero_daemon->address(); ?>
    <h3>Balance</h3>
    <?php $monero_daemon->getbalance(); ?>
  </body>
</html>
