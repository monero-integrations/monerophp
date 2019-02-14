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
        
        // https://github.com/monero-project/research-lab/blob/master/source-code/StringCT-java/src/how/monero/hodl/util/VarInt.java
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
                $result += ($i * (pow(128, $c)));
                $c += 1;
                $pos += 1;
                
                if ($isLastByteInVarInt)
                    break;
            }
            return $result;
        }
        
        public function pop_varint($data)
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
                $result += ($i * (pow(128, $c)));
                $c += 1;
                $pos += 1;
                
                if ($isLastByteInVarInt)
                    break;
            }
            for ($x = 0; $x < $pos; $x++)
               array_shift($data);
            return $data;
        }
    }
