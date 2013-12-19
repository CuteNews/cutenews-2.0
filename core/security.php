<?php

// XXTEA ---------------------------------------------------------------------------------------------------------------

// Since 1.5.0: XXTEA crypt-algorithm
function long2str($v, $w)
{
    $len = count($v);
    $n   = ($len - 1) << 2;

    if ($w)
    {
        $m = $v[$len - 1];
        if (($m < $n - 3) || ($m > $n)) return false;
        $n = $m;
    }

    $s = array();
    for ($i = 0; $i < $len; $i++) $s[$i] = pack("V", $v[$i]);
    if ($w) return substr(join('', $s), 0, $n);
    else    return join('', $s);

}

function str2long($s, $w)
{
    $v = unpack("V*", $s.str_repeat("\0", (4 - strlen($s) % 4) & 3));
    $v = array_values($v);
    if ($w) $v[count($v)] = strlen($s);
    return $v;
}

function int32($n)
{
    while ($n >= 2147483648)  $n -= 4294967296;
    while ($n <= -2147483649) $n += 4294967296;
    return (int)$n;
}

function xxtea_encrypt($str, $key)
{
    if ($str == "") return "";

    $v = str2long($str, true);
    $k = str2long($key, false);
    if (count($k) < 4) for ($i = count($k); $i < 4; $i++) $k[$i] = 0;

    $n      = count($v) - 1;
    $z      = $v[$n];
    $y      = $v[0];
    $delta  = 0x9E3779B9;
    $q      = floor(6 + 52 / ($n + 1));
    $sum    = 0;

    while (0 < $q--)
    {
        $sum = int32($sum + $delta);
        $e = $sum >> 2 & 3;
        for ($p = 0; $p < $n; $p++)
        {
            $y = $v[$p + 1];
            $mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $z = $v[$p] = int32($v[$p] + $mx);
        }
        $y = $v[0];
        $mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
        $z = $v[$n] = int32($v[$n] + $mx);
    }
    return long2str($v, false);
}

function xxtea_decrypt($str, $key)
{
    if ($str == "") return "";

    $v = str2long($str, false);
    $k = str2long($key, false);
    if (count($k) < 4) for ($i = count($k); $i < 4; $i++) $k[$i] = 0;

    $n      = count($v) - 1;
    $z      = $v[$n];
    $y      = $v[0];
    $delta  = 0x9E3779B9;
    $q      = floor(6 + 52 / ($n + 1));
    $sum    = int32($q * $delta);

    while ($sum != 0)
    {
        $e = $sum >> 2 & 3;
        for ($p = $n; $p > 0; $p--)
        {
            $z = $v[$p - 1];
            $mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $y = $v[$p] = int32($v[$p] - $mx);
        }
        $z      = $v[$n];
        $mx     = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
        $y      = $v[0] = int32($v[0] - $mx);
        $sum    = int32($sum - $delta);
    }
    return long2str($v, true);
}

// SHA256::hash --------------------------------------------------------------------------------------------------------
/*
 *  Based on http://csrc.nist.gov/cryptval/shs/sha256-384-512.pdf
 *
 *  © Copyright 2005 Developer's Network. All rights reserved.
 *  This is licensed under the Lesser General Public License (LGPL)
 *  This library is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2.1 of the License, or (at your option) any later version.
 */

function SHA256_sum()
{
    $T = 0;
    for($x = 0, $y = func_num_args(); $x < $y; $x++)
    {
        $a = func_get_arg($x);
        $c = 0;
        for($i = 0; $i < 32; $i++)
        {
            //    sum of the bits at $i
            $j = (($T >> $i) & 1) + (($a >> $i) & 1) + $c;
            //    carry of the bits at $i
            $c = ($j >> 1) & 1;
            //    strip the carry
            $j &= 1;
            //    clear the bit
            $T &= ~(1 << $i);
            //    set the bit
            $T |= $j << $i;
        }
    }
    return $T;
}

