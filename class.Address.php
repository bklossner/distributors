<?php

namespace com\bob\distributors;

class Address
{
	/**
	 * The first line of the street address
	 *
	 * @var string
	 */
	private $addr_1;

	/**
	 * The second line of the street address
	 *
	 * @var string
	 */
	private $addr_2;

	/**
	 * The city
	 *
	 * @var string
	 */
	private $city;

	/**
	 * The state
	 *
	 * @var string
	 */
	private $state;

	/**
	 * The zipcode (postal code)
	 *
	 * @var string
	 */
	private $zipcode;

	/**
	 * The country
	 *
	 * @var string
	 */
	private $country;


	/** @var array $validCountries  */
	public static $validCountries = ['USA', 'Canada'];


	/**
	 * Address constructor.
	 * @param $addr_1
	 * @param $addr_2
	 * @param $city
	 * @param $state
	 * @param $zipcode
	 * @param $country
	 */
	public function __construct($addr_1, $addr_2, $city, $state, $zipcode, $country)
	{
		$this->addr_1 = $addr_1;
		$this->addr_2 = $addr_2;
		$this->city = $city;
		$this->state = new State($state);
		$this->zipcode = $zipcode;
		$this->country = $country;
	}


	/**
	 * @return string
	 */
	public function getAddr1()
	{
		return ucwords($this->addr_1);
	}

	/**
	 * @param string $addr_1
	 */
	public function setAddr1($addr_1)
	{
		$this->addr_1 = $addr_1;
	}

	/**
	 * @return string
	 */
	public function getAddr2()
	{
		return ucwords($this->addr_2);
	}

	/**
	 * @param string $addr_2
	 */
	public function setAddr2($addr_2)
	{
		$this->addr_2 = $addr_2;
	}

	/**
	 * @return string
	 */
	public function getCity()
	{
		return ucwords($this->city);
	}

	/**
	 * @param string $city
	 */
	public function setCity($city)
	{
		$this->city = $city;
	}

	/**
	 * @return string
	 */
	public function getState()
	{
		return $this->state->getLongName();
	}

	/**
	 * @param string $state
	 */
	public function setState($state)
	{
		$this->state = new State($state);
	}

	/**
	 * @return string
	 */
	public function getZipcode()
	{
		return ucwords($this->zipcode);
	}

	/**
	 * @param string $zipcode
	 */
	public function setZipcode($zipcode)
	{
		$this->zipcode = $zipcode;
	}

	/**
	 * @return string
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * @param string $country
	 */
	public function setCountry($country)
	{
		$this->country = $country;
	}

	/**
	 * Output the address in HTML-friendly format
	 */
	function __toString()
	{
		$content = "
        <div class='address' itemprop='address' itemscope  itemtype='http://schema.org/PostalAddress'>
            <span class='streetAdress' itemprop='streetAddress'>" . ucwords($this->addr_1) . "</span><br>";

		if($this->addr_2) {
			$content .= "<span class='streetAdress' itemprop='streetAddress'>" . ucwords($this->addr_2) . "</span><br>";
		}

		$content .= "
			<span class='addressLocality' itemprop='addressLocality'>" . ucwords($this->city) . "</span>,
			<span class='addressRegion' itemprop='addressRegion'>" . $this->state->getShortName() . "</span>
			<span class='postalCode' itemprop='postalCode'>$this->zipcode</span>
		</div>";

		return $content;
	}

	protected function addressCase($string) {
		/*
		 * Exceptions in lower case are words you don't want converted
		 * Exceptions all in upper case are any words you don't want converted to title case
		 *   but should be converted to upper case, e.g.:
		 *   king henry viii or king henry Viii should be King Henry VIII
		 */
		$delimiters = [ " ", "-", "/", ".", "\'", "(", ")" ];
		$exceptions = [ "SW", "-A", "AEP", "LLC", "#C" ];

		foreach ($delimiters as $dlnr => $delimiter){
			$words = explode($delimiter, $string);
			$newwords = array();
			foreach ($words as $wordnr => $word){

				if (in_array(strtoupper($word), $exceptions)){
					// check exceptions list for any words that should be in upper case
					$word = strtoupper($word);
				}
				elseif (in_array(strtolower($word), $exceptions)){
					// check exceptions list for any words that should be in upper case
					$word = strtolower($word);
				}

				elseif (!in_array($word, $exceptions) ){
					// convert to uppercase (non-utf8 only)

					$word = ucfirst($word);

				}
				array_push($newwords, $word);
			}
			$string = join($delimiter, $newwords);
		}//foreach
		return $string;
	}
}