<?php

namespace com\bob\distributors;

/**
 * Class StateList
 * @package com\bob\distributors
 * @version 1.0.0
 * @author Bob Klossner <farfisa5@gmail.com>
 * @copyright 2017 Bob Klossner
 */
class StateList {

	/** @var array $states */
	private $states = array();

	/** @var int $stateCount */
	private $stateCount = 0;

	/**
	 * StateList constructor.
	 */
	public function __construct() {
	}

	/**
	 * @return int
	 */
	public function getStateCount() {
		return $this->stateCount;
	}

	/**
	 * @param int $newCount
	 */
	private function setStateCount(int $newCount) {
		$this->stateCount = $newCount;
	}

	/**
	 * @param int $stateNumberToGet
	 *
	 * @return mixed|null
	 */
	public function getState(int $stateNumberToGet) {
		if ( (is_numeric($stateNumberToGet)) &&
		     ($stateNumberToGet <= $this->getStateCount())) {
			return $this->states[$stateNumberToGet];
		} else {
			return NULL;
		}
	}

	/**
	 * @param State $dist_in
	 *
	 * @return int
	 */
	public function addState(State $dist_in) {

		// Check to see if the state has already been added
		$this->setStateCount($this->getStateCount() + 1);
		$this->states[$this->getStateCount()] = $dist_in;
		return $this->getStateCount();
	}

	/**
	 * @param State $dist_in
	 *
	 * @return int
	 */
	public function removeState(State $dist_in) {
		$counter = 0;
		while (++$counter <= $this->getStateCount()) {
			if ($dist_in->getShortName() ==
			    $this->states[$counter]->getShortName())
			{
				for ($x = $counter; $x < $this->getStateCount(); $x++) {
					$this->states[$x] = $this->states[$x + 1];
				}
				$this->setStateCount($this->getStateCount() - 1);
			}
		}
		return $this->getStateCount();
	}

}