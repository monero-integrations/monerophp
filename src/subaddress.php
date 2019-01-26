<?php /* -*- coding: utf-8; indent-tabs-mode: t; tab-width: 4 -*-
vim: ts=4 noet ai */

/*
	Streamable SHA-3 for PHP 5.2+, with no lib/ext dependencies!

	Copyright © 2018  Desktopd Developers

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU Lesser General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public License
	along with this program.  If not, see <https://www.gnu.org/licenses/>.

	@license LGPL-3+
	@file
*/


/*
	SHA-3 (FIPS-202) for PHP strings (byte arrays) (PHP 5.2.1+)
	PHP 7.0 computes SHA-3 about 4 times faster than PHP 5.2 - 5.6 (on x86_64)

	Based on the reference implementations, which are under CC-0
	Reference: http://keccak.noekeon.org/

	This uses PHP's native byte strings. Supports 32-bit as well as 64-bit
	systems. Also for LE vs. BE systems.
*/
class SHA3 {
	const SHA3_224 = 1;
	const SHA3_256 = 2;
	const SHA3_384 = 3;
	const SHA3_512 = 4;

	const SHAKE128 = 5;
	const SHAKE256 = 6;

		const KECCAK_256 = 7;


	public static function init ($type = null) {
		switch ($type) {
			case self::SHA3_224: return new self (1152, 448, 0x06, 28);
			case self::SHA3_256: return new self (1088, 512, 0x06, 32);
			case self::SHA3_384: return new self (832, 768, 0x06, 48);
			case self::SHA3_512: return new self (576, 1024, 0x06, 64);
			case self::SHAKE128: return new self (1344, 256, 0x1f);
			case self::SHAKE256: return new self (1088, 512, 0x1f);
			case self::KECCAK_256: return new self (1088, 512, 0x01, 32);
		}

		throw new Exception ('Invalid operation type');
	}


	/*
		Feed input to SHA-3 "sponge"
	*/
	public function absorb ($data) {
		if (self::PHASE_INPUT != $this->phase) {
			throw new Exception ('No more input accepted');
		}

		$rateInBytes = $this->rateInBytes;
		$this->inputBuffer .= $data;
		while (strlen ($this->inputBuffer) >= $rateInBytes) {
			list ($input, $this->inputBuffer) = array (
				substr ($this->inputBuffer, 0, $rateInBytes)
				, substr ($this->inputBuffer, $rateInBytes));

			$blockSize = $rateInBytes;
			for ($i = 0; $i < $blockSize; $i++) {
				$this->state[$i] = $this->state[$i] ^ $input[$i];
			}

			$this->state = self::keccakF1600Permute ($this->state);
			$this->blockSize = 0;
		}

		return $this;
	}

	/*
		Get hash output
	*/
	public function squeeze ($length = null) {
		$outputLength = $this->outputLength; // fixed length output
		if ($length && 0 < $outputLength && $outputLength != $length) {
			throw new Exception ('Invalid length');
		}

		if (self::PHASE_INPUT == $this->phase) {
			$this->finalizeInput ();
		}

		if (self::PHASE_OUTPUT != $this->phase) {
			throw new Exception ('No more output allowed');
		}
		if (0 < $outputLength) {
			$this->phase = self::PHASE_DONE;
			return $this->getOutputBytes ($outputLength);
		}

		$blockLength = $this->rateInBytes;
		list ($output, $this->outputBuffer) = array (
			substr ($this->outputBuffer, 0, $length)
			, substr ($this->outputBuffer, $length));
		$neededLength = $length - strlen ($output);
		$diff = $neededLength % $blockLength;
		if ($diff) {
			$readLength = (($neededLength - $diff) / $blockLength + 1)
				* $blockLength;
		} else {
			$readLength = $neededLength;
		}

		$read = $this->getOutputBytes ($readLength);
		$this->outputBuffer .= substr ($read, $neededLength);
		return $output . substr ($read, 0, $neededLength);
	}


	// internally used
	const PHASE_INIT = 1;
	const PHASE_INPUT = 2;
	const PHASE_OUTPUT = 3;
	const PHASE_DONE = 4;

	private $phase = self::PHASE_INIT;
	private $state; // byte array (string)
	private $rateInBytes; // positive integer
	private $suffix; // 8-bit unsigned integer
	private $inputBuffer = ''; // byte array (string): max length = rateInBytes
	private $outputLength = 0;
	private $outputBuffer = '';


	public function __construct ($rate, $capacity, $suffix, $length = 0) {
		if (1600 != ($rate + $capacity)) {
			throw new Error ('Invalid parameters');
		}
		if (0 != ($rate % 8)) {
			throw new Error ('Invalid rate');
		}

		$this->suffix = $suffix;
		$this->state = str_repeat ("\0", 200);
		$this->blockSize = 0;

		$this->rateInBytes = $rate / 8;
		$this->outputLength = $length;
		$this->phase = self::PHASE_INPUT;
		return;
	}

	protected function finalizeInput () {
		$this->phase = self::PHASE_OUTPUT;

		$input = $this->inputBuffer;
		$inputLength = strlen ($input);
		if (0 < $inputLength) {
			$blockSize = $inputLength;
			for ($i = 0; $i < $blockSize; $i++) {
				$this->state[$i] = $this->state[$i] ^ $input[$i];
			}

			$this->blockSize = $blockSize;
		}

		// Padding
		$rateInBytes = $this->rateInBytes;
		$this->state[$this->blockSize] = $this->state[$this->blockSize]
			^ chr ($this->suffix);
		if (($this->suffix & 0x80) != 0
			&& $this->blockSize == ($rateInBytes - 1)) {
			$this->state = self::keccakF1600Permute ($this->state);
		}
		$this->state[$rateInBytes - 1] = $this->state[$rateInBytes - 1] ^ "\x80";
		$this->state = self::keccakF1600Permute ($this->state);
	}

