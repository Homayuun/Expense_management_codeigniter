<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_tables extends CI_Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(100) NOT NULL UNIQUE,
                email VARCHAR(150) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(50) NOT NULL DEFAULT 'user',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                name VARCHAR(150) NULL,
                image_url VARCHAR(255) NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_profiles_user FOREIGN KEY (user_id) REFERENCES users(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            )
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                profile_id INT NOT NULL,
                name VARCHAR(100) NOT NULL,
                type ENUM('income','expense') NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_categories_profile FOREIGN KEY (profile_id) REFERENCES profiles(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            )
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                profile_id INT NOT NULL,
                category_id INT NOT NULL,
                title VARCHAR(150) NULL,
                amount DECIMAL(12,2) NOT NULL,
                type ENUM('income','expense') NOT NULL,
                transaction_date DATE NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_transactions_profile FOREIGN KEY (profile_id) REFERENCES profiles(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_transactions_category FOREIGN KEY (category_id) REFERENCES categories(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            )
        ");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS transactions");
        $this->db->query("DROP TABLE IF EXISTS categories");
        $this->db->query("DROP TABLE IF EXISTS profiles");
        $this->db->query("DROP TABLE IF EXISTS users");
    }
}