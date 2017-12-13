<?php

namespace com\bob\distributors;

/**
 * Class DistributorListIterator
 * @package com\bob\distributors
 * @version 1.0.0
 * @author Bob Klossner <farfisa5@gmail.com>
 * @copyright 2017 Bob Klossner
 */
class DistributorListIterator {

	/** @var DistributorList $distributorList */
	protected $distributorList;

	/** @var int $currentDistributor */
	protected $currentDistributor = 0;


	/**
	 * DistributorListIterator constructor.
	 *
	 * @param DistributorList $distributorList_in
	 */
	public function __construct(DistributorList $distributorList_in) {
		$this->distributorList = $distributorList_in;
	}

	/**
	 * @return mixed|null
	 */
	public function getCurrentDistributor() {
		if (($this->currentDistributor > 0) &&
		    ($this->distributorList->getDistributorCount() >= $this->currentDistributor)) {
			return $this->distributorList->getDistributor($this->currentDistributor);
		}
	}

	/**
	 * @return mixed|null
	 */
	public function getNextDistributor() {
		if ($this->hasNextDistributor()) {
			return $this->distributorList->getDistributor(++$this->currentDistributor);
		} else {
			return NULL;
		}
	}

	/**
	 * @return bool
	 */
	public function hasNextDistributor() {
		if ($this->distributorList->getDistributorCount() > $this->currentDistributor) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


}