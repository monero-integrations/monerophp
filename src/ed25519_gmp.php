<?php
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
class ed25519GMP
{
    public $b;

    public $q;

    public $l;

    public $d;

    public $I;

    public $By;

    public $Bx;

    public $B;

    public function __construct()
    {
        $this->b = 256;
        $this->q = "57896044618658097711785492504343953926634992332820282019728792003956564819949"; //gmp_sub(gmp_pow(2, 255),19);
        $this->l = "7237005577332262213973186563042994240857116359379907606001950938285454250989"; //gmp_add(gmp_pow(2,252),27742317777372353535851937790883648493);
        $this->d = "-4513249062541557337682894930092624173785641285191125241628941591882900924598840740"; //gmp_mul(-121665,$this->inv(121666));
        $this->I = "19681161376707505956807079304988542015446066515923890162744021073123829784752"; //$this->expmod(2,  gmp_div((gmp_sub($this->q,1)),4),$this->q);
        $this->By = "46316835694926478169428394003475163141307993866256225615783033603165251855960"; //gmp_mul(4,$this->inv(5));
        $this->Bx = "15112221349535400772501151409588531511454012693041857206046113283949847762202"; //$this->xrecover($this->By);
        $this->B = array(
            "15112221349535400772501151409588531511454012693041857206046113283949847762202",
            "46316835694926478169428394003475163141307993866256225615783033603165251855960"
        ); //array(gmp_mod($this->Bx,$this->q),gmp_mod($this->By,$this->q));
    }

    public function H($m)
    {
        return hash('sha512', $m, true);
    }

    //((n % M) + M) % M //python modulus craziness
    public function pymod($x, $m)
    {
        $mod = gmp_mod($x, $m);
        if ($mod < 0) {
            $mod = gmp_add($mod, $m);
        }

        return $mod;
    }

    public function expmod($b, $e, $m)
    {
        //if($e==0){return 1;}
        $t = gmp_powm($b, $e, $m);
        if ($t < 0) {
            $t = gmp_add($t, $m);
        }

        return $t;
    }

    public function inv($x)
    {
        return $this->expmod($x, gmp_sub($this->q, 2), $this->q);
    }

    public function xrecover($y)
    {
        $y2 = gmp_pow($y, 2);
        $xx = gmp_mul(gmp_sub($y2, 1), $this->inv(gmp_add(gmp_mul($this->d, $y2), 1)));
        $x = $this->expmod($xx, gmp_div(gmp_add($this->q, 3), 8, 0), $this->q);
        if ($this->pymod(gmp_sub(gmp_pow($x, 2), $xx), $this->q) != 0) {
            $x = $this->pymod(gmp_mul($x, $this->I), $this->q);
        }
        if (substr($x, -1)%2 != 0) {
            $x = gmp_sub($this->q, $x);
        }

        return $x;
    }

    public function edwards($P, $Q)
    {
        list($x1, $y1) = $P;
        list($x2, $y2) = $Q;
        $xmul = gmp_mul($x1, $x2);
        $ymul = gmp_mul($y1, $y2);
        $com = gmp_mul($this->d, gmp_mul($xmul, $ymul));
        $x3 = gmp_mul(gmp_add(gmp_mul($x1, $y2), gmp_mul($x2, $y1)), $this->inv(gmp_add(1, $com)));
        $y3 = gmp_mul(gmp_add($ymul, $xmul), $this->inv(gmp_sub(1, $com)));

        return array($this->pymod($x3, $this->q), $this->pymod($y3, $this->q));
    }

    public function scalarmult($P, $e)
    {
        if ($e == 0) {
            return array(0, 1);
        }
        $Q = $this->scalarmult($P, gmp_div($e, 2, 0));
        $Q = $this->edwards($Q, $Q);
        if (substr($e, -1)%2 == 1) {
            $Q = $this->edwards($Q, $P);
        }

        return $Q;
    }

    public function scalarloop($P, $e)
    {
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
        $binary_i = '';
        do {
            $binary_i = substr($decimal_i, -1)%2 .$binary_i;
            $decimal_i = gmp_div($decimal_i, '2', 0);
        } while (gmp_cmp($decimal_i, '0'));

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
        return (ord($h[(int) gmp_div($i, 8, 0)]) >> substr($i, -3)%8) & 1;
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
        $h = $this->H($sk);
        $sum = 0;
        for ($i = 3; $i < $this->b-2; $i++) {
            $sum = gmp_add($sum, gmp_mul(gmp_pow(2, $i), $this->bit($h, $i)));
        }
        $a = gmp_add(gmp_pow(2, $this->b-2), $sum);
        $A = $this->scalarmult($this->B, $a);
        $data = $this->encodepoint($A);

        return $data;
    }

    public function Hint($m)
    {
        $h = $this->H($m);
        $sum = 0;
        for ($i = 0; $i < $this->b*2; $i++) {
            $sum = gmp_add($sum, gmp_mul(gmp_pow(2, $i), $this->bit($h, $i)));
        }

        return $sum;
    }

    public function signature($m, $sk, $pk)
    {
        $h = $this->H($sk);
        $a = gmp_pow(2, (gmp_sub($this->b, 2)));
        for ($i = 3; $i < $this->b-2; $i++) {
            $a = gmp_add($a, gmp_mul(gmp_pow(2, $i), $this->bit($h, $i)));
        }
        $r = $this->Hint(substr($h, $this->b/8, ($this->b/4-$this->b/8)).$m);
        $R = $this->scalarmult($this->B, $r);
        $encR = $this->encodepoint($R);
        $S = $this->pymod(gmp_add($r, gmp_mul($this->Hint($encR.$pk.$m), $a)), $this->l);

        return $encR.$this->encodeint($S);
    }

    public function isoncurve($P)
    {
        list($x, $y) = $P;
        $x2 = gmp_pow($x, 2);
        $y2 = gmp_pow($y, 2);

        return $this->pymod(gmp_sub(gmp_sub(gmp_sub($y2, $x2), 1), gmp_mul($this->d, gmp_mul($x2, $y2))), $this->q) == 0;
    }

    public function decodeint($s)
    {
        $sum = 0;
        for ($i = 0; $i < $this->b; $i++) {
            $sum = gmp_add($sum, gmp_mul(gmp_pow(2, $i), $this->bit($s, $i)));
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
        if ($e == 0) {
            return array(0, 1);
        }
        $Q = $this->scalarmult($this->B, gmp_div($e, 2, 0));
        $Q = $this->edwards($Q, $Q);
        if (substr($e, -1)%2 == 1) {
            $Q = $this->edwards($Q, $this->B);
        }

        return $Q;
    }
}
