# `daemonRPC` class

[`src/daemonRPC.php`](https://github.com/monero-integrations/monerophp/tree/master/src/daemonRPC.php)

A class for making calls to a Monero daemon's RPC API using PHP

Parameters:

 - `$host <String>` Monero daemon port *(optional)*
 - `$port <nNmber>` Monero daemon protocol (*eg.* 'http') *(optional)*
 - `$protocol <String>` Monero daemon IP hostname *(optional)*
 - `$user <String>` Monero daemon RPC username *(optional)*
 - `$password <String>` Monero daemon RPC passphrase *(optional)*

Parameters can also be passed in as an associative array (object/dictionary,) as in:

```php
$daemonRPC = new daemonRPC(['host' => '127.0.0.1', 'port' => 28081])
```

If an object is used to provide parameters (as above,) parameters can be declared in any order.

### Methods

 - [`getblockcount`](#getblockcount)
 - [`on_getblockhash`](#on_getblockhash)
 - [`getblocktemplate`](#getblocktemplate)
 - [`submitblock`](#submitblock)
 - [`getlastblockheader`](#getlastblockheader)
 - [`getblockheaderbyhash`](#getblockheaderbyhash)
 - [`getblockheaderbyheight`](#getblockheaderbyheight)
 - [`getblock_by_hash`](#getblock_by_hash)
 - [`getblock_by_height`](#getblock_by_height)
 - [`get_connections`](#get_connections)
 - [`get_info`](#get_info)
 - [`hardfork_info`](#hardfork_info)
 - [`setbans`](#setbans)
 - [`getbans`](#getbans)

#### `getblockcount`

Look up how many blocks are in the longest chain known to the node

*No parameters*

Return: `<Object>`

```json
{
  "count": 993163,
  "status": "OK"
}
```

#### `on_getblockhash`

Look up a block's hash by its height

Parameters:

 - `$height <Number>` Height of block to look up

Return: `<String>`

```json
"e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6"
```

#### `getblocktemplate`

Construct a block template that can be mined upon

Parameters:

 - `$wallet_address <String>` Address of wallet to receive coinbase transactions if block is successfully mined
 - `$reserve_size <Number>` Reserve size

Return: `<Object>`

```json
{
  "blocktemplate_blob": "01029af88cb70568b84a11dc9406ace9e635918ca03b008f7728b9726b327c1b482a98d81ed83000000000018bd03c01ffcfcf3c0493d7cec7020278dfc296544f139394e5e045fcda1ba2cca5b69b39c9ddc90b7e0de859fdebdc80e8eda1ba01029c5d518ce3cc4de26364059eadc8220a3f52edabdaf025a9bff4eec8b6b50e3d8080dd9da417021e642d07a8c33fbe497054cfea9c760ab4068d31532ff0fbb543a7856a9b78ee80c0f9decfae01023ef3a7182cb0c260732e7828606052a0645d3686d7a03ce3da091dbb2b75e5955f01ad2af83bce0d823bf3dbbed01ab219250eb36098c62cbb6aa2976936848bae53023c00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000001f12d7c87346d6b84e17680082d9b4a1d84e36dd01bd2c7f3b3893478a8d88fb3",
  "difficulty": 982540729,
  "height": 993231,
  "prev_hash": "68b84a11dc9406ace9e635918ca03b008f7728b9726b327c1b482a98d81ed830",
  "reserved_offset": 246,
  "status": "OK"
}
```

#### `submitblock`

Submit a mined block to the network

Parameters:

 - `$block <String>` Block blob

Return: `<Object>`

[//]: # (TODO example)

#### `getlastblockheader`

Look up the block header of the latest block in the longest chain known to the node

*No parameters*

Return: `<Object>`

```json
{
  "block_header": {
    "depth": 0,
    "difficulty": 746963928,
    "hash": "ac0f1e226268d45c99a16202fdcb730d8f7b36ea5e5b4a565b1ba1a8fc252eb0",
    "height": 990793,
    "major_version": 1,
    "minor_version": 1,
    "nonce": 1550,
    "orphan_status": false,
    "prev_hash": "386575e3b0b004ed8d458dbd31bff0fe37b280339937f971e06df33f8589b75c",
    "reward": 6856609225169,
    "timestamp": 1457589942
  },
  "status": "OK"
}
```

#### `getblockheaderbyhash`

Look up a block header by height

Parameters:

 - `$height <Number>` Height of block

Return: `<Object>`

```json
{
  "block_header": {
    "depth": 78376,
    "difficulty": 815625611,
    "hash": "e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6",
    "height": 912345,
    "major_version": 1,
    "minor_version": 2,
    "nonce": 1646,
    "orphan_status": false,
    "prev_hash": "b61c58b2e0be53fad5ef9d9731a55e8a81d972b8d90ed07c04fd37ca6403ff78",
    "reward": 7388968946286,
    "timestamp": 1452793716
  },
  "status": "OK"
}
```

#### `getblockheaderbyheight`

Look up block information by height

Parameters:

 - `$height <Number>` Height of block  

Return: `<Object>`

```json
{
  "block_header": {
    "depth": 78376,
    "difficulty": 815625611,
    "hash": "e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6",
    "height": 912345,
    "major_version": 1,
    "minor_version": 2,
    "nonce": 1646,
    "orphan_status": false,
    "prev_hash": "b61c58b2e0be53fad5ef9d9731a55e8a81d972b8d90ed07c04fd37ca6403ff78",
    "reward": 7388968946286,
    "timestamp": 1452793716
  },
  "status": "OK"
}
```

#### `getblock_by_hash`

Look up block information by SHA256 hash

Parameters:

 - `$hash <String>` SHA256 hash of block  

Return: `<Object>`

```json
{
  "block_header": {
    "depth": 78376,
    "difficulty": 815625611,
    "hash": "e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6",
    "height": 912345,
    "major_version": 1,
    "minor_version": 2,
    "nonce": 1646,
    "orphan_status": false,
    "prev_hash": "b61c58b2e0be53fad5ef9d9731a55e8a81d972b8d90ed07c04fd37ca6403ff78",
    "reward": 7388968946286,
    "timestamp": 1452793716
  },
  "status": "OK"
}
```

#### `getblock_by_height`

Look up block information by height

Parameters:

 - `$height <Number>` Height of block 

Return: `<Object>`

```json
{
  "blob": "...",
  "block_header": {
    "depth": 80694,
    "difficulty": 815625611,
    "hash": "e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6",
    "height": 912345,
    "major_version": 1,
    "minor_version": 2,
    "nonce": 1646,
    "orphan_status": false,
    "prev_hash": "b61c58b2e0be53fad5ef9d9731a55e8a81d972b8d90ed07c04fd37ca6403ff78",
    "reward": 7388968946286,
    "timestamp": 1452793716
  },
  "json": "...",
  "status": "OK"
}
```

#### `get_connections`

Look up incoming and outgoing connections to your node

*No parameters*

Return: `<Object>`

```json
{
  "connections": [{
    "avg_download": 0,
    "avg_upload": 0,
    "current_download": 0,
    "current_upload": 0,
    "incoming": false,
    "ip": "76.173.170.133",
    "live_time": 1865,
    "local_ip": false,
    "localhost": false,
    "peer_id": "3bfe29d6b1aa7c4c",
    "port": "18080",
    "recv_count": 116396,
    "recv_idle_time": 23,
    "send_count": 176893,
    "send_idle_time": 1457726610,
    "state": "state_normal"
  },{
  ..
  }],
  "status": "OK"
}
```

#### `get_info`

Look up general information about the state of your node and the network

*No parameters*

Return: `<Object>`

```json
{
  "alt_blocks_count": 5,
  "difficulty": 972165250,
  "grey_peerlist_size": 2280,
  "height": 993145,
  "incoming_connections_count": 0,
  "outgoing_connections_count": 8,
  "status": "OK",
  "target": 60,
  "target_height": 993137,
  "testnet": false,
  "top_block_hash": "",
  "tx_count": 564287,
  "tx_pool_size": 45,
  "white_peerlist_size": 529
}
```

#### `hardfork_info`

Look up information regarding hard fork voting and readiness

*No parameters*

Return: `<Object>`

```json
{
  "alt_blocks_count": 0,
  "block_size_limit": 600000,
  "block_size_median": 85,
  "bootstrap_daemon_address": ?,
  "cumulative_difficulty": 40859323048,
  "difficulty": 57406,
  "free_space": 888592449536,
  "grey_peerlist_size": 526,
  "height": 1066107,
  "height_without_bootstrap": 1066107,
  "incoming_connections_count": 1,
  "offline":  ?,
  "outgoing_connections_count": 1,
  "rpc_connections_count": 1,
  "start_time": 1519963719,
  "status": OK,
  "target": 120,
  "target_height": 1066073,
  "testnet": 1,
  "top_block_hash": e438aae56de8e5e5c8e0d230167fcb58bc8dde09e369ff7689a4af146040a20e,
  "tx_count": 52632,
  "tx_pool_size": 0,
  "untrusted": ?,
  "was_bootstrap_ever_used: ?,
  "white_peerlist_size": 5
}
```

#### `setbans`

Ban another node by IP

Parameters:

 - `$ip <String>` IP address of node to ban

Return: `<Object>`

```json
{
  "status": "OK"
}
```

#### `getbans`

Get list of banned IPs

*No parameters*

 - `$ <>` 

Return: `<Object>`

```json
{
  "bans": [{
    "ip": 838969536,
    "seconds": 1457748792
  }],
  "status": "OK"
}
```

### Credits

Written by the [Monero Integrations team](https://github.com/monero-integrations/monerophp/graphs/contributors) (<support@monerointegrations.com>)

Using work from:
 - CryptoChangements [Monero_RPC] (<bW9uZXJv@gmail.com>) (https://github.com/cryptochangements34)
 - Serhack [Monero Integrations] (<nico@serhack.me>) (https://serhack.me)
 - TheKoziTwo [xmr-integration] (<thekozitwo@gmail.com>)
 - Andrew LeCody [EasyBitcoin-PHP]
 - Kacper Rowinski [jsonRPCClient] (<krowinski@implix.com>)
