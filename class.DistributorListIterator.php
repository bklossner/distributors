<?php

namespace com\bob\distributors;

/**
 * Created by PhpStorm.
 * User: bob
 * Date: 6/21/17
 * Time: 11:40 PM
 */
class DistributorListIterator {

	protected $distributorList;
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