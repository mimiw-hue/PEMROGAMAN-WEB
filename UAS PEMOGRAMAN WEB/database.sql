-- SQL Script to Create Database and Products Table
CREATE DATABASE IF NOT EXISTS db_erp;
USE db_erp;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(20) UNIQUE NOT NULL,
    nama_produk VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0
);
