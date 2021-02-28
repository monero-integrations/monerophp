<?php

/**
 * mnemonic.php - lightweight monero mnemonic class in php.
 * Copyright (C) 2019 Dan Libby
 *
 * Translated to PHP from https://raw.githubusercontent.com/bigreddmachine/MoneroPy/master/moneropy/mnemonic.py
 * initially using https://github.com/dan-da/py2php.   mnemonic.php contains the following notice.
  
 * Electrum - lightweight Bitcoin client
 * Copyright (C) 2011 thomasv@gitorious
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation files
 * (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge,
 * publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.  
 * Further improvements, notably support for multiple languages/wordsets adapted
 * from mnemonic.js found at https://xmr.llcoins.net/js/mnemonic.js which is in
 * the public domain.
 *
 * This PHP code, itself being an original work, is hereby placed in the public domain.
 */

namespace MoneroIntegrations\MoneroPhp;

/**
 * A standalone class to encode, decode, and validate monero mnemonics
 * All access to this class is via static methods, so it never needs to
 * be instantiated.
 */
class mnemonic {

    /**
     * Given a mnemonic seed word list, return a string of the seed checksum.
     */
    static function checksum($words, $prefix_len) {
        $plen = $prefix_len;
        $words = array_slice($words, null, count($words) > 13 ? 24 : 12);
        
        $wstr = '';
        foreach($words as $word) {
            $wstr .= ($plen == 0 ? $word : mb_substr($word, 0, $plen));
        }

        $checksum = crc32($wstr);
        $idx = $checksum % count($words);
        return $words[$idx];
    }
    
    /**
     * Given a mnemonic seed word list, check if checksum word is valid.
     * Returns boolean value.
     */
    static function validate_checksum($words, $prefix_len) {
        return (self::checksum($words, $prefix_len) == $words[count($words)-1]) ? true : false;
    }

    /**
     * Given an 8 byte word (or shorter),
     * pads to 8 bytes (adds 0 at left) and reverses endian byte order.
     */
    static function swap_endian($word) {
        $word = str_pad ( $word, 8, 0, STR_PAD_LEFT);
        return implode('', array_reverse(str_split($word, 2)));
    }
    
    /**
     * Given a hexadecimal key string (seed),
     * return it's mnemonic representation.
     *
     * @todo if anyone can make this work reliably with
     * pure PHP math (no gmp or bcmath), please submit a
     * pull request.
     */
    static function encode($seed, $wordset_name = null) {
        assert(mb_strlen($seed) % 8 == 0);
        $out = [];
        
        $wordset = self::get_wordset_by_name( $wordset_name );
        $words = $wordset['words'];
        
        $ng = count($words);
        for($i = 0; $i < mb_strlen($seed) / 8; $i ++) {
            $word = self::swap_endian(mb_substr($seed, 8*$i, (8*$i+8) - (8*$i) ));
            $x = gmp_init($word, 16);
            $w1 = gmp_mod($x,$ng);
            $w2 = gmp_mod(gmp_add(gmp_div($x, $ng), $w1), $ng);
            $w3 = gmp_mod(gmp_add(gmp_div(gmp_div($x, $ng), $ng), $w2), $ng);
            $out[] = $words[gmp_strval($w1)];
            $out[] = $words[gmp_strval($w2)];
            $out[] = $words[gmp_strval($w3)];
        }
        return $out;
    }

    /**
     * Given a hexadecimal key string (seed),
     * return it's mnemonic representation plus an
     * extra checksum word.
     */
    static function encode_with_checksum($message, $wordset_name = null) {
        $list = self::encode($message, $wordset_name);
        
        $wordset = self::get_wordset_by_name($wordset_name);
        $list[] = self::checksum($list, $wordset['prefix_len']);
        return $list ;
    }
    
    /**
     * Given a mnemonic word list, return a hexadecimal encoded string (seed).
     *
     * @todo if anyone can make this work reliably with
     * pure PHP math (no gmp or bcmath), please submit a
     * pull request.
     */
    static function decode($wlist, $wordset_name = null) {
        $wordset = self::get_wordset_by_name( $wordset_name );
        
        $plen = $wordset['prefix_len'];
        $tw = $wordset['trunc_words'];
        $wcount = count($tw);

        if (($plen === 0 && ($wcount % 3 !== 0)) || ($plen > 0 && ($wcount % 3 === 2))) {
            throw new \Exception("too few words");
        }
        if ($plen > 0 && (count($wlist) % 3 === 0)) {
            throw new \Exception("last word missing");
        }
        
        $out = '';

        for ($i = 0; $i < count($wlist)-1; $i += 3) {
        
            if($plen == 0) {
                $w1 = @$tw[$wlist[$i]];
                $w2 = @$tw[$wlist[$i + 1]];
                $w3 = @$tw[$wlist[$i + 2]];
            }
            else {
                $w1 = @$tw[mb_substr($wlist[$i], 0, $plen)];
                $w2 = @$tw[mb_substr($wlist[$i + 1], 0, $plen)];
                $w3 = @$tw[mb_substr($wlist[$i + 2], 0, $plen)];
            }
            
            if ($w1 === null || $w2 === null || $w3 === null) {
                throw new \Exception("invalid word in mnemonic");
            }
            // $x = (($w1 + ($n * (($w2 - $w1) % $n))) + (($n * $n) * (($w3 - $w2) % $n)));
            $x = gmp_add(gmp_add($w1, gmp_mul($wcount, (gmp_mod(gmp_sub($w2, $w1), $wcount)))), gmp_mul((gmp_mul($wcount,$wcount)), (gmp_mod(gmp_sub($w3, $w2), $wcount))));
            $out .= self::swap_endian(gmp_strval($x, 16));
        }
        return $out;
    }
    
