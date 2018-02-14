<?php
/*
Copyright (c) 2018 Monero-Integrations
*/
    require_once("SHA3.php");
    require_once("ed25519.php");
    
    class Cryptonote
    {
        protected $ed25519;
        public function __construct()
        {
            $this->ed25519 = new ed25519();
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
        
    }
