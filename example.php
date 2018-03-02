<?php

require_once('src/jsonRPCClient.php');
require_once('src/daemonRPC.php');
$daemonRPC = new daemonRPC('127.0.0.1', '28081'); // Change to match your daemon's IP address and port

$getblockcount = $daemonRPC->getblockcount();
$on_getblockhash = $daemonRPC->on_getblockhash(42069);
// $getblocktemplate = $daemonRPC->getblocktemplate($wallet_address);
// $submitblock = $daemonRPC->submitblock();
$getlastblockheader = $daemonRPC->getlastblockheader();
// $getblockheaderbyhash = $daemonRPC->getblockheaderbyhash($hash);
// $getblockheaderbyheight = $daemonRPC->getblockheaderbyheight($height);
// $getblock_by_hash = $daemonRPC->getblock_by_hash($hash);
// $getblock_by_height = $daemonRPC->getblock_by_height($height);
$get_connections = $daemonRPC->get_connections();
$get_info = $daemonRPC->get_info();
// $hardfork_info = $daemonRPC->hardfork_info();
// $setbans = $daemonRPC->setbans($ip);
// $getbans = $daemonRPC->getbans();

?>
<html>
  <body>
    <h1><a href="https://github.com/monero-integrations/monerophp">MoneroPHP</a></h1>   
    <p>MoneroPHP was developed by <a href="https://github.com/serhack">SerHack</a> and the <a href="https://github.com/monero-integrations/monerophp/graphs/contributors">Monero-Integrations</a> team! Please report any issues or request additional features <a href="https://github.com/monero-integrations/monerophp/issues">here</a>.</p>

    <h2><tt>daemonRPC.php</tt> example</h2>
    <p><i>Note: not all methods shown, nor all results from each method.</i></p>
    <dl>
      <dt><tt>getblockcount()</tt></dt>
      <dd>
        <p>Status: <?php echo $getblockcount['status']; ?></p>
        <p>Height: <?php echo $getblockcount['count']; ?></p>
      </dd>
      <dt><tt>on_getblockhash(42069)</tt></dt>
      <dd>
        <p>Block hash: <?php echo $on_getblockhash; ?></p>
      </dd>
      <dt><tt>getlastblockheader()</tt></dt>
      <dd>
        <p>Current block hash: <?php echo $getlastblockheader['block_header']['hash']; ?></p>
        <p>Previous block hash: <?php echo $getlastblockheader['block_header']['prev_hash']; ?></p>
      </dd>
      <dt><tt>get_connections()</tt></dt>
      <dd>
        <p>Connections: <?php echo count($get_connections['connections']); ?></p>
        <?php foreach ($get_connections['connections'] as $peer) { echo '<p>' . $peer['address'] . ' (' . ( $peer['height'] == $getblockcount['count'] ? 'synced' : ( $peer['height'] > $getblockcount['count'] ? 'ahead; syncing' : 'behind; syncing') ). ')</p>'; } ?>
      </dd>
      <dt><tt>get_info()</tt></dt>
      <dd>
        <p>Difficulty: <?php echo $get_info['difficulty']; ?></p>
        <p>Cumulative difficulty: <?php echo $get_info['cumulative_difficulty']; ?></p>
      </dd>
    </dl>
  </body>
</html>
