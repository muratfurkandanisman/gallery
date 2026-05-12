-- Create user_activity_logs table for tracking user login/logout activities
CREATE TABLE IF NOT EXISTS user_activity_logs (
    log_id BIGSERIAL PRIMARY KEY,
    user_id BIGINT,
    action VARCHAR(30) NOT NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT fk_logs_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    CONSTRAINT chk_logs_action CHECK (action IN ('LOGIN', 'LOGOUT', 'FAILED_LOGIN', 'ACTION'))
);

-- Create indexes for better query performance
CREATE INDEX idx_logs_user_id ON user_activity_logs(user_id);
CREATE INDEX idx_logs_created_at ON user_activity_logs(created_at DESC);
CREATE INDEX idx_logs_action ON user_activity_logs(action);
CREATE INDEX idx_logs_user_action_date ON user_activity_logs(user_id, action, created_at DESC);

-- Add comment for documentation
COMMENT ON TABLE user_activity_logs IS 'Tracks user login/logout and important actions for audit trail';
COMMENT ON COLUMN user_activity_logs.user_id IS 'User who performed the action (NULL for failed logins)';
COMMENT ON COLUMN user_activity_logs.action IS 'Type of action: LOGIN, LOGOUT, FAILED_LOGIN, ACTION';
COMMENT ON COLUMN user_activity_logs.ip_address IS 'IP address from which the action was performed';
COMMENT ON COLUMN user_activity_logs.user_agent IS 'Browser/client information';
COMMENT ON COLUMN user_activity_logs.details IS 'Additional details about the action in JSON format';
