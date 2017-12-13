<?php

namespace com\bob\distributors;

/**
 * Class DisplayDistributors
 * @package com\bob\distributors
 * @version 1.0.0
 * @author Bob Klossner <farfisa5@gmail.com>
 * @copyright 2017 Bob Klossner
 */
class DisplayDistributors {

	/**
	 * DisplayDistributors constructor.
	 */
	public function __construct() {
	}

	/**
	 * Factory Constructor.
	 * @return DisplayDistributors
	 */
	public static function create() {
		$instance = new self();
		return $instance;
	}

	/**
	 * Function to respond to changing the locale in the Distributors page to show Canadian or USA distributors
	 */
	public function getDistributorsByCountry() {

		// Get the main JSON file that is generated from the database query
		$file = JSON_FILE;

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
		 * Either by Canada or USA
		 * @var string $filter
		 */
		if(isset($_POST['countryFilter'])) {
			$filter = $_POST['countryFilter'];
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
		$uniqueLetterArray = array_unique($letterArray);

		echo "<div class='row'>";
		echo    "<div class='distributor-list-container col-md-12'>";

		// Start the iteration, starting from the letter
		foreach ($uniqueLetterArray as $uniqueLetter) {

			// Output the letter
			echo "<h2 id='$uniqueLetter' class='letter'>$uniqueLetter</h2>";

			// Iterate through the unique states array
			foreach ($uniqueStatesArray as $uniqueState) {

				// If $state begins with $uniqueLetter, output the state
				$firstLetter = substr($uniqueState, 0 , 1);
				if($firstLetter == $uniqueLetter) {

					echo "<div class='row'>";
					echo    "<div class='state-container col-md-12'>";

					// Iterate through the master distributors list
					$filteredDistributor = filter($distributorListByCountry, $uniqueState);

					// Get the sum of each distributors in the state/territory
					$distributorCount = count($filteredDistributor);

					// Create a State object with the $uniqueState
					/** @var State $state */
					$state = new State($uniqueState);

					// Pass the $distributorCount parameter into display, so we can display the number of distributors in each state
					echo $state->display($distributorCount);

					// Filter the distributors list by the current state we are visiting in the loop and place it in an array
					$distributorsInState = filter($distributorListByCountry, $state->getShortName());

					echo        "<div id='container-" . $state->getShortName() . "' class='row stores'>";

					/**
					 * An array that holds all of the distributors
					 * @var array $distributorsArray
					 */
					$distList = new DistributorList();

					// Iterate through the $distributorsInState, creating Distributors from each entry
					foreach ($distributorsInState as $shop) {
						/**
						 * Create a new Address object for the distributor with the values from the JSON file
						 * @var Address $address
						 */
						$address = new Address($shop->addr_1, $shop->addr_2, $shop->city, $shop->state, $shop->zipcode, $shop->country);

						/**
						 * Create a new Distributor object with all of the values from the JSON file
						 * @var Distributor $distributor
						 */
						$distributor = new Distributor($shop->name, $address, $shop->discountCode, $shop->phone, $shop->email);
						$distributor->setUid($shop->id);

						// Add the $distributor object to the DistributorList so we can iterate through it for display purposes.
						$distList->addDistributor($distributor);
					}

					// Add the DistributorList to the Iterator
					/** @var DistributorListIterator $distributorsIterator */
					$distributorsIterator = new DistributorListIterator($distList);

					// Iterate through the list of Distributors in this state and output it to the browser
					while ($distributorsIterator->hasNextDistributor()) {
						$distributor = $distributorsIterator->getNextDistributor();
						echo $distributor;
					}

					echo        "</div>";

					echo    "</div>";

					echo "</div>";
				}
			}
		}
		echo    '</div>';
		echo '<div>';

		// The die() function is necessary in Wordpress when AJAX calls are triggered to return values.
		// Check to see if this method is being called via AJAX by looking for a parameter being sent to it.
		// If we see a parameter then include the die() function.
		if(isset($_POST['countryFilter'])) {
			die();
		}
	}


	/**
	 * @param string $uid
	 *
	 * @return $this
	 */
	public function getDistributorByID(string $uid) {

		// Get the parameter uid from the browser
		if(isset($_GET['uid'])) {


		}
		else {
			$errorMsg = "<h5 style='margin-bottom:0'>We cannot locate this user.</h5>
						 <p>Please verify the URL entered in your browser is correct.</p>";
			echo $errorMsg;
		}
		$user = get_user_by('id', $uid);


		// Make sure there's no empty resultset
		if(!empty($user)) {

			// All of the info we need for a Distributor

			// Get all of the usermeta goodies needed to set the address
			$allMetaData = get_user_meta($uid);

			$uid = $allMetaData['_uid'][0];
			$this->name = $user->display_name;
			$addr1 = $allMetaData['_address_addr1'][0];
			$addr2 = "";
			if(isset($allMetaData['_address_addr2'][0])) {
				$addr2 = $allMetaData['_address_addr2'][0];
			}
			$city = $allMetaData['_address_city'][0];
			$state = $allMetaData['_address_state'][0];
			$country = $allMetaData['_address_country'][0];
			$zipcode = $allMetaData['_address_zipcode'][0];

			$address = new Address($addr1, $addr2, $city, $state, $zipcode, $country);

			$discountCode = $allMetaData['_discountCode'][0];
			$phone = $allMetaData['_phone'][0];
			$email = $allMetaData['_email'][0];
		}

		return $this;
	}


	/**
	 * Generate a Distributor based on the user id
	 * @param string $uid
	 */
	public function getDistributor(string $uid) {

		$distributor = null;

		$suid = $_GET[$uid];

		/**
		 * Get the user based on their defined id, saved in wp_usermeta.
		 */
		$queryArgs = array(
			'role' => 'Distributor',
			'meta_key' => '_uid',
			'meta_value' => $suid,
			'fields' => 'all_with_meta'
		);

		$userQuery = new WP_User_Query($queryArgs);

		$users = $userQuery->get_results();

		if( !empty($users)) {

			foreach ($users as $user) {

				$wp_userID = $user->ID;

				$allMetaData = get_user_meta($wp_userID);
				$name = $user->display_name;
				$addr1 = $allMetaData['_address_addr1'][0];
				$addr2 = "";
				if(isset($allMetaData['_address_addr2'][0])) {
					$addr2 = $allMetaData['_address_addr2'][0];
				}
				$city = $allMetaData['_address_city'][0];
				$state = $allMetaData['_address_state'][0];
				$country = $allMetaData['_address_country'][0];
				$zipcode = $allMetaData['_address_zipcode'][0];

				$address = new Address($addr1, $addr2, $city, $state, $zipcode, $country);

				$discountCode = $allMetaData['_discountCode'][0];
				$phone = $allMetaData['_phone'][0];
				if($phone == null) {
					$phone = "Not Listed";
				}
				$email = $allMetaData['_email'][0];
				if($email == null) {
					$email = "Not Listed";
				}

				// Create a new distributor object from the information
				$distributor = new Distributor($name, $address, $discountCode, $phone, $email);
				return $distributor;
			}
		}
		else {
			echo "<p>We cannot find the information.</p>";
			echo "<p>Please verify the URL in your browser is correct.</p>";
		}
	}


	/**
	 * Generate a Distributor based on the user id
	 * @param string $uid
	 * @return Distributor|null
	 */
	public function getCurrentDistributor(string $uid) {

		$currUID = $uid;
		$distributor = null;

		/**
		 * Get the user based on their defined id, saved in wp_usermeta.
		 */
		$queryArgs = array(
			'role' => 'Distributor',
			'meta_key' => '_uid',
			'meta_value' => $currUID,
			'fields' => 'all_with_meta'
		);

		$userQuery = new WP_User_Query($queryArgs);

		$users = $userQuery->get_results();

		// VAR DUMP
		//var_export($users);

		if( !empty($users)) {

			foreach ($users as $user) {

				$wp_userID = $user->ID;

				$allMetaData = get_user_meta($wp_userID);
				$name = $user->display_name;
				$addr1 = $allMetaData['_address_addr1'][0];
				$addr2 = "";
				if(isset($allMetaData['_address_addr2'][0])) {
					$addr2 = $allMetaData['_address_addr2'][0];
				}
				$city = $allMetaData['_address_city'][0];
				$state = $allMetaData['_address_state'][0];
				$country = $allMetaData['_address_country'][0];
				$zipcode = $allMetaData['_address_zipcode'][0];

				$address = new Address($addr1, $addr2, $city, $state, $zipcode, $country);

				$discountCode = $allMetaData['_discountCode'][0];
				$phone = $allMetaData['_phone'][0];
				if($phone == null) {
					$phone = "Not Listed";
				}
				$email = $allMetaData['_email'][0];
				if($email == null) {
					$email = "Not Listed";
				}

				// Create a new distributor object from the information
				$distributor = new Distributor($name, $address, $discountCode, $phone, $email);
			}
		}
		else {
			echo "<p>We cannot find the information.</p>";
			echo "<p>Please verify the URL in your browser is correct.</p>";
		}

		return $distributor;
	}


	/**
	 * Function to respond to changing the locale in the Distributors page to show Canadian or USA distributors
	 * @return void
	 */
	function updateProfile() {

		$response = "";

		$addr_1 = $_REQUEST['addr1'];
		$addr_2 = $_REQUEST['addr2'];
		$city = $_REQUEST['city'];
		$state = $_REQUEST['state'];
		$zip = $_REQUEST['zipcode'];
		$country = $_REQUEST['country'];
		$phone = $_REQUEST['phone'];
		$email = $_REQUEST['email'];
		$userid = $_REQUEST['userid'];


		// Get the existing distributor's information
		$savedInfo = $this->getCurrentDistributor($userid);

		$message = "";

		//
		// Compare all of the user's information
		//

		// Address 1
		if($addr_1 != $savedInfo->getAddressObject()->getAddr1()) {
			$message .= "<p>Update Address 1 to: <b>$addr_1</b></p>";
		}

		// Address 2
		if($addr_2 != $savedInfo->getAddressObject()->getAddr2()) {
			$message .= "<p>Update Address 2 to: <b>$addr_2</b></p>";
		}

		// City
		if($city != $savedInfo->getAddressObject()->getCity()) {
			$message .= "<p>Update City to: <b>$city</b></p>";
		}

		// State
		if($state != $savedInfo->getAddressObject()->getState()) {
			$message .= "<p>Update State to: <b>$state</b></p>";
		}

		// Zipcode
		if($zip != $savedInfo->getAddressObject()->getZipcode()) {
			$message .= "<p>Update ZIP Code to: <b>$zip</b></p>";
		}

		// Country
		if(strtolower($country) != $savedInfo->getAddressObject()->getCountry()) {
			$message .= "<p>Update Country to: <b>$country</b></p>";
		}

		// Phone
		if($phone != $savedInfo->getPhone()) {
			$message .= "<p>Update phone number to <b>$phone</b></p>";
		}

		// Email
		if($email != $savedInfo->getEmail()) {
			$message .= "<p>Update email address to <b>$email</b></p>";
		}


		// Generate the email
		$emailTo = "you@youremail.com";
		$subject = "Enter subject";


		// Comment out before moving to staging
		if($message != "") {

			$sendMail = wp_mail($emailTo, $subject, $message);

			if($sendMail) {
				$response = "A request has been sent with your updated information.";
			}
			else {
				$response = "An error has occurred. Please try again later.";
			}

			$response = "A request has been sent with a request to:";

		}

		echo $response . $message;

		// Required for AJAX to return a response in Wordpress
		die();
	}


	/**
	 * Get all of the distributors
	 * @return array
	 */
	public function getAllDistributors() {

		// Query to return all Distributors
		$users = array(
			'role' => 'Distributor',
			'fields' => 'all_with_meta'
		);

		$distributorQuery = new WP_User_Query($users);

		$resultSet = $distributorQuery->get_results();

		// An array to store the results
		$aUIDs = array();
		$aDisplayNames = array();

		if( !empty($resultSet)) {

			foreach ($resultSet as $user) {


				$wp_userID = $user->ID;

				// Put the value of the Display Names into one array
				$aDisplayNames[] = $user->display_name;

				$allMetaData = get_user_meta($wp_userID);

				// Put the value of the UID in another array
				$aUIDs[] = $allMetaData['_uid'][0];
			}
		}

		// Combine the two arrays into one, associative array
		$aDistributors = array_combine($aUIDs, $aDisplayNames);

		return $aDistributors;
	}


	/**
	 * Function to query the Wordpress database and return all users with a role of "Distributor"
	 * @return void
	 */
	public function emailAllDistributors() {

		// Query to return all users with the role of "Distributor"
		$queryArgs = array(
			'role' => 'Distributor',
			'fields' => 'all_with_meta',
		);

		$userQuery = new WP_User_Query($queryArgs);

		$users = $userQuery->get_results();

		if( !empty($users)) {

			foreach ($users as $user) {

				$wp_userID = $user->ID;

				$allMetaData = get_user_meta($wp_userID);
				$uid = $allMetaData['_uid'][0];
				echo "<a href='http://" . $_SERVER['HTTP_HOST'] . "/profile/?uid=$uid' title='Link to your profile'>Click Here to View Your Profile.</a><br>";
			}
		}
		else {
			echo "We cannot find any users with role = '" . $queryArgs['role'] . "' in this database.";
		}

	}


	/**
	 * Generate a GEOJson file from a regular JSON file
	 * @param string $fileIn  The JSON file we're reading from
	 */
	public function generateGeoJSONFile(string $fileIn) {

		/**
		 * The file we want to write the geojson information
		 */
		$geojson_filepath = dirname(dirname(__FILE__)) . '/data/map.geojson';

		// Enclose everything in a try/catch loop tossing an Exception
		try {

			// Read the contents of the master distributors json file
			$json = json_decode(file_get_contents($fileIn));

			/**
			 * The GeoJSON array structure
			 * @var array $geojson
			 * @see http://geojson.org
			 */
			$geojson = array(
				'type'      => 'FeatureCollection',
				'features'  => array()
			);

			// Iterate through the json data
			foreach ($json as $distributor) {

				// Get the distributor's information
				$id = $distributor->id;
				$name = ucwords(strtolower($distributor->name));
				$addr_1 = ucwords(strtolower($distributor->addr_1));
				$addr_2 = ucwords(strtolower($distributor->addr_2));
				$city = ucwords(strtolower($distributor->city));
				$state = $distributor->state;
				$zipcode = $distributor->zipcode;
				$country = $distributor->country;
				$discountCode = $distributor->discountCode;
				$phoneNum = $distributor->phone;
				$email = strtolower($distributor->email);

				// Combine all of the address key/value pairs to make an address string we can pass to Google Maps API
				if(strpos($addr_1, "PO BOX") === 0) {
					// Remove PO Box addresses from Google Map queries
					$address = $city . " " . $state . " " . $zipcode;
				}
				else {
					$address = $addr_1 . "" . $addr_2 . " " . $city . " " . $state . " " . $zipcode;
				}

				// URL Encode the address since we're passing it as an HTML parameter
				$encodedAddress = urlencode($address);

				// Create the Google Maps request string
				$request = "https://maps.googleapis.com/maps/api/geocode/json?address=$encodedAddress&key=" . GOOGLE_MAPS_API_KEY;

				// Call Google Maps API to get the coordinates of the distributor
				$data = file_get_contents($request);
				$response = $data ? json_decode($data): false;

				// Check that Google Maps returned an address (found a location) from the address we're looking for
				if($response && $response->status == "OK") {

					/** @var string $resStreetNum */
					$resStreetNum = null;

					/** @var string $resStreetName */
					$resStreetName = null;

					/** @var string $resCity */
					$resCity = null;

					/** @var string $resProvince */
					$resState = null;

					/** @var string $resZipCode */
					$resZipCode = null;

					/** @var string $resCountry */
					$resCountry = null;

					// Get the address from Google Map's response
					// This URL shows a sample JSON response from Google Maps:
					// https://developers.google.com/maps/documentation/geocoding/intro#GeocodingResponses
					$resStreetNum = $response->results[0]->address_components[0]->long_name;
					$resStreetName = $response->results[0]->address_components[1]->long_name;

					// Get the coordinates from Google Map's response
					$lat = $response->results[0]->geometry->location->lat;
					$lng = $response->results[0]->geometry->location->lng;

					// Check for any errors in the Google Map response
					// We need these values so if they are null, toss them into a JSON file so we can diagnose the problem
					if($resStreetNum == null || $resStreetName == null) {
						array_push($errorArray, $response);
					}

					$marker = array(
						'type' => 'Feature',
						'properties' => array(
							'id' => $id,
							'name' => $name,
							'addr' => $resStreetNum . ' ' . $resStreetName,
							'city' => $city,
							'state' => $state,
							'zipcode' => $zipcode,
							'country' => $country,
							'discountCode' => $discountCode,
							'phone' => $phoneNum,
							'email' => $email
						),
						'geometry' => array(
							'type' => 'Point',
							'coordinates' => array(
								$lng,
								$lat
							)
						)
					);
					array_push($geojson['features'], $marker);


				}
				file_put_contents($geojson_filepath, json_encode($geojson));
			}

		}
		catch (Exception $e) {
			//$this->log->addEntry("Error in generateGeoJSONFile() function: " . $e->getMessage() . " " . $e->getFile() . " " . $e->getLine() . " " . $e->getTraceAsString());
		}
	}
}