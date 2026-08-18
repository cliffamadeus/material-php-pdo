-- ==========================================
-- USERS TABLE
-- ==========================================

CREATE TABLE IF NOT EXISTS users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_email VARCHAR(255) NOT NULL,
    user_password VARCHAR(255) NOT NULL,

    user_role ENUM('admin', 'manager', 'user')
        NOT NULL DEFAULT 'user',

    user_is_verified TINYINT(1)
        NOT NULL DEFAULT 0,

    user_created_at TIMESTAMP
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    user_updated_at TIMESTAMP
        NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_users_email (user_email),
    INDEX idx_users_role (user_role),
    INDEX idx_users_verified (user_is_verified)
    
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- ==========================================
-- ACTIVITY LOGS TABLE
-- ==========================================

CREATE TABLE IF NOT EXISTS activity_logs (
    activity_log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- NULL when the user cannot be identified
    -- Example: failed login attempt
    user_id INT UNSIGNED NULL,

    -- Preserve email even if the user is later deleted
    user_email VARCHAR(255) NULL,

    activity_log_action VARCHAR(50)
        NOT NULL,

    activity_log_status ENUM('success', 'failed')
        NOT NULL DEFAULT 'success',

    activity_log_ip_address VARCHAR(45) NULL,

    activity_log_user_agent VARCHAR(255) NULL,

    activity_log_created_at TIMESTAMP
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Indexes
    INDEX idx_activity_user_id (user_id),
    INDEX idx_activity_email (user_email),
    INDEX idx_activity_action (activity_log_action),
    INDEX idx_activity_status (activity_log_status),
    INDEX idx_activity_created_at (activity_log_created_at),

    -- Composite indexes for common dashboard queries
    INDEX idx_activity_user_date (
        user_id,
        activity_log_created_at
    ),

    INDEX idx_activity_action_date (
        activity_log_action,
        activity_log_created_at
    ),

    -- Relationship
    CONSTRAINT fk_activity_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;