	protected function getOutputBytes ($outputLength) {
		// Squeeze
		$output = '';
		while (0 < $outputLength) {
			$blockSize = min ($outputLength, $this->rateInBytes);
			$output .= substr ($this->state, 0, $blockSize);
			$outputLength -= $blockSize;
			if (0 < $outputLength) {
				$this->state = self::keccakF1600Permute ($this->state);
			}
		}

		return $output;
	}

	/*
		1600-bit state version of Keccak's permutation
	*/
	protected static function keccakF1600Permute ($state) {
		$lanes = str_split ($state, 8);
		$R = 1;
		$values = "\1\2\4\10\20\40\100\200";

		for ($round = 0; $round < 24; $round++) {
			// θ step
			$C = array ();
			for ($x = 0; $x < 5; $x++) {
				// (x, 0) (x, 1) (x, 2) (x, 3) (x, 4)
				$C[$x] = $lanes[$x] ^ $lanes[$x + 5] ^ $lanes[$x + 10]
					^ $lanes[$x + 15] ^ $lanes[$x + 20];
			}
			for ($x = 0; $x < 5; $x++) {
				//$D = $C[($x + 4) % 5] ^ self::rotL64 ($C[($x + 1) % 5], 1);
				$D = $C[($x + 4) % 5] ^ self::rotL64One ($C[($x + 1) % 5]);
				for ($y = 0; $y < 5; $y++) {
					$idx = $x + 5 * $y; // x, y
					$lanes[$idx] = $lanes[$idx] ^ $D;
				}
			}
			unset ($C, $D);

			// ρ and π steps
			$x = 1;
			$y = 0;
			$current = $lanes[1]; // x, y
			for ($t = 0; $t < 24; $t++) {
				list ($x, $y) = array ($y, (2 * $x + 3 * $y) % 5);
				$idx = $x + 5 * $y;
				list ($current, $lanes[$idx]) = array ($lanes[$idx]
					, self::rotL64 ($current
						, (($t + 1) * ($t + 2) / 2) % 64));
			}
			unset ($temp, $current);

			// χ step
			$temp = array ();
			for ($y = 0; $y < 5; $y++) {
				for ($x = 0; $x < 5; $x++) {
					$temp[$x] = $lanes[$x + 5 * $y];
				}
				for ($x = 0; $x < 5; $x++) {
					$lanes[$x + 5 * $y] = $temp[$x]
						^ ((~ $temp[($x + 1) % 5]) & $temp[($x + 2) % 5]);

				}
			}
			unset ($temp);

			// ι step
			for ($j = 0; $j < 7; $j++) {
				$R = (($R << 1) ^ (($R >> 7) * 0x71)) & 0xff;
				if ($R & 2) {
					$offset = (1 << $j) - 1;
					$shift = $offset % 8;
					$octetShift = ($offset - $shift) / 8;
					$n = "\0\0\0\0\0\0\0\0";
					$n[$octetShift] = $values[$shift];

					$lanes[0] = $lanes[0]
						^ $n;
						//^ self::rotL64 ("\1\0\0\0\0\0\0\0", (1 << $j) - 1);
				}
			}
		}

		return implode ($lanes);
	}

	protected static function rotL64_64 ($n, $offset) {
		return ($n << $offset) & ($n >> (64 - $offset));
	}

	/*
		64-bit bitwise left rotation (Little endian)
	*/
	protected static function rotL64 ($n, $offset) {

		//$n = (binary) $n;
		//$offset = ((int) $offset) % 64;
		//if (8 != strlen ($n)) throw new Exception ('Invalid number');
		//if ($offset < 0) throw new Exception ('Invalid offset');

		$shift = $offset % 8;
		$octetShift = ($offset - $shift) / 8;
		$n = substr ($n, - $octetShift) . substr ($n, 0, - $octetShift);

		$overflow = 0x00;
		for ($i = 0; $i < 8; $i++) {
			$a = ord ($n[$i]) << $shift;
			$n[$i] = chr (0xff & $a | $overflow);
			$overflow = $a >> 8;
		}
		$n[0] = chr (ord ($n[0]) | $overflow);
		return $n;
	}

	/*
		64-bit bitwise left rotation (Little endian)
	*/
	protected static function rotL64One ($n) {
		list ($n[0], $n[1], $n[2], $n[3], $n[4], $n[5], $n[6], $n[7])
			= array (
				chr (((ord ($n[0]) << 1) & 0xff) ^ (ord ($n[7]) >> 7))
				,chr (((ord ($n[1]) << 1) & 0xff) ^ (ord ($n[0]) >> 7))
				,chr (((ord ($n[2]) << 1) & 0xff) ^ (ord ($n[1]) >> 7))
				,chr (((ord ($n[3]) << 1) & 0xff) ^ (ord ($n[2]) >> 7))
				,chr (((ord ($n[4]) << 1) & 0xff) ^ (ord ($n[3]) >> 7))
				,chr (((ord ($n[5]) << 1) & 0xff) ^ (ord ($n[4]) >> 7))
				,chr (((ord ($n[6]) << 1) & 0xff) ^ (ord ($n[5]) >> 7))
				,chr (((ord ($n[7]) << 1) & 0xff) ^ (ord ($n[6]) >> 7)));
		return $n;
	}
}

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2013 John Judy
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

