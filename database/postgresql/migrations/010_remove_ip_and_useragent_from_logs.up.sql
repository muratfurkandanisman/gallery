-- Remove unnecessary ip_address and user_agent columns from user_activity_logs
-- Details JSON will contain all necessary information

ALTER TABLE user_activity_logs DROP COLUMN user_agent;
ALTER TABLE user_activity_logs DROP COLUMN ip_address;
