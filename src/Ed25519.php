<?php

declare(strict_types=1);
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

namespace MoneroIntegrations\MoneroCrypto;

use MoneroIntegrations\MoneroCrypto\BigInteger;

use Exception;

/**
 * A class to represent points in a two-dimensional plane.
 */
class Point
{
    /**
     * The x-coordinate of the point.
     *
     * @var BigInteger
     */
    public $x;

    /**
     * The y-coordinate of the point.
     *
     * @var BigInteger
     */
    public $y;

    public function __construct(BigInteger $x, BigInteger $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function __toString()
    {
        return "x: " . $this->x . ", y: " . $this->y;
    }

    /**
     * Determines if two points are equal.
     */
    public function equals(Point $point): bool
    {
        return $this->x->equals($point->x) && $this->y->equals($point->y);
    }
}

class Ed25519
{
    /** @var BigInteger */
    public $b;
    /** @var BigInteger */
    public $q;
    /** @var BigInteger */
    public $l;
    /** @var BigInteger */
    public $d;
    /** @var BigInteger */
    public $I;
    /** @var Point */
    public $B;
    /** @var Point */
    public $identityPoint;

    public function __construct()
    {
        $this->b = new BigInteger("256"); // 2^8
        $this->q = new BigInteger("57896044618658097711785492504343953926634992332820282019728792003956564819949"); // 2^255 - 19
        $this->l = new BigInteger("7237005577332262213973186563042994240857116359379907606001950938285454250989"); // 2^252 + 27742317777372353535851937790883648493
        $this->d = new BigInteger("-4513249062541557337682894930092624173785641285191125241628941591882900924598840740"); // -121665 * $this->inv(121666);
        $this->I = new BigInteger("19681161376707505956807079304988542015446066515923890162744021073123829784752"); // 2^((q - 1) / 4) % q
        /*
            $By = 4 * inv(5)
            $Bx = xrecover($By)
        */
        $this->B = new Point(new BigInteger("15112221349535400772501151409588531511454012693041857206046113283949847762202"), new BigInteger("46316835694926478169428394003475163141307993866256225615783033603165251855960"));

        $this->identityPoint = new Point(new BigInteger("0"), new BigInteger("1"));
    }

    /**
     * Returns the SHA512 hash of a message in binary form.
     */
    public static function H(string $m): string
    {
        return hash('sha512', $m, true);
    }

    /**
     * Python modulus implementation (for negative numbers).
     */
    private static function pymod(BigInteger $x, BigInteger $m): BigInteger
    {
        $result = $x->mod($m);
        if ($result->cmp(0) < 0) {
            $result = $result->add($m);
        }

        return $result;
    }

    /**
     * Returns the an exponentiation modulo of a number.
     */
    public static function expmod(BigInteger $b, BigInteger $e, BigInteger $m): BigInteger
    {
        if ($e->cmp(0) == 0) {
            return new BigInteger("1");
        }

        $t = $b->powMod($e, $m);
        if ($t->cmp(0) < 0) {
            $t = $t->add($m);
        }

        return $t;
    }

    /**
     * Returns the multiplicative inverse modulo of a number.
     */
    public function inv(BigInteger $x): BigInteger
    {
        return $x->modInverse($this->q);
    }

    /**
     * Performs a recovery of the x-coordinate of a point on the Edwards25519 curve given the y-coordinate.
     */
    public function xrecover(BigInteger $y): BigInteger
    {
        $y2 = $y->pow(2);
        $xx = $y2->sub(1)->mul($this->inv($this->d->mul($y2)->add(1)));

        $x = $xx->powMod($this->q->add(3)->div(8), $this->q);
        $modq = self::pymod($x->pow(2)->sub($xx), $this->q);

        if ($modq->cmp(0) != 0) {
            $x = $x->mul($this->I)->mod($this->q);
        }
        if ($x->mod(2)->cmp(0) != 0) {
            $x = $this->q->sub($x);
        }

        return $x;
    }

