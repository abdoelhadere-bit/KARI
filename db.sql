show DATABASES

DROP DATABASE IF EXISTS kari;
CREATE DATABASE kari CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kari;

-- USERS
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('traveler','host','admin') NOT NULL DEFAULT 'traveler',
  status ENUM('active','disabled') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- RENTALS 
CREATE TABLE rentals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  host_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  city VARCHAR(100) NOT NULL,
  address VARCHAR(200),
  price_per_night DECIMAL(10,2) NOT NULL,
  max_guests INT NOT NULL,
  status ENUM('active','disabled') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_rentals_host
    FOREIGN KEY (host_id) REFERENCES users(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

ALTER TABLE rentals
ADD COLUMN image VARCHAR(255) NOT NULL AFTER address;


-- AVAILABILITY 
CREATE TABLE availability (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rental_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  CONSTRAINT fk_availability_rental
    FOREIGN KEY (rental_id) REFERENCES rentals(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT chk_availability_dates CHECK (end_date > start_date)
);

-- RESERVATIONS
CREATE TABLE reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rental_id INT NOT NULL,
  traveler_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  guests INT NOT NULL,
  total_price DECIMAL(10,2) NOT NULL DEFAULT 0,
  status ENUM('booked','cancelled') NOT NULL DEFAULT 'booked',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_reservations_rental
    FOREIGN KEY (rental_id) REFERENCES rentals(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT fk_reservations_traveler
    FOREIGN KEY (traveler_id) REFERENCES users(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT chk_reservation_dates CHECK (end_date > start_date)
);

DESCRIBE favorites

-- FAVORITES
CREATE TABLE favorites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  rental_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_favorites_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_favorites_rental
    FOREIGN KEY (rental_id) REFERENCES rentals(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT uq_favorite UNIQUE (user_id, rental_id)
);

-- REVIEWS
CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rental_id INT NOT NULL,
  user_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_reviews_rental
    FOREIGN KEY (rental_id) REFERENCES rentals(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_reviews_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5)
);

INSERT INTO users (name,email,password_hash,role) 
VALUES ('Host One','host@test.com','$2y$10$abcdefghijklmnopqrstuv','host');

INSERT INTO rentals (host_id,title,description,city,address,price_per_night,max_guests,status)
VALUES (1,'Appartement centre','Proche de tout','Casablanca','Centre ville',350,3,'active');

INSERT INTO reservations (rental_id, traveler_id, start_date, end_date, guests, total_price, status)
VALUES (1, 2, '2026-01-10', '2026-01-12', 2, 700, 'booked');

use kari

select * from users

delete from users where id=1
