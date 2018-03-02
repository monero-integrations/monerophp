<?php
/**
 * 
 * monerophp/daemonRPC
 * 
 * A class for making calls to Monero's RPC API using PHP.
 * https://github.com/monero-integrations/monerophp
 *
 * Using work from
 *   CryptoChangements (Daemon_RPC) <https://github.com/cryptochangements34>
 *   Serhack (Monero Integrations) <https://github/serhack>
 *   TheKoziTwo (xmr-integration) <thekozitwo@gmail.com>
 *   Andrew LeCody (EasyBitcoin-PHP)
 *   Kacper Rowinski (jsonRPCClient) <krowinski@implix.com>
 * 
 * @author     Monero Integrations Team <https://github.com/monero-integrations>
 * @copyright  2018
 * @license    MIT
 *  
 * ============================================================================
 * 
 * // Initialize Monero connection/object
 * $daemonRPC = new daemonRPC();
 * 
 * // Examples:
 * $height = $daemonRPC->getblockcount();
 * $block = $daemonRPC->getblock_by_height(1);
 * 
 */

require_once('jsonRPCClient.php');

class daemonRPC {
  private $client;

  private $protocol;
  private $host;
  private $port;
  private $url;
  private $user;
  private $password;

  /**
   *
   * Start a connection with the Monero daemon.
   * 
   * @param  string  $host      IP address of Monero daemon to connect to  (optional)
   * @param  int     $port      Port to use when accessing Monero daemon   (optional)
   * @param  string  $protocol  Protocol to acces daemon over (eg. 'http') (optional)
   * @param  string  $user      Username                                   (optional)
   * @param  string  $password  Password                                   (optional)
   *
   */
  function __construct($host = '127.0.0.1', $port = 18081, $protocol = 'http', $user = null, $password = null) {
    // TODO input validation
    
    $this->host = $host;
    $this->port = $port;
    $this->protocol = $protocol; // TODO: validate protocol (http, https, etc.)
    $this->user = $user;
    $this->password = $password;

    $this->url = $protocol.'://'.$host.':'.$port.'/json_rpc';
    $this->client = new jsonRPCClient($this->url, $this->user, $this->password);
  }

  /**
   *
   * Execute command on the Monero RPC API.
   *
   * @param  string  $method  RPC method to call
   * @param  string  $params  Options to include (optional)
   *
   * @return string  Call result
   *
   */
  protected function _run($method, $params = null) {
    // TODO input validation
    
    return $this->client->_run($method, $params);
  }

  /**
   *
   * Look up how many blocks are in the longest chain known to the node.
   *
   * @return object  Example: {  
   *   "count": 993163,  
   *   "status": "OK"  
   * }  
   *
   */
  public function getblockcount() {
    return $this->_run('getblockcount');
  }

  /**
   *
   * Look up a block's hash by its height.
   *
   * @return string  Example: 'e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6'
   *
   */
  public function on_getblockhash() {
    return $this->_run('on_getblockhash');
  }

  /**
   *
   * Retrieve a block template that can be mined upon.
   *
   * @param  string  $wallet_address  Address of wallet to receive coinbase transactions if block is successfully mined
   * @param  int     $reserve_size   Reserve size 
   *
   * @return object  Example: {
   *   "blocktemplate_blob": "01029af88cb70568b84a11dc9406ace9e635918ca03b008f7728b9726b327c1b482a98d81ed83000000000018bd03c01ffcfcf3c0493d7cec7020278dfc296544f139394e5e045fcda1ba2cca5b69b39c9ddc90b7e0de859fdebdc80e8eda1ba01029c5d518ce3cc4de26364059eadc8220a3f52edabdaf025a9bff4eec8b6b50e3d8080dd9da417021e642d07a8c33fbe497054cfea9c760ab4068d31532ff0fbb543a7856a9b78ee80c0f9decfae01023ef3a7182cb0c260732e7828606052a0645d3686d7a03ce3da091dbb2b75e5955f01ad2af83bce0d823bf3dbbed01ab219250eb36098c62cbb6aa2976936848bae53023c00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000001f12d7c87346d6b84e17680082d9b4a1d84e36dd01bd2c7f3b3893478a8d88fb3",
   *   "difficulty": 982540729,
   *   "height": 993231,
   *   "prev_hash": "68b84a11dc9406ace9e635918ca03b008f7728b9726b327c1b482a98d81ed830",
   *   "reserved_offset": 246,
   *   "status": "OK"
   * }
   *
   */
  public function getblocktemplate($wallet_address, $reserve_size) {
    // TODO full input validation
    
    if (!isset($wallet_address)) {
      throw new Exception('Error: Wallet address required');
    }
    if (!isset($reserve_size)) {
      throw new Exception('Error: Reserve size required');
    }
    
    $params = array('wallet_address' => $wallet_address, 'reserve_size' => $reserve_size);

    return $this->client->_run('getblocktemplate', $params);
  }