//ini_set('xdebug.max_nesting_level', 0);
/**
 * A PHP implementation of the Python ED25519 library
 *
 * @author johnj
 *
 * @link   http://ed25519.cr.yp.to/software.html Other ED25519 implementations this is referenced from
 */
class ed25519
{
    public $b;

    public $q;

    public $l;

    public $d;

    public $I;

    public $By;

    public $Bx;

    public $B;

    public $gmp; // Is the GMP extension available?

    public function __construct()
    {
        $this->b = 256;
        $this->q = "57896044618658097711785492504343953926634992332820282019728792003956564819949"; //bcsub(bcpow(2, 255),19);
        $this->l = "7237005577332262213973186563042994240857116359379907606001950938285454250989"; //bcadd(bcpow(2,252),27742317777372353535851937790883648493);
        $this->d = "-4513249062541557337682894930092624173785641285191125241628941591882900924598840740"; //bcmul(-121665,$this->inv(121666));
        $this->I = "19681161376707505956807079304988542015446066515923890162744021073123829784752"; //$this->expmod(2,  bcdiv((bcsub($this->q,1)),4),$this->q);
        $this->By = "46316835694926478169428394003475163141307993866256225615783033603165251855960"; //bcmul(4,$this->inv(5));
        $this->Bx = "15112221349535400772501151409588531511454012693041857206046113283949847762202"; //$this->xrecover($this->By);
        $this->B = array(
            "15112221349535400772501151409588531511454012693041857206046113283949847762202",
            "46316835694926478169428394003475163141307993866256225615783033603165251855960"
        ); //array(bcmod($this->Bx,$this->q),bcmod($this->By,$this->q));

        $this->gmp = extension_loaded('gmp');
    }

    public function H($m)
    {
        return hash('sha512', $m, true);
    }

    //((n % M) + M) % M //python modulus craziness
    public function pymod($x, $m)
    {
        if ($this->gmp) {
            $mod = gmp_mod($x, $m);
            if ($mod < 0) {
                $mod = gmp_add($mod, $m);
            }
        } else {
            $mod = bcmod($x, $m);
            if ($mod < 0) {
                $mod = bcadd($mod, $m);
            }
        }

        return $mod;
    }

    public function expmod($b, $e, $m)
    {
        //if($e==0){return 1;}
        if ($this->gmp) {
            $t = gmp_powm($b, $e, $m);
            if ($t < 0) {
                $t = gmp_add($t, $m);
            }
        } else {
            $t = bcpowmod($b, $e, $m);
            if ($t[0] === '-') {
                $t = bcadd($t, $m);
            }
        }

        return $t;
    }

    public function inv($x)
    {
        if ($this->gmp) {
            return $this->expmod($x, gmp_sub($this->q, 2), $this->q);
        } else {
            return $this->expmod($x, bcsub($this->q, 2), $this->q);
        }
    }

    public function xrecover($y)
    {
        if ($this->gmp) {
            $y2 = gmp_pow($y, 2);
            $xx = gmp_mul(gmp_sub($y2, 1), $this->inv(gmp_add(gmp_mul($this->d, $y2), 1)));
            $x = $this->expmod($xx, gmp_div(gmp_add($this->q, 3), 8, 0), $this->q);
            if ($this->pymod(gmp_sub(gmp_pow($x, 2), $xx), $this->q) != 0) {
                $x = $this->pymod(gmp_mul($x, $this->I), $this->q);
            }
            if (substr($x, -1)%2 != 0) {
                $x = gmp_sub($this->q, $x);
            }
        } else {
            $y2 = bcpow($y, 2);
            $xx = bcmul(bcsub($y2, 1), $this->inv(bcadd(bcmul($this->d, $y2), 1)));
            $x = $this->expmod($xx, bcdiv(bcadd($this->q, 3), 8, 0), $this->q);
            if ($this->pymod(bcsub(bcpow($x, 2), $xx), $this->q) != 0) {
                $x = $this->pymod(bcmul($x, $this->I), $this->q);
            }
            if (substr($x, -1)%2 != 0) {
                $x = bcsub($this->q, $x);
            }
        }

        return $x;
    }

    public function edwards($P, $Q)
    {
        if ($this->gmp) {
            list($x1, $y1) = $P;
            list($x2, $y2) = $Q;
            $xmul = gmp_mul($x1, $x2);
            $ymul = gmp_mul($y1, $y2);
            $com = gmp_mul($this->d, gmp_mul($xmul, $ymul));
            $x3 = gmp_mul(gmp_add(gmp_mul($x1, $y2), gmp_mul($x2, $y1)), $this->inv(gmp_add(1, $com)));
            $y3 = gmp_mul(gmp_add($ymul, $xmul), $this->inv(gmp_sub(1, $com)));

            return array($this->pymod($x3, $this->q), $this->pymod($y3, $this->q));
        } else {
            list($x1, $y1) = $P;
            list($x2, $y2) = $Q;
            $xmul = bcmul($x1, $x2);
            $ymul = bcmul($y1, $y2);
            $com = bcmul($this->d, bcmul($xmul, $ymul));
            $x3 = bcmul(bcadd(bcmul($x1, $y2), bcmul($x2, $y1)), $this->inv(bcadd(1, $com)));
            $y3 = bcmul(bcadd($ymul, $xmul), $this->inv(bcsub(1, $com)));

            return array($this->pymod($x3, $this->q), $this->pymod($y3, $this->q));
        }
    }

