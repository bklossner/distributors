<?php

namespace com\bob\distributors;

/**
 *
 * Class Distributor
 * @version 1.0.0
 * @author Bob Klossner <bobklossner@gmail.com>
 * @copyright 2017 Bob Klossner
 *
 */
class Distributor
{
	/**
	 * The unique ID of the distributor
	 * @var string
	 */
	private $uid;

	/**
	 * The name of the distributor
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The distributor's address
	 *
	 * @var Address
	 */
	private $address;

	/**
	 * The distributor's level
	 * This value determines the distributor's icon shown on the Google Map
	 *
	 * @var string
	 */
	private $discountCode;

	/**
	 * The distributor's phone number
	 *
	 * @var string
	 */
	private $phone;

	/**
	 * The distributor's email address
	 *
	 * @var string
	 */
	private $email;

	/**
	 * The distributor's user name that will be referenced in table wp_users
	 * @var string $uname   User Name
	 */
	private $uname;

	/**
	 * The icon that will be displayed after the distributor's name in the 'accordion' dropdown in the Distributors page.
	 * @var string $icon
	 */
	private $icon;

	/**
	 * The user role.
	 * Mapped to Wordpress table wp_options.wp_user_roles
	 */
	const WP_USER_ROLE = "distributor";

	/**
	 * Distributor constructor.
	 * @param string $name
	 * @param Address $address
	 * @param string $discountCode
	 * @param string $phone
	 * @param string $email
	 */
	public function __construct($name, Address $address, $discountCode, $phone, $email)
	{
		$this->name = $name;
		$this->address = $address;
		$this->discountCode = $discountCode;
		$this->phone = $phone;

		// Parse the $email address, removing a second email address if one exists
		$lookForSemicolon = strpos($email, ';');
		if($lookForSemicolon) {
			$this->email = substr($email, 0, $lookForSemicolon - 1);
		}
		else {
			$this->email = $email;
		}

		return $this;
	}

	/**
	 * Get the distributor's user id
	 * @return string
	 */
	public function getUid()
	{

		return $this->uid;
	}

	/**
	 * Set the distributor's user id
	 * @param $string $uid
	 */
	public function setUid($uid) {
		$this->uid = $uid;
		return $this;
	}

	/**
	 * Get the distributor's name
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set the distributor's name
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Get the distributor's address
	 * @return string
	 */
	public function getAddress()
	{
		return $this->address->__toString();
	}

	/**
	 * Get the Address object for database transactions
	 * @return Address
	 */
	public function getAddressObject() {
		return $this->address;
	}

	/**
	 * Set the distributor's address
	 * @param Address $address
	 */
	public function setAddress($address)
	{
		$this->address = $address;
		return $this;
	}

	/**
	 * Get the distributor's discount code
	 * @return string
	 */
	public function getDiscountCode()
	{
		return $this->discountCode;
	}

	/**
	 * Set the distributor's discount code
	 * @param string $discountCode
	 */
	public function setDiscountCode($discountCode)
	{
		$this->discountCode = $discountCode;
		return $this;
	}

	/**
	 * Get the distributor's phone number
	 * @return string
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * Get the distributor's phone number
	 * @param string $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}

	/**
	 * Generate a username from the distributor's store name by replacing any whitespaces with underscores
	 *   and limiting the username to two words with an underscore between them.
	 * @example:    Neo Geo Boomble'/pants  =   Neo_Geo
	 * @param string $uname
	 * @return void
	 */
	public function createUserName() {

		// Trim the text, removing before and after whitespace
		/** @var string $filtered */
		$filtered = trim($this->name);

		// An array full of things we want to remove from a string
		/** @var array $filterOptions */
		$filterOptions = ['.', ' ltd', ' llc', ' inc', ' & ', '&', 'llc',' LTD', ' LLC', ' INC', ',', ' - ', '-', '\''];

		$filtered = str_replace($filterOptions, '', $filtered);

		$filterSlashes = ['\\', '/'];

		$filtered = str_replace($filterSlashes, ' ', $filtered);

		// Check to see if there's a space in the user's name
		if(strpos($filtered, ' ')) {
			// Split the filtered string using a space as a delimiter
			$split = explode(" ", $filtered);
			$filtered = $split[0] . "_" . $split[1];
		}

		$this->uname = $filtered;
	}

	/**
	 * Function to retrieve the username
	 * @return string $uname  User Name
	 */
	public function getUserName() {
		return $this->uname;
	}


