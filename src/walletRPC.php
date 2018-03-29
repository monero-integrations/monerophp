<?php
/**
 * 
 * monerophp/walletRPC
 * 
 * A class for making calls to monero-wallet-rpc using PHP
 * https://github.com/monero-integrations/monerophp
 *
 * Using work from
 *   CryptoChangements [Monero_RPC] <bW9uZXJv@gmail.com> (https://github.com/cryptochangements34)
 *   Serhack [Monero Integrations] <nico@serhack.me> (https://serhack.me)
 *   TheKoziTwo [xmr-integration] <thekozitwo@gmail.com>
 *   Kacper Rowinski [jsonRPCClient] <krowinski@implix.com>
 * 
 * @author     Monero Integrations Team <support@monerointegrations.com> (https://github.com/monero-integrations)
 * @copyright  2018
 * @license    MIT
 *  
 * ============================================================================
 * 
 * // Initialize Monero connection/object
 * $walletRPC = new walletRPC();
 * 
 * // Examples:
 * $address = $walletRPC->getaddress();
 * $walletRPC->sign('The Times 03/Jan/2009 Chancellor on brink of second bailout for banks');
 * 
 */

require_once('jsonRPCClient.php');

class walletRPC
{
  private $client;

  private $protocol; 
  private $host;
  private $port;
  private $url;
  private $user;
  private $password;
  
  /**
   *
   * Start a connection with monero-wallet-rpc
   *
   * @param  string  $host      IP address of monero-wallet-rpc to connect to  (optional)
   * @param  int     $port      Port to use when accessing monero-wallet-rpc   (optional)
   * @param  string  $protocol  Protocol to acces daemon over (eg. 'http')     (optional)
   * @param  string  $user      Username                                       (optional)
   * @param  string  $password  Password                                       (optional)
   *
   */
  function __construct ($host = '127.0.0.1', $port = 18083, $protocol = 'http', $user = null, $password = null)
  {
    // TODO input validation
    
    $this->host = $host;
    $this->port = $port;
    $this->protocol = $protocol;
    $this->user = $user;
    $this->password = $password;

    $this->url = $protocol.'://'.$host.':'.$port.'/json_rpc';
    $this->client = new jsonRPCClient($this->url, $this->user, $this->password);
  }
   
  /**
   *
   * Execute command on the monero-wallet-rpc API
   *
   * @param  string  $method  RPC method to call
   * @param  object  $params  Parameters to include with call  (optional)
   *
   * @return string  Call result
   *
   */
  private function _run($method, $params = null)
  {
    $result = $this->client->_run($method, $params);
    return $result;
  }
    
  /**
   *
   * Print JSON object (for API)
   *
   * @param  object  $json  JSON object to print
   *
   * @return none
   *
   */
  public function _print($json)
  {
    $json_parsed = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo $json_parsed;
  }

  /**
   *
   * Get an account's balance
   *
   * @param  number  $account_index  Index of account to look up  (optional)
   *
   * @return object  Example: {
   *   "balance": 140000000000,
   *   "unlocked_balance": 50000000000
   * }
   *
   */
  public function get_balance($account_index = 0)
  {
    $get_balance_parameters = array('account_index' => $account_index);
    return $this->_run('get_balance', $get_balance_parameters);
  }
  
  /**
   *
   * Alias of get_balance()
   *
   * @param  number  $account_index  Index of account to look up  (optional)
   *
   * @return object  Example: {
   *   "balance": 140000000000,
   *   "unlocked_balance": 50000000000
   * }
   *
   */
  public function getbalance($account_index = 0)
  {
    $getbalance_parameters = array('account_index' => $account_index);
    return $this->_run('getbalance', $getbalance_parameters);
  }
  
  /**
   *
   * Look up wallet address
   *
   * @param  number  $account_index  Index of account to look up     (optional)
   * @param  number  $address_index  Index of subaddress to look up  (optional)
   *
   * @return object  Example: {
   *   "address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA"
   * }
   *
   */
  public function get_address($account_index = 0, $address_index = 0)
  {
    $get_address_parameters = array('account_index' => $account_index, 'address_index' => $address_index);
    return $this->_run('get_address', $get_address_parameters);
  }
  
  /**
   *
   * Alias of get_address()
   *
   * @param  number  $account_index  Index of account to look up     (optional)
   * @param  number  $address_index  Index of subaddress to look up  (optional)
   *
   * @return object  Example: {
   *   "address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA"
   * }
   *
   */
  public function getaddress($account_index = 0, $address_index = 0)
  {
    $getaddress_parameters = array('account_index' => $account_index, 'address_index' => $address_index);
    return $this->_run('getaddress', $getaddress_parameters);
  }
  
  /**
   *
   * Create a new subaddress
   *
   * @param  number  $account_index  The subaddress account index
   * @param  string  $label          The label to use
   *
   * @return object  Example: {
   *   "address": "Bh3ttLbjGFnVGCeGJF1HgVh4DfCaBNpDt7PQAgsC2GFug7WKskgfbTmB6e7UupyiijiHDQPmDC7wSCo9eLoGgbAFJQaAaDS"
   *   "address_index": 1
   * }
   *
   */
  public function create_address($account_index = 0, $label = '')
  {
    $create_address_parameters = array('account_index' => $account_index ,'label' => $label);
    $create_address_method = $this->_run('create_address', $create_address_parameters);

    $save = $this->store(); // Save wallet state after subaddress creation

    return $create_address_method;
  }
  
  /**
   *
   * Label a subaddress
   *
   * @param  number  The subaddress index to label
   * @param  string  The label to use
   *
   * @return none
   *
   */
  public function label_address($index, $label)
  {
    $label_address_parameters = array('index' => $index ,'label' => $label);
    return $this->_run('label_address', $label_address_parameters);
  }

  /**
   *
   * Get current accounts from wallet
   *
   * @param  none
   *
   * @return object  Example: {
   *   "subaddress_accounts": {
   *     "0": {
   *       "account_index": 0,
   *       "balance": 2808597352948771,
   *       "base_address": "A2XE6ArhRkVZqepY2DQ5QpW8p8P2dhDQLhPJ9scSkW6q9aYUHhrhXVvE8sjg7vHRx2HnRv53zLQH4ATSiHHrDzcSFqHpARF",
   *       "label": "Primary account",
   *       "tag": "",
   *       "unlocked_balance": 2717153096298162
   *     },
   *     "1": {
   *       "account_index": 1,
   *       "balance": 0,
   *       "base_address": "BcXKsfrvffKYVoNGN4HUFfaruAMRdk5DrLZDmJBnYgXrTFrXyudn81xMj7rsmU5P9dX56kRZGqSaigUxUYoaFETo9gfDKx5",
   *       "label": "Secondary account",
   *       "tag": "",
   *       "unlocked_balance": 0 )
   *    },
   *    "total_balance": 2808597352948771,
   *    "total_unlocked_balance": 2717153096298162
   * }
   *
   */
  public function get_accounts()
  {
    return $this->_run('get_accounts');
  }
  
