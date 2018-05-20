# `cryptonote` class

[`src/cryptonote.php`](https://github.com/monero-integrations/monerophp/tree/master/src/cryptonote.php)

### Methods

 - [`keccak_256`](#keccak_256)
 - [`gen_new_hex_seed`](#gen_new_hex_seed)
 - [`sc_reduce`](#sc_reduce)
 - [`hash_to_scalar`](#hash_to_scalar)
 - [`derive_viewKey`](#derive_viewKey)
 - [`gen_private_keys`](#gen_private_keys)
 - [`pk_from_sk`](#pk_from_sk)
 - [`gen_key_derivation`](#gen_key_derivation)
 - [`encode_varint`](#encode_varint)
 - [`derivation_to_scalar`](#derivation_to_scalar)
 - [`stealth_payment_id`](#stealth_payment_id)
 - [`txpub_from_extra`](#txpub_from_extra)
 - [`derive_public_key`](#derive_public_key)
 - [`is_output_mine`](#is_output_mine)
 - [`encode_address`](#encode_address)
 - [`verify_checksum`](#verify_checksum)
 - [`decode_address`](#decode_address)
 - [`integrated_addr_from_keys`](#integrated_addr_from_keys)
 - [`address_from_seed`](#address_from_seed)

#### `keccak_256`

Derive a Keccak256 hash from a string

Parameters:

 - `$message <String>` Hex encoded string of the data to hash

Return: `<String>` Hex encoded string of the hashed data

[//]: # (TODO example)

#### `gen_new_hex_seed`

Generate a hexadecimal seed

Return: `<String>` A hex encoded string of 32 random bytes

[//]: # (TODO example)

#### `sc_reduce`

Parameters:

 - `$input <String>` 

[//]: # (TODO return type and example)

#### `hash_to_scalar`

`Hs` in the cryptonote white paper

Parameters:

 - `$data <String>` Hex encoded data to hash

Return: `<String>` A 32 byte encoded integer

[//]: # (TODO example)

#### `derive_viewKey`

Derive a deterministic private view key from a private spend key

Parameters:

 - `$spendKey <String>` A deterministic private view key represented as a 32 byte hex string

Return: `<String>`

[//]: # (TODO example)

#### `gen_private_keys`

Generate a pair of random private keys

Parameters:

 - `$seed <String>` A hex string to be used as a seed (this should be random)

Return: `<Array>` An array containing a private spend key and a deterministic view key 

[//]: # (TODO example)

#### `pk_from_sk`

Get a public key from a private key on the ed25519 curve

Parameters:

 - `$privKey <String>` A 32 byte hex encoded private key

Return: `<String>`

[//]: # (TODO example)

#### `gen_key_derivation`

Generate key derivation

Parameters:

 - `$public <String>` a 32 byte hex encoding of a point on the ed25519 curve used as a public key
 - `$private <String>` a 32 byte hex encoded private key

Return: `<String>` The hex encoded key derivation

[//]: # (TODO example)

#### `encode_varint`

Parameters:

 - `$der <>` 
 - `$index <>` 

[//]: # (TODO return type and example)

#### `derivation_to_scalar`

Parameters:

 - `$ <>` 

[//]: # (TODO return type and example)

#### `stealth_payment_id`

A one way function used for both encrypting and decrypting 8 byte payment IDs

Parameters:

 - `$payment_id <String>` 
 - `$tx_pub_key <String>` 
 - `$viewkey <String>` 

Return: `<String>`

[//]: # (TODO example)

#### `txpub_from_extra`

Takes transaction extra field as hex string and returns transaction public key 'R' as hex string

Parameters:

 - `$extra <String>` 

Return: `<String>`

[//]: # (TODO example)

#### `derive_public_key`

Parameters:

 - `$der <>` 
 - `$index <>` 
 - `$pub <>` 

Return: `<String>`

[//]: # (TODO example)

#### `is_output_mine`

Perform the calculation P = P' as described in the cryptonote whitepaper

Parameters:

 - `$txPublic <String>` 32 byte transaction public key R
 - `$privViewkey <String>` 32 byte receiver private view key a
 - `$publicSpendkey <String>` 32 byte receiver public spend key B
 - `$index <Number>` Otput index
 - `$P <String>` Output you want to check against P

Return: `<Boolean>`

[//]: # (TODO example)

#### `encode_address`

Create a valid base58 encoded Monero address from public keys

Parameters:

 - `$pSpendKey <String>` Public spend key
 - `$pViewKey <String>` Public view key

Return: `<String>` Base58 encoded Monero address

[//]: # (TODO example)

#### `verify_checksum`

Parameters:

 - `$address <>` 

Return: `<Boolean>`

[//]: # (TODO example)

#### `decode_address`

Decode a base58 encoded Monero address

Parameters:

 - `$address <String>` A base58 encoded Monero address 

Return: `<Array>` An array containing the Address network byte, public spend key, and public view key
[//]: # (TODO example)
  

#### `integrated_addr_from_keys`

Create an integrated address from public keys and a payment ID

Parameters:

 - `$public_spendkey <String>` A 32 byte hex encoded public spend key
 - `$public_viewkey <String>` A 32 byte hex encoded public view key
 - `$payment_id <String>` An 8 byte hex string to use as a payment id 

Return: `<String>` Integrated address

[//]: # (TODO example)

#### `address_from_seed`

Derive Monero address from seed

Parameters:

 - `$hex_seed <String>` Hex string to use as seed

Return: `<String>` A base58 encoded Monero address

[//]: # (TODO example)
