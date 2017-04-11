BEGIN;

DROP TABLE purshase_with;
DROP TABLE purshase;
DROP TABLE status;
DROP TABLE store;
DROP TABLE people_like;
DROP TABLE people;

CREATE TABLE people(
	id bigint not null primary key,
	name text not null,
	maps_location TEXT
);

CREATE TABLE people_like(
	id bigint not null primary key,
	people_id bigint not null,
	page_name text not null,
	category text not null,
	page_picture text not null,
	fl_list boolean default true,
	foreign key(people_id) references people(id)	
);

CREATE TABLE store(
	id serial not null primary key,
	description text not null,
	date_creation date default date(now())
);

CREATE TABLE status(
	id serial not null primary key,
	description text not null
);

CREATE TABLE purshase(
	id serial not null primary key,
	store_id int,
	like_id bigint,
	people_id bigint not null,
	status_id int not null,
	min_people int not null,
	max_people int not null,
	date_until date not null,
	deposite_information TEXT not null,
	track_link TEXT,
	maps_address TEXT,
	foreign key(store_id) references store(id),
	foreign key(like_id) references people_like(id),
	foreign key(people_id) references people(id),
	foreign key(status_id) references status(id)	
);

CREATE TABLE purshase_with(
	id serial not null primary key,
	people_id bigint not null,
	purshase_id int not null,
	product_link text not null,
	price numeric(11,2) not null,
	receipt TEXT,
	fl_deposit_received boolean default false,
	foreign key(people_id) references people(id),
	foreign key(purshase_id) references purshase(id)
);

INSERT INTO status values (1, 'Em Andamento');
INSERT INTO store values (1, 'Submarino', now());
COMMIT;