  /**
   *
   * Create a new sub-account from your wallet
   *
   * @param  string  A label for the account
   *
   * @return none
   *
   */
  public function create_account($label = '')
  {
    $create_account_parameters = array('label' => $label);
    $create_account_method = $this->_run('create_account', $create_account_parameters);

    $save = $this->store(); // Save wallet state after account creation

    return $create_account_method;
  }
  
  /**
   *
   * Label an account
   *
   * @param  number $account_index  Index of account to label
   * @param  string $label          Label to apply
   *
   * @return none
   *
   */
  public function label_account($account_index, $label)
  {
    $label_account_parameters = array('account_index' => $account_index, 'label' => $label);
    $label_account_method = $this->_run('label_account', $label_account_parameters);

    $save = $this->store(); // Save wallet state after account label

    return $label_account_method;
  }
  
  /**
   *
   * Get account tags
   *
   * @param  none
   *
   * @return object  Example: {
   *   "account_tags": {
   *     "0": {
   *       "accounts": {
   *         "0": 0,
   *         "1": 1
   *       },
   *       "label": "",
   *       "tag": "Example tag"
   *     }
   *   }
   * }
   *
   */
  public function get_account_tags()
  {
    return $this->_run('get_account_tags');
  }
  
  /**
   *
   * Tag an account
   *
   * @param  array   $accounts  Account indices to tag
   * @param  string  $tag       Tag to apply
   *
   * @return none
   *
   */
  public function tag_accounts($accounts, $tag)
  {
    $tag_accounts_parameters = array('accounts' => $accounts, 'tag' => $tag);
    $tag_accounts_method = $this->_run('tag_accounts', $tag_accounts_parameters);

    $save = $this->store(); // Save wallet state after account tagginng

    return $this->_run('tag_accounts');
  }
  
  /**
   *
   * Untag accounts
   *
   * @param  array   $accounts  Account indices to untag
   *
   * @return none
   *
   */
  public function untag_accounts($accounts)
  {
    $untag_accounts_parameters = array('accounts' => $accounts);
    $untag_accounts_method = $this->_run('untag_accounts', $untag_accounts_parameters);

    $save = $this->store(); // Save wallet state after untagging accounts

    return $untag_accounts_method;
  }
  
  /**
   *
   * Describe a tag
   * 
   * @param  string  $tag          Tag to describe
   * @param  string  $description  Description to apply to tag
   *
   * @return object  Example: {
   * }
   *
   */
  public function set_account_tag_description($tag, $description)
  {
    $set_account_tag_description_parameters = array('tag' => $tag, 'description' => $description);
    $set_account_tag_description_method = $this->_run('set_account_tag_description', $set_account_tag_description_parameters);

    $save = $this->store(); // Save wallet state after describing tag

    return $set_account_tag_description_method;
  }
  
  /**
   *
   * Look up current height of wallet
   *
   * @param  none
   *
   * @return object  Example: {
   *   "height": 994310
   * }
   *
   */
  public function get_height()
  {
    return $this->_run('get_height');
  }
  
  /**
   *
   * Alias of get_height()
   *
   * @param  none
   *
   * @return object  Example: {
   *   "height": 994310
   * }
   *
   */
  public function getheight()
  {
    return $this->_run('getheight');
  }

  /**
   *
   * Send monero to a number of recipients.  Parameters can be passed in individually (as listed below) or as an array (as listed at bottom.)  If multiple destinations are required, use the array format and use
   * 
   * @param  string   $amount           Amount to transfer
   * @param  string   $address          Address to transfer to
   * @param  string   $payment_id       Payment ID                                                (optional)
   * @param  number   $mixin            Mixin number                                              (optional)
   * @param  number   $account_index    Account to send from                                      (optional)
   * @param  string   $subaddr_indices  Comma-separeted list of subaddress indices to spend from  (optional)
   * @param  number   $priority         Transaction priority                                      (optional)
   * @param  number   $unlock_time      UNIX time or block height to unlock output                (optional)
   * @param  boolean  $do_not_relay     Do not relay transaction                                  (optional)
   * 
   *   OR
   * 
   * @param  object  $params            Array containing any of the options listed above, where only amount and address are required
   *
   * @return object  Example: {
   *   "amount": "1000000000000",
   *   "fee": "1000020000",
   *   "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
   *   "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
   * }
   *
   */
  public function transfer($amount, $address = '', $payment_id = '', $mixin = 6, $account_index = 0, $subaddr_indices = '', $priority = 2, $unlock_time = 0, $do_not_relay = false)
  {
    if (is_array($amount)) { // Parameters passed in as object
      $params = $amount;

      if (array_key_exists('destinations', $params)) {
        $destinations = $params['destinations'];

        foreach ($destinations as $key => $amount) {
          if ($key == 'amount') {
            // Convert from moneroj to tacoshi (piconero)
            $destinations[$key] = $amount * 1000000000000;
          }
        }
      } else {
        if (array_key_exists('amount', $params)) {
          $amount = $params['amount'];
        } else {
          throw new Exception('Error: Amount required');
        }
        if (array_key_exists('address', $params)) {
          $address = $params['address'];
        } else {
          throw new Exception('Error: Address required');
        }
    
        // Convert from moneroj to tacoshi (piconero)
        $new_amount = $amount  * 1000000000000;

        $destinations = array('amount' => $new_amount, 'address' => $address);
      }
      if (array_key_exists('payment_id', $params)) {
        $payment_id = $params['payment_id'];
      }
      if (array_key_exists('mixin', $params)) {
        $mixin = $params['mixin'];
      }
      if (array_key_exists('account_index', $params)) {
        $account_index = $params['account_index'];
      }
      if (array_key_exists('subaddr_indices', $params)) {
        $subaddr_indices = $params['subaddr_indices'];
      }
      if (array_key_exists('priority', $params)) {
        $priority = $params['priority'];
      }
      if (array_key_exists('unlock_time', $params)) {
        $unlock_time = $params['unlock_time'];
      }
      if (array_key_exists('do_not_relay', $params)) {
        $do_not_relay = $params['do_not_relay'];
      }
    } else { // Legacy parameters used

      // Convert from moneroj to tacoshi (piconero)
      $new_amount = $amount  * 1000000000000;

      $destinations = array('amount' => $new_amount, 'address' => $address);
    }

    $transfer_parameters = array('destinations' => array($destinations), 'mixin' => $mixin, 'get_tx_key' => true, 'payment_id' => $payment_id, 'account_index' => $account_index, 'subaddr_indices' => $subaddr_indices, 'priority' => $priority, 'do_not_relay' => $do_not_relay);
    $transfer_method = $this->_run('transfer', $transfer_parameters);

    $save = $this->store(); // Save wallet state after transfer

    return $transfer_method;
  }
  