	/**
	 * Function that checks wp_users table to see if the user/distributor exists
	 * @return array
	 */
	public function saveUserToDatabase() {

		// Instantiate the return array
		$progress = array();

		// Check to see if the distributor already exists in the wp_users database
		if( !username_exists($this->getUserName()) ) {

			// If the user name does not exist, create a new user and add all of the stuff to the wp_users and
			//  wp_user_meta tables

			/** @var string $passwd */
			$passwd = wp_generate_password( $length=12, $include_standard_special_chars=false );


			/**
			 * Create an array of all the stuff we want to associate the user with
			 * @var array $userdata
			 */
			$userdata = array(
				'user_login'    => utf8_encode($this->getUserName()),
				'user_pass'     => utf8_encode($passwd),
				'user_nicename' => utf8_encode($this->getUserName()),
				'user_email'    => utf8_encode($this->getEmail()),
				'role'          => utf8_encode($this->getRole()),
				'display_name'  => strtoupper(utf8_encode($this->getName()))
			);


			// Add the user to the wp_users table with the $userdata array information
			try {
				$userID = wp_insert_user($userdata);
				if( ! is_wp_error($userID) ) {

					/**
					 * Save user password to their wp_user_meta table.
					 */

					// Add the Distributor user id
					add_user_meta($userID, '_uid', utf8_encode($this->getUid()));

					// Add the password to table wp_usermeta.
					add_user_meta($userID, '_passwd', $passwd);

					// Add the first line of the address to wp_usermeta.
					// Prefixing the address entries with '_address' for ease of clarification.
					add_user_meta($userID, '_address_addr1', utf8_encode($this->getAddressObject()->getAddr1()));

					// If there's an addr_2 entry field, add that to wp_usermeta.
					if($this->getAddressObject()->getAddr2()) {
						add_user_meta($userID, '_address_addr2', utf8_encode($this->getAddressObject()->getAddr2()));
					}

					// Add city to wp_usermeta
					add_user_meta($userID, '_address_city', utf8_encode($this->getAddressObject()->getCity()));

					// Add state to wp_usermeta
					add_user_meta($userID, '_address_state', utf8_encode($this->getAddressObject()->getState()));

					// Add country to wp_usermeta
					add_user_meta($userID, '_address_country', utf8_encode($this->getAddressObject()->getCountry()));

					// Add zipcode to wp_usermeta
					add_user_meta($userID, '_address_zipcode', utf8_encode($this->getAddressObject()->getZipcode()));

					// Add discountCode to wp_usermeta
					add_user_meta($userID, '_discountCode', utf8_encode($this->getDiscountCode()));

					// add phone number to wp_usermeta
					add_user_meta($userID, '_phone', utf8_encode($this->getPhone()));

					// add email address to wp_usermeta
					add_user_meta($userID, '_email', utf8_encode($this->getEmail()));
				}
				$progress = array('type'=>'new', 'message'=>'Distributor name: '.$this->getName().'<br/> Username:'.$this->getUserName());
				//$progress .= 'Distributor "'.$this->getName().'" found and added.<br>';
			}
			catch(Exception $e) {
				$progress = array('type'=>'error', 'message'=>"<p>ERROR: Could not add user to wp_users. " . $e->getTraceAsString() . $this->getName()."</p>");
			}

		}
		// If the user name exists, update all of their information
		else{

			$userID = "";

			// Get their wordpress_id number
			$user = get_user_by('user_login',$this->getUserName());

			if($user)
			{
				$userID = $user->ID;
			}

			update_user_meta($userID, '_address_addr1', utf8_encode($this->getAddressObject()->getAddr1()));

			// If there's an addr_2 entry field, add that to wp_usermeta.
			if($this->getAddressObject()->getAddr2()) {
				update_user_meta($userID, '_address_addr2', utf8_encode($this->getAddressObject()->getAddr2()));
			}

			update_user_meta($userID, '_address_city', utf8_encode($this->getAddressObject()->getCity()));
			update_user_meta($userID, '_address_state', utf8_encode($this->getAddressObject()->getState()));
			update_user_meta($userID, '_address_country', utf8_encode($this->getAddressObject()->getCountry()));
			update_user_meta($userID, '_address_zipcode', utf8_encode($this->getAddressObject()->getZipcode()));
			update_user_meta($userID, '_discountCode', utf8_encode($this->getDiscountCode()));
			update_user_meta($userID, '_phone', utf8_encode($this->getPhone()));
			update_user_meta($userID, '_email', utf8_encode($this->getEmail()));

			$progress = array('type'=>'update');
		}

		return $progress;
	}



