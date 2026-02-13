<?php
require_once 'config.php';
$conn = getDBConnection();

// Fetch about content
$about_query = "SELECT * FROM about_content";
$about_result = $conn->query($about_query);
$about_data = [];
while($row = $about_result->fetch_assoc()) {
    $about_data[$row['section_name']] = $row['content'];
}

// Fetch skills
$skills_query = "SELECT * FROM skills ORDER BY category, proficiency DESC";
$skills_result = $conn->query($skills_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me - Data Analyst Portfolio</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">
                <i class="fas fa-chart-line"></i>
                Data<span>Analyst</span>
            </a>
            
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php" class="active">About</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="admin/login.php">Admin</a></li>
            </ul>
            
            <div class="mobile-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" style="padding: 3rem 2rem;">
        <div class="hero-content">
            <h1>About Me</h1>
            <p class="subtitle">Passionate about turning data into insights</p>
        </div>
    </section>

    <!-- About Content Section -->
    <section class="container">
        <div class="about-grid">
            <div class="about-content">
                <h2>My Story</h2>
                <p><?php echo nl2br(htmlspecialchars($about_data['bio'] ?? 'Welcome to my portfolio. I am a dedicated data analyst.')); ?></p>
                
                <div style="margin-top: 2rem;">
                    <h3>What I Do</h3>
                    <p>I specialize in extracting meaningful insights from complex datasets. My expertise includes:</p>
                    <ul style="margin-left: 2rem; line-height: 2;">
                        <li>📊 Data Analysis & Statistical Modeling</li>
                        <li>📈 Business Intelligence & Dashboard Creation</li>
                        <li>🔍 Data Mining & Pattern Recognition</li>
                        <li>💡 Predictive Analytics & Forecasting</li>
                        <li>📉 Performance Metrics & KPI Tracking</li>
                        <li>🎯 Data Visualization & Storytelling</li>
                    </ul>
                </div>
                
                <div style="margin-top: 2rem;">
                    <h3>Contact Information</h3>
                    <p><i class="fas fa-envelope" style="color: var(--primary-orange); margin-right: 0.5rem;"></i> 
                       <strong>Email:</strong> <?php echo htmlspecialchars($about_data['email'] ?? 'email@example.com'); ?></p>
                    <p><i class="fas fa-phone" style="color: var(--primary-green); margin-right: 0.5rem;"></i> 
                       <strong>Phone:</strong> <?php echo htmlspecialchars($about_data['phone'] ?? 'Not provided'); ?></p>
                    <p><i class="fas fa-map-marker-alt" style="color: var(--primary-blue); margin-right: 0.5rem;"></i> 
                       <strong>Location:</strong> <?php echo htmlspecialchars($about_data['location'] ?? 'Not provided'); ?></p>
                </div>
            </div>
            
            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&h=800&fit=crop" alt="Data Analytics">
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section class="container" style="background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%); padding: 4rem 2rem; margin-top: 4rem;">
        <div class="section-title">
            <h2>Technical Skills</h2>
            <p>Tools and technologies I work with</p>
        </div>
        
        <div class="skills-grid" style="max-width: 1000px; margin: 0 auto;">
            <?php
            if ($skills_result && $skills_result->num_rows > 0) {
                while($skill = $skills_result->fetch_assoc()) {
                    ?>
                    <div class="skill-item">
                        <h4><?php echo htmlspecialchars($skill['skill_name']); ?></h4>
                        <small style="color: var(--primary-green);"><?php echo htmlspecialchars($skill['category']); ?></small>
                        <div class="skill-bar">
                            <div class="skill-progress" data-width="<?php echo $skill['proficiency']; ?>" style="width: 0%;"></div>
                        </div>
                        <small style="color: var(--dark-gray);"><?php echo $skill['proficiency']; ?>% Proficiency</small>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </section>

    <!-- Experience Timeline Section -->
    <section class="container">
        <div class="section-title">
            <h2>Professional Journey</h2>
            <p>Key milestones in my career</p>
        </div>
        
        <div style="max-width: 800px; margin: 0 auto;">
            <div style="border-left: 4px solid var(--primary-orange); padding-left: 2rem; margin-bottom: 2rem;">
                <div style="position: relative;">
                    <div style="position: absolute; left: -2.5rem; top: 0; width: 1.5rem; height: 1.5rem; background: var(--primary-blue); border-radius: 50%; border: 4px solid white;"></div>
                    <h3 style="color: var(--primary-blue);">Senior Data Analyst</h3>
                    <p style="color: var(--primary-green); font-weight: 600;">2022 - Present</p>
                    <p>Leading data analysis initiatives, developing advanced dashboards, and providing strategic insights for business decisions.</p>
                </div>
            </div>
            
            <div style="border-left: 4px solid var(--primary-orange); padding-left: 2rem; margin-bottom: 2rem;">
                <div style="position: relative;">
                    <div style="position: absolute; left: -2.5rem; top: 0; width: 1.5rem; height: 1.5rem; background: var(--primary-green); border-radius: 50%; border: 4px solid white;"></div>
                    <h3 style="color: var(--primary-blue);">Data Analyst</h3>
                    <p style="color: var(--primary-green); font-weight: 600;">2020 - 2022</p>
                    <p>Analyzed complex datasets, created visualizations, and collaborated with cross-functional teams to drive data-driven decisions.</p>
                </div>
            </div>
            
            <div style="border-left: 4px solid var(--primary-orange); padding-left: 2rem;">
                <div style="position: relative;">
                    <div style="position: absolute; left: -2.5rem; top: 0; width: 1.5rem; height: 1.5rem; background: var(--primary-orange); border-radius: 50%; border: 4px solid white;"></div>
                    <h3 style="color: var(--primary-blue);">Junior Analyst</h3>
                    <p style="color: var(--primary-green); font-weight: 600;">2018 - 2020</p>
                    <p>Started my data analytics journey, learning essential skills and contributing to various analytical projects.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="container" style="text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue)); margin-top: 4rem; border-radius: 20px; color: white;">
        <h2 style="color: white;">Let's Work Together</h2>
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">Have a project in mind? I'd love to help turn your data into insights.</p>
        <div class="cta-buttons">
            <a href="projects.php" class="btn btn-primary">View My Work</a>
            <a href="mailto:<?php echo htmlspecialchars($about_data['email'] ?? 'email@example.com'); ?>" class="btn btn-secondary">Get in Touch</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About</h3>
                <p><?php echo htmlspecialchars(substr($about_data['bio'] ?? 'Data Analyst Portfolio', 0, 150)); ?></p>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="index.php">Home</a>
                <a href="about.php">About</a>
                <a href="projects.php">Projects</a>
                <a href="admin/login.php">Admin Login</a>
            </div>
            
            <div class="footer-section">
                <h3>Contact</h3>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($about_data['email'] ?? 'email@example.com'); ?></p>
                
                <div class="social-links">
                    <a href="#" class="social-link" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-link" title="GitHub"><i class="fab fa-github"></i></a>
                    <a href="#" class="social-link" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link" title="Email"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Data Analyst Portfolio. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="script.js"></script>
</body>
</html>
<?php $conn->close(); ?>