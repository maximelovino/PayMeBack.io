<?php

/**
 * Class DBConnection
 * Singleton containing functions to retrieve data from the database
 */
class DBConnection {
	private $host = "localhost";
	private $username = "php";
	private $password = "3eXLjcN5PQXv39Vd";
	private $dbName = "petits_comptes_entre_amis";
	private $charset = "utf8";
	private $connection;
	private static $instance = null;

	private function __construct() {
		$this->connection = new PDO("mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}", $this->username, $this->password);
	}

	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new DBConnection();
		}
		return self::$instance;
	}

	public function getConnection() {
		return $this->connection;
	}

	public function getAllUsers() {
		$query = $this->connection->prepare("SELECT * FROM t_users ORDER BY username");
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getAllEventsForUser($username) {
		$query = $this->connection->prepare("SELECT * FROM t_group_membership JOIN t_events ON t_events.event_id = t_group_membership.event_id HAVING username=:username");
		$query->bindParam(":username", $username);
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getAllCurrencies() {
		return $this->connection->query("SELECT * FROM t_currencies ORDER BY currency_code", PDO::FETCH_ASSOC);
	}

	public function getCurrency($currencyCode) {
		$query = $this->connection->prepare("SELECT * FROM t_currencies WHERE currency_code=:code");
		$query->bindParam(':code', $currencyCode);
		$query->execute();
		return $query->fetch(PDO::FETCH_ASSOC);
	}

	public function insertNewEvent($title, $description, $users, $currency, $weights) {

		//TODO Check that it went through
		//TODO wrap in transaction

		$eventInsertionSQL = 'INSERT INTO t_events VALUES (DEFAULT,:name,:desc,:currency)';
		$eventInsertionQuery = $this->connection->prepare($eventInsertionSQL);
		$eventInsertionQuery->bindParam(':name', $title);
		$eventInsertionQuery->bindParam(':desc', $description);
		$eventInsertionQuery->bindParam(':currency', $currency);
		$eventInsertionQuery->execute();
		$idEvent = $this->connection->lastInsertId();


		foreach ($users as $user) {
			$insertionQuery = $this->connection->prepare('INSERT INTO t_group_membership VALUES (:username,:id, :weight)');
			$insertionQuery->bindParam(':username', $user);
			$insertionQuery->bindParam(':id', $idEvent);
			$insertionQuery->bindParam(':weight', $weights[$user]);
			$insertionQuery->execute();
		}
	}

	public function insertExpense($title, $description, $eventID, $amount, $date, $buyer, $involvedUsers) {
		//TODO transaction
		$expenseInsertionQuery = $this->connection->prepare("INSERT INTO t_expenses VALUES (DEFAULT , :title, :desc, :amount, :date, :buyer, :event)");
		$expenseInsertionQuery->bindParam(':title', $title);
		$expenseInsertionQuery->bindParam(':desc', $description);
		$expenseInsertionQuery->bindParam(':amount', $amount);
		$expenseInsertionQuery->bindParam(':date', $date);
		$expenseInsertionQuery->bindParam(':buyer', $buyer);
		$expenseInsertionQuery->bindParam(':event', $eventID);
		$expenseInsertionQuery->execute();
		$idExpense = $this->connection->lastInsertId();

		foreach ($involvedUsers as $user) {
			$insertionQuery = $this->connection->prepare("INSERT INTO t_expense_membership VALUES (:id,:name)");
			$insertionQuery->bindParam(':id', $idExpense);
			$insertionQuery->bindParam(':name', $user);
			$insertionQuery->execute();
		}

	}

	public function insertReimbursement($payingUser, $payedUser, $reimbursementEventID, $amount, $date) {
		$reimbursementInsertionQuery = $this->connection->prepare("INSERT INTO t_reimbursement VALUES (DEFAULT, :paying, :payed, :event, :amount, :date)");
		$reimbursementInsertionQuery->bindParam(':paying', $payingUser);
		$reimbursementInsertionQuery->bindParam(':payed', $payedUser);
		$reimbursementInsertionQuery->bindParam(':event', $reimbursementEventID);
		$reimbursementInsertionQuery->bindParam(':amount', $amount);
		$reimbursementInsertionQuery->bindParam(':date', $date);
		$reimbursementInsertionQuery->execute();
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
		$records = $this->connection->prepare('SELECT username FROM t_users WHERE username= :uname');
		$records->bindParam(':uname', $username);
		$records->execute();
		return $records->fetchAll();
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

	public function getAllReimbursementsForEvent($id) {
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
				$balance[$result['username']] = round($total / $result['rounding_multiple']) * $result['rounding_multiple'];
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
		$reimbursements = $this->getAllReimbursementsForEvent($eventID);
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
}