	/**
	 * Function to get the user's role.
	 * It's static.
	 * @return string
	 */
	public function getRole() {
		return self::WP_USER_ROLE;
	}


	/**
	 * Function to retrieve a Distributor's info based on their wordpress wp_user id
	 * @param string $uid
	 */
	public function getDistributor($uid) {
		$user = get_user_by('id', $uid);
		$distributor = null;

		// Make sure there's no empty resultset
		if(!empty($user)) {

			// All of the info we need for a Distributor

			// Get all of the usermeta goodies needed to set the address
			$allMetaData = get_user_meta($uid);

			$uid = $allMetaData['_uid'][0];
			$addr1 = $allMetaData['_address_addr1'][0];
			$addr2 = "";
			if(isset($allMetaData['_address_addr2'][0])) {
				$addr2 = $allMetaData['_address_addr2'][0];
			}
			$city = $allMetaData['_address_city'][0];
			$state = $allMetaData['_address_state'][0];
			$country = $allMetaData['_address_country'][0];
			$zipcode = $allMetaData['_address_zipcode'][0];
			$discountCode = $allMetaData['_discountCode'][0];
			$phone = $allMetaData['_phone'][0];
			$email = $allMetaData['_email'][0];


			//$name, Address $address, $discountCode, $phone, $email
			// Create a Distributor object from all of the stuff
			$this->name = $user->display_name;
			$this->address = new Address($addr1, $addr2, $city, $state, $zipcode, $country);
			$this->discountCode = $discountCode;
			$this->phone = $phone;
			$this->email = $email;

			$distributor = new Distributor($this->name, $this->address, $this->discountCode, $this->phone, $this->email);
			$distributor->setUid($uid);
		}

		return $distributor;
	}


	/**
	 * @inheritDoc
	 */
	function __toString()
	{
		$content =  "<div class='store col-xs-12 col-sm-6 col-md-4'>" .
		                "<div class='address-container'>" .
		                    "<h4 class='name'>" . $this->titleCase($this->getName()) . "<img class='icon' src='" . $this->icon . "' width='16'></h4>" .
$this->address .
		                "</div>" .
		            "<div class='contact-info-container'>";

		// If the Distributor has a phone number, display it
		if($this->getPhone() != null) {
			$content .= "<p class='phoneNumber'>Ph: " . $this->getPhone() . "</p>";
		}
		else {
			$content .= "<p class='phoneNumber'>Ph: Not listed</p>";
		}

		// If the Distributor has an email address, display it
		if($this->getEmail() != null) {
			$content .= "<p class='email'>Email: " . $this->getEmail() . "</p>";
		}
		else {
			$content .= "<p class='email'>Email: Not listed</p>";
		}

		$content .= "</div></div>";

		return $content;
	}

	/**
	 * Function to convert cases of certain words in the Distributor's Name
	 * @param $string
	 * @return string
	 */
	function titleCase($string) {
		/*
		 * Exceptions in lower case are words you don't want converted
		 * Exceptions all in upper case are any words you don't want converted to title case
		 *   but should be converted to upper case, e.g.:
		 *   king henry viii or king henry Viii should be King Henry VIII
		 */
		$delimiters = [ " ", "-", "/", ".", "\'", "O'", "Mc", "(", ")" ];
		$exceptions = [ "CSI", "PO", "PA", "NC", "LC", "SO", "s", "SW", "LSH", "AEP", "MHQ", "LLC", "LLC,", "L.A.W.S.", "PSE", "-A", "NC/SO", "WAC", "CC" ];

		foreach ($delimiters as $dlnr => $delimiter){
			$words = explode($delimiter, $string);
			$newwords = array();
			foreach ($words as $wordnr => $word){

				if (in_array(strtoupper($word), $exceptions)){
					// check exceptions list for any words that should be in upper case
					$word = strtoupper($word);
				}
				elseif (in_array(strtolower($word), $exceptions)){
					// check exceptions list for any words that should be in upper case
					$word = strtolower($word);
				}

				elseif (!in_array($word, $exceptions) ){
					// convert to uppercase (non-utf8 only)

					$word = ucfirst($word);

				}
				array_push($newwords, $word);
			}
			$string = join($delimiter, $newwords);
		}//foreach
		return $string;
	}

}
