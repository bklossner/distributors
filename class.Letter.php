<?php

namespace com\bob\distributors;

/**
 * Created by PhpStorm.
 * User: bob
 * Date: 6/27/17
 * Time: 3:09 PM
 *
 * A simple class to hold a letter of the alphabet and a boolean value responsible for CSS styling
 */
class Letter {

	/**
	 * A letter
	 * @var $string letter
	 */
	private $letter;

	/**
	 * Property that determines if a letter contains states that have distributors
	 * @var bool
	 */
	private $hasDistributors = false;


	function __construct($letter) {
		$this->letter = $letter;
	}

	/**
	 * @return string
	 */
	public function getLetter() {
		return $this->letter;
	}

	/**
	 * @param string $letter
	 */
	public function setLetter( $letter ) {
		$this->letter = $letter;
	}

	function __toString() {
		return "<h2 id='$this->letter' class='letter'>$this->letter</h2>";
	}

}