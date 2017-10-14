<?php
require_once 'DBConnection.php';

class DataValidator {
	public static function isValidCurrency($currencyCode) {
		return DBConnection::getInstance()->getCurrency($currencyCode) != null;
	}

	public static function usernameExists($username) {
		return DBConnection::getInstance()->getUsersMatching($username) > 0;
	}

	public static function isValidUsersArray($usersArray) {
		return count($usersArray) > 1;
	}

	public static function isValidUsername($username) {
		$regexp = '/^[a-z0-9]{1,256}$/';
		return preg_match($regexp, $username);
	}

	public static function isValidName($name) {
		$regexp = '/^[a-zA-Z0-9\-\s]+$/';
		return preg_match($regexp, $name);
	}

	public static function isValidEmail($mail) {
		$regexp = '/^[a-z0-9_\-\.]+@[a-z0-9_\-\.]+\.[a-z]+$/';
		return preg_match($regexp, $mail);
	}

	public static function isValidAmount($amount) {
		$regexp = '/^[0-9]+(\.[0-9]{1,2})?$/';
		return preg_match($regexp, $amount);
	}

	public static function isValidTitle($title) {
		$regexp = '/^.{1,256}$/';
		return preg_match($regexp, $title);
	}

	public static function isValidWeight($weight) {
		$regexp = '/^[1-9][0-9]*$/';
		return preg_match($regexp, $weight);
	}

	private static function isLeapYear($year) {
		return ($year % 4 == 0) && ($year % 100 == 0) && ($year % 400 != 0);
	}

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