    public function scalarmult($P, $e)
    {
        if ($this->gmp) {
            if ($e == 0) {
                return array(0, 1);
            }
            $Q = $this->scalarmult($P, gmp_div($e, 2, 0));
            $Q = $this->edwards($Q, $Q);
            if (substr($e, -1)%2 == 1) {
                $Q = $this->edwards($Q, $P);
            }
        } else {
            if ($e == 0) {
                return array(0, 1);
            }
            $Q = $this->scalarmult($P, bcdiv($e, 2, 0));
            $Q = $this->edwards($Q, $Q);
            if (substr($e, -1)%2 == 1) {
                $Q = $this->edwards($Q, $P);
            }
        }

        return $Q;
    }

    public function scalarloop($P, $e)
    {
        if ($this->gmp) {
            $temp = array();
            $loopE = $e;
            while ($loopE > 0) {
                array_unshift($temp, $loopE);
                $loopE = gmp_div($loopE, 2, 0);
            }
            $Q = array();
            foreach ($temp as $e) {
                if ($e == 1) {
                    $Q = $this->edwards(array(0, 1), $P);
                } elseif (substr($e, -1)%2 == 1) {
                    $Q = $this->edwards($this->edwards($Q, $Q), $P);
                } else {
                    $Q = $this->edwards($Q, $Q);
                }
            }
        } else {
            $temp = array();
            $loopE = $e;
            while ($loopE > 0) {
                array_unshift($temp, $loopE);
                $loopE = bcdiv($loopE, 2, 0);
            }
            $Q = array();
            foreach ($temp as $e) {
                if ($e == 1) {
                    $Q = $this->edwards(array(0, 1), $P);
                } elseif (substr($e, -1)%2 == 1) {
                    $Q = $this->edwards($this->edwards($Q, $Q), $P);
                } else {
                    $Q = $this->edwards($Q, $Q);
                }
            }
        }

        return $Q;
    }

    public function bitsToString($bits)
    {
        $string = '';
        for ($i = 0; $i < $this->b/8; $i++) {
            $sum = 0;
            for ($j = 0; $j < 8; $j++) {
                $bit = $bits[$i*8+$j];
                $sum += (int) $bit << $j;
            }
            $string .= chr($sum);
        }

        return $string;
    }

    public function dec2bin_i($decimal_i)
    {
        if ($this->gmp) {
        $binary_i = '';
            do {
                $binary_i = substr($decimal_i, -1)%2 .$binary_i;
                $decimal_i = gmp_div($decimal_i, '2', 0);
            } while (gmp_cmp($decimal_i, '0'));
        } else {
            $binary_i = '';
            do {
                $binary_i = substr($decimal_i, -1)%2 .$binary_i;
                $decimal_i = bcdiv($decimal_i, '2', 0);
            } while (bccomp($decimal_i, '0'));
        }

        return ($binary_i);
    }

    public function encodeint($y)
    {
        $bits = substr(str_pad(strrev($this->dec2bin_i($y)), $this->b, '0', STR_PAD_RIGHT), 0, $this->b);

        return $this->bitsToString($bits);
    }

    public function encodepoint($P)
    {
        list($x, $y) = $P;
        $bits = substr(str_pad(strrev($this->dec2bin_i($y)), $this->b-1, '0', STR_PAD_RIGHT), 0, $this->b-1);
        $bits .= (substr($x, -1)%2 == 1 ? '1' : '0');

        return $this->bitsToString($bits);
    }

    public function bit($h, $i)
    {
        if ($this->gmp) {
            return (ord($h[(int) gmp_div($i, 8, 0)]) >> substr($i, -3)%8) & 1;
        } else {
            return (ord($h[(int) bcdiv($i, 8, 0)]) >> substr($i, -3)%8) & 1;
        }
    }

    /**
     * Generates the public key of a given private key
     *
     * @param string $sk the secret key
     *
     * @return string
     */
    public function publickey($sk)
    {
        if ($this->gmp) {
            $h = $this->H($sk);
            $sum = 0;
            for ($i = 3; $i < $this->b-2; $i++) {
                $sum = gmp_add($sum, gmp_mul(gmp_pow(2, $i), $this->bit($h, $i)));
            }
            $a = gmp_add(gmp_pow(2, $this->b-2), $sum);
            $A = $this->scalarmult($this->B, $a);
            $data = $this->encodepoint($A);
        } else {
            $h = $this->H($sk);
            $sum = 0;
            for ($i = 3; $i < $this->b-2; $i++) {
                $sum = bcadd($sum, bcmul(bcpow(2, $i), $this->bit($h, $i)));
            }
            $a = bcadd(bcpow(2, $this->b-2), $sum);
            $A = $this->scalarmult($this->B, $a);
            $data = $this->encodepoint($A);
        }

        return $data;
    }

    public function Hint($m)
    {
        if ($this->gmp) {
            $h = $this->H($m);
            $sum = 0;
            for ($i = 0; $i < $this->b*2; $i++) {
                $sum = gmp_add($sum, gmp_mul(gmp_pow(2, $i), $this->bit($h, $i)));
            }
        } else {
            $h = $this->H($m);
            $sum = 0;
            for ($i = 0; $i < $this->b*2; $i++) {
                $sum = bcadd($sum, bcmul(bcpow(2, $i), $this->bit($h, $i)));
            }
        }

        return $sum;
    }