  /**
   *
   * Same as transfer, but splits transfer into more than one transaction if necessary
   *
   */
  public function transfer_split($amount, $address = '', $payment_id = '', $mixin = 6, $account_index = 0, $subaddr_indices = '', $priority = 2, $unlock_time = 0, $do_not_relay = false)
  {
    if (is_array($amount)) { // Parameters passed in as object
      $params = $amount;

      if (array_key_exists('destinations', $params)) {
        $destinations = $params['destinations'];

        foreach ($destinations as $destination => $recipient) {
          if (!array_key_exists('amount', $destinations[$destination])) {
            throw new Exception('Error: Amount required for each destination');
          }
          if (!array_key_exists('address', $destinations[$destination])) {
            throw new Exception('Error: Address required for each destination');
          }

          // Convert from moneroj to tacoshi (piconero)
          $destinations[$destination]['amount'] = $destinations[$destination]['amount'] * 1000000000000;
        }
      } else {
        if (array_key_exists('amount', $params)) {
          $amount = $params['amount'];
        } else {
          throw new Exception('Error: Amount required');
        }
        if (array_key_exists('address', $params)) {
          $address = $params['address'];
        } else {
          throw new Exception('Error: Address required');
        }
    
        // Convert from moneroj to tacoshi (piconero)
        $new_amount = $amount * 1000000000000;

        $destinations = array('amount' => $new_amount, 'address' => $address);
      }
      if (array_key_exists('mixin', $params)) {
        $mixin = $params['mixin'];
      }
      if (array_key_exists('payment_id', $params)) {
        $payment_id = $params['payment_id'];
      }
      if (array_key_exists('account_index', $params)) {
        $account_index = $params['account_index'];
      }
      if (array_key_exists('subaddr_indices', $params)) {
        $subaddr_indices = $params['subaddr_indices'];
      }
      if (array_key_exists('priority', $params)) {
        $priority = $params['priority'];
      }
      if (array_key_exists('unlock_time', $params)) {
        $unlock_time = $params['unlock_time'];
      }
      if (array_key_exists('unlock_time', $params)) {
        $unlock_time = $params['unlock_time'];
      }
      if (array_key_exists('do_not_relay', $params)) {
        $do_not_relay = $params['do_not_relay'];
      }
    } else { // Legacy parameters used
      // Convert from moneroj to tacoshi (piconero)
      $new_amount = $amount * 1000000000000;

      $destinations = array('amount' => $new_amount, 'address' => $address);
    }

    $transfer_split_parameters = array('destinations' => array($destinations), 'mixin' => $mixin, 'get_tx_key' => true, 'account_index' => $account_index, 'subaddr_indices' => $subaddr_indices, 'payment_id' => $payment_id, 'priority' => $priority, 'unlock_time' => $unlock_time, 'do_not_relay' => $do_not_relay);
    $transfer_method = $this->_run('transfer_split', $transfer_split_parameters);

    $save = $this->store(); // Save wallet state after transfer

    return $transfer_method;
  }
  
  /**
   *
   * Send all dust outputs back to the wallet to make them easier to spend (and mix)
   *
   * @param  none
   *
   * @return object  Example: {
   *   "multisig_txset": []
   * }
   *
   */
  public function sweep_dust()
  {
    return $this->_run('sweep_dust');
  }
  
  /**
   *
   * Send all unimzable output back to the wallet
   *
   * @param  none
   *
   * @return object  Example: {
   *   "multisig_txset": []
   * }
   *
   */
  public function sweep_unmixable()
  {
    return $this->_run('sweep_unmixable');
  }
  
  /**
   *
   * Send all unlocked balance from current account to an address
   * 
   * @param  string   $address          Address to transfer to
   * @param  string   $subaddr_indices  Comma-seperated list of Subaddress indices to sweep  (optional)
   * @param  number   $account_index    Account to sweep from                                (optional)
   * @param  string   $payment_id       Payment ID                                           (optional)
   * @param  number   $mixin            Mixin number                                         (optional)
   * @param  number   $priority         Payment ID                                           (optional)
   * @param  number   $below_amount     Only send outputs below this amount                  (optional)
   * @param  number   $unlock_time      UNIX time or block height to unlock output           (optional)
   * @param  boolean  $do_not_relay     Do not relay transaction                             (optional)
   * 
   *   OR
   * 
   * @param  object  $params            Array containing any of the options listed above, where only amount and address are required
   *
   * @return object  Example: {
   *   "amount": "1000000000000",
   *   "fee": "1000020000",
   *   "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
   *   "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
   * }
   *
   */
  public function sweep_all($address, $subaddr_indices = '', $account_index = 0, $payment_id = '', $mixin = 6, $priority = 2, $below_amount = 0, $unlock_time = 0, $do_not_relay = false)
  {
    if (is_array($address)) { // Parameters passed in as object
      $params = $address;

      if (array_key_exists('address', $params)) {
        $address = $params['address'];
      } else {
        throw new Exception('Error: Address required');
      }
      if (array_key_exists('subaddr_indices', $params)) {
        $subaddr_indices = $params['subaddr_indices'];
      }
      if (array_key_exists('account_index', $params)) {
        $account_index = $params['account_index'];
      }
      if (array_key_exists('payment_id', $params)) {
        $payment_id = $params['payment_id'];
      }
      if (array_key_exists('mixin', $params)) {
        $mixin = $params['mixin'];
      }
      if (array_key_exists('priority', $params)) {
        $priority = $params['priority'];
      }
      if (array_key_exists('below_amount', $params)) {
        $below_amount = $params['below_amount'];

        // Convert from moneroj to tacoshi (piconero)
        $new_below_amount = $below_amount * 1000000000000;
      }
      if (array_key_exists('unlock_time', $params)) {
        $unlock_time = $params['unlock_time'];
      }
      if (array_key_exists('do_not_relay', $params)) {
        $do_not_relay = $params['do_not_relay'];
      }
    } else { // Legacy parameters used
      // Convert from moneroj to tacoshi (piconero)
      $new_below_amount = $below_amount * 1000000000000;
    }

    $sweep_all_parameters = array('address' => $address, 'mixin' => $mixin, 'get_tx_key' => true, 'subaddr_indices' => $subaddr_indices, 'account_index' => $account_index, 'payment_id' => $payment_id, 'priority' => $priority, 'below_amount' => $new_below_amount, 'unlock_time' => $unlock_time, 'do_not_relay' => $do_not_relay);
    $sweep_all_method = $this->_run('sweep_all', $sweep_all_parameters);

    $save = $this->store(); // Save wallet state after transfer

    return $sweep_all_method;
  }
  
