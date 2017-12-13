<?php

namespace com\bob\distributors;

/**
 * Class StateListIterator
 * @package com\bob\distributors
 * @version 1.0.0
 * @author Bob Klossner <farfisa5@gmail.com>
 * @copyright 2017 Bob Klossner
 */
class StateListIterator {

	/** @var StateList $stateList */
	protected $stateList;

	/** @var int $currentState */
	protected $currentState = 0;


	/**
	 * StateListIterator constructor.
	 *
	 * @param StateList $stateList_in
	 */
	public function __construct(StateList $stateList_in) {
		$this->stateList = $stateList_in;
	}


	/**
	 * @return mixed|null
	 */
	public function getCurrentState() {
		if (($this->currentState > 0) &&
		    ($this->stateList->getStateCount() >= $this->currentState)) {
			return $this->stateList->getState($this->currentState);
		}
	}


	/**
	 * @return mixed|null
	 */
	public function getNextState() {
		if ($this->hasNextState()) {
			return $this->stateList->getState(++$this->currentState);
		} else {
			return NULL;
		}
	}


	/**
	 * @return bool
	 */
	public function hasNextState() {
		if ($this->stateList->getStateCount() > $this->currentState) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}