    /**
     * Performs the elliptic curve operation P + Q on the Edwards25519 curve.
     */
    public function edwards(Point $P, Point $Q): Point
    {
        $x1 = $P->x;
        $y1 = $P->y;
        $x2 = $Q->x;
        $y2 = $Q->y;

        $x3 = $x1->mul($y2)->add($x2->mul($y1))->mul($this->inv((new BigInteger("1"))->add($this->d->mul($x1)->mul($x2)->mul($y1)->mul($y2))));
        $y3 = $y1->mul($y2)->add($x1->mul($x2))->mul($this->inv((new BigInteger("1"))->sub($this->d->mul($x1)->mul($x2)->mul($y1)->mul($y2))));

        $x3 = $x3->mod($this->q);
        $y3 = $y3->mod($this->q);

        return new Point($x3, $y3);
    }

    /**
     * Performs the scalar multiplication of a point on the Edwards25519 curve.
     */
    public function scalarmult(Point $P, BigInteger $e): Point
    {
        if ($e->cmp(0) == 0) {
            return $this->identityPoint;
        }

        $Q = $this->scalarmult($P, $e->div(2));
        $Q = $this->edwards($Q, $Q);

        if ($e->mod(2)->cmp(1) == 0) {
            $Q = $this->edwards($Q, $P);
        }

        return $Q;
    }

    /**
     * Converts a decimal number to binary.
     */
    public function encodeint(BigInteger $y): string
    {
        return $y->toBytes();
    }

    /**
     * Encodes a point on the Edwards25519 curve to a hexadecimal string.
     */
    public function encodePoint(Point $P): string
    {
        $x = $P->x;
        $y = $P->y;

        $result = $y->toBytes();
        $result .= $x->binaryAnd(new BigInteger("1"))->toBytes();

        // Convert to little-endian
        $result = strrev($result);
        return bin2hex($result);
    }

    /**
     * Returns the bit at position i of the hash h.
     */
    public function bit(string $h, BigInteger $i): int
    {
        return (ord($h[$i->div(8)->toNumber()]) >> $i->mod(8)->toNumber()) & 1;
    }

    /**
     * Returns the decimal representation of the input's hash (SHA-512).
     */
    public function Hint(mixed $m, bool $asBigInt = false): BigInteger|string
    {
        $h = $this->H($m instanceof BigInteger ? $m->toString() : $m);
        $res = new BigInteger(bin2hex($h), 16);

        return $asBigInt ? $res : $res->toDec();
    }

    /**
     * Determines the public key from a secret key.
     */
    public function publickey(BigInteger $sk): string
    {
        return $this->encodePoint($this->scalarmult($this->B, $sk));
    }


    /**
     * Determines if a point is on the Edwards25519 curve.
     */
    public function isoncurve(Point $P): bool
    {
        $x2 = $P->x->pow(2);
        $y2 = $P->y->pow(2);

        return self::pymod($y2->sub($x2)->sub(1)->sub($this->d->mul($x2)->mul($y2)), $this->q)->cmp(0) == 0;
    }

    /**
     * Decodes a string of bits to a decimal number.
     */
    public function decodeint(string $s): BigInteger
    {
        $result = new BigInteger(bin2hex($s), 16);
        return new BigInteger($result->toDec());
    }

    /**
     * Decodes a point on the Edwards25519 curve from a hexadecimal string.
     */
    public function decodepoint(string $encoded): Point
    {
        // Convert to little-endian
        $encoded = strrev(hex2bin($encoded));

        $y = $this->decodeint(substr($encoded, 0, 32));
        $x = $this->xrecover($y);

        if ($x->binaryAnd(new BigInteger("1"))->toNumber() != $this->bit($encoded, new BigInteger("255"))) {
            $x = $this->q->sub($x);
        }

        $P = new Point($x, $y);

        if (!$this->isoncurve($P)) {
            throw new Exception("Decoding point that is not on curve");
        }

        return $P;
    }
    // The code below is by the Monero-Integrations team

    /**
     * Scalar multiplication of the base point by a scalar e
     */
    public function scalarmult_base(BigInteger $e): Point
    {
        return $this->scalarmult($this->B, $e);
    }
}