    public function signature($m, $sk, $pk)
    {
        if ($this->gmp) {
            $h = $this->H($sk);
            $a = gmp_pow(2, (gmp_sub($this->b, 2)));
            for ($i = 3; $i < $this->b-2; $i++) {
                $a = gmp_add($a, gmp_mul(gmp_pow(2, $i), $this->bit($h, $i)));
            }
            $r = $this->Hint(substr($h, $this->b/8, ($this->b/4-$this->b/8)).$m);
            $R = $this->scalarmult($this->B, $r);
            $encR = $this->encodepoint($R);
            $S = $this->pymod(gmp_add($r, gmp_mul($this->Hint($encR.$pk.$m), $a)), $this->l);
        } else {
            $h = $this->H($sk);
            $a = bcpow(2, (bcsub($this->b, 2)));
            for ($i = 3; $i < $this->b-2; $i++) {
                $a = bcadd($a, bcmul(bcpow(2, $i), $this->bit($h, $i)));
            }
            $r = $this->Hint(substr($h, $this->b/8, ($this->b/4-$this->b/8)).$m);
            $R = $this->scalarmult($this->B, $r);
            $encR = $this->encodepoint($R);
            $S = $this->pymod(bcadd($r, bcmul($this->Hint($encR.$pk.$m), $a)), $this->l);
        }

        return $encR.$this->encodeint($S);
    }

    public function isoncurve($P)
    {
        if ($this->gmp) {
            list($x, $y) = $P;
            $x2 = gmp_pow($x, 2);
            $y2 = gmp_pow($y, 2);

            return $this->pymod(gmp_sub(gmp_sub(gmp_sub($y2, $x2), 1), gmp_mul($this->d, gmp_mul($x2, $y2))), $this->q) == 0;
        } else {
            list($x, $y) = $P;
            $x2 = bcpow($x, 2);
            $y2 = bcpow($y, 2);

            return $this->pymod(bcsub(bcsub(bcsub($y2, $x2), 1), bcmul($this->d, bcmul($x2, $y2))), $this->q) == 0;
        }
    }

    public function decodeint($s)
    {
        if ($this->gmp) {
            $sum = 0;
            for ($i = 0; $i < $this->b; $i++) {
                $sum = gmp_add($sum, gmp_mul(gmp_pow(2, $i), $this->bit($s, $i)));
            }
        } else {
            $sum = 0;
            for ($i = 0; $i < $this->b; $i++) {
                $sum = bcadd($sum, bcmul(bcpow(2, $i), $this->bit($s, $i)));
            }
        }

        return $sum;
    }

    /*
     * def decodepoint(s):
      y = sum(2**i * bit(s,i) for i in range(0,b-1))
      x = xrecover(y)
      if x & 1 != bit(s,b-1): x = q-x
      P = [x,y]
      if not isoncurve(P): raise Exception("decoding point that is not on curve")
      return P

     */
    public function decodepoint($s)
    {
        if ($this->gmp) {
            $y = 0;
            for ($i = 0; $i < $this->b-1; $i++) {
                $y = gmp_add($y, gmp_mul(gmp_pow(2, $i), $this->bit($s, $i)));
            }
            $x = $this->xrecover($y);
            if (substr($x, -1)%2 != $this->bit($s, $this->b-1)) {
                $x = gmp_sub($this->q, $x);
            }
            $P = array($x, $y);
            if (!$this->isoncurve($P)) {
                throw new \Exception("Decoding point that is not on curve");
            }
        } else {
            $y = 0;
            for ($i = 0; $i < $this->b-1; $i++) {
                $y = bcadd($y, bcmul(bcpow(2, $i), $this->bit($s, $i)));
            }
            $x = $this->xrecover($y);
            if (substr($x, -1)%2 != $this->bit($s, $this->b-1)) {
                $x = bcsub($this->q, $x);
            }
            $P = array($x, $y);
            if (!$this->isoncurve($P)) {
                throw new \Exception("Decoding point that is not on curve");
            }
        }

        return $P;
    }

    public function checkvalid($s, $m, $pk)
    {
        if (strlen($s) != $this->b/4) {
            throw new \Exception('Signature length is wrong');
        }
        if (strlen($pk) != $this->b/8) {
            throw new \Exception('Public key length is wrong: '.strlen($pk));
        }
        $R = $this->decodepoint(substr($s, 0, $this->b/8));
        try {
            $A = $this->decodepoint($pk);
        } catch (\Exception $e) {
            return false;
        }
        $S = $this->decodeint(substr($s, $this->b/8, $this->b/4));
        $h = $this->Hint($this->encodepoint($R).$pk.$m);

        return $this->scalarmult($this->B, $S) == $this->edwards($R, $this->scalarmult($A, $h));
    }

    // The code below is by the Monero-Integrations team

    public function scalarmult_base($e)
    {
        if ($this->gmp) {
            if ($e == 0) {
                return array(0, 1);
            }
            $Q = $this->scalarmult($this->B, gmp_div($e, 2, 0));
            $Q = $this->edwards($Q, $Q);
            if (substr($e, -1)%2 == 1) {
                $Q = $this->edwards($Q, $this->B);
            }
        } else {
            if ($e == 0) {
                return array(0, 1);
            }
            $Q = $this->scalarmult($this->B, bcdiv($e, 2, 0));
            $Q = $this->edwards($Q, $Q);
            if (substr($e, -1)%2 == 1) {
                $Q = $this->edwards($Q, $this->B);
            }
        }

        return $Q;
    }
}

