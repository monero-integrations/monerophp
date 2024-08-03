<?php

declare(strict_types=1);
/*
  Copyright (c) 2018, Monero Integrations

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.
*/

namespace MoneroIntegrations\MoneroCrypto;

use kornrunner\Keccak as keccak;

use Exception;

enum MoneroNetwork: string
{
    case mainnet = "mainnet";
    case stagenet = "stagenet";
    case testnet = "testnet";
}

class Cryptonote
{
    public static $all_network_prefixes = [
        "mainnet" => [
            "STANDARD" => "12", // dechex(18)
            "INTEGRATED" => "13", // dechex(19)
            "SUBADDRESS" => "2A" // dechex(42)
        ],
        "stagenet" => [
            "STANDARD" => "18", // dechex(24)
            "INTEGRATED" => "19", // dechex(25)
            "SUBADDRESS" => "24", // dechex(36)
        ],
        "testnet" => [
            "STANDARD" => "35", // dechex(53)
            "INTEGRATED" => "36", // dechex(54)
            "SUBADDRESS" => "3F", // dechex(63)
        ]
    ];
    protected $network_prefixes;

    protected $ed25519;

    public function __construct(MoneroNetwork $network)
    {
        $this->network_prefixes = self::$all_network_prefixes[$network->value];
        $this->ed25519 = new Ed25519();
    }

    /**
     * Hashes a hexadecimal string with Keccak-256.
     */
    public static function keccak_256($message): string
    {
        $bin = new BigInteger($message, 16);
        $hash = keccak::hash($bin->toBytes(), 256);

        return $hash;
    }

    /**
     * Generates a new random hexadecimal seed (32 bytes).
     */
    public function gen_new_hex_seed(): string
    {
        $bytes = random_bytes(32);
        return bin2hex($bytes);
    }

    /**
     * Performs sc_reduce (mod l) on a hexadecimal string.
     */
    public function sc_reduce(string $input): string
    {
        $modulo = new BigInteger($input, 16);
        $result = $modulo->mod($this->ed25519->l)->toHex();
        return $result;
    }

    /**
     * Hashes a string and reduces it to a scalar.
     */
    public function hash_to_scalar(string $data): string
    {
        $hash = self::keccak_256($data);
        $scalar = $this->sc_reduce($hash);
        return $scalar;
    }

    /*
     * Derive a deterministic private view key from a private spend key
     * @param string A private spend key represented as a 32 byte hex string
     *
     * @return string A deterministic private view key represented as a 32 byte hex string
     */
    public function derive_viewKey(string $spendKey): string
    {
        return $this->hash_to_scalar($spendKey);
    }

    /*
     * Generate a pair of random private keys
     *
     * @param string A hex string to be used as a seed (this should be random)
     *
     * @return array An array containing a private spend key and a deterministic view key
     */
    public function gen_private_keys(string $seed): array
    {
        $spendKey = $this->sc_reduce($seed);
        $viewKey = $this->derive_viewKey($spendKey);

        return [
            "spendKey" => $spendKey,
            "viewKey" => $viewKey
        ];
    }

    /*
     * Get a public key from a private key on the ed25519 curve
     *
     * @param string a 32 byte hex encoded private key
     *
     * @return string a 32 byte hex encoding of a point on the curve to be used as a public key
     */
    public function pk_from_sk(string $privKey): string
    {
        return $this->ed25519->publickey($this->ed25519->decodeint($privKey));
    }

    /*
     * Generate key derivation
     *
     * @param string a 32 byte hex encoding of a point on the ed25519 curve used as a public key
     * @param string a 32 byte hex encoded private key
     *
     * @return string The hex encoded key derivation
     */
    public function gen_key_derivation(string $public, string $private): string
    {
        $point = $this->ed25519->scalarmult($this->ed25519->decodepoint(hex2bin($public)), $this->ed25519->decodeint(hex2bin($private)));
        $res = $this->ed25519->scalarmult($point, new BigInteger(8));
        return bin2hex($this->ed25519->encodePoint($res));
    }

