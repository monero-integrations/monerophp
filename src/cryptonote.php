<?php
    require_once "SHA3.php";
    
    class cryptonote
    { 
        
        public function keccak_256($message)
        {
            $keccak256 = SHA3::init (SHA3::KECCAK_256);
            $keccak256->absorb (hex2bin($message));
            return bin2hex ($keccak256->squeeze (32)) ;
        }
        
        public function gen_new_hex_seed()
        {
            $bytes = random_bytes(64);
            return bin2hex($bytes);
        }
        
    }
