<?php

namespace com\bob\distributors;

/**
 * Class Address
 * @package com\bob\distributors
 * @version 1.0.0
 * @author Bob Klossner <farfisa5@gmail.com>
 * @copyright 2017 Bob Klossner
 */
class Address
{
	/** @var string $addr_1 */
	private $addr_1;

	/** @var string $addr_2 */
	private $addr_2;

	/** @var string $city */
	private $city;

	/** @var State $state */
	private $state;

	/** @var string $zipcode */
	private $zipcode;

	/** @var string $country */
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
	public function __construct(string $addr_1, string $addr_2, string $city, State $state, string $zipcode, string $country)
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
	public function setAddr1(string $addr_1)
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
	public function setAddr2(string $addr_2)
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
	public function setCity(string $city)
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
	public function setState(string $state)
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
	public function setZipcode(string $zipcode)
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
	public function setCountry(string $country)
	{
		$this->country = $country;
	}

	/**
	 * Output the address as HTML
	 * @return string
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

	/**
	 * @param string $addr
	 *
	 * @return string
	 */
	protected function addressCase(string $addr) {
		/*
		 * Exceptions in lower case are words you don't want converted
		 * Exceptions all in upper case are any words you don't want converted to title case
		 *   but should be converted to upper case, e.g.:
		 *   king henry viii or king henry Viii should be King Henry VIII
		 */
		$delimiters = [ " ", "-", "/", ".", "\'", "(", ")" ];
		$exceptions = [ "SW", "-A", "AEP", "LLC", "#C" ];

		foreach ($delimiters as $dlnr => $delimiter){
			$words = explode($delimiter, $addr);
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
			$addr = join($delimiter, $newwords);
		}//foreach
		return $addr;
	}
}