  /**
   *
   * Sweep a single key image to an address
   * 
   * @param  string   $key_image     Key image to sweep
   * @param  string   $address       Address to transfer to
   * @param  string   $payment_id    Payment ID                                  (optional)
   * @param  number   $below_amount  Only send outputs below this amount         (optional)
   * @param  number   $mixin         Mixin number                                (optional)
   * @param  number   $priority      Payment ID                                  (optional)
   * @param  number   $unlock_time   UNIX time or block height to unlock output  (optional)
   * @param  boolean  $do_not_relay  Do not relay transaction                    (optional)
   * 
   *   OR
   * 
   * @param  object  $params         Array containing any of the options listed above, where only amount and address are required
   *
   * @return object  Example: {
   *   "amount": "1000000000000",
   *   "fee": "1000020000",
   *   "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
   *   "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
   * }
   *
   */
  public function sweep_single($key_image, $address, $payment_id = '', $mixin = 6, $priority = 2, $below_amount = 0, $unlock_time = 0, $do_not_relay = 0)
  {
    if (is_array($key_image)) { // Parameters passed in as object
      $params = $key_image;

      if (array_key_exists('key_image', $params)) {
        $key_image = $params['key_image'];
      } else {
        throw new Exception('Error: Key image required');
      }
      if (array_key_exists('address', $params)) {
        $address = $params['address'];
      } else {
        throw new Exception('Error: Address required');
      }

      if (array_key_exists('payment_id', $params)) {
        $payment_id = $params['payment_id'];
      }
      if (array_key_exists('mixin', $params)) {
        $mixin = $params['mixin'];
      }
      if (array_key_exists('account_index', $params)) {
        $account_index = $params['account_index'];
      }
      if (array_key_exists('priority', $params)) {
        $priority = $params['priority'];
      }
      if (array_key_exists('unlock_time', $params)) {
        $unlock_time = $params['unlock_time'];
      }
      if (array_key_exists('unlock_time', $params)) {
        $unlock_time = $params['unlock_time'];
      }
      if (array_key_exists('below_amount', $params)) {
        $below_amount = $params['below_amount'];

        // Convert from moneroj to tacoshi (piconero)
        $new_below_amount = $below_amount * 1000000000000;
      }
      if (array_key_exists('do_not_relay', $params)) {
        $do_not_relay = $params['do_not_relay'];
      }
    } else { // Legacy parameters used
      // Convert from moneroj to tacoshi (piconero)
      $new_below_amount = $below_amount * 1000000000000;
    }

    $sweep_single_parameters = array('address' => $address, 'mixin' => $mixin, 'get_tx_key' => true, 'account_index' => $account_index, 'payment_id' => $payment_id, 'priority' => $priority, 'below_amount' => $new_below_amount, 'unlock_time' => $unlock_time, 'do_not_relay' => $do_not_relay);
    $sweep_single_method = $this->_run('sweep_single', $sweep_single_parameters);

    $save = $this->store(); // Save wallet state after transfer

    return $sweep_single_method;
  }
  
  /**
   *
   * Relay a transaction
   *
   * @param  string  $hex  Transaction blob to relay
   *
   * @return object  // TODO example
   *
   */
  public function relay_tx($hex)
  {
    $relay_tx_parameters = array('hex' => $hex);
    $relay_tx_method = $this->_run('relay_tx_method', $relay_tx_parameters);

    $save = $this->store(); // Save wallet state after transaction relay

    return $this->_run('relay_tx');
  }
  
  /**
   *
   * Save wallet
   *
   */
  public function store()
  {
    return $this->_run('store');
  }
  
  /**
   *
   * Get a list of incoming payments using a given payment id
   *
   * @param  string  $payment_id  Payment ID to look up
   *
   * @return object  Example: {
   *   "payments": [{
   *     "amount": 10350000000000,
   *     "block_height": 994327,
   *     "payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030",
   *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
   *     "unlock_time": 0
   *   }]
   * }
   *
   */
  public function get_payments($payment_id)
  {
    $get_payments_parameters = array('payment_id' => $payment_id);
    return $this->_run('get_payments', $get_payments_parameters);
  }
  
  /**
   *
   * Get a list of incoming payments using a given payment ID (or a list of payments IDs) from a given height
   *
   * @param  string  $payment_id        Payment ID to look up
   * @param  string  $min_block_height  Height to begin search
   *
   * @return object  Example: {
   *   "payments": [{
   *     "amount": 10350000000000,
   *     "block_height": 994327,
   *     "payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030",
   *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
   *     "unlock_time": 0
   *   }]
   * }
   *
   */
  public function get_bulk_payments($payment_id, $min_block_height)
  {
    $get_bulk_payments_parameters = array('payment_id' => $payment_id, 'min_block_height' => $min_block_height);
    return $this->_run('get_bulk_payments', $get_bulk_payments_parameters);
  }
  
  /**
   *
   * Look up incoming transfers
   *
   * @param  string  $type             Type of transfer to look up; must be 'all', 'available', or 'unavailable' (incoming transfers which have already been spent)
   * @param  number  $account_index    Index of account to look up                                                                                                   (optional)
   * @param  string  $subaddr_indices  Comma-seperated list of subaddress indices to look up                                                                         (optional)
   *
   * @return object  Example: {
   *   "transfers": [{
   *     "amount": 10000000000000,
   *     "global_index": 711506,
   *     "spent": false,
   *     "tx_hash": "&lt;c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1&gt;",
   *     "tx_size": 5870
   *   },{
   *     "amount": 300000000000,
   *     "global_index": 794232,
   *     "spent": false,
   *     "tx_hash": "&lt;c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1&gt;",
   *     "tx_size": 5870
   *   },{
   *     "amount": 50000000000,
   *     "global_index": 213659,
   *     "spent": false,
   *     "tx_hash": "&lt;c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1&gt;",
   *     "tx_size": 5870
   *   }]
   * }
   */
  public function incoming_transfers($type = 'all', $account_index = 0, $subaddr_indices = '')
  {
    $incoming_parameters = array('transfer_type' => $type, 'account_index' => $account_index, 'subaddr_indices' => $subaddr_indices);
    return $this->_run('incoming_transfers', $incoming_parameters);
  }
  