  /**
   *
   * Submit a mined block to the network.
   *
   * @param  string  $block  Block blob data string
   *
   * @return string  // TODO: example
   *
   */
  public function submitblock() {
    return $this->_run('submitblock');
  }

  /**
   *
   * Block header information for the most recent block is easily retrieved with this method.
   *
   * @return object  Example: {
   *   "block_header": {
   *     "depth": 0,
   *     "difficulty": 746963928,
   *     "hash": "ac0f1e226268d45c99a16202fdcb730d8f7b36ea5e5b4a565b1ba1a8fc252eb0",
   *     "height": 990793,
   *     "major_version": 1,
   *     "minor_version": 1,
   *     "nonce": 1550,
   *     "orphan_status": false,
   *     "prev_hash": "386575e3b0b004ed8d458dbd31bff0fe37b280339937f971e06df33f8589b75c",
   *     "reward": 6856609225169,
   *     "timestamp": 1457589942
   *   },
   *   "status": "OK"
   * }
   *
   */
  public function getlastblockheader() {
    return $this->_run('getlastblockheader');
  }

  /**
   *
   * Block header information can be retrieved using either a block's hash or height.
   *
   * @param  string  $hash  The block's SHA256 hash
   *
   * @return object  Example: {
   *   "block_header": {
   *     "depth": 78376,
   *     "difficulty": 815625611,
   *     "hash": "e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6",
   *     "height": 912345,
   *     "major_version": 1,
   *     "minor_version": 2,
   *     "nonce": 1646,
   *     "orphan_status": false,
   *     "prev_hash": "b61c58b2e0be53fad5ef9d9731a55e8a81d972b8d90ed07c04fd37ca6403ff78",
   *     "reward": 7388968946286,
   *     "timestamp": 1452793716
   *   },
   *   "status": "OK"
   * }
   *
   */
  public function getblockheaderbyhash($hash) {
    // TODO input validation
    
    return $this->_run('getlastblockheader', $hash);
  }

  /**
   *
   * Similar to getblockheaderbyhash() above, this method includes a block's height as an input parameter to retrieve basic information about the block.
   *
   * @param  int     $height  The block's height
   *
   * @return object  Example: {
   *   "block_header": {
   *     "depth": 78376,
   *     "difficulty": 815625611,
   *     "hash": "e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6",
   *     "height": 912345,
   *     "major_version": 1,
   *     "minor_version": 2,
   *     "nonce": 1646,
   *     "orphan_status": false,
   *     "prev_hash": "b61c58b2e0be53fad5ef9d9731a55e8a81d972b8d90ed07c04fd37ca6403ff78",
   *     "reward": 7388968946286,
   *     "timestamp": 1452793716
   *   },
   *   "status": "OK"
   * }
   *
   */
  public function getblockheaderbyheight($height) {
    // TODO full input validation

    if (!isset($height)) {
      throw new Exception('Error: Block height required');
    }
    
    return $this->_run('getblockheaderbyheight', $height);
  }

  /**
   *
   * Get block information by its SHA256 hash.
   *
   * @param  string  The block's SHA256 hash
   *
   * @return object  Example: {
   *   "blob": "...",
   *   "block_header": {
   *     "depth": 12,
   *     "difficulty": 964985344,
   *     "hash": "510ee3c4e14330a7b96e883c323a60ebd1b5556ac1262d0bc03c24a3b785516f",
   *     "height": 993056,
   *     "major_version": 1,
   *     "minor_version": 2,
   *     "nonce": 2036,
   *     "orphan_status": false,
   *     "prev_hash": "0ea4af6547c05c965afc8df6d31509ff3105dc7ae6b10172521d77e09711fd6d",
   *     "reward": 6932043647005,
   *     "timestamp": 1457720227
   *   },
   *   "json": "...",
   *   "status": "OK"
   * }
   *
   */
  public function getblock_by_hash($hash) {
    // TODO input validation
    
    return $this->_run('getblock', $hash);
  }

