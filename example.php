<?php

// Make sure to display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('src/jsonRPCClient.php');
require_once('src/daemonRPC.php');

$daemonRPC = new daemonRPC('127.0.0.1', 28081); // Change to match your daemon (monerod) IP address and port; 18081 is the default port for mainnet, 28081 for testnet, 38081 for stagenet
$getblockcount = $daemonRPC->getblockcount();
$on_getblockhash = $daemonRPC->on_getblockhash(42069);
// $getblocktemplate = $daemonRPC->getblocktemplate('9sZABNdyWspcpsCPma1eUD5yM3efTHfsiCx3qB8RDYH9UFST4aj34s5Ygz69zxh8vEBCCqgxEZxBAEC4pyGkN4JEPmUWrxn', 60);
// $submitblock = $daemonRPC->submitblock($block_blob);
$getlastblockheader = $daemonRPC->getlastblockheader();
// $getblockheaderbyhash = $daemonRPC->getblockheaderbyhash('fc7ba2a76071f609e39517dc0388a77f3e27cc2f98c8e933918121b729ee6f27');
// $getblockheaderbyheight = $daemonRPC->getblockheaderbyheight(696969);
// $getblock_by_hash = $daemonRPC->getblock_by_hash('fc7ba2a76071f609e39517dc0388a77f3e27cc2f98c8e933918121b729ee6f27');
// $getblock_by_height = $daemonRPC->getblock_by_height(696969);
$get_connections = $daemonRPC->get_connections();
$get_info = $daemonRPC->get_info();
// $hardfork_info = $daemonRPC->hardfork_info();
// $setbans = $daemonRPC->setbans('8.8.8.8');
// $getbans = $daemonRPC->getbans();

require_once('src/walletRPC.php');

$walletRPC = new walletRPC('127.0.0.1', 28082); // Change to match your wallet (monero-wallet-rpc) IP address and port; 18082 is the default port for mainnet, 28082 for testnet, 38082 for stagenet
// $create_wallet = $walletRPC->create_wallet('monero_wallet', ''); // Creates a new wallet named monero_wallet with no passphrase.  Comment this line and edit the next line to use your own wallet
$open_wallet = $walletRPC->open_wallet('monero_wallet', '');
$get_address = $walletRPC->get_address();
$get_accounts = $walletRPC->get_accounts();
$get_balance = $walletRPC->get_balance();
// $getheight = $walletRPC->getheight();
// $transfer = $walletRPC->transfer(1, '9sZABNdyWspcpsCPma1eUD5yM3efTHfsiCx3qB8RDYH9UFST4aj34s5Ygz69zxh8vEBCCqgxEZxBAEC4pyGkN4JEPmUWrxn'); // First account generated from mnemonic 'gang dying lipstick wonders howls begun uptight humid thirsty irony adept umpire dusted update grunt water iceberg timber aloof fudge rift clue umpire venomous thirsty'
// $transfer = $walletRPC->transfer(['address' => '9sZABNdyWspcpsCPma1eUD5yM3efTHfsiCx3qB8RDYH9UFST4aj34s5Ygz69zxh8vEBCCqgxEZxBAEC4pyGkN4JEPmUWrxn', 'amount' => 1, 'priority' => 1]); // Passing parameters in as array
// $transfer = $walletRPC->transfer(['destinations' => ['amount' => 1, 'address' => '9sZABNdyWspcpsCPma1eUD5yM3efTHfsiCx3qB8RDYH9UFST4aj34s5Ygz69zxh8vEBCCqgxEZxBAEC4pyGkN4JEPmUWrxn', 'amount' => 2, 'address' => 'BhASuWq4HcBL1KAwt4wMBDhkpwseFe6pNaq5DWQnMwjBaFL8isMZzcEfcF7x6Vqgz9EBY66g5UBrueRFLCESojoaHaTPsjh'], 'priority' => 1]); // Multiple payments in one transaction
// $sweep_all = $walletRPC->sweep_all('9sZABNdyWspcpsCPma1eUD5yM3efTHfsiCx3qB8RDYH9UFST4aj34s5Ygz69zxh8vEBCCqgxEZxBAEC4pyGkN4JEPmUWrxn');
// $sweep_all = $walletRPC->sweep_all(['address' => '9sZABNdyWspcpsCPma1eUD5yM3efTHfsiCx3qB8RDYH9UFST4aj34s5Ygz69zxh8vEBCCqgxEZxBAEC4pyGkN4JEPmUWrxn', 'priority' => 1]);
// $get_transfers = $walletRPC->get_transfers('in', true);
// $incoming_transfers = $walletRPC->incoming_transfers('all');
// $mnemonic = $walletRPC->mnemonic();

