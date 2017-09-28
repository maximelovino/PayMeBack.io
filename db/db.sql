create table t_currencies
(
	currency_code varchar(3) not null
		primary key,
	rounding_multiple double not null,
	full_name varchar(256) not null,
	constraint t_currencies_currency_code_uindex
		unique (currency_code)
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
		unique (event_id),
	constraint t_events_t_currencies_currency_code_fk
		foreign key (currency_code) references t_currencies (currency_code)
)
;

create index t_events_t_currencies_currency_code_fk
	on t_events (currency_code)
;

create table t_expense_membership
(
	transaction_id int not null,
	username varchar(256) not null
		primary key
)
;

create index t_expense_membership_t_expenses_transaction_id_fk
	on t_expense_membership (transaction_id)
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
		unique (transaction_id),
	constraint t_expenses_t_events_event_id_fk
		foreign key (event_id) references t_events (event_id)
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
		foreign key (transaction_id) references t_expenses (transaction_id)
;

create table t_group_membership
(
	username varchar(256) not null,
	event_id int not null,
	coefficient int default '1' not null,
	primary key (username, event_id),
	constraint t_group_membership_t_events_event_id_fk
		foreign key (event_id) references t_events (event_id)
)
;

create index t_group_membership_t_events_event_id_fk
	on t_group_membership (event_id)
;

create table t_users
(
	username varchar(256) not null
		primary key,
	first_name varchar(256) null,
	last_name varchar(256) null,
	email varchar(256) null,
	password varchar(256) null,
	constraint t_users_username_uindex
		unique (username)
)
;

alter table t_expense_membership
	add constraint t_expense_membership_t_users_username_fk
		foreign key (username) references t_users (username)
;

alter table t_expenses
	add constraint t_expenses_t_users_username_fk
		foreign key (buyer_username) references t_users (username)
;

alter table t_group_membership
	add constraint t_group_membership_t_users_username_fk
		foreign key (username) references t_users (username)
;

