<?php

// Make sure to display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('src/daemonRPC.php');
use MoneroPHP\daemonRPC;

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
use MoneroPHP\walletRPC;

$walletRPC = new walletRPC('127.0.0.1', 28083); // Change to match your wallet (monero-wallet-rpc) IP address and port; 18083 is the customary port for mainnet, 28083 for testnet, 38083 for stagenet
$create_wallet = $walletRPC->create_wallet('monero_wallet', ''); // Creates a new wallet named monero_wallet with no passphrase.  Comment this line and edit the next line to use your own wallet
$open_wallet = $walletRPC->open_wallet('monero_wallet', '');
$get_address = $walletRPC->get_address();
$get_accounts = $walletRPC->get_accounts();
$get_balance = $walletRPC->get_balance();
// $create_address = $walletRPC->create_address(0, 'This is an example subaddress label'); // Create a subaddress on account 0
// $tag_accounts = $walletRPC->tag_accounts([0], 'This is an example account tag');
// $get_height = $walletRPC->get_height();
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
    <h1>
      <a href="https://github.com/monero-integrations/monerophp" title="Monero Integrations">
        <img src=" data:image/png;base64,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" height="22pt" style="height: 1em; margin-bottom: -4pt" /> <!-- Image source is just base64-encoded -->
        MoneroPHP
      </a>
    </h1>   
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
            echo '<p>Account ' . $account['account_index'] . ': <tt>' . $account['base_address'] . '</tt><br />';
            echo 'Balance: <tt>' . $account['balance'] / pow(10, 12) . '</tt> (<tt>' . $account['unlocked_balance'] / pow(10, 12) . '</tt> unlocked)<br />';
            echo ( $account['label'] ) ? 'Label: <tt>' . $account['label'] . '</tt><br />' : '';
            echo ( $account['tag'] ) ? 'Tag: <tt>' . $account['tag'] . '</tt><br />' : '';
            echo '</p>';
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
 