    public function derivation_to_scalar(string $der, int $index): string
    {
        $encoded = Varint::encodeVarint($index);
        $data = $der . $encoded;
        return $this->hash_to_scalar($data);
    }

    // this is a one way function used for both encrypting and decrypting 8 byte payment IDs
    public function stealth_payment_id(string $payment_id, string $tx_pub_key, string $viewkey): string
    {
        if (strlen($payment_id) != 16) {
            throw new Exception("Error: Incorrect payment ID size. Should be 8 bytes");
        }
        $der = $this->gen_key_derivation($tx_pub_key, $viewkey);
        $data = $der . '8d';
        $hash = self::keccak_256($data);
        $key = substr($hash, 0, 16);
        $result = bin2hex(pack('H*', $payment_id) ^ pack('H*', $key));
        return $result;
    }

    // takes transaction extra field as hex string and returns transaction public key 'R' as hex string
    public function txpub_from_extra(string $extra): string
    {
        $parsed = array_map("hexdec", str_split($extra, 2));

        if ($parsed[0] == 1) {
            return substr($extra, 2, 64);
        }

        if ($parsed[0] == 2) {
            if ($parsed[0] == 2 || $parsed[2] == 1) {
                //$offset = (($parsed[1] + 2) *2) + 2;
                return substr($extra, (($parsed[1] + 2) * 2) + 2, 64);
            }
        }
    }

    public function derive_public_key(string $der, int $index, string $pub): string
    {
        $scalar = $this->derivation_to_scalar($der, $index);
        $sG = $this->ed25519->scalarmult_base($this->ed25519->decodeint(hex2bin($scalar)));
        $pubPoint = $this->ed25519->decodepoint(hex2bin($pub));
        $key = $this->ed25519->encodePoint($this->ed25519->edwards($pubPoint, $sG));
        return bin2hex($key);
    }

