-- Move all image paths from car_images into cars.images JSON text column
-- and remove the car_images table.

ALTER TABLE cars
ADD COLUMN IF NOT EXISTS images TEXT;

ALTER TABLE cars
ALTER COLUMN images SET DEFAULT '[]';

WITH grouped AS (
  SELECT car_id, COALESCE(json_agg(image_path ORDER BY sort_order)::text, '[]') AS images_json
  FROM car_images
  GROUP BY car_id
)
UPDATE cars c
SET images = g.images_json
FROM grouped g
WHERE c.car_id = g.car_id;

UPDATE cars
SET images = '[]'
WHERE images IS NULL OR btrim(images) = '';

ALTER TABLE cars
ALTER COLUMN images SET NOT NULL;

DROP TABLE IF EXISTS car_images;
