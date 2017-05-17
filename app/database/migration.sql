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
	link text not null,
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
	rank numeric(11,2),
	foreign key(store_id) references store(id),
	foreign key(like_id) references people_like(id),
	foreign key(people_id) references people(id),
	foreign key(status_id) references status(id)	
);

CREATE TABLE purshase_with(
	id serial not null primary key,
	people_id bigint not null,
	status_id int not null,
	purshase_id int not null,
	product_link text not null,
	price numeric(11,2) not null,
	receipt TEXT,
	fl_deposit_done boolean default false,
	fl_deposit_received boolean default false,
	rank numeric(11,2),
	foreign key(people_id) references people(id),
	foreign key(status_id) references status(id),
	foreign key(purshase_id) references purshase(id)
);

CREATE TABLE config(
	key varchar(100) not null primary key,
	content text not null
);

#status
INSERT INTO status values (1, 'Aguardando participantes');
INSERT INTO status values (2, 'Aguardando deposito(s)');
INSERT INTO status values (3, 'Processo de compra na loja');
INSERT INTO status values (4, 'Produtos a caminho (Loja)');
INSERT INTO status values (5, 'Entregando produtos');
INSERT INTO status values (6, 'Encerrada');
INSERT INTO status values (7, 'Aguardando entrega do produto');
INSERT INTO status values (8, 'Produto entregado');
INSERT INTO status values (9, 'Cancelada');

# Lojas
INSERT INTO store values (1, 'Submarino', now());

# Config
INSERT INTO config values ('STATUS_WAITING_PEOPLE', '1');
INSERT INTO config values ('STATUS_CANCELED', '9');
COMMIT;