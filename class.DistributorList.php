<?php

namespace com\bob\distributors;

/**
 * Class DistributorList
 * @package com\bob\distributors
 * @version 1.0.0
 * @author Bob Klossner <farfisa5@gmail.com>
 * @copyright 2017 Bob Klossner
 */
class DistributorList {

	/** @var array $distributors */
	private $distributors = array();

	/** @var int $distributorCount */
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
	 * @param int $newCount
	 */
	private function setDistributorCount(int $newCount) {
		$this->distributorCount = $newCount;
	}

	/**
	 * @param int $distributorNumberToGet
	 *
	 * @return mixed|null
	 */
	public function getDistributor(int $distributorNumberToGet) {
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