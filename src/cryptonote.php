<?php
/*
Copyright (c) 2018 Monero-Integrations
*/
    require_once("SHA3.php");
    require_once("ed25519.php");
    require_once("base58.php");

    class Cryptonote
    {
        protected $ed25519;
        public function __construct()
        {
            $this->ed25519 = new ed25519();
	    $this->base58 = new base58();
        }
        public function keccak_256($message)
        {
            $keccak256 = SHA3::init (SHA3::KECCAK_256);
            $keccak256->absorb (hex2bin($message));
            return bin2hex ($keccak256->squeeze (32)) ;
        }
        
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
        
        public function derive_viewKey($spendKey)
        {
            $hashed = $this->keccak_256($spendKey);
            $viewKey = $this->sc_reduce($hashed);
            return $viewKey;
        }
        
        public function gen_private_keys($seed)
        {
            $spendKey = $this->sc_reduce($seed);
            $viewKey = $this->derive_viewKey($spendKey);
            $result = array("spendKey" => $spendKey,
                            "viewKey" => $viewKey);

            return $result;
        }
        
        public function pk_from_sk($pubKey)
        {
	    $keyInt = $this->ed25519->decodeint(hex2bin($pubKey));
	    $aG = $this->ed25519->scalarmult_base($keyInt);
            return bin2hex($this->ed25519->encodepoint($aG));
        }

        public function gen_key_derivation($public, $private)
        {
            $point = $this->ed25519->scalarmult($this->ed25519->decodepoint(hex2bin($public)), $this->ed25519->decodeint(hex2bin($private)));
            $res = $this->ed25519->scalarmult($point, 8);
            return bin2hex($this->ed25519->encodepoint($res));
        }

	public function encode_address($pSpendKey, $pViewKey)
	{
	    // mainnet network byte is 18 (0x12)
	    $data = "12" . $pSpendKey . $pViewKey;
	    $encoded = $this->base58->encode($data);
	    return $encoded;
	}

	public function verify_checksum($address)
	{
	    $decoded = $this->base58->decode($address);
	    $checksum = substr($decoded, -8);
	    $test = substr($decoded, 0, 130);
	    $checksum_hash = $this->keccak_256(substr($decoded, 0, 130));
	    $calculated = substr($checksum_hash, 0, 8);
	    if($checksum == $calculated){
	    	return true;
	    }
	    else
		return false;
	}

	// param (string) $address = base58 encoded monero address
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
    }
