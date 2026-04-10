-- Update existing car descriptions to use the new gallery name

UPDATE cars
SET description = REPLACE(description, 'Velocity Elite', 'FEE CARS')
WHERE description LIKE '%Velocity Elite%';
