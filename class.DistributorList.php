<?php

namespace com\bob\distributors;

/**
 * Created by PhpStorm.
 * User: bob
 * Date: 6/21/17
 * Time: 10:19 PM
 */
class DistributorList {

	/**
	 * @var array $distributors
	 */
	private $distributors = array();

	/**
	 * @var int $distributorCount
	 */
	private $distributorCount = 0;

	/**
	 * DistributorList constructor.
	 */
	public function __construct() {
	}

	/**
	 * @return int
	 */
	public function getDistributorCount() {
		return $this->distributorCount;
	}

	/**
	 * @param $newCount
	 */
	private function setDistributorCount($newCount) {
		$this->distributorCount = $newCount;
	}

	/**
	 * @param $distributorNumberToGet
	 *
	 * @return mixed|null
	 */
	public function getDistributor($distributorNumberToGet) {
		if ( (is_numeric($distributorNumberToGet)) &&
		     ($distributorNumberToGet <= $this->getDistributorCount())) {
			return $this->distributors[$distributorNumberToGet];
		} else {
			return NULL;
		}
	}

	/**
	 * @param Distributor $dist_in
	 *
	 * @return int
	 */
	public function addDistributor(Distributor $dist_in) {
		$this->setDistributorCount($this->getDistributorCount() + 1);
		$this->distributors[$this->getDistributorCount()] = $dist_in;
		return $this->getDistributorCount();
	}

	/**
	 * @param Distributor $dist_in
	 *
	 * @return int
	 */
	public function removeDistributor(Distributor $dist_in) {
		$counter = 0;
		while (++$counter <= $this->getDistributorCount()) {
			if ($dist_in->getName() ==
			    $this->distributors[$counter]->getName())
			{
				for ($x = $counter; $x < $this->getDistributorCount(); $x++) {
					$this->distributors[$x] = $this->distributors[$x + 1];
				}
				$this->setDistributorCount($this->getDistributorCount() - 1);
			}
		}
		return $this->getDistributorCount();
	}
}