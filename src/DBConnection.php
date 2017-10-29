<?php

/**
 * Class DBConnection
 * Singleton containing functions to retrieve and transform data from the database
 */
class DBConnection {
	private $host = "localhost";
	private $username = "php";
	private $password = "3eXLjcN5PQXv39Vd";
	private $dbName = "petits_comptes_entre_amis";
	private $charset = "utf8";
	private $connection;
	private static $instance = null;

	/**
	 * DBConnection private constructor.
	 */
	private function __construct() {
		$this->connection = new PDO("mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}", $this->username, $this->password);
	}

	/**
	 * @return DBConnection instance
	 */
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new DBConnection();
		}
		return self::$instance;
	}

	/**
	 * @return PDO connection of the instance
	 */
	public function getConnection() {
		return $this->connection;
	}

	/**
	 * @return array containing the list of all users from the database, each as an associative array
	 */
	public function getAllUsers() {
		$query = $this->connection->prepare("SELECT * FROM t_users ORDER BY username");
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * @param $username string The username for which we want the list of events
	 * @return array containing the list of events the user is part of, each as an associative array
	 */
	public function getAllEventsForUser($username) {
		$query = $this->connection->prepare("SELECT * FROM t_group_membership JOIN t_events ON t_events.event_id = t_group_membership.event_id HAVING username=:username");
		$query->bindParam(":username", $username);
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * @return array containing the list of currencies available, each as an associative array
	 */
	public function getAllCurrencies() {
		$query = $this->connection->prepare("SELECT * FROM t_currencies ORDER BY currency_code");
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * @param $currencyCode string The currency code for a currency
	 * @return mixed An associative array of all the fields of the currency in the DB
	 */
	public function getCurrency($currencyCode) {
		$query = $this->connection->prepare("SELECT * FROM t_currencies WHERE currency_code=:code");
		$query->bindParam(':code', $currencyCode);
		$query->execute();
		return $query->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * @param $title string The title of the event to insert
	 * @param $description string The description of the event to insert
	 * @param $users array An array of users participating in the event
	 * @param $currency string The currency code for the event
	 * @param $weights array An array of (user -> weight) specifying the weights of each user for the event
	 * @return bool That specifies if the insertion was successful or not
	 */
	public function insertNewEvent($title, $description, $users, $currency, $weights) {
		$this->connection->beginTransaction();
		$eventInsertionSQL = 'INSERT INTO t_events VALUES (DEFAULT,:name,:desc,:currency)';
		$eventInsertionQuery = $this->connection->prepare($eventInsertionSQL);
		$eventInsertionQuery->bindParam(':name', $title);
		$eventInsertionQuery->bindParam(':desc', $description);
		$eventInsertionQuery->bindParam(':currency', $currency);
		$result = $eventInsertionQuery->execute();
		if (!$result) {
			$this->connection->rollBack();
			return false;
		}
		$idEvent = $this->connection->lastInsertId();


		foreach ($users as $user) {
			$insertionQuery = $this->connection->prepare('INSERT INTO t_group_membership VALUES (:username,:id, :weight)');
			$insertionQuery->bindParam(':username', $user);
			$insertionQuery->bindParam(':id', $idEvent);
			$insertionQuery->bindParam(':weight', $weights[$user]);
			$result = $insertionQuery->execute();
			if (!$result) {
				$this->connection->rollBack();
				return false;
			}
		}
		$this->connection->commit();
		return true;
	}

	/**
	 * @param $title string The title of the expense to insert
	 * @param $description string The description of the expense to insert
	 * @param $eventID int The ID of the event the expense is associated with
	 * @param $amount double The amount of the expense to insert
	 * @param $date string The date of the expense to insert in the format YYYY-MM-DD
	 * @param $buyer string The username of the buyer
	 * @param $involvedUsers array Array containing the usernames of the users involved
	 * @return bool That specifies if the insertion was successful or not
	 */
	public function insertExpense($title, $description, $eventID, $amount, $date, $buyer, $involvedUsers) {
		$this->connection->beginTransaction();
		$expenseInsertionQuery = $this->connection->prepare("INSERT INTO t_expenses VALUES (DEFAULT , :title, :desc, :amount, :date, :buyer, :event)");
		$expenseInsertionQuery->bindParam(':title', $title);
		$expenseInsertionQuery->bindParam(':desc', $description);
		$expenseInsertionQuery->bindParam(':amount', $amount);
		$expenseInsertionQuery->bindParam(':date', $date);
		$expenseInsertionQuery->bindParam(':buyer', $buyer);
		$expenseInsertionQuery->bindParam(':event', $eventID);
		$result = $expenseInsertionQuery->execute();
		if (!$result) {
			$this->connection->rollBack();
			return false;
		}
		$idExpense = $this->connection->lastInsertId();

		foreach ($involvedUsers as $user) {
			$insertionQuery = $this->connection->prepare("INSERT INTO t_expense_membership VALUES (:id,:name)");
			$insertionQuery->bindParam(':id', $idExpense);
			$insertionQuery->bindParam(':name', $user);
			$result = $insertionQuery->execute();
			if (!$result) {
				$this->connection->rollBack();
				return false;
			}
		}
		$this->connection->commit();
		return true;
	}

	/**
	 * @param $payingUser string Username of the user paying
	 * @param $payedUser string Username of the user payed
	 * @param $eventID int ID of the event
	 * @param $amount double Amount of the payment
	 * @param $date string Date of direct payment, in the format YYYY-MM-DD
	 * @return bool That specifies if the insertion was successful or not
	 */
	public function insertDirectPayment($payingUser, $payedUser, $eventID, $amount, $date) {
		$directPaymentQuery = $this->connection->prepare("INSERT INTO t_reimbursement VALUES (DEFAULT, :paying, :payed, :event, :amount, :date)");
		$directPaymentQuery->bindParam(':paying', $payingUser);
		$directPaymentQuery->bindParam(':payed', $payedUser);
		$directPaymentQuery->bindParam(':event', $eventID);
		$directPaymentQuery->bindParam(':amount', $amount);
		$directPaymentQuery->bindParam(':date', $date);
		return $directPaymentQuery->execute();
	}

	public function selectSingleEventByID($id) {
		$eventQuery = $this->connection->prepare('SELECT * FROM t_events WHERE event_id=:event');
		$eventQuery->bindParam(':event', $id);
		$eventQuery->execute();
		$event = $eventQuery->fetch(PDO::FETCH_ASSOC);
		return $event;
	}

	public function selectUsersForEvent($event_id) {
		$peopleQuery = $this->connection->prepare('SELECT * FROM t_group_membership JOIN t_users ON t_group_membership.username = t_users.username WHERE event_id=:id');
		$peopleQuery->bindParam(':id', $event_id);
		$peopleQuery->execute();
		return $peopleQuery->fetchAll(PDO::FETCH_ASSOC);
	}

	public function insertNewUser($username, $first_name, $last_name, $email, $password_hash) {
		$insertion = $this->connection->prepare('INSERT INTO t_users VALUES (:username,:first_name,:last_name,:email,:password)');
		$insertion->bindParam(':username', $username);
		$insertion->bindParam(':first_name', $first_name);
		$insertion->bindParam(':last_name', $last_name);
		$insertion->bindParam(':email', $email);
		$insertion->bindParam(':password', $password_hash);
		return $insertion->execute();
	}

	public function getUsersMatching($username) {
		$records = $this->connection->prepare('SELECT * FROM t_users WHERE username= :uname');
		$records->bindParam(':uname', $username);
		$records->execute();
		return $records->fetchAll();
	}

	public function getSingleUser($username) {
		$records = $this->connection->prepare('SELECT * FROM t_users WHERE username= :uname');
		$records->bindParam(':uname', $username);
		$records->execute();
		return $records->fetch(PDO::FETCH_ASSOC);
	}

	public function loginOK($username, $password) {
		$record = $this->connection->prepare('SELECT username, password FROM t_users WHERE username= :username');
		$record->bindParam(':username', $username);
		$record->execute();
		$result = $record->fetch(PDO::FETCH_ASSOC);
		return count($result) > 0 && password_verify($password, $result['password']);
	}

	public function getAllExpensesForEvent($id) {
		$query = $this->connection->prepare('SELECT * FROM t_expenses WHERE event_id=:id ORDER BY date DESC');
		$query->bindParam(':id', $id);
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getAllExpensesForEventByUser($id) {
		$expenses = $this->getAllExpensesForEvent($id);
		$byUser = array();
		foreach ($expenses as $expense) {
			$buyer = $expense['buyer_username'];
			if (!isset($byUser[$buyer])) {
				$byUser[$buyer] = 0;
			}
			$byUser[$buyer] += $expense['amount'];
		}
		return $byUser;
	}

	public function getAllDirectPaymentsForEvent($id) {
		$query = $this->connection->prepare('SELECT * FROM t_reimbursement WHERE event_id=:id ORDER BY date DESC');
		$query->bindParam(':id', $id);
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getExpensesByUserForExpense($transaction_id) {
		$query = $this->connection->prepare('SELECT * FROM t_expenses JOIN t_expense_membership ON t_expenses.transaction_id = t_expense_membership.transaction_id  JOIN t_coeffsByTransaction ON t_coeffsByTransaction.transaction_id = t_expenses.transaction_id JOIN t_group_membership ON t_group_membership.username = t_expense_membership.username AND t_group_membership.event_id = t_expenses.event_id JOIN t_events ON t_expenses.event_id = t_events.event_id JOIN t_currencies ON t_events.currency_code = t_currencies.currency_code WHERE t_expenses.transaction_id=:id;');
		$query->bindParam(':id', $transaction_id);
		$query->execute();
		$results = $query->fetchAll(PDO::FETCH_ASSOC);
		$balance = array();
		foreach ($results as $result) {
			if ($result['buyer_username'] != $result['username']) {
				$total = ($result['coefficient'] / $result['sum']) * $result['amount'];
				$balance[$result['username']] = $this->roundAmountToCurrency($total, $result['currency_code']);
			}
		}
		return $balance;
	}

	public function getSingleExpenseDetail($transaction_id) {
		$query = $this->connection->prepare('SELECT * FROM t_expenses WHERE transaction_id=:id');
		$query->bindParam(':id', $transaction_id);
		$query->execute();
		return $query->fetch(PDO::FETCH_ASSOC);
	}

	public function getBalanceForEvent($eventID) {
		$expenses = $this->getAllExpensesForEvent($eventID);
		$reimbursements = $this->getAllDirectPaymentsForEvent($eventID);
		$balance = array();
		foreach ($expenses as $expense) {
			$data = $this->getExpensesByUserForExpense($expense['transaction_id']);
			$buyer = $expense['buyer_username'];

			foreach ($data as $user => $amount) {
				if (!isset($balance[$user])) {
					$balance[$user] = array();
				}
				if (!isset($balance[$buyer])) {
					$balance[$buyer] = array();
				}

				if (!isset($balance[$user][$buyer])) {
					$balance[$user][$buyer] = 0;
				}

				if (!isset($balance[$buyer][$user])) {
					$balance[$buyer][$user] = 0;
				}
				$balance[$buyer][$user] += $amount;
				$balance[$user][$buyer] -= $amount;
			}
		}
		foreach ($reimbursements as $reimbursement) {
			$in = $reimbursement['paying_username'];
			$out = $reimbursement['payed_username'];
			$amount = $reimbursement['amount'];

			if (!isset($balance[$out])) {
				$balance[$out] = array();
			}
			if (!isset($balance[$in])) {
				$balance[$in] = array();
			}

			if (!isset($balance[$out][$in])) {
				$balance[$out][$in] = 0;
			}

			if (!isset($balance[$in][$out])) {
				$balance[$in][$out] = 0;
			}

			$balance[$in][$out] += $amount;
			$balance[$out][$in] -= $amount;
		}
		return $balance;
	}

	public function getBalanceForEachEventForUser($username) {
		$events = $this->getAllEventsForUser($username);
		$balance = array();
		foreach ($events as $event) {
			$balance[$event['event_id']] = 0;
			$totalBalance = $this->getBalanceForEvent($event['event_id']);
			if (isset($totalBalance[$username])) {
				foreach ($totalBalance[$username] as $subBalance) {
					$balance[$event['event_id']] += $subBalance;
				}
			}
		}
		return $balance;
	}

	public function getTotalBalanceForUser($username) {
		$balanceByEvent = $this->getBalanceForEachEventForUser($username);
		$byCurrency = array();
		foreach ($balanceByEvent as $key => $value) {
			$event = $this->selectSingleEventByID($key);
			$currencyCode = $event['currency_code'];
			if (!isset($byCurrency[$currencyCode])) {
				$byCurrency[$currencyCode] = 0;
			}
			$byCurrency[$currencyCode] += $value;
		}
		return $byCurrency;
	}

	public function deleteEventByID($id) {
		$query = $this->connection->prepare('DELETE FROM t_events WHERE event_id=:id');
		$query->bindParam(':id', $id);
		$query->execute();
	}

	public function deleteExpenseByID($id) {
		$query = $this->connection->prepare('DELETE FROM t_expenses WHERE transaction_id=:id');
		$query->bindParam(':id', $id);
		$query->execute();
	}

	public function deleteDirectPaymentByID($id) {
		$query = $this->connection->prepare('DELETE FROM t_reimbursement WHERE reimbursement_id=:id');
		$query->bindParam(':id', $id);
		$query->execute();
	}

	public function roundAmountToCurrency($amount, $currencyCode) {
		$amountValue = doubleval($amount);
		$currency = $this->getCurrency($currencyCode);

		return round($amountValue / $currency['rounding_multiple']) * $currency['rounding_multiple'];
	}

	public function getDirectPayment($id) {
		$query = $this->connection->prepare('SELECT * FROM t_reimbursement WHERE reimbursement_id=:id');
		$query->bindParam(':id', $id);
		$query->execute();
		return $query->fetch(PDO::FETCH_ASSOC);
	}
}