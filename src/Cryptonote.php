<?php
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

namespace MoneroIntegrations\MoneroPhp;

    use Exception;

    class Cryptonote
    {
        protected $ed25519;
        public function __construct()
        {
            $this->ed25519 = new ed25519();
            $this->base58 = new base58();
            $this->varint = new Varint();
        }

        /*
         * @param string Hex encoded string of the data to hash
         * @return string Hex encoded string of the hashed data
         *
         */
        public function keccak_256($message)
        {
            $keccak256 = SHA3::init (SHA3::KECCAK_256);
            $keccak256->absorb (hex2bin($message));
            return bin2hex ($keccak256->squeeze (32)) ;
        }

        /*
         * @return string A hex encoded string of 32 random bytes
         *
         */
        public function gen_new_hex_seed()
        {
            $bytes = random_bytes(32);
            return bin2hex($bytes);
        }

        public function sc_reduce($input)
        {
            $integer = $this->ed25519->decodeint(hex2bin($input));

            $modulo = bcmod($integer , $this->ed25519->l);

            $result = bin2hex($this->ed25519->encodeint($modulo));
            return $result;
        }

        /*
         * Hs in the cryptonote white paper
         *
         * @param string Hex encoded data to hash
         *
         * @return string A 32 byte encoded integer
         */
        public function hash_to_scalar($data)
        {
            $hash = $this->keccak_256($data);
            $scalar = $this->sc_reduce($hash);
            return $scalar;
        }

        /*
         * Derive a deterministic private view key from a private spend key
         * @param string A private spend key represented as a 32 byte hex string
         *
         * @return string A deterministic private view key represented as a 32 byte hex string
         */
        public function derive_viewKey($spendKey)
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
        public function gen_private_keys($seed)
        {
            $spendKey = $this->sc_reduce($seed);
            $viewKey = $this->derive_viewKey($spendKey);
            $result = array("spendKey" => $spendKey,
                            "viewKey" => $viewKey);

            return $result;
        }

        /*
         * Get a public key from a private key on the ed25519 curve
         *
         * @param string a 32 byte hex encoded private key
         *
         * @return string a 32 byte hex encoding of a point on the curve to be used as a public key
         */
        public function pk_from_sk($privKey)
        {
        $keyInt = $this->ed25519->decodeint(hex2bin($privKey));
        $aG = $this->ed25519->scalarmult_base($keyInt);
            return bin2hex($this->ed25519->encodepoint($aG));
        }

        /*
         * Generate key derivation
         *
         * @param string a 32 byte hex encoding of a point on the ed25519 curve used as a public key
         * @param string a 32 byte hex encoded private key
         *
         * @return string The hex encoded key derivation
         */
        public function gen_key_derivation($public, $private)
        {
            $point = $this->ed25519->scalarmult($this->ed25519->decodepoint(hex2bin($public)), $this->ed25519->decodeint(hex2bin($private)));
            $res = $this->ed25519->scalarmult($point, 8);
            return bin2hex($this->ed25519->encodepoint($res));
        }

        public function derivation_to_scalar($der, $index)
        {
            $encoded = $this->varint->encode_varint($index);
            $data = $der . $encoded;
            return $this->hash_to_scalar($data);
        }

        // this is a one way function used for both encrypting and decrypting 8 byte payment IDs
        public function stealth_payment_id($payment_id, $tx_pub_key, $viewkey)
        {
            if(strlen($payment_id) != 16)
            {
               throw new Exception("Error: Incorrect payment ID size. Should be 8 bytes");
            }
            $der = $this->gen_key_derivation($tx_pub_key, $viewkey);
            $data = $der . '8d';
            $hash = $this->keccak_256($data);
            $key = substr($hash, 0, 16);
            $result = bin2hex(pack('H*',$payment_id) ^ pack('H*',$key));
            return $result;
        }

        // takes transaction extra field as hex string and returns transaction public key 'R' as hex string
        public function txpub_from_extra($extra)
        {
            $parsed = array_map("hexdec", str_split($extra, 2));

            if($parsed[0] == 1)
            {
                return substr($extra, 2, 64);
            }

            if($parsed[0] == 2)
            {
                if($parsed[0] == 2 || $parsed[2] == 1)
                {
                    //$offset = (($parsed[1] + 2) *2) + 2;
                    return substr($extra, (($parsed[1] + 2) *2) + 2, 64);
                }
            }
        }

        public function derive_public_key($der, $index, $pub)
        {
            $scalar = $this->derivation_to_scalar($der, $index);
            $sG = $this->ed25519->scalarmult_base($this->ed25519->decodeint(hex2bin($scalar)));
            $pubPoint = $this->ed25519->decodepoint(hex2bin($pub));
            $key = $this->ed25519->encodepoint($this->ed25519->edwards($pubPoint, $sG));
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
        public function is_output_mine($txPublic, $privViewkey, $publicSpendkey, $index, $P)
        {
            $derivation = $this->gen_key_derivation($txPublic, $privViewkey);
            $Pprime = $this->derive_public_key($derivation, $index, $publicSpendkey);

            if($P == $Pprime)
            {
               return true;
            }
            else
              return false;
        }

        /*
         * Create a valid base58 encoded Monero address from public keys
         *
         * @param string Public spend key
         * @param string Public view key
         *
         * @return string Base58 encoded Monero address
         */
    public function encode_address($pSpendKey, $pViewKey)
    {
        // mainnet network byte is 18 (0x12)
        $data = "12" . $pSpendKey . $pViewKey;
        $checksum = $this->keccak_256($data);
        $encoded  = $this->base58->encode($data . substr($checksum, 0, 8));
	    
        return $encoded;
    }

    public function verify_checksum($address)
    {
        $decoded = $this->base58->decode($address);
        $checksum = substr($decoded, -8);
        $checksum_hash = $this->keccak_256(substr($decoded, 0, 130));
        $calculated = substr($checksum_hash, 0, 8);
        if($checksum == $calculated){
            return true;
        }
        else
        return false;
    }

    /*
         * Decode a base58 encoded Monero address
         *
         * @param string A base58 encoded Monero address
         *
         * @return array An array containing the Address network byte, public spend key, and public view key
         */
    public function decode_address($address)
        {
            $decoded = $this->base58->decode($address);

        if(!$this->verify_checksum($address)){
        throw new Exception("Error: invalid checksum");
        }

        $network_byte = substr($decoded, 0, 2);
        $public_spendKey = substr($decoded, 2, 64);
        $public_viewKey = substr($decoded, 66, 64);

        $result = array("networkByte" => $network_byte,
                "spendKey" => $public_spendKey,
                "viewKey" => $public_viewKey);
            return $result;
        }

        /*
         * Get an integrated address from public keys and a payment id
         *
         * @param string A 32 byte hex encoded public spend key
         * @param string A 32 byte hex encoded public view key
         * @param string An 8 byte hex string to use as a payment id
         */
        public function integrated_addr_from_keys($public_spendkey, $public_viewkey, $payment_id)
        {
            // 0x13 is the mainnet network byte for integrated addresses
            $data = "13".$public_spendkey.$public_viewkey.$payment_id;
            $checksum = substr($this->keccak_256($data), 0, 8);
            $result = $this->base58->encode($data.$checksum);
            return $result;
        }

        /*
         * Generate a Monero address from seed
         *
         * @param string Hex string to use as seed
         *
         * @return string A base58 encoded Monero address
         */
    public function address_from_seed($hex_seed)
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
    public function generate_subaddr_secret_key($major_index, $minor_index, $sec_key)
    {
	$prefix = "5375624164647200";
	$index = pack("II", $major_index, $minor_index);
	return $this->hash_to_scalar($prefix . $sec_key . bin2hex($index));
    }
	
	public function generate_subaddress_spend_public_key($spend_public_key, $subaddr_secret_key)
	{
	$mInt = $this->ed25519->decodeint(hex2bin($subaddr_secret_key));
	$mG = $this->ed25519->scalarmult_base($mInt);
	$D = $this->ed25519->edwards($this->ed25519->decodepoint(hex2bin($spend_public_key)), $mG);
	return bin2hex($this->ed25519->encodepoint($D));
	}
	
	public function generate_subaddr_view_public_key($subaddr_spend_public_key, $view_secret_key)
	{
		$point = $this->ed25519->scalarmult($this->ed25519->decodepoint(hex2bin($subaddr_spend_public_key)), $this->ed25519->decodeint(hex2bin($view_secret_key)));
		return bin2hex($this->ed25519->encodepoint($point));
	}
	
	public function generate_subaddress($major_index, $minor_index, $view_secret_key, $spend_public_key)
	{
		$subaddr_secret_key = $this->generate_subaddr_secret_key($major_index, $minor_index, $view_secret_key);
		$subaddr_public_spend_key = $this->generate_subaddress_spend_public_key($spend_public_key, $subaddr_secret_key);
		$subaddr_public_view_key = $this->generate_subaddr_view_public_key($subaddr_public_spend_key, $view_secret_key);
		// mainnet subaddress network byte is 42 (0x2a)
        $data = "2a" . $subaddr_public_spend_key . $subaddr_public_view_key;
        $checksum = $this->keccak_256($data);
        $encoded = $this->base58->encode($data . substr($checksum, 0, 8));
		return $encoded;
	}
      
    }
