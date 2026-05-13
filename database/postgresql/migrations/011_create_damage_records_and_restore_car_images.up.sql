-- Re-create normalized car_images table and backfill data from cars.images JSON
-- Then create damage_records and seed approximately half of the cars.

CREATE TABLE IF NOT EXISTS car_images (
  image_id BIGSERIAL PRIMARY KEY,
  car_id BIGINT NOT NULL REFERENCES cars(car_id) ON DELETE CASCADE,
  image_path VARCHAR(400) NOT NULL,
  sort_order INTEGER NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_car_images_car_id ON car_images(car_id);
CREATE INDEX IF NOT EXISTS idx_car_images_sort_order ON car_images(car_id, sort_order);
CREATE UNIQUE INDEX IF NOT EXISTS uq_car_images_car_path ON car_images(car_id, image_path);

WITH expanded AS (
  SELECT
    c.car_id,
    NULLIF(btrim(img.image_path), '') AS image_path,
    img.sort_order::INTEGER AS sort_order
  FROM cars c
  LEFT JOIN LATERAL (
    SELECT value AS image_path, ordinality AS sort_order
    FROM jsonb_array_elements_text(
      CASE
        WHEN c.images IS NULL OR btrim(c.images) = '' THEN '[]'::jsonb
        ELSE c.images::jsonb
      END
    ) WITH ORDINALITY
  ) AS img ON TRUE
)
INSERT INTO car_images (car_id, image_path, sort_order)
SELECT e.car_id, e.image_path, e.sort_order
FROM expanded e
WHERE e.image_path IS NOT NULL
  AND NOT EXISTS (
    SELECT 1
    FROM car_images ci
    WHERE ci.car_id = e.car_id
      AND ci.image_path = e.image_path
  );

CREATE TABLE IF NOT EXISTS damage_records (
  damage_id BIGSERIAL PRIMARY KEY,
  car_id BIGINT NOT NULL REFERENCES cars(car_id) ON DELETE CASCADE,
  damage_area VARCHAR(80) NOT NULL,
  damage_type VARCHAR(80) NOT NULL,
  damage_level VARCHAR(20) NOT NULL CHECK (damage_level IN ('MINOR', 'MODERATE', 'MAJOR')),
  estimated_cost NUMERIC(12,2),
  description TEXT,
  recorded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_damage_records_car_id ON damage_records(car_id);
CREATE INDEX IF NOT EXISTS idx_damage_records_level ON damage_records(damage_level);

WITH ranked_cars AS (
  SELECT car_id, ROW_NUMBER() OVER (ORDER BY car_id) AS rn
  FROM cars
  WHERE is_deleted = 0
),
seed_targets AS (
  SELECT car_id
  FROM ranked_cars
  WHERE rn % 2 = 0
),
seed_payload AS (
  SELECT
    st.car_id,
    CASE (st.car_id % 4)
      WHEN 0 THEN 'Front Bumper'
      WHEN 1 THEN 'Rear Bumper'
      WHEN 2 THEN 'Left Door'
      ELSE 'Right Fender'
    END AS damage_area,
    CASE (st.car_id % 3)
      WHEN 0 THEN 'Scratch'
      WHEN 1 THEN 'Dent'
      ELSE 'Paint Wear'
    END AS damage_type,
    CASE (st.car_id % 5)
      WHEN 0 THEN 'MAJOR'
      WHEN 1 THEN 'MODERATE'
      ELSE 'MINOR'
    END AS damage_level,
    CASE (st.car_id % 5)
      WHEN 0 THEN 4500.00
      WHEN 1 THEN 2200.00
      ELSE 900.00
    END AS estimated_cost,
    NULL AS description
  FROM seed_targets st
)
INSERT INTO damage_records (car_id, damage_area, damage_type, damage_level, estimated_cost, description)
SELECT sp.car_id, sp.damage_area, sp.damage_type, sp.damage_level, sp.estimated_cost, sp.description
FROM seed_payload sp
WHERE NOT EXISTS (
  SELECT 1
  FROM damage_records dr
  WHERE dr.car_id = sp.car_id
);
