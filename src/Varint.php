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

/**
 * Varint class for encoding and decoding variable-length integers.
 */
class Varint
{
    /**
     * Encodes an integer into a varint format.
     * @return array<int> The encoded varint.
     */
    public static function encodeVarint(int $value): array
    {
        $data = array_fill(0, 8, 0);
        $pos = 0;

        while (($value & 0xFFFFFF80) !== 0) {
            $data[$pos] = ($value & 0x7F) | 0x80;
            $pos++;
            $value >>= 7;
        }
        $data[$pos] = $value & 0x7F;
        $pos++;

        return array_slice($data, 0, $pos);
    }

    /**
     * Decodes a varint from a hexadecimal string.
     * @param array<int> $data The data to decode.
     */
    public static function decodeVarint(array $data): int
    {
        $result = 0;
        $c = 0;
        $pos = 0;

        while (true) {
            $isLastByteInVarInt = true;
            $i = $data[$pos] & 0xFF; // Ensure we're working with an unsigned integer
            if ($i >= 128) {
                $isLastByteInVarInt = false;
                $i -= 128;
            }
            $result += $i * pow(128, $c);
            $c++;
            $pos++;
            if ($isLastByteInVarInt) {
                break;
            }
        }

        return $result;
    }

    /**
     * @todo: Fix this function
     */
    public function pop_varint($data)
    {
        $result = 0;
        $c = 0;
        $pos = 0;

        while (true) {
            $isLastByteInVarInt = true;
            $i = hexdec($data[$pos]);
            if ($i >= 128) {
                $isLastByteInVarInt = false;
                $i -= 128;
            }
            $result += ($i * (pow(128, $c)));
            $c += 1;
            $pos += 1;

            if ($isLastByteInVarInt) {
                break;
            }
        }
        for ($x = 0; $x < $pos; $x++) {
            array_shift($data);
        }
        return $data;
    }
}