    /*
     * Perform the calculation P = P' as described in the cryptonote whitepaper
     *
     * @param string 32 byte transaction public key R
     * @param string 32 byte receiver private view key a
     * @param string 32 byte receiver public spend key B
     * @param int output index
     * @param string output you want to check against P
     */
    public function is_output_mine(string $txPublic, string $privViewkey, string $publicSpendkey, int $index, string $P): bool
    {
        $derivation = $this->gen_key_derivation($txPublic, $privViewkey);
        $Pprime = $this->derive_public_key($derivation, $index, $publicSpendkey);

        if ($P == $Pprime) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Create a valid base58 encoded Monero address from public keys
     *
     * @param string Public spend key
     * @param string Public view key
     *
     * @return string Base58 encoded Monero address
     */
    public function encode_address(string $pSpendKey, string $pViewKey): string
    {
        $data = $this->network_prefixes["STANDARD"] . $pSpendKey . $pViewKey;
        $checksum = self::keccak_256($data);
        $encoded  = Base58::encode($data . substr($checksum, 0, 8));

        return $encoded;
    }

    public function verify_checksum(string $address): bool
    {
        $decoded = Base58::decode($address);
        $checksum = substr($decoded, -8);
        $checksum_hash = self::keccak_256(substr($decoded, 0, -8));
        $calculated = substr($checksum_hash, 0, 8);
        return $checksum === $calculated;
    }

    /*
         * Decode a base58 encoded Monero address
         *
         * @param string A base58 encoded Monero address
         *
         * @return array An array containing the Address network byte, public spend key, and public view key
         */
    public function decode_address(string $address): array
    {
        $decoded = Base58::decode($address);

        if (!$this->verify_checksum($address)) {
            throw new Exception("Error: invalid checksum");
        }

        $network_byte = substr($decoded, 0, 2);
        $public_spendKey = substr($decoded, 2, 64);
        $public_viewKey = substr($decoded, 66, 64);

        $result = array(
            "networkByte" => $network_byte,
            "spendKey" => $public_spendKey,
            "viewKey" => $public_viewKey
        );
        return $result;
    }

    /*
     * Get an integrated address from public keys and a payment id
     *
     * @param string A 32 byte hex encoded public spend key
     * @param string A 32 byte hex encoded public view key
     * @param string An 8 byte hex string to use as a payment id
     */
    public function integrated_addr_from_keys(string $public_spendkey, string $public_viewkey, string $payment_id): string
    {
        $data = $this->network_prefixes["INTEGRATED"] . $public_spendkey . $public_viewkey . $payment_id;
        $checksum = substr(self::keccak_256($data), 0, 8);
        $result = Base58::encode($data . $checksum);
        return $result;
    }

    /*
     * Generate a Monero address from seed
     *
     * @param string Hex string to use as seed
     *
     * @return string A base58 encoded Monero address
     */
    public function address_from_seed(string $hex_seed): string
    {
        $private_keys = $this->gen_private_keys($hex_seed);
        $private_viewKey = $private_keys["viewKey"];
        $private_spendKey = $private_keys["spendKey"];

        $public_spendKey = $this->pk_from_sk($private_spendKey);
        $public_viewKey = $this->pk_from_sk($private_viewKey);

        $address = $this->encode_address($public_spendKey, $public_viewKey);
        return $address;
    }

    // m = Hs(a || i)
    public function generate_subaddr_secret_key(string $sec_key, int $major_index, int $minor_index): string
    {
        $prefix = "5375624164647200";
        $index = pack("II", $major_index, $minor_index);
        return $this->hash_to_scalar($prefix . $sec_key . bin2hex($index));
    }

    public function generate_subaddress_spend_public_key(string $spend_public_key, string $subaddr_secret_key): string
    {
        $mInt = $this->ed25519->decodeint(hex2bin($subaddr_secret_key));
        $mG = $this->ed25519->scalarmult_base($mInt);
        $D = $this->ed25519->edwards($this->ed25519->decodepoint(hex2bin($spend_public_key)), $mG);
        return bin2hex($this->ed25519->encodePoint($D));
    }

    public function generate_subaddr_view_public_key(string $subaddr_spend_public_key, string $view_secret_key): string
    {
        $point = $this->ed25519->scalarmult($this->ed25519->decodepoint(hex2bin($subaddr_spend_public_key)), $this->ed25519->decodeint(hex2bin($view_secret_key)));
        return bin2hex($this->ed25519->encodePoint($point));
    }

    public function generate_subaddress(string $spend_public_key, string $view_secret_key, string $major_index, int $minor_index): string
    {
        $subaddr_secret_key = $this->generate_subaddr_secret_key($major_index, $minor_index, $view_secret_key);
        $subaddr_public_spend_key = $this->generate_subaddress_spend_public_key($spend_public_key, $subaddr_secret_key);
        $subaddr_public_view_key = $this->generate_subaddr_view_public_key($subaddr_public_spend_key, $view_secret_key);
        $data = $this->network_prefixes["SUBADDRESS"] . $subaddr_public_spend_key . $subaddr_public_view_key;
        $checksum = self::keccak_256($data);
        $encoded = Base58::encode($data . substr($checksum, 0, 8));
        return $encoded;
    }

    public function deserialize_block_header(string $block): array
    {
        $data = str_split($block, 2);

        $major_version = Varint::decodeVarint($data);
        $data = array_slice($data, 1);

        $minor_version = Varint::decodeVarint($data);
        $data = array_slice($data, 1);

        $timestamp = Varint::decodeVarint($data);
        $data = array_slice($data, 1);

        $nonce = Varint::decodeVarint($data);
        return [
            "major_version" => $major_version,
            "minor_version" => $minor_version,
            "timestamp" => $timestamp,
            "nonce" => $nonce
        ];
    }
}