?>
<html>
  <body>
    <h1><a href="https://github.com/monero-integrations/monerophp">MoneroPHP</a></h1>   
    <p>MoneroPHP was developed by <a href="https://github.com/serhack">SerHack</a> and the <a href="https://github.com/monero-integrations/monerophp/graphs/contributors">Monero-Integrations team</a>! Please report any issues or request additional features at <a href="https://github.com/monero-integrations/monerophp/issues">github.com/monero-integrations/monerophp</a>.</p>

    <h2><tt>daemonRPC.php</tt> example</h2>
    <p><i>Note: not all methods shown, nor all results from each method.</i></p>
    <dl>
      <dt><tt>getblockcount()</tt></dt>
      <dd>
        <p>Status: <tt><?php echo $getblockcount['status']; ?></tt></p>
        <p>Height: <tt><?php echo $getblockcount['count']; ?></tt></p>
      </dd>
      <dt><tt>on_getblockhash(42069)</tt></dt>
      <dd>
        <p>Block hash: <tt><?php echo $on_getblockhash; ?></tt></p>
      </dd>
      <dt><tt>getlastblockheader()</tt></dt>
      <dd>
        <p>Current block hash: <tt><?php echo $getlastblockheader['block_header']['hash']; ?></tt></p>
        <p>Previous block hash: <tt><?php echo $getlastblockheader['block_header']['prev_hash']; ?></tt></p>
      </dd>
      <dt><tt>get_connections()</tt></dt>
      <dd>
        <p>Connections: <?php echo count($get_connections['connections']); ?></p>
        <?php foreach ($get_connections['connections'] as $peer) { echo '<p><tt>' . $peer['address'] . ' (' . ( $peer['height'] == $getblockcount['count'] ? 'synced' : ( $peer['height'] > $getblockcount['count'] ? 'ahead; syncing' : 'behind; syncing') ). ')</tt></p>'; } ?>
      </dd>
      <dt><tt>get_info()</tt></dt>
      <dd>
        <p>Difficulty: <tt><?php echo $get_info['difficulty']; ?></tt></p>
        <p>Cumulative difficulty: <tt><?php echo $get_info['cumulative_difficulty']; ?></tt></p>
      </dd>
    </dl>
    <h2><tt>walletRPC.php</tt> example</h2>
    <p><i>Note: not all methods shown, nor all results from each method.</i></p>
    <dl>
      <!--
      <dt><tt>get_address()</tt></dt>
      <dd>
        <?php foreach ($get_address['addresses'] as $account) { echo '<p>' . $account['label'] . ': <tt>' . $account['address'] . '</tt></p>'; } ?>
      </dd>
      -->
      <dt><tt>get_accounts()</tt></dt>
      <dd>
        <p>Accounts: <?php echo count($get_accounts['subaddress_accounts']); ?></p>
        <?php
          foreach ($get_accounts['subaddress_accounts'] as $account) {
            echo '<p><table><tr><td style="text-align: right;">Account ' . $account['account_index'] . ': </td><td><tt>' . $account['base_address'] . '</tt></td></tr>';
            echo ( $account['label'] ) ? '<tr><td style="text-align: right;">Label: </td><td><tt>' . $account['label'] . '</tt></td></tr>' : '';
            echo ( $account['tag'] ) ? '<tr><td style="text-align: right;">Tag: </td><td><tt>' . $account['tag'] . '</tt></td></tr>' : '';
            echo '<tr><td style="text-align: right;">Balance: </td><td><tt>' . $account['balance'] / pow(10, 12) . '</tt> (<tt>' . $account['unlocked_balance'] / pow(10, 12) . '</tt> unlocked)</td></tr></table></p>';
          }
        ?>
      </dd>
      <dt><tt>get_balance()</tt></dt>
      <dd>
        <p>Balance: <tt><?php echo $get_balance['balance'] / pow(10, 12); ?></tt></p>
        <p>Unlocked balance: <tt><?php echo $get_balance['unlocked_balance'] / pow(10, 12); ?></tt></p>
      </dd>
    </dl>
  </body>
  <!-- ignore the code below, it's just CSS styling -->
  <head>
    <style>
      body {
        color: #fff;
        background: #000;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif;
      }

      a, a:active, a:hover, a:visited {
        text-decoration: none;
        display: inline-block;
        position: relative;
        color: #ff6600;
      }
      a::after {
        content: '';
        position: absolute;
        width: 100%;
        transform: scaleX(0);
        height: 2px;
        bottom: 0;
        left: 0;
        background-color: #ff6600;
        transform-origin: bottom right;
        transition: transform 0.25s ease-out;
      }
      a:hover::after {
        transform: scaleX(1);
        transform-origin: bottom left;
      }

      dt tt {
        padding: 0.42em;
        background: #4c4c4c;
        text-shadow: 1px 1px 0px #000;
      }
      dd tt {
        font-size: 14px;
      }
    </style>
  </head>
</html>
 