create table t_currencies
(
  currency_code     VARCHAR(3)   NOT NULL
    PRIMARY KEY,
  rounding_multiple DOUBLE       NOT NULL,
  full_name         VARCHAR(256) NOT NULL,
  CONSTRAINT t_currencies_currency_code_uindex
  UNIQUE (currency_code)
)
;

create table t_events
(
  event_id          INT AUTO_INCREMENT
    PRIMARY KEY,
  event_name        VARCHAR(256) NOT NULL,
  event_description TEXT         NULL,
  currency_code     VARCHAR(3)   NOT NULL,
  CONSTRAINT t_events_event_id_uindex
  UNIQUE (event_id),
  CONSTRAINT t_events_t_currencies_currency_code_fk
  FOREIGN KEY (currency_code) REFERENCES t_currencies (currency_code)
)
;

create index t_events_t_currencies_currency_code_fk
  ON t_events (currency_code)
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
  transaction_id INT AUTO_INCREMENT
    PRIMARY KEY,
  title          VARCHAR(256) NOT NULL,
  description    TEXT         NULL,
  amount         DOUBLE       NOT NULL,
  date           DATE         NOT NULL,
  buyer_username VARCHAR(256) NOT NULL,
  event_id       INT          NOT NULL,
  CONSTRAINT t_expenses_transaction_id_uindex
  UNIQUE (transaction_id),
  CONSTRAINT t_expenses_t_events_event_id_fk
  FOREIGN KEY (event_id) REFERENCES t_events (event_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
;

create index t_expenses_t_events_event_id_fk
  ON t_expenses (event_id)
;

create index t_expenses_t_users_username_fk
  ON t_expenses (buyer_username)
;

alter table t_expense_membership
  ADD CONSTRAINT t_expense_membership_t_expenses_transaction_id_fk
FOREIGN KEY (transaction_id) REFERENCES t_expenses (transaction_id)
  ON UPDATE CASCADE
  ON DELETE CASCADE
;

create table t_group_membership
(
  username    VARCHAR(256)    NOT NULL,
  event_id    INT             NOT NULL,
  coefficient INT DEFAULT '1' NOT NULL,
  PRIMARY KEY (username, event_id),
  CONSTRAINT t_group_membership_t_events_event_id_fk
  FOREIGN KEY (event_id) REFERENCES t_events (event_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
;

create index t_group_membership_t_events_event_id_fk
  ON t_group_membership (event_id);

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
  username   VARCHAR(256) NOT NULL
    PRIMARY KEY,
  first_name VARCHAR(256) NULL,
  last_name  VARCHAR(256) NULL,
  email      VARCHAR(256) NULL,
  password   VARCHAR(256) NULL,
  CONSTRAINT t_users_username_uindex
  UNIQUE (username)
)
;

alter table t_expense_membership
  ADD CONSTRAINT t_expense_membership_t_users_username_fk
FOREIGN KEY (username) REFERENCES t_users (username)
  ON UPDATE CASCADE
;

alter table t_expenses
  ADD CONSTRAINT t_expenses_t_users_username_fk
FOREIGN KEY (buyer_username) REFERENCES t_users (username)
  ON UPDATE CASCADE
;

alter table t_group_membership
  ADD CONSTRAINT t_group_membership_t_users_username_fk
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
    `petits_comptes_entre_amis`.`t_group_membership` ON ((
      (`petits_comptes_entre_amis`.`t_expense_membership`.`username` =
       `petits_comptes_entre_amis`.`t_group_membership`.`username`) AND
      (`petits_comptes_entre_amis`.`t_expenses`.`event_id` =
       `petits_comptes_entre_amis`.`t_group_membership`.`event_id`))))
  GROUP BY `petits_comptes_entre_amis`.`t_expenses`.`transaction_id`;