    /**
     * Given a wordset identifier, returns the full wordset
     */
    static public function get_wordset_by_name($name = null) {
        $name = $name ?: 'english';
        $wordset = self::get_wordsets();
        $ws = @$wordset[$name];
        if( !$ws ) {
            throw new \Exception("Invalid wordset $name");
        }
        return $ws;
    }
    
    /**
     * Given a mnemonic array of words, returns name of matching
     * wordset that contains all words, or null if not found.
     *
     * throws an exception if more than one wordset matches all words,
     * but in theory that should never happen.
     */
    static public function find_wordset_by_mnemonic($mnemonic) {
        $sets = self::get_wordsets();
        $matched_wordsets = [];
        foreach($sets as $ws_name => $ws) {
            
            // note, to make the search faster, we truncate each word
            // according to prefix_len of the wordset, and lookup
            // by key in trunc_words, rather than searching through
            // entire wordset array.
            $allmatch = true;
            foreach($mnemonic as $word) {
                $tw = $ws['prefix_len'] == 0 ? $word : mb_substr($word, 0, $ws['prefix_len']);
                if( @$ws['trunc_words'][$tw] === null) {
                    $allmatch = false;
                    break;
                }
            }
            if( $allmatch) {
                $matched_wordsets[] = $ws_name;
            }
        }
        
        $cnt = count($matched_wordsets);
        if($cnt > 1) {
            throw new \Exception("Ambiguous match. mnemonic matches $cnt wordsets.");
        }
        
        return @$matched_wordsets[0];
    }
    
    
    /**
     * returns list of available wordsets
     */
    static public function get_wordset_list() {
        return array_keys( self::get_wordsets() );
    }
    
    /**
     * This function returns all available wordsets.
     *
     * Each wordset is in a separate file in wordsets/*.ws.php
     */
    static public function get_wordsets() {
        
        static $wordsets = null;
        if( $wordsets ) {
            return $wordsets;
        }
        
        $wordsets = [];
        $files = glob(__DIR__ . 'wordsets/*.ws.php');
        foreach($files as $f) {
            require_once($f);
    
            list($wordset) = explode('.', basename($f));
            $classname = __NAMESPACE__ . '\\' . $wordset;
            
            $wordsets[$wordset] = [
                'name' => $classname::name(),
                'english_name' => $classname::english_name(),
                'prefix_len' => $classname::prefix_length(),
                'words' => $classname::words(),
            ];
        }
     
        // This loop adds the key 'trunc_words' to each wordset, which contains
        // a pre-generated list of words truncated to length prefix_len.
        // This list is optimized for fast lookup of the truncated word
        // with the format being [ <ctruncated_word> => <index> ].
        // This optimization assumes/requires that each truncated word is unique.
        // A further optimization could be to only pre-generate trunc_words on the fly
        // when a wordset is actually used, rather than for all wordsets.
        foreach($wordsets as &$ws) {
            
            $tw = [];
            $plen = $ws['prefix_len'];
            $i = 0;
            foreach( $ws['words'] as $w) {
                $key = $plen == 0 ? $w : mb_substr($w, 0, $plen);
                $tw[$key] = $i++;
            }
    
            $ws['trunc_words'] = $tw;
        }
        return $wordsets;
    }
    
}


interface wordset {

    /* Returns name of wordset in the wordset's native language.
     * This is a human-readable string, and should be capitalized
     * if the language supports it.
     */
    static public function name() : string;

    /* Returns name of wordset in english.    
     * This is a human-readable string, and should be capitalized
     */
    static public function english_name() : string;
    
    /* Returns integer indicating length of unique prefix,
     * such that each prefix of this length is unique across
     * the entire set of words.
     *
     * A value of 0 indicates that there is no unique prefix
     * and the entire word must be used instead.
     */
    static public function prefix_length() : int;
    
    /* Returns an array of all words in the wordset.
     */
    static public function words() : array;    
};