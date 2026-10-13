-- Create database
CREATE DATABASE IF NOT EXISTS file_manager_v4;
USE file_manager_v4;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    storage_used BIGINT DEFAULT 0,
    storage_limit BIGINT DEFAULT 10737418240,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- Storage clusters (depends on users)
CREATE TABLE IF NOT EXISTS storage_clusters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL,
    is_default TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_cluster (user_id, slug),
    INDEX idx_user (user_id)
);

-- Folders (depends on users and clusters)
CREATE TABLE IF NOT EXISTS folders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    cluster_id INT NOT NULL,
    parent_id INT NULL,
    name VARCHAR(255) NOT NULL,
    is_starred TINYINT(1) DEFAULT 0,
    last_opened TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (cluster_id) REFERENCES storage_clusters(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES folders(id) ON DELETE CASCADE,
    INDEX idx_user_cluster (user_id, cluster_id),
    INDEX idx_parent (parent_id),
    INDEX idx_deleted (deleted_at),
    INDEX idx_starred (user_id, is_starred)
);

-- Files (depends on users, clusters, folders)
CREATE TABLE IF NOT EXISTS files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    cluster_id INT NOT NULL,
    folder_id INT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT NOT NULL,
    file_type VARCHAR(100),
    file_category VARCHAR(50),
    is_starred TINYINT(1) DEFAULT 0,
    last_opened TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (cluster_id) REFERENCES storage_clusters(id) ON DELETE CASCADE,
    FOREIGN KEY (folder_id) REFERENCES folders(id) ON DELETE CASCADE,
    INDEX idx_user_cluster (user_id, cluster_id),
    INDEX idx_folder (folder_id),
    INDEX idx_deleted (deleted_at),
    INDEX idx_starred (user_id, is_starred),
    INDEX idx_file_category (file_category)
);

-- Shared items (depends on users)
CREATE TABLE IF NOT EXISTS shared_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    item_type ENUM('file', 'folder') NOT NULL,
    item_id INT NOT NULL,
    shared_with_user_id INT NOT NULL,
    permissions ENUM('view', 'download', 'edit', 'share', 'full') DEFAULT 'view',
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_with_user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_share (item_type, item_id, shared_with_user_id),
    INDEX idx_shared_with (shared_with_user_id),
    INDEX idx_expires (expires_at)
);

-- User starred items (depends on users)
CREATE TABLE IF NOT EXISTS user_starred_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    item_type ENUM('file', 'folder') NOT NULL,
    item_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_star (user_id, item_type, item_id),
    INDEX idx_user (user_id)
);

-- Sync sessions (depends on users)
CREATE TABLE IF NOT EXISTS sync_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_id VARCHAR(64) UNIQUE NOT NULL,
    status ENUM('idle', 'running', 'completed', 'failed') DEFAULT 'idle',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    files_synced INT DEFAULT 0,
    total_files INT DEFAULT 0,
    last_sync_at TIMESTAMP NULL,
    error_message TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status),
    INDEX idx_session (session_id)
);

-- Sync queue (depends on users)
CREATE TABLE IF NOT EXISTS sync_queue (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_id VARCHAR(64) NOT NULL,
    item_type ENUM('file', 'folder') NOT NULL,
    item_id INT NOT NULL,
    action ENUM('create', 'update', 'delete', 'skip') NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_status (session_id, status),
    INDEX idx_user (user_id)
);