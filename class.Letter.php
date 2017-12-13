<?php

namespace com\bob\distributors;

/**
 * Class Letter
 * @package com\bob\distributors
 * @version 1.0.0
 * @author Bob Klossner <farfisa5@gmail.com>
 * @copyright 2017 Bob Klossner
 */
class Letter {

	/** @var $string letter */
	private $letter;

	/**
	 * Letter constructor.
	 *
	 * @param $letter
	 */
	function __construct(string $letter) {
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
	public function setLetter(string $letter) {
		$this->letter = $letter;
	}

	/**
	 * @return string
	 */
	function __toString() {
		return "<h2 id='$this->letter' class='letter'>$this->letter</h2>";
	}

}