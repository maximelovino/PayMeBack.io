<?php
require_once 'DBConnection.php';

/**
 * Class DataValidator
 * Collection of static functions to validate forms data
 */
class DataValidator {

	/**
	 * @param $currencyCode string The currency code to check
	 * @return bool That specifies if the currency exists in our DB
	 */
	public static function isValidCurrency($currencyCode) {
		return DBConnection::getInstance()->getCurrency($currencyCode) != null;
	}

	/**
	 * @param $username string The username to check
	 * @return bool That specifies if the username exists
	 */
	public static function usernameExists($username) {
		return DBConnection::getInstance()->getSingleUser($username) != null;
	}

	/**
	 * @param $id int The event id to check
	 * @return bool That specifies if the event exists
	 */
	public static function isValidEventID($id) {
		return DBConnection::getInstance()->selectSingleEventByID($id) != null;
	}

	/**
	 * @param $usersArray array An array of users to validate
	 * @return bool That specifies if we have a valid users array (more than 1 person)
	 */
	public static function isValidUsersArray($usersArray) {
		return count($usersArray) > 1;
	}

	/**
	 * @param $username string The user to check
	 * @param $event_id int The event for which to check
	 * @return bool That specifies if the user is part of the event
	 */
	public static function hasUserAccessToEvent($username, $event_id) {
		$eventsForUser = DBConnection::getInstance()->getAllEventsForUser($username);

		foreach ($eventsForUser as $event) {
			if ($event['event_id'] == $event_id) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param $username string The username to test
	 * @return int 0 if not valid, 1 if valid
	 */
	public static function isValidUsername($username) {
		$regexp = '/^[a-z0-9]{1,256}$/';
		return preg_match($regexp, $username);
	}

	/**
	 * @param $name string The name to test
	 * @return int 0 if not valid, 1 if valid
	 */
	public static function isValidName($name) {
		$regexp = '/^[a-zA-Z0-9\-\s]+$/';
		return preg_match($regexp, $name);
	}

	/**
	 * @param $mail string The mail to test
	 * @return int 0 if not valid, 1 if valid
	 */
	public static function isValidEmail($mail) {
		$regexp = '/^[a-z0-9_\-\.]+@[a-z0-9_\-\.]+\.[a-z]+$/';
		return preg_match($regexp, $mail);
	}

	/**
	 * @param $amount string The amount to test
	 * @return int 0 if not valid, 1 if valid
	 */
	public static function isValidAmount($amount) {
		$regexp = '/^[0-9]+(\.[0-9]{1,2})?$/';
		return preg_match($regexp, $amount);
	}

	/**
	 * @param $title string The title to test
	 * @return int 0 if not valid, 1 if valid
	 */
	public static function isValidTitle($title) {
		$regexp = '/^.{1,256}$/';
		return preg_match($regexp, $title);
	}

	/**
	 * @param $weight string The weight to test
	 * @return int 0 if not valid, 1 if valid
	 */
	public static function isValidWeight($weight) {
		$regexp = '/^[1-9][0-9]*$/';
		return preg_match($regexp, $weight);
	}

	/**
	 * @param $year int The year to test
	 * @return bool That specifies if the given year is a leap year
	 */
	private static function isLeapYear($year) {
		return ($year % 4 == 0) && ($year % 100 == 0) && ($year % 400 != 0);
	}

	/**
	 * @param $date string The date to test
	 * @return bool That specifies if a date is valid (in format and in the calendar)
	 */
	public static function isValidDate($date) {
		$regexp = '/^[1-9][0-9]{3}-((0[1-9])|(1[0-2]))-(([0-2][0-9])|(3[0-1]))$/';
		if (!preg_match($regexp, $date)) {
			return false;
		} else {
			//check logical validity with months count
			list($year, $month, $day) = explode('-', $date);

			switch ($month) {
				case 1 | 3 | 5 | 7 | 8 | 10 | 12:
					return $day <= 31;
				case 2:
					return $day <= 28 || ($day == 29 && self::isLeapYear($year));
				default:
					return $day <= 30;
			}

		}
	}
}