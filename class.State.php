<?php

namespace com\bob\distributors;

/**
 * Class State
 * @package com\bob\distributors
 * @version 1.0.0
 * @author Bob Klossner <farfisa5@gmail.com>
 * @copyright 2017 Bob Klossner
 */
class State {

	/** @var string $stateAbbreviation  The abbreviation of the state/province */
	private $stateAbbreviation;

	/** @var array $aStates */
	private $aStates = [
		'AL' => 'Alabama',
		'AK' => 'Alaska',
		'AZ' => 'Arizona',
		'AR' => 'Arkansas',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Connecticut',
		'DE' => 'Delaware',
		'DC' => 'District of Columbia',
		'FL' => 'Florida',
		'GA' => 'Georgia',
		'HI' => 'Hawaii',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'IA' => 'Iowa',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'ME' => 'Maine',
		'MD' => 'Maryland',
		'MA' => 'Massachusetts',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MS' => 'Mississippi',
		'MO' => 'Missouri',
		'MT' => 'Montana',
		'NE' => 'Nebraska',
		'NV' => 'Nevada',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NY' => 'New York',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PA' => 'Pennsylvania',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VT' => 'Vermont',
		'VA' => 'Virginia',
		'WA' => 'Washington',
		'WV' => 'West Virginia',
		'WI' => 'Wisconsin',
		'WY' => 'Wyoming',
		'BC' => 'British Columbia',
		'ON' => 'Ontario',
		'NL' => 'Newfoundland and Labrador',
		'NS' => 'Nova Scotia',
		'PE' => 'Prince Edward Island',
		'NB' => 'New Brunswick',
		'QC' => 'Quebec',
		'PQ' => 'Quebec',
		'MB' => 'Manitoba',
		'SK' => 'Saskatchewan',
		'AB' => 'Alberta',
		'NT' => 'Northwest Territories',
		'NU' => 'Nunavut',
		'YT' => 'Yukon Territory'
	];

	/** @var DistributorList $distributorList */
	private $distributorList;


	// A state can contain multiple Distributor objects

	/**
	 * State constructor.
	 * @param $stateAbbreviation
	 */
	public function __construct(string $stateAbbreviation) {
		$this->stateAbbreviation = $stateAbbreviation;
	}

	/**
	 * Get the full name of the state
	 * @return string
	 */
	public function getLongName() {
		return $this->aStates[$this->stateAbbreviation];
	}

	/**
	 * Get the abbreviated name of the state
	 * @return string
	 */
	public function getShortName() {
		return $this->stateAbbreviation;
	}

	/**
	 * Output the state in an HTML-friendly format for display purposes
	 * @return string
	 */
	public function __toString() {
		$content =
			"<h3 id='$this->stateAbbreviation' class='state'>
				<span class='glyphicon glyphicon-plus' aria-hidden='true'></span> "
				. $this->getLongName() .
			"</h3>";
		return trim($content);
	}

	/**
	 * Add a distributor to the state
	 * It will be held in a DistributorList
	 * @param Distributor $dist_in
	 */
	public function addDistributor(Distributor $dist_in) {
		$this->distributorList->addDistributor($dist_in);
	}

	/**
	 * Get the list of Distributors for this state and output it in an HTML-friendly format
	 */
	public function displayDistributorsInState() {

		// Create an iterator that will loop through the distributorList object
		$distributorListIterator = new DistributorListIterator($this->distributorList);

		// Output each distributor in an HTML-friendly manner
		while ($distributorListIterator->hasNextDistributor()) {
			$distributor = $distributorListIterator->getNextDistributor();
			echo $distributor->__toString();
		}
	}

	/**
	 * @return array
	 */
	public function getAllStates() {
		return $this->aStates;
	}

	/**
	 * Format State
	 *
	 * Note: Does not format addresses, only states. $input should be as exact as possible, problems
	 * will probably arise in long strings, example 'I live in Kentukcy' will produce Indiana.
	 *
	 * @example echo myClass::format_state( 'Florida', 'abbr'); // FL
	 * @example echo myClass::format_state( 'we\'re from georgia' ) // Georgia
	 *
	 * @param  string $input  Input to be formatted
	 * @param  string $format Accepts 'abbr' to output abbreviated state, default full state name.
	 * @return string          Formatted state on success,
	 */
	public function format_state(string $input, string $format = '' ) {
		if( ! $input || empty( $input ) )
			return "";

		foreach( $this->aStates as $abbr => $name ) {
			if ( preg_match( "/\b($name)\b/", ucwords( strtolower( $input ) ), $match ) )  {
				if( 'abbr' == $format ){
					return $abbr;
				}
				else return $name;
			}
			elseif( preg_match("/\b($abbr)\b/", strtoupper( $input ), $match) ) {
				if( 'abbr' == $format ){
					return $abbr;
				}
				else return $name;
			}
		}
		return "";
	}
}