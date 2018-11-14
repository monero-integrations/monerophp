<?php
/*
Copyright (c) 2018 Monero-Integrations
*/
    class Varint
    {
        public function encode_varint($data)
        {
            $orig = $data;

            if ($data < 0x80)
            {
               return bin2hex(pack('C', $data));
            }

            $encodedBytes = [];
            while ($data > 0)
            {
               $encodedBytes[] = 0x80 | ($data & 0x7f);
               $data >>= 7;
            }

            $encodedBytes[count($encodedBytes)-1] &= 0x7f;
            $bytes = call_user_func_array('pack', array_merge(array('C*'), $encodedBytes));;
            return bin2hex($bytes);
        }
        
        public function decode_varint($data)
        {
            $result = 0;
            $c = 0;
            $pos = 0;
            
            while (true)
            {
                $isLastByteInVarInt = true;
                $i = hexdec($data[$pos]);
		        if ($i >= 128)
		        {
                    $isLastByteInVarInt = false;
                    $i -= 128;
                }
                $result += ($i * (128 ** $c));
                $c += 1;
                $pos += 1;
                
                if ($isLastByteInVarInt);
                    break;
            }
            return $result;
        }
    }