function SHA256_hash($str)
{
    // Increase speed for newest version of PHP
    if (function_exists('hash_algos'))
    {
        $algos = hash_algos();
        if (in_array('sha256', $algos) && function_exists('hash'))
            return hash('sha256', $str);
    }

    $chunks = null;
    $M = strlen($str);                //    number of bytes
    $L1 = ($M >> 28) & 0x0000000F;    //    top order bits
    $L2 = $M << 3;                    //    number of bits
    $l = pack('N*', $L1, $L2);
    $k = $L2 + 64 + 1 + 511;
    $k -= $k % 512 + $L2 + 64 + 1;
    $k >>= 3;                           //    convert to byte count
    $str .= chr(0x80) . str_repeat(chr(0), $k) . $l;
    preg_match_all( '#.{64}#', $str, $chunks );
    $chunks = $chunks[0];

    // H(0)
    $hash = array
    (
        (int)0x6A09E667, (int)0xBB67AE85,
        (int)0x3C6EF372, (int)0xA54FF53A,
        (int)0x510E527F, (int)0x9B05688C,
        (int)0x1F83D9AB, (int)0x5BE0CD19,
    );


    // Compute
    $vars = 'abcdefgh';
    $K = null;

    $a = $b = $c = $d = $e = $f = $h = $g = false;
    if($K === null)
    {
        $K = array(
            (int)0x428A2F98, (int)0x71374491, (int)0xB5C0FBCF, (int)0xE9B5DBA5,
            (int)0x3956C25B, (int)0x59F111F1, (int)0x923F82A4, (int)0xAB1C5ED5,
            (int)0xD807AA98, (int)0x12835B01, (int)0x243185BE, (int)0x550C7DC3,
            (int)0x72BE5D74, (int)0x80DEB1FE, (int)0x9BDC06A7, (int)0xC19BF174,
            (int)0xE49B69C1, (int)0xEFBE4786, (int)0x0FC19DC6, (int)0x240CA1CC,
            (int)0x2DE92C6F, (int)0x4A7484AA, (int)0x5CB0A9DC, (int)0x76F988DA,
            (int)0x983E5152, (int)0xA831C66D, (int)0xB00327C8, (int)0xBF597FC7,
            (int)0xC6E00BF3, (int)0xD5A79147, (int)0x06CA6351, (int)0x14292967,
            (int)0x27B70A85, (int)0x2E1B2138, (int)0x4D2C6DFC, (int)0x53380D13,
            (int)0x650A7354, (int)0x766A0ABB, (int)0x81C2C92E, (int)0x92722C85,
            (int)0xA2BFE8A1, (int)0xA81A664B, (int)0xC24B8B70, (int)0xC76C51A3,
            (int)0xD192E819, (int)0xD6990624, (int)0xF40E3585, (int)0x106AA070,
            (int)0x19A4C116, (int)0x1E376C08, (int)0x2748774C, (int)0x34B0BCB5,
            (int)0x391C0CB3, (int)0x4ED8AA4A, (int)0x5B9CCA4F, (int)0x682E6FF3,
            (int)0x748F82EE, (int)0x78A5636F, (int)0x84C87814, (int)0x8CC70208,
            (int)0x90BEFFFA, (int)0xA4506CEB, (int)0xBEF9A3F7, (int)0xC67178F2
        );
    }

    $W = array();
    for($i = 0, $numChunks = sizeof($chunks); $i < $numChunks; $i++)
    {
        //    initialize the registers
        for($j = 0; $j < 8; $j++)
            ${$vars{$j}} = $hash[$j];

        //    the SHA-256 compression function
        for($j = 0; $j < 64; $j++)
        {
            if($j < 16)
            {
                $T1  = ord($chunks[$i][$j*4]) & 0xFF; $T1 <<= 8;
                $T1 |= ord($chunks[$i][$j*4+1]) & 0xFF; $T1 <<= 8;
                $T1 |= ord($chunks[$i][$j*4+2]) & 0xFF; $T1 <<= 8;
                $T1 |= ord($chunks[$i][$j*4+3]) & 0xFF;
                $W[$j] = $T1;
            }
            else
            {
                $W[$j] = SHA256_sum(((($W[$j-2] >> 17) & 0x00007FFF) | ($W[$j-2] << 15)) ^ ((($W[$j-2] >> 19) & 0x00001FFF) | ($W[$j-2] << 13)) ^ (($W[$j-2] >> 10) & 0x003FFFFF), $W[$j-7], ((($W[$j-15] >> 7) & 0x01FFFFFF) | ($W[$j-15] << 25)) ^ ((($W[$j-15] >> 18) & 0x00003FFF) | ($W[$j-15] << 14)) ^ (($W[$j-15] >> 3) & 0x1FFFFFFF), $W[$j-16]);
            }

            $T1 = SHA256_sum($h, ((($e >> 6) & 0x03FFFFFF) | ($e << 26)) ^ ((($e >> 11) & 0x001FFFFF) | ($e << 21)) ^ ((($e >> 25) & 0x0000007F) | ($e << 7)), ($e & $f) ^ (~$e & $g), $K[$j], $W[$j]);
            $T2 = SHA256_sum(((($a >> 2) & 0x3FFFFFFF) | ($a << 30)) ^ ((($a >> 13) & 0x0007FFFF) | ($a << 19)) ^ ((($a >> 22) & 0x000003FF) | ($a << 10)), ($a & $b) ^ ($a & $c) ^ ($b & $c));
            $h = $g;
            $g = $f;
            $f = $e;
            $e = SHA256_sum($d, $T1);
            $d = $c;
            $c = $b;
            $b = $a;
            $a = SHA256_sum($T1, $T2);
        }

        //    compute the next hash set
        for($j = 0; $j < 8; $j++)
            $hash[$j] = SHA256_sum(${$vars{$j}}, $hash[$j]);
    }

    // HASH HEX
    $str = '';
    reset($hash);
    do { $str .= sprintf('%08x', current($hash)); } while(next($hash));

    return $str;
}

