<?php

namespace com\bob\distributors;

/**
 * Created by PhpStorm.
 * User: bob
 * Date: 6/26/17
 * Time: 2:29 PM
 */
class StateListIterator {
	
	protected $stateList;
	protected $currentState = 0;

	public function __construct(StateList $stateList_in) {
		$this->stateList = $stateList_in;
	}
	public function getCurrentState() {
		if (($this->currentState > 0) &&
		    ($this->stateList->getStateCount() >= $this->currentState)) {
			return $this->stateList->getState($this->currentState);
		}
	}
	public function getNextState() {
		if ($this->hasNextState()) {
			return $this->stateList->getState(++$this->currentState);
		} else {
			return NULL;
		}
	}
	public function hasNextState() {
		if ($this->stateList->getStateCount() > $this->currentState) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}