/**
 *
 * monerophp/base58
 *
 * A PHP Base58 codec
 * https://github.com/monero-integrations/monerophp
 *
 * Using work from
 *   bigreddmachine [MoneroPy] (https://github.com/bigreddmachine)
 *   Paul Shapiro [mymonero-core-js] (https://github.com/paulshapiro)
 *
 * @author     Monero Integrations Team <support@monerointegrations.com> (https://github.com/monero-integrations)
 * @copyright  2018
 * @license    MIT
 *
 * ============================================================================
 *
 * // Initialize class
 * $base58 = new base58();
 *
 * // Encode a hexadecimal (base16) string as base58
 * $encoded = $base58->encode('0137F8F06C971B168745F562AA107B4D172F336271BC0F9D3B510C14D3460DFB27D8CEBE561E73AC1E11833D5EA40200EB3C82E9C66ACAF1AB1A6BB53C40537C0B7A22160B0E');
 *
 * // Decode
 * $decoded = $base58->decode('479cG5opa54beQWSyqNoWw5tna9sHUNmMTtiFqLPaUhDevpJ2YLwXAggSx5ePdeFrYF8cdbmVRSmp1Kn3t4Y9kFu7rZ7pFw');
 *
 */

class base58
{
  static $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
  static $encoded_block_sizes = [0, 2, 3, 5, 6, 7, 9, 10, 11];
  static $full_block_size = 8;
  static $full_encoded_block_size = 11;

  /**
   *
   * Convert a hexadecimal string to a binary array
   *
   * @param    string  $hex  A hexadecimal string to convert to a binary array
   *
   * @return   array
   *
   */
  private function hex_to_bin($hex)
  {
    if (gettype($hex) != 'string') {
      throw new Exception('base58->hex_to_bin(): Invalid input type (must be a string)');
    }
    if (strlen($hex) % 2 != 0) {
      throw new Exception('base58->hex_to_bin(): Invalid input length (must be even)');
    }

    $res = array_fill(0, strlen($hex) / 2, 0);
    for ($i = 0; $i < strlen($hex) / 2; $i++) {
      $res[$i] = intval(substr($hex, $i * 2, $i * 2 + 2 - $i * 2), 16);
    }
    return $res;
  }

  /**
   *
   * Convert a binary array to a hexadecimal string
   *
   * @param    array   $bin  A binary array to convert to a hexadecimal string
   *
   * @return   string
   *
   */
  private function bin_to_hex($bin)
  {
    if (gettype($bin) != 'array') {
      throw new Exception('base58->bin_to_hex(): Invalid input type (must be an array)');
    }

    $res = [];
    for ($i = 0; $i < count($bin); $i++) {
      $res[] = substr('0'.dechex($bin[$i]), -2);
    }
    return join($res);
  }

  /**
   *
   * Convert a string to a binary array
   *
   * @param    string   $str  A string to convert to a binary array
   *
   * @return   array
   *
   */
  private function str_to_bin($str)
  {
    if (gettype($str) != 'string') {
      throw new Exception('base58->str_to_bin(): Invalid input type (must be a string)');
    }

    $res = array_fill(0, strlen($str), 0);
    for ($i = 0; $i < strlen($str); $i++) {
      $res[$i] = ord($str[$i]);
    }
    return $res;
  }

  /**
   *
   * Convert a binary array to a string
   *
   * @param    array   $bin  A binary array to convert to a string
   *
   * @return   string
   *
   */
  private function bin_to_str($bin)
  {
    if (gettype($bin) != 'array') {
      throw new Exception('base58->bin_to_str(): Invalid input type (must be an array)');
    }

    $res = array_fill(0, count($bin), 0);
    for ($i = 0; $i < count($bin); $i++) {
      $res[$i] = chr($bin[$i]);
    }
    return preg_replace('/[[:^print:]]/', '', join($res)); // preg_replace necessary to strip errant non-ASCII characters eg. ''
  }

  /**
   *
   * Convert a UInt8BE (one unsigned big endian byte) array to UInt64
   *
   * @param    array   $data  A UInt8BE array to convert to UInt64
   *
   * @return   number
   *
   */
  private function uint8_be_to_64($data)
  {
    if (gettype($data) != 'array') {
      throw new Exception ('base58->uint8_be_to_64(): Invalid input type (must be an array)');
    }

    $res = 0;
    $i = 0;
    switch (9 - count($data)) {
      case 1:
        $res = bcadd(bcmul($res, bcpow(2, 8)), $data[$i++]);
      case 2:
        $res = bcadd(bcmul($res, bcpow(2, 8)), $data[$i++]);
      case 3:
        $res = bcadd(bcmul($res, bcpow(2, 8)), $data[$i++]);
      case 4:
        $res = bcadd(bcmul($res, bcpow(2, 8)), $data[$i++]);
      case 5:
        $res = bcadd(bcmul($res, bcpow(2, 8)), $data[$i++]);
      case 6:
        $res = bcadd(bcmul($res, bcpow(2, 8)), $data[$i++]);
      case 7:
        $res = bcadd(bcmul($res, bcpow(2, 8)), $data[$i++]);
      case 8:
        $res = bcadd(bcmul($res, bcpow(2, 8)), $data[$i++]);
        break;
      default:
        throw new Exception('base58->uint8_be_to_64: Invalid input length (1 <= count($data) <= 8)');
      }
    return $res;
  }

