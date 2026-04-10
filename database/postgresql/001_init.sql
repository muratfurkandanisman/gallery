-- Legacy one-shot SQL script.
-- Preferred approach is versioned migrations under:
-- database/postgresql/migrations/*.up.sql
-- and run with scripts/migrate.php

-- PostgreSQL initial migration for gallery app

DROP TABLE IF EXISTS favorites CASCADE;
DROP TABLE IF EXISTS inquiries CASCADE;
DROP TABLE IF EXISTS car_images CASCADE;
DROP TABLE IF EXISTS cars CASCADE;
DROP TABLE IF EXISTS users CASCADE;

CREATE TABLE users (
  user_id BIGSERIAL PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'USER' CHECK (role IN ('USER', 'ADMIN')),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login_at TIMESTAMP NULL,
  is_active SMALLINT NOT NULL DEFAULT 1 CHECK (is_active IN (0, 1))
);

CREATE TABLE cars (
  car_id BIGSERIAL PRIMARY KEY,
  brand VARCHAR(80) NOT NULL,
  model VARCHAR(80) NOT NULL,
  year INTEGER NOT NULL,
  price NUMERIC(12,2) NOT NULL,
  mileage INTEGER NOT NULL,
  fuel_type VARCHAR(40),
  gear_type VARCHAR(40),
  color VARCHAR(40),
  description TEXT,
  status VARCHAR(20) NOT NULL DEFAULT 'AVAILABLE' CHECK (status IN ('AVAILABLE', 'SOLD')),
  created_by BIGINT NOT NULL REFERENCES users(user_id),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  sold_at TIMESTAMP NULL,
  is_deleted SMALLINT NOT NULL DEFAULT 0 CHECK (is_deleted IN (0, 1))
);

CREATE TABLE car_images (
  image_id BIGSERIAL PRIMARY KEY,
  car_id BIGINT NOT NULL REFERENCES cars(car_id) ON DELETE CASCADE,
  image_path VARCHAR(400) NOT NULL,
  sort_order INTEGER NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE favorites (
  user_id BIGINT NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
  car_id BIGINT NOT NULL REFERENCES cars(car_id) ON DELETE CASCADE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, car_id)
);

CREATE TABLE inquiries (
  inquiry_id BIGSERIAL PRIMARY KEY,
  user_id BIGINT NOT NULL REFERENCES users(user_id),
  car_id BIGINT NOT NULL REFERENCES cars(car_id),
  message TEXT NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'NEW' CHECK (status IN ('NEW', 'IN_PROGRESS', 'CLOSED')),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  handled_by BIGINT NULL REFERENCES users(user_id),
  handled_at TIMESTAMP NULL
);

CREATE INDEX idx_cars_status ON cars(status);
CREATE INDEX idx_cars_brand_model ON cars(brand, model);
CREATE INDEX idx_cars_price ON cars(price);
CREATE INDEX idx_cars_year ON cars(year);
CREATE INDEX idx_inquiries_status ON inquiries(status);