/* 
* Rivest/Shamir/Adelman (RSA) compatible functions
* to generate keys and encode/decode 
*
* With a great thanks to:
* Ilya Rudev <www@polar-lights.com>
* Glenn Haecker <ghaecker@idworld.net>
* Segey Semenov <sergei2002@mail.ru>
* Suivan <ssuuii@gmx.net>
*
* Prime-Numbers.org provide small prime numbers list.
* You can browse all small prime numbers(small than 10,000,000,000) there.
* There's totally 455042511 prime numbers.
* http://www.prime-numbers.org/
*/

class RSA
{
    /*
    * Function for generating keys. Return array where
    * $array[0] -> modulo N
    * $array[1] -> public key E
    * $array[2] -> private key D
    * Public key pair is N and E
    * Private key pair is N and D
    */
    function generate_keys ($p, $q)
    {
      	$n = bcmul($p, $q);
      
      	//m (we need it to calculate D and E) 
      	$m = bcmul(bcsub($p, 1), bcsub($q, 1));
      
      	// Public key  E 
      	$e = $this->findE($m);
      
      	// Private key D
      	$d = $this->extend($e,$m);
      
      	$keys = array ($n, $e, $d);
      
      	return $keys;
    }

    /* 
    * Standard method of calculating D
    * D = E-1 (mod N)
    * It's presumed D will be found in less then 16 iterations 
    */
    function extend ($Ee, $Em)
    {
      	$u1 = '1';
      	$u2 = '0';
      	$u3 = $Em;
      	$v1 = '0';
      	$v2 = '1';
      	$v3 = $Ee;

      	while (bccomp($v3, 0) != 0)
        {
            $qq = bcdiv($u3, $v3, 0);
            $t1 = bcsub($u1, bcmul($qq, $v1));
            $t2 = bcsub($u2, bcmul($qq, $v2));
            $t3 = bcsub($u3, bcmul($qq, $v3));
            $u1 = $v1;
            $u2 = $v2;
            $u3 = $v3;
            $v1 = $t1;
            $v2 = $t2;
            $v3 = $t3;
            $z  = '1';
        }

      	$uu = $u1;
      	$vv = $u2;

      	if (bccomp($vv, 0) == -1)
            $inverse = bcadd($vv, $Em);
        else
            $inverse = $vv;

      	return $inverse;
    }

    /* 
    * This function return Greatest Common Divisor for $e and $m numbers 
    */
    function GCD($e,$m)
    {
      	$y = $e;
      	$x = $m;

      	while (bccomp($y, 0) != 0)
        {
            // modulus function
            $w = bcsub($x, bcmul($y, bcdiv($x, $y, 0)));;
            $x = $y;
            $y = $w;
      	}

      	return $x;
    }

    /*
    * Calculating E under conditions:
    * GCD(N,E) = 1 and 1<E<N
    */
    function findE($m)
    {
        $e = '3';
        if (bccomp($this->GCD($e, $m), '1') != 0)
        {
            $e = '5';
            $step = '2';

            while (bccomp($this->GCD($e, $m), '1') != 0)
            {
                $e = bcadd($e, $step);
                if ($step == '2') $step = '4'; else $step = '2';
            }
        }

        return $e;
    }

    /*
    * ENCRYPT function returns
    * X = M^E (mod N)
    */
    function encrypt ($m, $e, $n, $s=3)
    {
        $coded   = '';
        $max     = strlen($m);
        $packets = ceil($max/$s);
        
        for ($i=0; $i < $packets; $i++)
        {
            $packet = substr($m, $i*$s, $s);
            $code   = '0';

            for($j=0; $j<$s; $j++)
            {
                $code = bcadd($code, bcmul(ord($packet[$j]), bcpow('256', $j)));
            }

            $code   = bcpowmod($code, $e, $n);
            $coded .= $code.' ';
        }

      	return trim($coded);
    }

    /*
    ENCRYPT function returns
    M = X^D (mod N)
    */
    function decrypt ($c, $d, $n)
    {
        $coded   = explode(' ', $c);
        $message = '';
        $max     = count($coded);

        for ($i=0; $i < $max; $i++)
        {
            $code = bcpowmod($coded[$i], $d, $n);

            while (bccomp($code, '0') != 0)
            {
                $ascii    = bcmod($code, '256');
                $code     = bcdiv($code, '256', 0);
                $message .= chr($ascii);
            }
        }

        return $message;
    }
}
