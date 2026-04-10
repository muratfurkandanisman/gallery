-- Seed: FEE CARS 15-car inventory

DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM users WHERE LOWER(email) = LOWER('admin@velocityelite.local')) THEN
    INSERT INTO users (full_name, email, password_hash, role, is_active)
    VALUES (
      'Velocity Admin',
      'admin@velocityelite.local',
      '$2y$10$0K8nA5kWwOlceA9jB0.7juw9hI.4YBa6XecQK/1Jf9eS0J6lrjuMa',
      'ADMIN',
      1
    );
  END IF;
END $$;

-- 1
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Porsche', '911 Turbo S', 2024, 245000, 1200, 'Benzin', 'Otomatik (PDK)', 'Gumus (GT Silver)',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Porsche' AND model='911 Turbo S' AND year=2024 AND price=245000);

-- 2
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Ferrari', 'F8 Tributo', 2023, 380000, 850, 'Benzin', 'Otomatik (DCT)', 'Siyah (Nero Daytona)',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Ferrari' AND model='F8 Tributo' AND year=2023 AND price=380000);

-- 3
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Lamborghini', 'Huracan STO', 2024, 420000, 500, 'Benzin', 'Otomatik (LDF)', 'Beyaz (Bianco Monocerus)',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Lamborghini' AND model='Huracan STO' AND year=2024 AND price=420000);

-- 4
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Aston Martin', 'DBS Volante', 2023, 350000, 2100, 'Benzin', 'Otomatik (ZF)', 'Safir Mavi',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Aston Martin' AND model='DBS Volante' AND year=2023 AND price=350000);

-- 5
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'McLaren', '720S', 2022, 310000, 4500, 'Benzin', 'Otomatik (SSG)', 'Metalik Gri',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='McLaren' AND model='720S' AND year=2022 AND price=310000);

-- 6
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Audi', 'R8 V10 Performance', 2023, 220000, 3200, 'Benzin', 'Otomatik (S-Tronic)', 'Tango Kirmizi',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Audi' AND model='R8 V10 Performance' AND year=2023 AND price=220000);

-- 7
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'BMW', 'M8 Competition', 2024, 185000, 1500, 'Benzin', 'Otomatik (M Steptronic)', 'Gece Mavisi',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='BMW' AND model='M8 Competition' AND year=2024 AND price=185000);

-- 8
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Mercedes-AMG', 'GT Black Series', 2022, 450000, 800, 'Benzin', 'Otomatik (AMG DCT)', 'Saten Siyah',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Mercedes-AMG' AND model='GT Black Series' AND year=2022 AND price=450000);

-- 9
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Bentley', 'Continental GT Speed', 2024, 340000, 1100, 'Benzin', 'Otomatik', 'Yaris Yesili',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Bentley' AND model='Continental GT Speed' AND year=2024 AND price=340000);

-- 10
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Chevrolet', 'Corvette Z06 (C8)', 2024, 165000, 2400, 'Benzin', 'Otomatik (DCT)', 'Parlak Sari',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Chevrolet' AND model='Corvette Z06 (C8)' AND year=2024 AND price=165000);

-- 11
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Nissan', 'GT-R Nismo', 2024, 215000, 1800, 'Benzin', 'Otomatik (GR6)', 'Inci Beyazi',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Nissan' AND model='GT-R Nismo' AND year=2024 AND price=215000);

-- 12
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Ferrari', 'Roma', 2023, 290000, 2700, 'Benzin', 'Otomatik (DCT)', 'Rosso Corsa (Kirmizi)',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Ferrari' AND model='Roma' AND year=2023 AND price=290000);

-- 13
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Mercedes-Benz', 'G63 AMG', 2024, 280000, 3500, 'Benzin', 'Otomatik (9G-Tronic)', 'Metalik Gumus',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Mercedes-Benz' AND model='G63 AMG' AND year=2024 AND price=280000);

-- 14
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Range Rover', 'SV Autobiography', 2024, 260000, 4200, 'Hibrit', 'Otomatik', 'Koyu Yesil',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Range Rover' AND model='SV Autobiography' AND year=2024 AND price=260000);

-- 15
INSERT INTO cars (brand, model, year, price, mileage, fuel_type, gear_type, color, description, status, created_by, is_deleted)
SELECT 'Porsche', 'Taycan Turbo S', 2024, 210000, 5600, 'Elektrik', 'Otomatik', 'Safir Mavi',
       'FEE CARS kalite standartlarinda ekspertiz raporlu ve satisa hazir.', 'AVAILABLE',
       (SELECT user_id FROM users WHERE LOWER(email)=LOWER('admin@velocityelite.local') FETCH FIRST 1 ROWS ONLY), 0
WHERE NOT EXISTS (SELECT 1 FROM cars WHERE brand='Porsche' AND model='Taycan Turbo S' AND year=2024 AND price=210000);

-- Sample placeholder images (zip images not available in workspace at seed time)
INSERT INTO car_images (car_id, image_path, sort_order)
SELECT c.car_id, v.image_path, 1
FROM (
  VALUES
    ('Porsche','911 Turbo S',2024,'https://picsum.photos/seed/velocity-1/1280/860'),
    ('Ferrari','F8 Tributo',2023,'https://picsum.photos/seed/velocity-2/1280/860'),
    ('Lamborghini','Huracan STO',2024,'https://picsum.photos/seed/velocity-3/1280/860'),
    ('Aston Martin','DBS Volante',2023,'https://picsum.photos/seed/velocity-4/1280/860'),
    ('McLaren','720S',2022,'https://picsum.photos/seed/velocity-5/1280/860'),
    ('Audi','R8 V10 Performance',2023,'https://picsum.photos/seed/velocity-6/1280/860'),
    ('BMW','M8 Competition',2024,'https://picsum.photos/seed/velocity-7/1280/860'),
    ('Mercedes-AMG','GT Black Series',2022,'https://picsum.photos/seed/velocity-8/1280/860'),
    ('Bentley','Continental GT Speed',2024,'https://picsum.photos/seed/velocity-9/1280/860'),
    ('Chevrolet','Corvette Z06 (C8)',2024,'https://picsum.photos/seed/velocity-10/1280/860'),
    ('Nissan','GT-R Nismo',2024,'https://picsum.photos/seed/velocity-11/1280/860'),
    ('Ferrari','Roma',2023,'https://picsum.photos/seed/velocity-12/1280/860'),
    ('Mercedes-Benz','G63 AMG',2024,'https://picsum.photos/seed/velocity-13/1280/860'),
    ('Range Rover','SV Autobiography',2024,'https://picsum.photos/seed/velocity-14/1280/860'),
    ('Porsche','Taycan Turbo S',2024,'https://picsum.photos/seed/velocity-15/1280/860')
) AS v(brand, model, year, image_path)
JOIN cars c ON c.brand = v.brand AND c.model = v.model AND c.year = v.year
WHERE NOT EXISTS (
  SELECT 1 FROM car_images ci WHERE ci.car_id = c.car_id AND ci.sort_order = 1
);

