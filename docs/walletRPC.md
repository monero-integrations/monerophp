# `walletRPC` class

[`src/walletRPC.php`](https://github.com/monero-integrations/monerophp/tree/master/src/walletRPC.php)

A class for making calls to monero-wallet-rpc using PHP

Parameters:

 - `$host <String>` monero-wallet-rpc hostname *(optional)*
 - `$port <int>` monero-wallet-rpc port *(optional)*
 - `$protocol <String>` monero-wallet-rpc protocol (eg. 'http') *(optional)*
 - `$user <String>` monero-wallet-rpc RPC username *(optional)*
 - `$password <String>` monero-wallet-rpc RPC passphrase *(optional)*

Parameters can also be passed in as an associative array (object/dictionary,) as in:

```php
$walletRPC = new walletRPC(['host' => '127.0.0.1', 'port' => 28083])
```

If an object is used to provide parameters (as above,) parameters can be declared in any order.

### Methods

 - [`_transform`](#_transform)
 - [`get_balance` (alias: `getbalance`)](#get_balance)
 - [`get_address` (alias: `getaddress`)](#get_address)
 - [`create_address`](#create_address)
 - [`label_address`](#label_address)
 - [`get_accounts`](#get_accounts)
 - [`create_account`](#create_account)
 - [`label_account`](#label_account)
 - [`get_account_tags`](#get_account_tags)
 - [`tag_accounts`](#tag_accounts)
 - [`untag_accounts`](#untag_accounts)
 - [`set_account_tag_description`](#set_account_tag_description)
 - [`get_height` (alias: `getheight`)](#get_height)
 - [`transfer`](#transfer)
 - [`transfer_split`](#transfer_split)
 - [`sweep_dust`](#sweep_dust)
 - [`sweep_unmixable`](#sweep_unmixable)
 - [`sweep_all`](#sweep_all)
 - [`sweep_single`](#sweep_single)
 - [`relay_tx`](#relay_tx)
 - [`store`](#store)
 - [`get_payments`](#get_payments)
 - [`get_bulk_payments`](#get_bulk_payments)
 - [`incoming_transfers`](#incoming_transfers)
 - [`query_key`](#query_key)
 - [`view_key`](#view_key)
 - [`spend_key`](#spend_key)
 - [`mnemonic`](#mnemonic)
 - [`make_integrated_address`](#make_integrated_address)
 - [`split_integrated_address`](#split_integrated_address)
 - [`stop_wallet`](#stop_wallet)
 - [`rescan_blockchain`](#rescan_blockchain)
 - [`set_tx_notes`](#set_tx_notes)
 - [`get_tx_notes`](#get_tx_notes)
 - [`set_attribute`](#set_attribute)
 - [`get_attribute`](#get_attribute)
 - [`get_tx_key`](#get_tx_key)
 - [`check_tx_key`](#check_tx_key)
 - [`get_tx_proof`](#get_tx_proof)
 - [`check_tx_proof`](#check_tx_proof)
 - [`get_spend_proof`](#get_spend_proof)
 - [`check_spend_proof`](#check_spend_proof)
 - [`get_reserve_proof`](#get_reserve_proof)
 - [`check_reserve_proof`](#check_reserve_proof)
 - [`get_transfers`](#get_transfers)
 - [`get_transfer_by_txid`](#get_transfer_by_txid)
 - [`sign`](#sign)
 - [`verify`](#verify)
 - [`export_key_images`](#export_key_images)
 - [`import_key_images`](#import_key_images)
 - [`make_uri`](#make_uri)
 - [`parse_uri`](#parse_uri)
 - [`get_address_book`](#get_address_book)
 - [`add_address_book`](#add_address_book)
 - [`delete_address_book`](#delete_address_book)
 - [`rescan_spent`](#rescan_spent)
 - [`start_mining`](#start_mining)
 - [`stop_mining`](#stop_mining)
 - [`get_languages`](#get_languages)
 - [`create_wallet`](#create_wallet)
 - [`open_wallet`](#open_wallet)
 - [`is_multisig`](#is_multisig)
 - [`prepare_multisig`](#prepare_multisig)
 - [`make_multisig`](#make_multisig)
 - [`export_multisig_info`](#export_multisig_info)
 - [`import_multisig_info`](#import_multisig_info)
 - [`finalize_multisig`](#finalize_multisig)
 - [`sign_multisig`](#sign_multisig)
 - [`submit_multisig`](#submit_multisig)
 - [`get_client` (alias: `getClient`)](#get_client)

#### `_transform`

Convert from moneroj to tacoshi (piconero)

Parameters:

 - `$method <number>` Amount (in monero) to transform to tacoshi (piconero) *(optional)*

Return: `<Number>`

#### `get_balance`

Look up an account's balance

Parameters:

 - `$account_index <number>` Index of account to look up *(
  optional)*

Return: `<Object>`

```json
{
  "balance": 140000000000,
  "unlocked_balance": 50000000000
}
```

Alias: `getbalance`

#### `get_address`

Look up wallet address(es)

Parameters:

 - `$account_index <number>` Index of account to look up *(optional)*
 - `$address_index <number>` Index of subaddress to look up *(optional)*

Return: `<Object>`

```json
{
 "address": "A2XE6ArhRkVZqepY2DQ5QpW8p8P2dhDQLhPJ9scSkW6q9aYUHhrhXVvE8sjg7vHRx2HnRv53zLQH4ATS",
 "addresses": [
   {
     "address": "A2XE6ArhRkVZqepY2DQ5QpW8p8P2dhDQLhPJ9scSkW6q9aYUHhrhXVvE8sjg7vHRx2HnRv53zLQH",
     "address_index": 0,
     "label": "Primary account",
     "used": true
   }, {
     "address": "Bh3ttLbjGFnVGCeGJF1HgVh4DfCaBNpDt7PQAgsC2GFug7WKskgfbTmB6e7UupyiijiHDQPmDC7w",
     "address_index": 1,
     "label": "",
     "used": true
   }
 ]
}
```

Alias: `getaddress`

#### `create_address`

Create a new subaddress

Parameters:

 - `$account_index <number>` The subaddress account index
 - `$label <String>` A label to the new subaddress

Return: `<Object>`

```json
{
  "address": "Bh3ttLbjGFnVGCeGJF1HgVh4DfCaBNpDt7PQAgsC2GFug7WKskgfbTmB6e7UupyiijiHDQPmDC7wSC",
  "address_index": 1
}
```

#### `label_address`

Label a subaddress

Parameters:

 - `$index <number>`  The index of the subaddress to label
 - `$label <String>`  The label to apply

#### `get_accounts`

Look up wallet accounts

Return: `<Object>`

```json
{
  "subaddress_accounts": {
   "0": {
     "account_index": 0,
     "balance": 2808597352948771,
     "base_address": "A2XE6ArhRkVZqepY2DQ5QpW8p8P2dhDQLhPJ9scSkW6q9aYUHhrhXVvE8sjg7vHRx2HnR",
     "label": "Primary account",
     "tag": "",
     "unlocked_balance": 2717153096298162
   },
   "1": {
     "account_index": 1,
     "balance": 0,
     "base_address": "BcXKsfrvffKYVoNGN4HUFfaruAMRdk5DrLZDmJBnYgXrTFrXyudn81xMj7rsmU5P9dX56",
     "label": "Secondary account",
     "tag": "",
     "unlocked_balance": 0
  },
  "total_balance": 2808597352948771,
  "total_unlocked_balance": 2717153096298162
}
```

#### `create_account`

Create a new account

Parameters:

 - `$label <String>` Label to apply to new account

#### `label_account`

Label an account

Parameters:

 - `$account_index <number>` Index of account to label
 - `$label <String>` Label to apply

#### `get_account_tags`

Look up account tags

Return: `<Object>`

```json
{
  "account_tags": {
   "0": {
     "accounts": {
       "0": 0,
       "1": 1
     },
     "label": "",
     "tag": "Example tag"
   }
 }
}
```

#### `tag_accounts`

Tag accounts

Parameters:

 - `$accounts <Array>` The indices of the accounts to tag
 - `$tag <String>` Tag to apply

#### `untag_accounts`

Untag accounts

Parameters:

 - `$accounts <Array>` The indices of the accounts to untag

#### `set_account_tag_description`

Describe a tag

Parameters:

 - `$tag <String>` Tag to describe
 - `$description <String>` Description to apply to tag

Return: `<Object>`

[//]: # (TODO example)

#### `get_height`

Look up how many blocks are in the longest chain known to the wallet

Return: `<Object>`

```json
{
  "height": 994310
}
```

Alias: `getheight`

#### `transfer`

Send monero

Parameters can be passed in individually (as listed below) or as an object/dictionary (as listed below) or as an object/dictionary (as listed at bottom)

To send to multiple recipients, use the object/dictionary (bottom) format and pass an array of recipient addresses and amount arrays in the destinations field (as in "destinations = [['amount' => 1, 'address' => ...], ['amount' => 2, 'address' => ...]]")

Parameters:

 - `$amount <String>` Amount of monero to send
 - `$address <String>` Address to receive funds
 - `$payment_id <String>` Payment
 - `$mixin <number>` Mixin number (ringsize - 1)
 - `$account_index <number>` Account to send
 - `$subaddr_indices <String>` Comma-separated list of subaddress indices to spend
 - `$priority <number>` Transaction
 - `$unlock_time <number>` UNIX time or block height to unlock
 - `$do_not_relay <boolean>` Do not relay

    *or*

 - `$params <Object>` Array containing any of the options listed above, where only amount and address or a destionation's array are required

Return: `<Object>`

```json
{
  "amount": "1000000000000",
  "fee": "1000020000",
  "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
  "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
}
```

#### `transfer_split`

Same as transfer, but splits transfer into more than one transaction if necessary

Return: `<Object>`

[//]: # (TODO example)

#### `sweep_dust`

Send all dust outputs back to the wallet

Return: `<Object>`

[//]: # (TODO example)

#### `sweep_unmixable`

Send all unmixable outputs back to the wallet

Return: `<Object>`

[//]: # (TODO example)

#### `sweep_all`

Send all unlocked outputs from an account to an address

Parameters:

 - `$address <String>` Address to receive funds
 - `$subaddr_indices <String>` Comma-separated list of subaddress indices to sweep *(optional)*
 - `$account_index <number>` Index of the account to sweep *(optional)*
 - `$payment_id <String>` Payment ID *(optional)*
 - `$mixin <number>` Mixin number (ringsize - 1) *(optional)*
 - `$priority <number>` Payment ID *(optional)*
 - `$below_amount <number>` Only send outputs below this amount *(optional)*
 - `$unlock_time <number>` UNIX time or block height to unlock output *(optional)*
 - `$do_not_relay <boolean>` Do not relay transaction *(optional)*

    *or*

 - `$params <Object>` Array containing any of the options listed above, where only address is required

Return: `<Object>`

```json
{
  "amount": "1000000000000",
  "fee": "1000020000",
  "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
  "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
}
```

#### `sweep_single`

Sweep a single key image to an address

Parameters:

 - `$key_image <String>` Key image to sweep
 - `$address <String>` Address to receive funds
 - `$payment_id <String>` Payment ID *(optional)*
 - `$below_amount <number>` Only send outputs below this amount *(optional)*
 - `$mixin <number>` Mixin number (ringsize - 1) *(optional)*
 - `$priority <number>` Payment ID *(optional)*
 - `$unlock_time <number>` UNIX time or block height to unlock output *(optional)*
 - `$do_not_relay <boolean>` Do not relay transaction *(optional)*

    *or*

 - `$params <Object>` Array containing any of the options listed above, where
Return: `<Object>`

```json
{
  "amount": "1000000000000",
  "fee": "1000020000",
  "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
  "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
}
```

#### `relay_tx`

Relay a transaction

Parameters:

 - `$hex <String>` Blob of transaction to relay

Return: `<Object>`

[//]: # (TODO example)

#### `store`

Save wallet

[//]: # (TODO example)

#### `get_payments`

Look up incoming payments by payment ID

Parameters:

 - `$payment_id <String>` Payment ID to look up

Return: `<Object>`

```json
{
  "payments": [{
   "amount": 10350000000000,
   "block_height": 994327,
   "payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030",
   "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
   "unlock_time": 0
 }]
}
```

#### `get_bulk_payments`

Look up incoming payments by payment ID (or a list of payments IDs) from a given height

Parameters:

 - `$payment_ids <Array>` Array of payment IDs to look up
 - `$min_block_height <String>` Height to begin search

Return: `<Object>`

```json
{
  "payments": [{
   "amount": 10350000000000,
   "block_height": 994327,
   "payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030",
   "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
   "unlock_time": 0
 }]
}
```

#### `incoming_transfers`

Look up incoming transfers

Parameters:

 - `$type <String>` Type of transfer to look up; must be 'all', 'available', or 'unavailable' (incoming transfers which have already been spent)
 - `$account_index <number>` Index of account to look up *(optional)*
 - `$subaddr_indices <String>` Comma-separated list of subaddress indices to look up *(optional)*

Return: `<Object>`

```json
{
  "transfers": [{
   "amount": 10000000000000,
   "global_index": 711506,
   "spent": false,
   "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
   "tx_size": 5870
 },{
   "amount": 300000000000,
   "global_index": 794232,
   "spent": false,
   "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
   "tx_size": 5870
 },{
   "amount": 50000000000,
   "global_index": 213659,
   "spent": false,
   "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
   "tx_size": 5870
 }]
}
```

#### `query_key`

Look up a wallet key

Parameters:

 - `$key_type <String>` Type of key to look up; must be 'view_key', 'spend_key', or 'mnemonic'

Return: `<Object>`

```json
{
  "key": "7e341d..."
}
```

#### `view_key`

Look up wallet view key

Return: `<Object>`

```json
{
  "key": "7e341d..."
}
```

#### `spend_key`

Look up wallet spend key

Return: `<Object>`

```json
{
  "key": "2ab810..."
}
```

#### `mnemonic`

Look up wallet mnemonic seed

Return: `<Object>`

```json
{
  "key": "2ab810..."
}
```

#### `make_integrated_address`

Create an integrated address from a given payment ID

Parameters:

 - `$payment_id <String>` Payment ID *(optional)*

Return: `<Object>`

```json
{
  "integrated_address": "4BpEv3WrufwXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CG
}
```

#### `split_integrated_address`

Look up the wallet address and payment ID corresponding to an integrated address

Parameters:

 - `$integrated_address <String>` Integrated address to split

Return: `<Object>`

```json
{
  "payment_id": "420fa29b2d9a49f5",
  "standard_address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJ
}
```

#### `stop_wallet`

Stop the wallet, saving the state

#### `rescan_blockchain`

Rescan the blockchain from scratch

#### `set_tx_notes`

Add notes to transactions

 - `$txids <Array>` Array of transaction IDs to note

Parameters:

 - `$notes <Array>` Array of notes (strings) to add

#### `get_tx_notes`

Look up transaction note

Parameters:

 - `$txids <Array>` Array of transaction IDs (strings) to look up

Return: `<Object>`

[//]: # (TODO example)

#### `set_attribute`

Set a wallet option

Parameters:

 - `$key <String>` Option to set
 - `$value <String>` Value to set

#### `get_attribute`

Look up a wallet option

Parameters:

 - `$key <String>` Wallet option to query
Return: `<Object>`

[//]: # (TODO example)

#### `get_tx_key`

Look up a transaction key

Parameters:

 - `$txid <String>` Transaction ID to look up

Return:

```json
Example: {
  "tx_key": "e8e97866b1606bd87178eada8f995bf96d2af3fec5db0bc570a451ab1d589b0f"
}
```

#### `check_tx_key`

Check a transaction key

Parameters:

 - `$address <String>` Address that sent transaction
 - `$txid <String>` Transaction ID
 - `$tx_key <String>` Transaction key

Return:

```json
Example: {
  "confirmations": 1,
  "in_pool": ,
  "received": 0
}
```

#### `get_tx_proof`

Create proof (signature) of transaction

Parameters:

 - `$address <String>` Address that spent funds
 - `$txid <String>` Transaction ID
Return: `<Object>`

```json
{
  "signature": "InProofV1Lq4nejMXxMnAdnLeZhHe3FGCmFdnSvzVM1AiGcXjngTRi4hfHPcDL9D4th7KUuvF9ZH
}
```

#### `check_tx_proof`

Verify transaction proof

Parameters:

 - `$address <String>` Address that spent funds
 - `$txid <String>` Transaction ID
 - `$signature <String>` Signature (tx_proof)

Return:

```json
{
  "confirmations": 2,
  "good": 1,
  "in_pool": ,
  "received": 15752471409492,
}
```

#### `get_spend_proof`

Create proof of a spend

Parameters:

 - `$txid <String>` Transaction ID

Return: `<Object>`

```json
{
  "signature": "SpendProofV1RnP6ywcDQHuQTBzXEMiHKbe5ErzRAjpUB1h4RUMfGPNv4bbR6V7EFyiYkCrURwbb
}
```

#### `check_spend_proof`

Verify spend proof

Parameters:

 - `$txid <String>` Transaction ID
 - `$signature <String>` Spend proof to verify

Return: `<Object>`

```json
{
  "good": 1
}
```

#### `get_reserve_proof`

Create proof of reserves

Parameters:

 - `$account_index <String>` Comma-separated list of account indices of which to prove

Return:

```json
{
  "signature": "ReserveProofV11BZ23sBt9sZJeGccf84mzyAmNCP3KzYbE111111111111AjsVgKzau88VxXVGA
}
```

#### `check_reserve_proof`

Verify a reserve proof

Parameters:

 - `$address <String>` Wallet address
 - `$signature <String>` Reserve proof

Return: `<Object>`

```json
{
  "good": 1,
  "spent": 0,
  "total": 0
}
```

#### `get_transfers`

Look up transfers

Parameters:

 - `$input_types <Array>` Array of transfer type strings; possible values include 'all', 'in', 'out', 'pending', 'failed', and 'pool' *(optional)*
 - `$account_index <number>` Index of account to look *(optional)*
 - `$subaddr_indices <String>` Comma-separated list of subaddress indices to look up *(optional)*
 - `$min_height <number>` Minimum block height to use when looking up *(optional)*
 - `$max_height <number>` Maximum block height to use when looking up *(optional)*

    *or*

 - `$inputs_types <Object>` Array containing any of the options listed above, where only an input types array is required

Return: `<Object>`

```json
{
  "pool": [{
   "amount": 500000000000,
   "fee": 0,
   "height": 0,
   "note": "",
   "payment_id": "758d9b225fda7b7f",
   "timestamp": 1488312467,
   "txid": "da7301d5423efa09fabacb720002e978d114ff2db6a1546f8b820644a1b96208",
   "type": "pool"
 }]
}
```

#### `get_transfer_by_txid`

Look up transaction by transaction ID

Parameters:

 - `$txid <String>` Transaction ID to look up
 - `$account_index <String>` Index of account to query *(optional)*

Return: `<Object>`

```json
{
  "transfer": {
   "amount": 10000000000000,
   "fee": 0,
   "height": 1316388,
   "note": "",
   "payment_id": "0000000000000000",
   "timestamp": 1495539310,
   "txid": "f2d33ba969a09941c6671e6dfe7e9456e5f686eca72c1a94a3e63ac6d7f27baf",
   "type": "in"
 }
}
```

#### `sign`

Sign a string

Parameters:

 - `$data <String>` Data to sign

Return: `<Object>`

```json
{
  "signature": "SigV1Xp61ZkGguxSCHpkYEVw9eaWfRfSoAf36PCsSCApx4DUrKWHEqM9CdNwjeuhJii6LHDVDFxv
}
```

#### `verify`

Verify a signature

Parameters:

 - `$data <String>` Signed data
 - `$address <String>` Address that signed data
 - `$signature <String>` Signature to verify

Return:

```json
{
  "good": true
}
```

#### `export_key_images`

Export an array of signed key images

Return: `<Object>`

[//]: # (TODO example)

#### `import_key_images`

Import a signed set of key images

Parameters:

 - `$signed_key_images <Array>` Array of signed key images

Return:

```json
{
 // TODO example
 height: ,
 spent: ,
 unspent:
}
```

#### `make_uri`

Create a payment URI using the official URI specification

Parameters:

 - `$address <String>` Address to receive funds
 - `$amount <String>` Amount of monero to request
 - `$payment_id <String>` Payment ID *(optional)*
 - `$recipient_name <String>` Name of recipient *(optional)*
 - `$tx_description <String>` Payment description *(optional)*

Return: `<Object>`

[//]: # (TODO example)

#### `parse_uri`

Parse a payment URI

Parameters:

 - `$uri <String>` Payment URI

Return: `<Object>`

```json
{
  "uri": {
   "address": "44AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VB
   "amount": 10,
   "payment_id": "0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef",
   "recipient_name": "Monero Project donation address",
   "tx_description": "Testing out the make_uri function"
 }
}
```

#### `get_address_book`

Look up address book entries

Parameters:

 - `$entries <Array>` Array of address book entry indices to look up

Return: `<Object>`

[//]: # (TODO example)

#### `add_address_book`

Add entry to the address book

Parameters:

 - `$address <String>` Address to add to address book
 - `$payment_id <String>` Payment ID to use with address in address book *(optional)*
 - `$description <String>` Description of address *(optional)*

Return: `<Object>`

[//]: # (TODO example)

#### `delete_address_book`

Delete an entry from the address book

Parameters:

 - `$index <Array>` Index of the address book entry to remove

#### `rescan_spent`

Rescan the blockchain for spent outputs

#### `start_mining`

Start mining

 - `$threads_count <number>` Number of threads with which to mine
 - `$do_background_mining <boolean>` Mine in background?
 - `$ignore_battery <boolean>` Ignore battery?

#### `stop_mining`

Stop mining

#### `get_languages`

Look up a list of available languages for your wallet's seed

Return: `<Object>`

[//]: # (TODO example)

#### `create_wallet`

Create a new wallet

 - `$filename <String>` Filename of new wallet to create
 - `$password <String>` Password of new wallet to create
 - `$language <String>` Language of new wallet to create

#### `open_wallet`

Open a wallet

Parameters:

 - `$filename <String>` Filename of wallet to open
 - `$password <String>` Password of wallet to open

#### `is_multisig`

Check if wallet is multisig

Return: `<Object>`

[//]: # (TODO multisig wallet example)

Non-multisignature wallet return:

```json
{
  "multisig": ,
  "ready": ,
  "threshold": 0,
  "total": 0
}
```

#### `prepare_multisig`

Create information needed to create a multisignature wallet

Return: `<Object>`

```json
{
  "multisig_info": "MultisigV1WBnkPKszceUBriuPZ6zoDsU6RYJuzQTiwUqE5gYSAD1yGTz85vqZGetawVvioa
}
```

#### `make_multisig`

Create a multisignature wallet

Parameters:

 - `$multisig_info <String>` Multisignature information (from eg. prepare_multisig)
 - `$threshold <String>` Threshold required to spend from multisignature wallet
 - `$password <String>` Passphrase to apply to multisignature wallet

Return: `<Object>`

[//]: # (TODO example)

#### `export_multisig_info`

Export multisignature information

Return: `<Object>`

[//]: # (TODO example)

#### `import_multisig_info`

Import multisignature information

Parameters:

 - `$info <String>` Multisignature info (from eg. prepare_multisig)

Return: `<Object>`

[//]: # (TODO example)

#### `finalize_multisig`

Finalize a multisignature wallet

Parameters:

 - `$multisig_info <String>` Multisignature info (from eg. prepare_multisig)
 - `$password <String>` Multisignature info (from eg. prepare_multisig)

Return: `<Object>`

[//]: # (TODO example)

#### `sign_multisig`

Sign a multisignature transaction

Parameters:

 - `$tx_data_hex <String>` Blob of transaction to sign

Return: `<Object>`

[//]: # (TODO example)

#### `submit_multisig`

Submit (relay) a multisignature transaction

Parameters:

 - `$tx_data_hex <String>` Blob of transaction to submit

Return: `<Object>`

[//]: # (TODO example)

#### `get_client`

Return the `jsonRPCClient` used by the class

Return: `jsonRPCClient`

Alias: `getClient`

### Credits

Written by the [Monero Integrations team](https://github.com/monero-integrations/monerophp/graphs/contributors) (<support@monerointegrations.com>)

Using work from:
 - CryptoChangements [Monero_RPC] (<bW9uZXJv@gmail.com>) (https://github.com/cryptochangements34)
 - Serhack [Monero Integrations] (<nico@serhack.me>) (https://serhack.me)
 - TheKoziTwo [xmr-integration] (<thekozitwo@gmail.com>)
 - Kacper Rowinski [jsonRPCClient] (<krowinski@implix.com>)
