CREATE DATABASE IF NOT EXISTS api_rest_recaudo_en_linea;
USE api_rest_recaudo_en_linea;

CREATE TABLE users (
    id int(255) auto_increment not null,
    name varchar(50) not null,
    tipe_doc varchar(50) not null,
    number_doc varchar(255) not null,
    email varchar(255) not null,
    password varchar(255) not null,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_users PRIMARY KEY(id)
) ENGINE=InnoDb;

CREATE TABLE invoices(
    id int(255) auto_increment not null,
    user_id int(255) not null, 
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_invoices PRIMARY KEY(id),
    CONSTRAINT fk_invoice_user FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;