  /**
   *
   * Convert a UInt64 (unsigned 64 bit integer) to a UInt8BE array
   *
   * @param    number   $num   A UInt64 number to convert to a UInt8BE array
   * @param    integer  $size  Size of array to return
   *
   * @return   array
   *
   */
  private function uint64_to_8_be($num, $size)
  {
    if (gettype($num) != ('integer' || 'double')) {
      throw new Exception ('base58->uint64_to_8_be(): Invalid input type ($num must be a number)');
    }
    if (gettype($size) != 'integer') {
      throw new Exception ('base58->uint64_to_8_be(): Invalid input type ($size must be an integer)');
    }
    if ($size < 1 || $size > 8) {
      throw new Exception ('base58->uint64_to_8_be(): Invalid size (1 <= $size <= 8)');
    }

    $res = array_fill(0, $size, 0);
    for ($i = $size - 1; $i >= 0; $i--) {
      $res[$i] = bcmod($num, bcpow(2, 8));
      $num = bcdiv($num, bcpow(2, 8));
    }
    return $res;
  }

  /**
   *
   * Convert a hexadecimal (Base16) array to a Base58 string
   *
   * @param    array   $data
   * @param    array   $buf
   * @param    number  $index
   *
   * @return   array
   *
   */
  private function encode_block($data, $buf, $index)
  {
    if (gettype($data) != 'array') {
      throw new Exception('base58->encode_block(): Invalid input type ($data must be an array)');
    }
    if (gettype($buf) != 'array') {
      throw new Exception('base58->encode_block(): Invalid input type ($buf must be an array)');
    }
    if (gettype($index) != ('integer' || 'double')) {
      throw new Exception('base58->encode_block(): Invalid input type ($index must be a number)');
    }
    if (count($data) < 1 or count($data) > self::$full_encoded_block_size) {
      throw new Exception('base58->encode_block(): Invalid input length (1 <= count($data) <= 8)');
    }

    $num = self::uint8_be_to_64($data);
    $i = self::$encoded_block_sizes[count($data)] - 1;
    while ($num > 0) {
      $remainder = bcmod($num, 58);
      $num = bcdiv($num, 58);
      $buf[$index + $i] = ord(self::$alphabet[$remainder]);
      $i--;
    }
    return $buf;
  }

  /**
   *
   * Encode a hexadecimal (Base16) string to Base58
   *
   * @param    string  $hex  A hexadecimal (Base16) string to convert to Base58
   *
   * @return   string
   *
   */
  public function encode($hex)
  {
    if (gettype($hex) != 'string') {
      throw new Exception ('base58->encode(): Invalid input type (must be a string)');
    }

    $data = self::hex_to_bin($hex);
    if (count($data) == 0) {
      return '';
    }

    $full_block_count = floor(count($data) / self::$full_block_size);
    $last_block_size = count($data) % self::$full_block_size;
    $res_size = $full_block_count * self::$full_encoded_block_size + self::$encoded_block_sizes[$last_block_size];

    $res = array_fill(0, $res_size, ord(self::$alphabet[0]));

    for ($i = 0; $i < $full_block_count; $i++) {
      $res = self::encode_block(array_slice($data, $i * self::$full_block_size, ($i * self::$full_block_size + self::$full_block_size) - ($i * self::$full_block_size)), $res, $i * self::$full_encoded_block_size);
    }

    if ($last_block_size > 0) {
      $res = self::encode_block(array_slice($data, $full_block_count * self::$full_block_size, $full_block_count * self::$full_block_size + $last_block_size), $res, $full_block_count * self::$full_encoded_block_size);
    }

    return self::bin_to_str($res);
  }

  /**
   *
   * Convert a Base58 input to hexadecimal (Base16)
   *
   * @param    array    $data
   * @param    array    $buf
   * @param    integer  $index
   *
   * @return   array
   *
   */
  private function decode_block($data, $buf, $index)
  {
    if (gettype($data) != 'array') {
      throw new Exception('base58->decode_block(): Invalid input type ($data must be an array)');
    }
    if (gettype($buf) != 'array') {
      throw new Exception('base58->decode_block(): Invalid input type ($buf must be an array)');
    }
    if (gettype($index) != ('integer' || 'double')) {
      throw new Exception('base58->decode_block(): Invalid input type ($index must be a number)');
    }

    $res_size = self::index_of(self::$encoded_block_sizes, count($data));
    if ($res_size <= 0) {
      throw new Exception('base58->decode_block(): Invalid input length ($data must be a value from base58::$encoded_block_sizes)');
    }

    $res_num = 0;
    $order = 1;
    for ($i = count($data) - 1; $i >= 0; $i--) {
      $digit = strpos(self::$alphabet, chr($data[$i]));
      if ($digit < 0) {
        throw new Exception("base58->decode_block(): Invalid character ($digit \"{$digit}\" not found in base58::$alphabet)");
      }

      $product = bcadd(bcmul($order, $digit), $res_num);
      if ($product > bcpow(2, 64)) {
        throw new Exception('base58->decode_block(): Integer overflow ($product exceeds the maximum 64bit integer)');
      }

      $res_num = $product;
      $order = bcmul($order, 58);
    }
    if ($res_size < self::$full_block_size && bcpow(2, 8 * $res_size) <= 0) {
      throw new Exception('base58->decode_block(): Integer overflow (bcpow(2, 8 * $res_size) exceeds the maximum 64bit integer)');
    }
  
    $tmp_buf = self::uint64_to_8_be($res_num, $res_size);
    for ($i = 0; $i < count($tmp_buf); $i++) {
      $buf[$i + $index] = $tmp_buf[$i];
    }
    return $buf;
  }

