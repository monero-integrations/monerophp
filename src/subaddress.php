<?php 
/**
 *
 * monerophp/subaddress
 *
 * PHP Monero subaddress implementation
 * https://github.com/monero-integrations/monerophp
 *
 * @author Monero Integrations Team <support@monerointegrations.com> (https://github.com/monero-integrations)
 *
 */

 /**
 * PHP subaddress implementation
 *
 * This class provides methods for generating subaddresses and related keys.
 *
 * @package monerophp
 *
 */
class subaddress
{
	/**
     * @var ed25519 The ed25519 instance.
     */
	protected $ed25519;

	/**
     * @var base58 The base58 instance.
     */
	protected $base58;

	/**
     * @var bool Indicates if the GMP extension is loaded.
     */
	protected $gmp;
	
	/**
     * Creates a new subaddress class instance.
     */
	public function __construct()
	{
		$this->ed25519 = new ed25519();
		$this->base58 = new base58();
		$this->gmp = extension_loaded('gmp');
	}
	
	/**
     * Reduce a scalar modulo of the ed25519 prime.
     *
     * @param string $input The hex-encoded scalar to be reduced.
	 * 
     * @return string Hex-encoded reduced scalar.
	 * 
     */
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
	
	/**
     * Add two points on the edwards25519 curve.
     *
     * @param string $point1 The first hex-encoded point.
     * @param string $point2 The second hex-encoded point.
	 * 
     * @return string Hex-encoded result of the addition.
	 * 
	 */
	private function ge_add($point1, $point2)
	{
		$point3 = $this->ed25519->edwards($this->ed25519->decodepoint(hex2bin($point1)), $this->ed25519->decodepoint(hex2bin($point2)));
		return bin2hex($this->ed25519->encodepoint($point3));
	}
	
	/**
     * Perform scalar multiplication of a point.
     *
     * @param string $public The hex-encoded public point.
     * @param string $secret The hex-encoded secret scalar.
	 * 
     * @return string Hex-encoded result of scalar multiplication.
	 * 
     */
	private function ge_scalarmult($public, $secret)
	{
		$point = $this->ed25519->decodepoint(hex2bin($public));
		$scalar = $this->ed25519->decodeint(hex2bin($secret));
		$res = $this->ed25519->scalarmult($point, $scalar);
		return bin2hex($this->ed25519->encodepoint($res));
	}
	
	/**
     * Perform scalar multiplication of a base point.
     *
     * @param string $scalar The hex-encoded scalar.
	 * 
     * @return string Hex-encoded result of base point scalar multiplication.
	 * 
     */
	private function ge_scalarmult_base($scalar)
	{
		$decoded = $this->ed25519->decodeint(hex2bin($scalar));
		$res = $this->ed25519->scalarmult_base($decoded);
		return bin2hex($this->ed25519->encodepoint($res));
	}
	
	/**
     * Calculate the Keccak-256 hash of input data.
     *
     * @param string $message The hex-encoded data to hash.
	 * 
     * @return string Hex-encoded hash result.
	 * 
     */
	private function keccak_256($message)
	{
		$keccak256 = SHA3::init(SHA3::KECCAK_256);
		$keccak256->absorb(hex2bin($message));
		return bin2hex($keccak256->squeeze(32)) ;
	}

	/**
     * Calculate the hash-to-scalar result (in the CryptoNote whitepaper).
     *
     * @param string $data The hex-encoded data to hash.
	 * 
     * @return string Hex-encoded scalar result.
	 * 
     */
	private function hash_to_scalar($data)
	{
		$hash = $this->keccak_256($data);
		$scalar = $this->sc_reduce($hash);
		return $scalar;
	}
	
	/**
     * Generate the subaddress secret key.
     *
     * @param int $major_index Major index.
     * @param int $minor_index Minor index.
     * @param string $sec_key The hex-encoded secret key.
	 * 
     * @return string Hex-encoded subaddress secret key.
	 * 
     */
	public function generate_subaddr_secret_key($major_index, $minor_index, $sec_key)
	{
		$prefix = "5375624164647200"; // hex encoding of string "SubAddr"
		$index = pack("II", $major_index, $minor_index);
		return $this->hash_to_scalar($prefix . $sec_key . bin2hex($index));
	}
	
	/**
     * Generate the subaddress spend public key.
     *
     * @param string $spend_public_key The hex-encoded spend public key.
     * @param string $subaddr_secret_key The hex-encoded subaddress secret key.
	 * 
     * @return string Hex-encoded subaddress spend public key.
	 * 
     */
	public function generate_subaddress_spend_public_key($spend_public_key, $subaddr_secret_key)
	{
		$mG = $this->ge_scalarmult_base($subaddr_secret_key);
		$D = $this->ge_add($spend_public_key, $mG);
		return $D;
	}
	
	/**
     * Generate the subaddress view public key.
     *
     * @param string $subaddr_spend_public_key The hex-encoded subaddress spend public key.
     * @param string $view_secret_key The hex-encoded view secret key.
	 * 
     * @return string Hex-encoded subaddress view public key.
	 * 
     */
	public function generate_subaddr_view_public_key($subaddr_spend_public_key, $view_secret_key)
	{
		return $this->ge_scalarmult($subaddr_spend_public_key, $view_secret_key);
	}
	
	/**
     * Generate a subaddress.
     *
     * @param int $major_index Major index.
     * @param int $minor_index Minor index.
     * @param string $view_secret_key The hex-encoded view secret key.
     * @param string $spend_public_key The hex-encoded spend public key.
	 * 
     * @return string Base58-encoded subaddress.
	 * 
     */
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
