<?php

namespace com\bob\distributors;

/**
 * Created by PhpStorm.
 * User: bob
 * Date: 7/23/17
 * Time: 3:45 PM
 */

class Navbar {

	private $locale;

	private $aAlphabet = array(
		"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M",
		"N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"
	);

	private $aHasDistributors = array();

	/**
	 * Navbar constructor.
	 */
	public function __construct($locale = 'USA') {

		// Get the main JSON file that is generated from the database query
		$file = DISTRIBUTORS_JSON;

		// Make the request
		$request = wp_remote_get( $file );

		// If the remote request fails, wp_remote_get() will return a WP_Error, so let’s check if the $request variable is an error:
		if( is_wp_error( $request ) ) {
			return false; // Bail early
		}

		// Retrieve the data
		$body = wp_remote_retrieve_body( $request );
		$distributors = json_decode( $body );

		/**
		 * The string that determines how to filter the distributors
		 * @var string $filter
		 */
		if(isset($_POST['locale'])) {
			$filter = $_POST['locale'];
		}
		else {
			// On initial page load, it will display distributors in the United States
			$filter = "USA";
		}

		/**
		 * Array that holds the result of the country filter
		 * @var array $distributorListByCountry
		 */
		$distributorListByCountry = filter($distributors, $filter);

		/**
		 * An array that will hold all of the states.
		 * This will later be filtered to hold unique values only
		 * @var array $allStatesArray
		 */
		$allStatesArray = array();


		// Iterate through the list of Distributors that have been filtered by the country
		foreach ($distributorListByCountry as $distr) {
			// The list of master/duplicate arrays
			$allStatesArray[] = $distr->state;
		}

		// Return an array of unique states from the $allStatesArray
		$uniqueStatesArray = array_unique($allStatesArray);


		// From the $uniqueStatesArray, get the first letter of each state

		// An array that will hold the first letter of each unique state from $uniqueStatesArray
		/** @var array $letterArray */
		$letterArray = array();

		// Iterate through the $uniqueStatesArray
		foreach ($uniqueStatesArray as $state) {
			// Extract the first letter of the state and place it into the array
			$letterArray[] = substr($state, 0, 1);
		}

		// Create an array of unique letters for display/iteration purposes
		/** @var array $uniqueLetterArray */
		$this->aHasDistributors = array_unique($letterArray);
	}

	/**
	 * Factory Constructor.
	 * @return Navbar
	 */
	public static function create() {
		$instance = new self();
		return $instance;
	}


	/**
	 * @return string
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * @param string $locale
	 *
	 * @return Navbar
	 */
	public function setLocale( $locale ) {
		$this->locale = $locale;

		return $this;
	}

	/**
	 * AJAX function to change the locale
	 */
	public function changeLocale() {
		if(isset($_POST['locale'])) {
			$navbar = new Navbar($_POST['locale']);
			echo $navbar;
			die();
		}


	}

	/**
	 * __toString magic method
	 *
	 * @return string
	 */
	public function __toString() {

		// Wrap the navbar in an unnumbered list
		$content = "<ul class='navbar-state list-inline'>";

		// Go through the alphabet comparing each letter to what is in the $aDistributorInStateLetter array
		foreach ($this->aAlphabet as $alphabetLetter) {

			$hasDistributor = false;
			foreach ($this->aHasDistributors as $uniqueLetter) {

				if($alphabetLetter == $uniqueLetter) {
					$hasDistributor = true;
				}
			}

			if($hasDistributor == true) {
				$content .= "<li class='has-distributors'>";
				$content .=     "<h2><a class='scroll' href='#$alphabetLetter'>$alphabetLetter</a></h2>";
				$content .= "</li>";
			}
			else {
				$content .= "<li><h2>$alphabetLetter</h2></li>";
			}
		}

		$content .= "</ul>";
		return $content;
	}


}