  /**
   *
   * Decode a Base58 string to hexadecimal (Base16)
   *
   * @param    string  $hex  A Base58 string to convert to hexadecimal (Base16)
   *
   * @return   string
   *
   */
  public function decode($enc)
  {
    if (gettype($enc) != 'string') {
      throw new Exception ('base58->decode(): Invalid input type (must be a string)');
    }

    $enc = self::str_to_bin($enc);
    if (count($enc) == 0) {
      return '';
    }
    $full_block_count = floor(bcdiv(count($enc), self::$full_encoded_block_size));
    $last_block_size = bcmod(count($enc), self::$full_encoded_block_size);
    $last_block_decoded_size = self::index_of(self::$encoded_block_sizes, $last_block_size);

    $data_size = $full_block_count * self::$full_block_size + $last_block_decoded_size;

    $data = array_fill(0, $data_size, 0);
    for ($i = 0; $i <= $full_block_count; $i++) {
      $data = self::decode_block(array_slice($enc, $i * self::$full_encoded_block_size, ($i * self::$full_encoded_block_size + self::$full_encoded_block_size) - ($i * self::$full_encoded_block_size)), $data, $i * self::$full_block_size);
    }

    if ($last_block_size > 0) {
      $data = self::decode_block(array_slice($enc, $full_block_count * self::$full_encoded_block_size, $full_block_count * self::$full_encoded_block_size + $last_block_size), $data, $full_block_count * self::$full_block_size);
    }

    return self::bin_to_hex($data);
  }

  /**
   *
   * Search an array for a value
   * Source: https://stackoverflow.com/a/30994678
   *
   * @param    array   $haystack  An array to search
   * @param    string  $needle    A string to search for
   *)
   * @return   number             The index of the element found (or -1 for no match)
   *
   */
  private function index_of($haystack, $needle)
  {
    if (gettype($haystack) != 'array') {
      throw new Exception ('base58->decode(): Invalid input type ($haystack must be an array)');
    }
    // if (gettype($needle) != 'string') {
    //   throw new Exception ('base58->decode(): Invalid input type ($needle must be a string)');
    // }

    foreach ($haystack as $key => $value) if ($value === $needle) return $key;
    return -1;
  }
}

/*
  Copyright (c) 2018-2019, Monero Integrations
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

class subaddress
{
	protected $ed25519;
	protected $base58;
	
	public function __construct()
	{
		$this->ed25519 = new ed25519();
		$this->base58 = new base58();
	}
	
	private function sc_reduce($input)
	{
		$integer = $this->ed25519->decodeint(hex2bin($input));
		if($this->ed25519->gmp)
			$modulo = gmp_mod($integer , $this->ed25519->l);
		else
			$modulo = bcmod($integer , $this->ed25519->l);
		$result = bin2hex($this->ed25519->encodeint($modulo));
		return $result;
	}
	
	private function ge_add($point1, $point2)
	{
		$point3 = $this->ed25519->edwards($this->ed25519->decodepoint(hex2bin($point1)), $this->ed25519->decodepoint(hex2bin($point2)));
		return bin2hex($this->ed25519->encodepoint($point3));
	}
	
	private function ge_scalarmult($public, $secret)
	{
		$point = $this->ed25519->decodepoint(hex2bin($public));
		$scalar = $this->ed25519->decodeint(hex2bin($secret));
		$res = $this->ed25519->scalarmult($point, $scalar);
		return bin2hex($this->ed25519->encodepoint($res));
	}
	
	private function ge_scalarmult_base($scalar)
	{
		$decoded = $this->ed25519->decodeint(hex2bin($scalar));
		$res = $this->ed25519->scalarmult_base($decoded);
		return bin2hex($this->ed25519->encodepoint($res));
	}
	
	/*
	 * @param string Hex encoded string of the data to hash
	 * @return string Hex encoded string of the hashed data
	 *
	 */
	private function keccak_256($message)
	{
		$keccak256 = SHA3::init(SHA3::KECCAK_256);
		$keccak256->absorb(hex2bin($message));
		return bin2hex($keccak256->squeeze(32)) ;
	}

	/*
	 * Hs in the cryptonote white paper
	 *
	 * @param string Hex encoded data to hash
	 *
	 * @return string A 32 byte encoded integer
	 */
	private function hash_to_scalar($data)
	{
		$hash = $this->keccak_256($data);
		$scalar = $this->sc_reduce($hash);
		return $scalar;
	}
	
	public function generate_subaddr_secret_key($major_index, $minor_index, $sec_key)
	{
		$prefix = "5375624164647200"; // hex encoding of string "SubAddr"
		$index = pack("II", $major_index, $minor_index);
		return $this->hash_to_scalar($prefix . $sec_key . bin2hex($index));
	}
	
	public function generate_subaddress_spend_public_key($spend_public_key, $subaddr_secret_key)
	{
		$mG = $this->ge_scalarmult_base($subaddr_secret_key);
		$D = $this->ge_add($spend_public_key, $mG);
		return $D;
	}
	
	public function generate_subaddr_view_public_key($subaddr_spend_public_key, $view_secret_key)
	{
		return $this->ge_scalarmult($subaddr_spend_public_key, $view_secret_key);
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
