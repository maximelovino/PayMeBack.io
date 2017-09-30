<?php

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

	public function selectSingleEventByID($id) {
		$eventQuery = $this->connection->prepare('SELECT * FROM t_events WHERE event_id=:event');
		$eventQuery->bindParam(':event', $id);
		$eventQuery->execute();
		$event = $eventQuery->fetch(PDO::FETCH_ASSOC);
		return $event;
	}

	public function selectUsersForEvent($event_id) {
		$peopleQuery = $this->connection->prepare('SELECT username FROM t_group_membership WHERE event_id=:id');
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
		$insertion->execute();
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
}