  /**
   *
   * Query wallet key
   *
   * @param  string  $key_type  Type of key to look up;  must be 'view_key', 'spend_key', or 'mnemonic'
   *
   * @return object  Example: {
   *   "key": "7e341d..."
   * }
   *
   */
  public function query_key($key_type)
  {
    $query_key_parameters = array('key_type' => $key_type);
    return $this->_run('query_key', $query_key_parameters);
  }
  
  /**
   *
   * Look up wallet view key
   *
   * @param  none
   *
   * @return object  Example: {
   *   "key": "7e341d..."
   * }
   *
   */
  public function view_key()
  {
    $view_key_parameters = array('key_type' => 'view_key');
    return $this->_run('query_key', $view_key_parameters);
  }
  
  /**
   *
   * Look up wallet spend key
   *
   * @param  none
   *
   * @return object  Example: {
   *   "key": "2ab810..."
   * }
   *
   */
  public function spend_key()
  {
    $spend_key_parameters = array('key_type' => 'spend_key');
    return $this->_run('query_key', $spend_key_parameters);
  }
  
  /**
   *
   * Look up wallet spend key
   *
   * @param  none
   *
   * @return object  Example: {
   *   "key": "2ab810..."
   * }
   *
   */
  public function mnemonic()
  {
    $mnemonic_parameters = array('key_type' => 'mnemonic');
    return $this->_run('query_key', $mnemonic_parameters);
  }
  
  /**
   *
   * Make an integrated address from the wallet address and a payment ID
   *
   * @param  string  $payment_id  Payment ID to use when generating an integrated address  (optional)
   *
   * @return object  Example: {
   *   "integrated_address": "4BpEv3WrufwXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQQ8H2RRJveAtUeiFs6J"
   * }
   *
   */
  public function make_integrated_address($payment_id = null)
  {
    $integrate_address_parameters = array('payment_id' => $payment_id);
    return $this->_run('make_integrated_address', $integrate_address_parameters);
  }
  
  /**
   *
   * Retrieve the standard address and payment ID corresponding to an integrated address
   *
   * @param  string  $integrated_address  Integrated address to split
   *
   * @return object  Example: {
   *   "payment_id": "&lt;420fa29b2d9a49f5&gt;",
   *   "standard_address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA"
   * }
   *
   */
  public function split_integrated_address($integrated_address)
  {
 
    $split_parameters = array('integrated_address' => $integrated_address);
    return $this->_run('split_integrated_address', $split_parameters);
  }
  
  /**
   *
   * Stop the wallet, saving the state
   *
   * @param  none
   *
   * @return none
   *
   */
  public function stop_wallet()
  {
    return $this->_run('stop_wallet');
  }
  
  /**
   *
   * Rescan blockchain from scratch
   *
   * @param  none
   *
   * @return none
   *
   */
  public function rescan_blockchain()
  {
    return $this->_run('rescan_blockchain');
  }
  
  /**
   *
   * Set arbitrary string notes for transactions
   *
   * @param  array  $txids  Array of transaction IDs (strings) to apply notes to
   * @param  array  $notes  Array of notes (strings) to add
   *
   * @return none
   *
   */
  public function set_tx_notes($txids, $notes)
  {
    $notes_parameters = array('txids' => $txids, 'notes' => $notes);
    return $this->_run('set_tx_notes', $notes_parameters);
  }
  
  /**
   *
   * Get string notes for transactions
   *
   * @param  array  $txids  Array of transaction IDs (strings) to look up
   *
   * @return none
   *
   */
  public function get_tx_notes($txids)
  {
    $notes_parameters = array('txids' => $txids);
    return $this->_run('get_tx_notes', $notes_parameters);
  }
  
  /**
   *
   * Set an option in the wallet
   *
   * @param  string  $key    Option to set
   * @param  string  $value  Value to set
   *
   * @return none
   *
   */
  public function set_attribute($key, $value)
  {
    $set_attribute_parameters = array('key' => $key, 'value' => $value);
    return $this->_run('set_attribute', $set_attribute_parameters);
  }
  
  /**
   *
   * Get a wallet option
   *
   * @param  string  $key  Wallet option to query
   *
   * @return object  Example: {
   *   // TODO example
   * }
   *
   */
  public function get_attribute($key)
  {
    $get_attribute_parameters = array('key' => $key);
    return $this->_run('get_attribute', $get_attribute_parameters);
  }
  
  /**
   *
   * Get a transaction key
   *
   * @param   string  $txid  Transaction ID
   *
   * @return  object  Example: {
   *   "tx_key": "e8e97866b1606bd87178eada8f995bf96d2af3fec5db0bc570a451ab1d589b0f"
   * }
   *
   */
  public function get_tx_key($txid)
  {
    $get_tx_key_parameters = array('txid' => $txid);
    return $this->_run('get_tx_key', $get_tx_key_parameters);
  }
  
  /**
   *
   * Check a transaction key
   *
   * @param   string  $address  Address that sent transfer
   * @param   string  $txid     Transaction ID
   * @param   string  $tx_key   Transaction key
   *
   * @return  object  Example: {
   *   "confirmations": 1,
   *   "in_pool": ,
   *   "received": 0
   * }
   *
   */
  public function check_tx_key($address, $txid, $tx_key)
  {
    $check_tx_key_parameters = array('address' => $address, 'txid' => $txid, 'tx_key' => $tx_key);
    return $this->_run('check_tx_key', $check_tx_key_parameters);
  }
  
  /**
   *
   * Get proof (signature) of transaction
   *
   * @param  string  $address  Address that spent funds
   * @param  string  $txid     Transaction ID
   *
   * @return object  Example: {
   *   "signature": "InProofV1Lq4nejMXxMnAdnLeZhHe3FGCmFdnSvzVM1AiGcXjngTRi4hfHPcDL9D4th7KUuvF9ZHnzCDXysNBhfy7gFvUfSbQWiqWtzbs35yUSmtW8orRZzJpYKNjxtzfqGthy1U3puiF"
   * }
   *
   */
  public function get_tx_proof($address, $txid)
  {
    $get_tx_proof_parameters = array('address' => $address, 'txid' => $txid);
    return $this->_run('get_tx_proof', $get_tx_proof_parameters);
  }
  
