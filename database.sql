-- Database: portfolio_db
-- Create database
CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

-- Projects table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    project_type ENUM('image', 'html', 'pdf', 'powerbi', 'github', 'dashboard') NOT NULL,
    file_path VARCHAR(500),
    external_link VARCHAR(500),
    thumbnail VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- About section
CREATE TABLE IF NOT EXISTS about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Skills table
CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) NOT NULL,
    proficiency INT DEFAULT 0,
    category VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default about content
INSERT INTO about_content (section_name, content) VALUES
('bio', 'I am a passionate Data Analyst with expertise in transforming raw data into actionable insights. With strong analytical skills and proficiency in various data analysis tools, I help organizations make data-driven decisions.'),
('email', 'your.email@example.com'),
('phone', '+1234567890'),
('location', 'Your City, Country');

-- Insert sample skills
INSERT INTO skills (skill_name, proficiency, category) VALUES
('Python', 90, 'Programming'),
('SQL', 85, 'Database'),
('Power BI', 88, 'Visualization'),
('Excel', 92, 'Tools'),
('R', 75, 'Programming'),
('Tableau', 80, 'Visualization');

-- Sample projects
INSERT INTO projects (title, description, project_type, external_link, display_order) VALUES
('Sales Dashboard', 'Interactive Power BI dashboard analyzing sales trends', 'powerbi', 'https://example.com/powerbi', 1),
('GitHub Portfolio', 'Collection of data analysis projects', 'github', 'https://github.com/yourusername', 2);