  /**
   *
   * Get block information by its height.
   *
   * @param  int     $height  The block's height
   *
   * @return object  Example: {
   *   "blob": "...",
   *   "block_header": {
   *     "depth": 80694,
   *     "difficulty": 815625611,
   *     "hash": "e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6",
   *     "height": 912345,
   *     "major_version": 1,
   *     "minor_version": 2,
   *     "nonce": 1646,
   *     "orphan_status": false,
   *     "prev_hash": "b61c58b2e0be53fad5ef9d9731a55e8a81d972b8d90ed07c04fd37ca6403ff78",
   *     "reward": 7388968946286,
   *     "timestamp": 1452793716
   *   },
   *   "json": "...",
   *   "status": "OK"
   * }
   *
   */
  public function getblock_by_height($height) {
    // TODO input validation
    
    $heightString = (string) $height; // Cast input to string
    return $this->_run('getblock', $heightString);
  }

  /**
   *
   * Retrieve information about incoming and outgoing connections to your node.
   *
   * @return object  Example: {
   *   "connections": [{
   *     "avg_download": 0,
   *     "avg_upload": 0,
   *     "current_download": 0,
   *     "current_upload": 0,
   *     "incoming": false,
   *     "ip": "76.173.170.133",
   *     "live_time": 1865,
   *     "local_ip": false,
   *     "localhost": false,
   *     "peer_id": "3bfe29d6b1aa7c4c",
   *     "port": "18080",
   *     "recv_count": 116396,
   *     "recv_idle_time": 23,
   *     "send_count": 176893,
   *     "send_idle_time": 1457726610,
   *     "state": "state_normal"
   *   },{
   *   ...
   *   }],
   *   "status": "OK"
   * }
   *
   */
  public function get_connections() {
    return $this->_run('get_connections');
  }

  /**
   *
   * Retrieve general information about the state of your node and the network.
   *
   * @return object  Example: {
   *   "alt_blocks_count": 5,
   *   "difficulty": 972165250,
   *   "grey_peerlist_size": 2280,
   *   "height": 993145,
   *   "incoming_connections_count": 0,
   *   "outgoing_connections_count": 8,
   *   "status": "OK",
   *   "target": 60,
   *   "target_height": 993137,
   *   "testnet": false,
   *   "top_block_hash": "",
   *   "tx_count": 564287,
   *   "tx_pool_size": 45,
   *   "white_peerlist_size": 529
   * }
   *
   */
  public function get_info() {
    return $this->_run('get_info');
  }

  /**
   *
   * Look up information regarding hard fork voting and readiness.
   *
   * @return object  Example: {
   *   "earliest_height": 1009827,
   *   "enabled": false,
   *   "state": 2,
   *   "status": "OK",
   *   "threshold": 0,
   *   "version": 1,
   *   "votes": 7277,
   *   "voting": 2,
   *   "window": 10080
   * }
   *
   */
  public function hardfork_info() {
    return $this->_run('hard_fork_info');
  }

  /**
   *
   * Ban another node by IP.
   *
   * @return object  Example: {
   *   "status": "OK"
   * }
   *
   */
  public function setbans($ip) {
    // TODO full input validation

    if (!isset($ip)) {
      throw new Exception('Error: IP address required');
    }

    return $this->_run('setbans', $ip);
  }

  /**
   *
   * Get list of banned IPs.
   *
   * @return object  Example: {
   *   "bans": [{
   *     "ip": 838969536,
   *     "seconds": 1457748792
   *   }],
   *   "status": "OK"
   * }
   *
   */
  public function getbans() {
    return $this->run('getbans');
  }

  /**
   *
   * Get the node's current height.
   *
   * @return object  Example: {
   *   "height": 993488,
   *   "status": "OK"
   * }
   *
   */
  public function getheight() {
    return $this->_run('/getheight');
  }

  /**
   *
   * Look up one or more transactions by hash.  If set to decode as JSON, will return field 'element_as_json'
   *
   * @param  array    $txs_hashes      An array of transactions hashes
   * @param  boolean  $decode_as_json  Decode as JSON rather than binary  (optional)
   *
   * @return object  Example: {
   *   "status": "OK",
   *   "txs_as_hex": ["..."]
   * }
   *
   */
  public function gettransactions($txs_hashes, $decode_as_json = false) {
    // TODO full input validation

    if (!isset($txs_hashes)) {
      throw new Exception('Error: Transaction hash(es) required');
    }

    $params = array('txs_hashes' => $txs_hashes, 'decode_as_json' => $decode_as_json);

    return $this->_run('/gettransactions', $params);
  }

  /**
   *
   * Check if outputs have been spent using the key image associated with the output.
   *
   * @param  array   $key_images  List of key image hex strings to check.
   *
   * @return object  Example: {
   *   "spent_status": [1,2],
   *   "status": "OK"
   * }
   *
   */
  public function is_key_image_spent($key_images) {
    // TODO full input validation

    if (!isset($key_images)) {
      throw new Exception('Error: Key image(s) required');
    }

    $params = array('key_images' => $key_images);

    return $this->_run('/is_key_image_spent', $params);
  }

