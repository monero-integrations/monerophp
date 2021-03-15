<?php 

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
		$this->gmp = extension_loaded('gmp');
	}
	
	private function sc_reduce($input)
	{
		$integer = $this->ed25519->decodeint(hex2bin($input));
		if($this->gmp)
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
