create table t_currencies
(
	currency_code varchar(3) not null
		primary key,
	rounding_multiple double not null,
	full_name varchar(256) not null,
	constraint t_currencies_currency_code_uindex
  UNIQUE (currency_code)
)
;

create table t_events
(
	event_id int auto_increment
		primary key,
	event_name varchar(256) not null,
	event_description text null,
	currency_code varchar(3) not null,
	constraint t_events_event_id_uindex
  UNIQUE (event_id),
	constraint t_events_t_currencies_currency_code_fk
  FOREIGN KEY (currency_code) REFERENCES t_currencies (currency_code)
)
;

create index t_events_t_currencies_currency_code_fk
	on t_events (currency_code)
;

create table t_expense_membership
(
  transaction_id INT          NOT NULL,
  username       VARCHAR(256) NOT NULL,
  PRIMARY KEY (transaction_id, username)
)
;

CREATE INDEX t_expense_membership_t_users_username_fk
  ON t_expense_membership (username)
;

create table t_expenses
(
	transaction_id int auto_increment
		primary key,
	title varchar(256) not null,
	description text null,
	amount double not null,
	date date not null,
	buyer_username varchar(256) not null,
	event_id int not null,
	constraint t_expenses_transaction_id_uindex
  UNIQUE (transaction_id),
	constraint t_expenses_t_events_event_id_fk
  FOREIGN KEY (event_id) REFERENCES t_events (event_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
;

create index t_expenses_t_events_event_id_fk
	on t_expenses (event_id)
;

create index t_expenses_t_users_username_fk
	on t_expenses (buyer_username)
;

alter table t_expense_membership
	add constraint t_expense_membership_t_expenses_transaction_id_fk
FOREIGN KEY (transaction_id) REFERENCES t_expenses (transaction_id)
  ON UPDATE CASCADE
  ON DELETE CASCADE
;

create table t_group_membership
(
	username varchar(256) not null,
	event_id int not null,
	coefficient int default '1' not null,
	primary key (username, event_id),
	constraint t_group_membership_t_events_event_id_fk
  FOREIGN KEY (event_id) REFERENCES t_events (event_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
;

create index t_group_membership_t_events_event_id_fk
	on t_group_membership (event_id)
;

CREATE TABLE t_reimbursement
(
  reimbursement_id INT AUTO_INCREMENT
    PRIMARY KEY,
  paying_username  VARCHAR(256) NOT NULL,
  payed_username   VARCHAR(256) NOT NULL,
  event_id         INT          NOT NULL,
  amount           INT          NOT NULL,
  date             DATE         NOT NULL,
  CONSTRAINT t_reimbursement_reimbursement_id_uindex
  UNIQUE (reimbursement_id),
  CONSTRAINT t_reimbursement_t_events_event_id_fk
  FOREIGN KEY (event_id) REFERENCES t_events (event_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE INDEX t_reimbursement_t_events_event_id_fk
  ON t_reimbursement (event_id);

CREATE INDEX t_reimbursement_t_users_username_fk
  ON t_reimbursement (paying_username);

CREATE INDEX t_reimbursement_t_users_username_payed_fk
  ON t_reimbursement (payed_username);

create table t_users
(
	username varchar(256) not null
		primary key,
	first_name varchar(256) null,
	last_name varchar(256) null,
	email varchar(256) null,
	password varchar(256) null,
	constraint t_users_username_uindex
  UNIQUE (username)
)
;

alter table t_expense_membership
	add constraint t_expense_membership_t_users_username_fk
FOREIGN KEY (username) REFERENCES t_users (username)
  ON UPDATE CASCADE
;

alter table t_expenses
	add constraint t_expenses_t_users_username_fk
FOREIGN KEY (buyer_username) REFERENCES t_users (username)
  ON UPDATE CASCADE
;

alter table t_group_membership
	add constraint t_group_membership_t_users_username_fk
FOREIGN KEY (username) REFERENCES t_users (username)
  ON UPDATE CASCADE;

ALTER TABLE t_reimbursement
  ADD CONSTRAINT t_reimbursement_t_users_username_fk
FOREIGN KEY (paying_username) REFERENCES t_users (username)
  ON UPDATE CASCADE;

ALTER TABLE t_reimbursement
  ADD CONSTRAINT t_reimbursement_t_users_username_payed_fk
FOREIGN KEY (payed_username) REFERENCES t_users (username)
  ON UPDATE CASCADE;

CREATE VIEW t_coeffsbytransaction AS
  SELECT
    `petits_comptes_entre_amis`.`t_expenses`.`transaction_id`           AS `transaction_id`,
    sum(`petits_comptes_entre_amis`.`t_group_membership`.`coefficient`) AS `sum`
  FROM ((`petits_comptes_entre_amis`.`t_expenses`
    JOIN `petits_comptes_entre_amis`.`t_expense_membership`
      ON ((`petits_comptes_entre_amis`.`t_expenses`.`transaction_id` =
           `petits_comptes_entre_amis`.`t_expense_membership`.`transaction_id`))) JOIN
    `petits_comptes_entre_amis`.`t_group_membership`
      ON ((`petits_comptes_entre_amis`.`t_expense_membership`.`username` =
           `petits_comptes_entre_amis`.`t_group_membership`.`username`)))
  WHERE `petits_comptes_entre_amis`.`t_expenses`.`event_id`
  GROUP BY `petits_comptes_entre_amis`.`t_expenses`.`transaction_id`;