  /**
   *
   * Broadcast a raw transaction to the network.
   *
   * @param  string  $tx_as_hex  Full transaction information as hexidecimal string.
   *
   * @return object  // TODO: example result
   *
   */
  public function sendrawtransaction($tx_as_hex) {
    // TODO full input validation

    if (!isset($tx_as_hex)) {
      throw new Exception('Error: Transaction required');
    }

    $params = array('tx_as_hex' => $tx_as_hex);

    return $this->_run('/sendrawtransaction', $params);
  }

  /**
   *
   * Show information about valid transactions seen by the node but not yet mined into a block, as well as spent key image information in the node's memory.
   *
   * @return object  Example: {
   *   "spent_key_images": [{
   *     "id_hash": "1edb9ecc39602040282d326070ad22cb473c952c0d6280c9c4c3b853fb34f3bc",
   *     "txs_hashes": ["409911b2be02e3f0e930b326c67ab9e74675885ce23d71bb3bd28b62bc3e7803"]
   *   },{
   *     "id_hash": "4adb4bb63b3397027340ca4e6c45f4ce2147dfb3a4e0fafdec18834ae594a05e",
   *     "txs_hashes": ["946f1f4c52e3426a41959c93b60078f314813bc4bdebcf69b8ee11d593b2bd14"]
   **   },
   *   ...],
   *   "status": "OK",
   *   "transactions": [{
   *     "blob_size": 25761,
   *     "fee": 290000000000,
   *     "id_hash": "11d4cff23e610fac6a2b89187ad61d429a5e226652693dcac5d83d506eb92b96",
   *     "kept_by_block": false,
   *     "last_failed_height": 0,
   *     "last_failed_id_hash": "0000000000000000000000000000000000000000000000000000000000000000",
   *     "max_used_block_height": 954508,
   *     "max_used_block_id_hash": "03f96b374778bc059e47b96e2beec2e6d4d9e0ad39afeabdbcd77e1bd5a62f81",
   *     "receive_time": 1457676127,
   *     "tx_json": "{\n  \"version\": 1, \n  \"unlock_time\": 0, \n  \"vin\": [ {\n      \"key\": {\n        \"amount\": 70000000000, \n        \"key_offsets\": [ 63408, 18978, 78357, 16560\n        ], \n        \"k_image\": \"7319134bfc50668251f5b899c66b005805ee255c136f0e1cecbb0f3a912e09d4\"\n      }\n    },  ...  ], \n  \"vout\": [ {\n      \"amount\": 80000000000, \n      \"target\": {\n        \"key\": \"094e6a1b187385533665f89db741149f42d95fdc50bdd2a2384bcd1dc5209c55\"\n      }\n    },  ...  ], \n  \"extra\": [ 2, 33, 0, 15, 56, 190, 21, 169, 77, 13, 182, 209, 51, 35, 54, 96, 89, 237, 96, 23, 24, 107, 240, 79, 40, 86, 64, 68, 45, 166, 119, 192, 17, 225, 23, 1, 31, 159, 145, 15, 173, 255, 165, 192, 55, 84, 127, 154, 163, 25, 85, 204, 212, 127, 147, 133, 118, 218, 166, 52, 78, 188, 131, 235, 9, 159, 105, 158\n  ], \n  \"signatures\": [ \"966e5a67fbdbf72d7dc0364b705121a58e0ced7e2ab45747b6b154c05a1afe04fac4aac7f64faa2dc6dd4d51b8277f11e2f2ec7729fac225088befe3b8399c0b71a4cb55b9d0e20f93d305c78cebceff1bcfcfaf225428dfcfaaec630c88720ab65bf5d3399dd1ac82ea0ecf308b3f80d9780af7742fb157692cd60515a7e2086878f082117fa80fff3d257de7d3a2e9cc8b3472ef4a5e545d90e1159523a60f38d16cece783579627124776813334bdb2a2df4171ef1fa12bf415da338ce5085c01e7a715638ef5505aebec06a0625aaa72d13839838f7d4f981673c8f05f08408e8b372f900af7227c49cfb1e1febab6c07dd42b7c26f921cf010832841205\",  ...  ]\n}"
   *   },
   *   ...]
   * }
   *
   */
  public function get_transaction_pool() {
    return $this->_run('/get_transaction_pool');
  }

  /**
   *
   * Send a command to the daemon to safely disconnect and shut down.
   *
   * @return object  Example: {
   *   "status": "OK"
   * }
   *
   */
  public function stop_daemon() {
    return $this->_run('/stop_daemon');
  }

}
