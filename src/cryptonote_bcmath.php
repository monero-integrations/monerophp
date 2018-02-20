<?php
/*
Copyright (c) 2018 Monero-Integrations
*/
    require_once("SHA3.php");
    require_once("ed25519_bcmath.php");
    
    class CryptonoteBCMath
    {
        protected $ed25519;
        public function __construct()
        {
            $this->ed25519 = new ed25519BCMath();
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
        
    }
