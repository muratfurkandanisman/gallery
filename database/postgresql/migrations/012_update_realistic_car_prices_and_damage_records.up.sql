-- Make vehicle prices and damage records more realistic for the current catalog.

UPDATE cars
SET price = CASE car_id
  WHEN 1 THEN 15900000.00
  WHEN 2 THEN 38500000.00
  WHEN 3 THEN 42000000.00
  WHEN 4 THEN 28500000.00
  WHEN 5 THEN 27000000.00
  WHEN 6 THEN 18900000.00
  WHEN 8 THEN 36000000.00
  WHEN 9 THEN 24500000.00
  WHEN 10 THEN 14200000.00
  WHEN 11 THEN 16800000.00
  WHEN 12 THEN 30500000.00
  WHEN 13 THEN 22500000.00
  WHEN 14 THEN 19800000.00
  WHEN 15 THEN 12400000.00
  ELSE price
END
WHERE car_id IN (1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12, 13, 14, 15);

UPDATE cars
SET is_deleted = 1,
    status = 'SOLD',
    updated_at = CURRENT_TIMESTAMP
WHERE car_id IN (16, 17, 18);

DELETE FROM damage_records;

INSERT INTO damage_records (car_id, damage_area, damage_type, damage_level, estimated_cost, description, recorded_at)
VALUES
  (1, 'Front Bumper', 'Stone Chip', 'MINOR', 185000.00, 'Yuksek hiz kullanimina bagli hafif tas izleri.', CURRENT_TIMESTAMP),
  (2, 'Rear Bumper', 'Paint Repair', 'MINOR', 420000.00, 'Park manevrasinda olusan lokal boya islemi.', CURRENT_TIMESTAMP),
  (3, 'Left Door', 'Dent', 'MODERATE', 980000.00, 'Kapida orta seviyede ezik ve boya ihtiyaci.', CURRENT_TIMESTAMP),
  (4, 'Right Fender', 'Scratch', 'MINOR', 360000.00, 'Yuzeysel cizik ve polisaj ihtiyaci.', CURRENT_TIMESTAMP),
  (5, 'Front Splitter', 'Underbody Scrape', 'MINOR', 310000.00, 'Alcak giris cikista alt bolgede surtme izi.', CURRENT_TIMESTAMP),
  (6, 'Rear Bumper', 'Impact Dent', 'MODERATE', 540000.00, 'Geri manevra kaynakli arka tampon deformasyonu.', CURRENT_TIMESTAMP),
  (8, 'Left Rear Quarter', 'Track Wear', 'MODERATE', 760000.00, 'Pist kullanimi kaynakli lokal kaporta ve boya ihtiyaci.', CURRENT_TIMESTAMP),
  (10, 'Front Bumper', 'Curb Scuff', 'MINOR', 290000.00, 'Sehir ici kullanima bagli tampon altinda hafif hasar.', CURRENT_TIMESTAMP);