  /**
   *
   * Verify transaction poof
   *
   * @param  string  $address    Address that spent funds
   * @param  string  $txid       Transaction ID
   * @param  string  $signature  Signature (tx_proof)
   *
   * @return   Example: {
   *   "confirmations": 2,
   *   "good": 1,
   *   "in_pool": ,
   *   "received": 15752471409492,
   * }
   *
   */
  public function check_tx_proof($address, $txid, $signature)
  {
    $check_tx_proof_parameters = array('address' => $address, 'txid' => $txid, 'signature' => $signature);
    return $this->_run('check_tx_proof', $check_tx_proof_parameters);
  }
  
  /**
   *
   * Get proof of a spend
   *
   * @param  string  $txid  Transaction ID
   *
   * @return object  Example: {
   *   "signature": "SpendProofV1RnP6ywcDQHuQTBzXEMiHKbe5ErzRAjpUB1h4RUMfGPNv4bbR6V7EFyiYkCrURwbbrYWWxa6Kb38ZWWYTQhr2Y1cRHVoDBkK9GzBbikj6c8GWyKbu3RKi9hoYp2fA9zze7UEdeNrYrJ3tkoE6mkR3Lk5HP6X2ixnjhUTG65EzJgfCS4qZ85oGkd17UWgQo6fKRC2GRgisER8HiNwsqZdUTM313RmdUX7AYaTUNyhdhTinVLuaEw83L6hNHANb3aQds5CwdKCUQu4pkt5zn9K66z16QGDAXqL6ttHK6K9TmDHF17SGNQVPHzffENLGUf7MXqS3Pb6eijeYirFDxmisZc1n2mh6d5EW8ugyHGfNvbLEd2vjVPDk8zZYYr7NyJ8JjaHhDmDWeLYy27afXC5HyWgJH5nDyCBptoCxxDnyRuAnNddBnLsZZES399zJBYHkGb197ZJm85TV8SRC6cuYB4MdphsFdvSzygnjFtbAcZWHy62Py3QCTVhrwdUomAkeNByM8Ygc1cg245Se1V2XjaUyXuAFjj8nmDNoZG7VDxaD2GT9dXDaPd5dimCpbeDJEVoJXkeEFsZF85WwNcd67D4s5dWySFyS8RbsEnNA5UmoF3wUstZ2TtsUhiaeXmPwjNvnyLif3ASBmFTDDu2ZEsShLdddiydJcsYFJUrN8L37dyxENJN41RnmEf1FaszBHYW1HW13bUfiSrQ9sLLtqcawHAbZWnq4ZQLkCuomHaXTRNfg63hWzMjdNrQ2wrETxyXEwSRaodLmSVBn5wTFVzJe5LfSFHMx1FY1xf8kgXVGafGcijY2hg1yw8ru9wvyba9kdr16Lxfip5RJGFkiBDANqZCBkgYcKUcTaRc1aSwHEJ5m8umpFwEY2JtakvNMnShjURRA3yr7GDHKkCRTSzguYEgiFXdEiq55d6BXDfMaKNTNZzTdJXYZ9A2j6G9gRXksYKAVSDgfWVpM5FaZNRANvaJRguQyqWRRZ1gQdHgN4DqmQ589GPmStrdfoGEhk1LnfDZVwkhvDoYfiLwk9Z2JvZ4ZF4TojUupFQyvsUb5VPz2KNSzFi5wYp1pqGHKv7psYCCodWdte1waaWgKxDken44AB4k6wg2V8y1vG7Nd4hrfkvV4Y6YBhn6i45jdiQddEo5Hj2866MWNsdpmbuith7gmTmfat77Dh68GrRukSWKetPBLw7Soh2PygGU5zWEtgaX5g79FdGZg"
   * }
   *
   */
  public function get_spend_proof($txid)
  {
    $get_spend_proof_parameters = array('txid' => $txid);
    return $this->_run('get_spend_proof', $get_spend_proof_parameters);
  }
  
  /**
   *
   * Verify spend proof
   *
   * @param  string  $txid       Transaction ID
   * @param  string  $signature  Spend proof to verify
   *
   * @return object  Example: {
   *   "good": 1
   * }
   *
   */
  public function check_spend_proof($txid, $signature)
  {
    $check_spend_proof_parameters = array('txid' => $txid, 'signature' => $signature);
    return $this->_run('check_spend_proof', $check_spend_proof_parameters);
  }
  
  /**
   *
   * Get proof of reserves
   *
   * @param  string  $account_index  Comma-separated list of account indices of which to prove reserves.  If empty, proves reserve of all accounts  (optional)
   *
   * @return   Example: {
   *   "signature": "ReserveProofV11BZ23sBt9sZJeGccf84mzyAmNCP3KzYbE111111111111AjsVgKzau88VxXVGACbYgPVrDGC84vBU61Gmm2eiYxdZULAE4yzBxT1D9epWgCT7qiHFvFMbdChf3CpR2YsZj8CEhp8qDbitsfdy7iBdK6d5pPUiMEwCNsCGDp8AiAc6sLRiuTsLEJcfPYEKe"
   * }
   *
   */
  public function get_reserve_proof($account_index = 'all')
  {
    if ($account_index == 'all') {
      $check_spend_proof_parameters = array('all' => true);
    } else {
      $check_spend_proof_parameters = array('account_index' => $account_index);
    }

    return $this->_run('get_reserve_proof');
  }
  
  /**
   *
   * Verify a reserve proof
   *
   * @param  string  $address    Wallet address
   * @param  string  $signature  Reserve proof
   *
   * @return object  Example: {
   *   "good": 1,
   *   "spent": 0,
   *   "total": 0
   * }
   *
   */
  public function check_reserve_proof($address, $signature)
  {
    $check_reserve_proof_parameters = array('address' => $address, 'signature' => $signature);
    return $this->_run('check_reserve_proof', $check_reserve_proof_parameters);
  }
  
