<?php
/*
Copyright (c) 2018 Monero-Integrations
*/
    require_once("varint.php");
    
    class Serialize
    {
        protected $varint;
        
        public function __construct()
        {
            $this->varint = new Varint();
        }
        
        public function deserialize_block_header($block)
        {
          $data = str_split($block, 2);
          
          $major_version = $this->varint->decode_varint($data);
          $data = $this->varint->pop_varint($data);
          
          $minor_version = $this->varint->decode_varint($data);
          $data = $this->varint->pop_varint($data);
          
          $timestamp = $this->varint->decode_varint($data);
          $data = $this->varint->pop_varint($data);
          
          $nonce = $this->varint->decode_varint($data);
          $data = $this->varint->pop_varint($data);
          
          return array("major_version" => $major_version,
                       "minor_version" => $minor_version,
                       "timestamp" => $timestamp,
                       "nonce" => $nonce);
        }

    }