  /**
   *
   * Look up transfers
   *
   * @param  array   $input_types      Array of transfer type strings; possible values include 'all', in', 'out', 'pending', 'failed', and 'pool'  (optional)
   * @param  number  $account_index    Index of account to look up                                                                                 (optional)
   * @param  string  $subaddr_indices  Comma-seperated list of subaddress indices to look up                                                       (optional)
   * @param  number  $min_height       Minimum block height to use when looking up transfers                                                       (optional)
   * @param  number  $max_height       Maximum block height to use when looking up transfers                                                       (optional)
   *
   *   OR
   *
   * @param  object  $inputs_types      Array containing any of the options listed above, where only input types is required
   *
   * @return object  Example: {
   *   "pool": [{
   *     "amount": 500000000000,
   *     "fee": 0,
   *     "height": 0,
   *     "note": "",
   *     "payment_id": "758d9b225fda7b7f",
   *     "timestamp": 1488312467,
   *     "txid": "da7301d5423efa09fabacb720002e978d114ff2db6a1546f8b820644a1b96208",
   *     "type": "pool"
   *   }]
   * }
   *
   */
  public function get_transfers($input_types = ['all'], $account_index = 0, $subaddr_indices = '', $min_height = 0, $max_height = 4206931337)
  {
    if (is_string($input_types)) { // If user is using old method
      $get_transfers_parameters = array($input_type => $account_index); // $get_transfers_parameters = array($input_type => $input_value);
    } else {
      if (is_object($input_types)) { // Parameters passed in as object
        $params = $input_types;

        if (array_key_exists('input_types', $params)) {
          $input_types = $params['input_types'];
        } else {
          $input_types = ['all'];
        }
        if (array_key_exists('account_index', $params)) {
          $account_index = $params['account_index'];
        }
        if (array_key_exists('subaddr_indices', $params)) {
          $subaddr_indices = $params['subaddr_indices'];
        }
        if (array_key_exists('min_height', $params)) {
          $min_height = $params['min_height'];
        }
        if (array_key_exists('max_height', $params)) {
          $max_height = $params['max_height'];
        }
      }

      $get_transfers_parameters = array('account_index' => $account_index, 'subaddr_indices' => $subaddr_indices, 'min_height' => $min_height, 'max_height' => $max_height);
      for ($i = 0; $i < count($input_types); $i++) {
        $get_transfers_parameters[$input_types[$i]] = true;
      }
    }

    if (($min_height || $max_height) && $max_height != 4206931337) {
      $get_transfers_parameters['filter_by_height'] = true;
    }

    return $this->_run('get_transfers', $get_transfers_parameters);
  }
  
  /**
   *
   * Show information about a transfer with a given transaction ID
   *
   * @param  string  $txid           Transaction ID to look up
   * @param  string  $account_index  Index of account to query  (optional)
   *
   * @return object  Example: {
   *   "transfer": {
   *     "amount": 10000000000000,
   *     "fee": 0,
   *     "height": 1316388,
   *     "note": "",
   *     "payment_id": "0000000000000000",
   *     "timestamp": 1495539310,
   *     "txid": "f2d33ba969a09941c6671e6dfe7e9456e5f686eca72c1a94a3e63ac6d7f27baf",
   *     "type": "in"
   *   }
   * }
   *
   */
  public function get_transfer_by_txid($txid, $account_index = 0)
  {
    $get_transfer_by_txid_parameters = array('txid' => $txid, 'account_index' => $account_index);
    return $this->_run('get_transfer_by_txid', $get_transfer_by_txid_parameters);
  }
  
  /**
   *
   * Sign a string
   *
   * @param  string  $data  Data to sign
   *
   * @return object  Example: {
   *   "signature": "SigV1Xp61ZkGguxSCHpkYEVw9eaWfRfSoAf36PCsSCApx4DUrKWHEqM9CdNwjeuhJii6LHDVDFxvTPijFsj3L8NDQp1TV"
   * }
   *
   */
  public function sign($data)
  {
 
    $sign_parameters = array('string' => $data);
    return $this->_run('sign',$sign_parameters);
  }
  
  /**
   *
   * Verify a signature on a string
   *
   * @param  string   $data       Signed data
   * @param  string   $address    Address that signed data
   * @param  string   $signature  Signature to verify
   *
   * @return boolean  $good       Verification status
   * 
   */
  public function verify($data, $address, $signature)
  {
    $notes_parameters = array('data' => $data, 'address' => $address, 'signature' => $signature);
    return $this->_run('verify', $notes_parameters);
  }
  
  /**
   *
   * Export a signed set of key images
   *
   * @param  none
   *
   * @return  array  $signed_key_images  Array of signed key images
   *
   */
  public function export_key_images()
  {
    return $this->_run('export_key_images');
  }
  
  /**
   *
   * Import a signed set of key images
   *
   * @param  array   $signed_key_images  Array of signed key images
   *
   * @return number  $height
   * @return number  $spent
   * @return number  $unspent
   * 
   */
  public function import_key_images($signed_key_images)
  {
    $import_parameters = array('signed_key_images' => $signed_key_images);
    return $this->_run('import_key_images', $import_parameters);
  }

  /**
   *
   * Create a payment URI using the official URI spec
   *
   * @param  string  $address         Address to include
   * @param  string  $amount          Amount to request
   * @param  string  $recipient_name  Name of recipient    (optional)
   * @param  string  $tx_description     Payment description  (optional)
   *
   * @return object  Example: 
   *
   */
  public function make_uri($address, $amount, $recipient_name = null, $tx_description = null)
  {
    // Convert from moneroj to tacoshi (piconero)
    $new_amount = $amount * 1000000000000;
       
    $uri_parameters = array('address' => $address, 'amount' => $new_amount, 'payment_id' => '', 'recipient_name' => $recipient_name, 'tx_description' => $tx_description);
    return $this->_run('make_uri', $uri_parameters);
  }

  /**
   *
   * Parse a payment URI to get payment information
   *
   * @param  string  $uri  Payment URI
   *
   * @return object  Example: {
   *   "uri": {
   *     "address": "44AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VBBEt7f2wfn3RVGQBEP3A",
   *     "amount": 10,
   *     "payment_id": "0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef",
   *     "recipient_name": "Monero Project donation address",
   *     "tx_description": "Testing out the make_uri function
   "
   *   }
   * }
   *
   */
  public function parse_uri($uri)
  {
    $uri_parameters = array('uri' => $uri);
    return $this->_run('parse_uri', $uri_parameters);
  }
  
  /**
   *
   * Retrieve entries from the address book
   *
   * @param  array   $entries  Array of indices to return from the address book
   *
   * @return array   $entries  Array of entries returned from the address book
   * 
   */
  public function get_address_book($entries)
  {
    $entries_parameters = array('entries' => $entries);
    return $this->_run('get_address_book', $entries_parameters);
  }
  
  /**
   *
   * Retrieve entries from the address book
   *
   * @param  string  $address      Address to add to address book
   * @param  string  $payment_id   Payment ID to use with address in address book  (optional)
   * @param  string  $description  Description of address                          (optional)
   *
   * @return number  $index        Index of address in address book
   * 
   */
  public function add_address_book($address, $payment_id, $description)
  {
    $address_parameters = array('address' => $address, 'payment_id' => $payment_id, 'description' => $description);
    return $this->_run('add_address_book', $address_parameters);
  }
  
  /**
   *
   * Delete an entry from the address book
   *
   * @param  array   $index  Index of the address book entry to remove
   *
   * @return none
   * 
   */
  public function delete_address_book($index)
  {
    $delete_parameters = array('index' => $index);
    return $this->_run('delete_address_book', $delete_parameters);
  }
  
  /**
   *
   * Rescan the blockchain for spent outputs
   * 
   */
  public function rescan_spent()
  {
    return $this->_run('rescan_spent');
  }
  
  /**
   *
   * Start mining in the Monero daemon
   *
   * @param  number   $threads_count         Number of threads with which to mine
   * @param  boolean  $do_background_mining  Mine in backgound?
   * @param  boolean  $ignore_battery        Ignore battery?  
   *
   * @return none
   * 
   */
  public function start_mining($threads_count, $do_background_mining, $ignore_battery)
  {
    $mining_parameters = array('threads_count' => $threads_count, 'do_background_mining' => $do_background_mining, 'ignore_battery' => $ignore_battery);
    return $this->_run('start_mining', $mining_parameters);
  }
  
  /**
   *
   * Stop mining
   *
   * @param  none
   *
   * @return none
   * 
   */
  public function stop_mining()
  {
    return $this->_run('stop_mining');
  }
  
  /**
   *
   * Get a list of available languages for your wallet's seed
   *
   * @param  none
   * 
   * @return array  List of available languages
   *
   */
  public function get_languages()
  {
    return $this->_run('get_languages');
  }
  
  /**
   *
   * Create a new wallet
   *
   * @param  string  $filename  Filename to use for new wallet
   * @param  string  $password  Password to use for new wallet
   *
   * @return none
   *
   */
  public function create_wallet($filename = 'monero_wallet', $password = null)
  {
    $create_wallet_parameters = array('filename' => $filename, 'password' => $password, 'language' => 'English');
    return $this->_run('create_wallet', $create_wallet_parameters);
  }
  
  /**
   *
   * Open a wallet
   *
   * @param  string  $filename  Filename to use for new wallet
   * @param  string  $password  Password to use for new wallet
   *
   * @return none
   *
   */
  public function open_wallet($filename = 'monero_wallet', $password = null)
  {
    $open_wallet_parameters = array('filename' => $filename, 'password' => $password);
    return $this->_run('open_wallet',$open_wallet_parameters);
  }
  
  /**
   *
   * Check if wallet is multisig
   *
   * @param  none
   *
   * @return object  Example: (non-multisignature wallet) {
   *   "multisig": ,
   *   "ready": ,
   *   "threshold": 0,
   *   "total": 0
   * } // TODO multisig wallet example
   *
   */
  public function is_multisig()
  {
    return $this->_run('is_multisig');
  }
  
  /**
   *
   * Get information needed to create a multisignature wallet
   *
   * @param  none
   *
   * @return object  Example: {
   *   "multisig_info": "MultisigV1WBnkPKszceUBriuPZ6zoDsU6RYJuzQTiwUqE5gYSAD1yGTz85vqZGetawVvioaZB5cL86kYkVJmKbXvNrvEz7o5kibr7tHtenngGUSK4FgKbKhKSZxVXRYjMRKEdkcbwFBaSbsBZxJFFVYwLUrtGccSihta3F4GJfYzbPMveCFyT53oK"
   * }
   *
   */
  public function prepare_multisig()
  {
    return $this->_run('prepare_multisig');
  }
  
  /**
   *
   * Make a multisig account.  Account described by multisig_info must have nonzero balance
   *
   * @param  string  $multisig_info  Multisignature information (from eg. prepare_multisig) 
   * @param  string  $threshold      Threshold required to spend from multisig
   * @param  string  $password       Passphrase to apply to multisig address
   *
   * @return object  Example: {
   *   // TODO example
   * }
   *
   */
  public function make_multisig($multisig_info, $threshold, $password = '')
  {
    $make_multisig_parameters = array('multisig_info' => $multisig_info, 'threshold' => $threshold, 'password' => $password);
    return $this->_run('make_multisig', $make_multisig_parameters);
  }
  
  /**
   *
   * Export multisignature information
   *
   * @param  none
   *
   * @return object  Example: {
   *   // TODO example
   * }
   *
   */
  public function export_multisig_info()
  {
    return $this->_run('export_multisig_info');
  }
  
  /**
   *
   * Import mutlisignature information
   *
   * @param  string  $info  Multisig info (from eg. prepare_multisig)
   *
   * @return   Example: {
   *   // TODO example
   * }
   *
   */
  public function import_multisig_info($info)
  {
    $import_multisig_info_parameters = array('info' => $info);
    return $this->_run('import_multisig_info', $import_multisig_info_parameters);
  }
  
  /**
   *
   * Finalize a multisignature wallet
   *
   * @param  string  $multisig_info  Multisig info (from eg. prepare_multisig)
   * @param  string  $password       Multisig info (from eg. prepare_multisig)
   *
   * @return   Example: {
   *   // TODO example
   * }
   *
   */
  public function finalize_multisig($multisig_info, $password = '')
  {
    $finalize_multisig_parameters = array('multisig_info' => $multisig_info, 'password' => $password);
    return $this->_run('finalize_multisig', $finalize_multisig_parameters);
  }
  
  /**
   *
   * Sign a multisignature transaction
   *
   * @param  string  $tx_data_hex  Transaction as hex blob
   *
   * @return object  Example: {
   *   // TODO example
   * }
   *
   */
  public function sign_multisig($tx_data_hex)
  {
    $sign_multisig_parameters = array('tx_data_hex' => $tx_data_hex);

    return $this->_run('sign_multisig', $sign_multisig_parameters);
  }
  
  /**
   *
   * Submit (relay) a multisignature transaction
   *
   * @param  string  $tx_data_hex  Transaction as hex blob
   *
   * @return   Example: {
   *   // TODO example
   * }
   *
   */
  public function submit_multisig($tx_data_hex)
  {
    $submit_multisig_parameters = array('tx_data_hex' => $tx_data_hex);
    return $this->_run('submit_multisig', $submit_multisig_parameters);
  }

}

?>
