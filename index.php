<?php
require_once 'config.php';
$conn = getDBConnection();

// Fetch featured projects
$featured_query = "SELECT * FROM projects WHERE is_featured = 1 ORDER BY display_order ASC LIMIT 6";
$featured_result = $conn->query($featured_query);

// Fetch about content
$about_query = "SELECT * FROM about_content WHERE section_name = 'bio'";
$about_result = $conn->query($about_query);
$about_data = $about_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Analyst Portfolio - Home</title>
    
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
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="about.php">About</a></li>
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
    <section class="hero">
        <div class="hero-content">
            <h1>Data-Driven Insights</h1>
            <p class="subtitle">Transforming Raw Data into Actionable Intelligence</p>
            <p>Welcome to my portfolio! I specialize in analyzing complex datasets, creating compelling visualizations, and delivering insights that drive business decisions.</p>
            <div class="cta-buttons">
                <a href="projects.php" class="btn btn-primary">View My Projects</a>
                <a href="about.php" class="btn btn-secondary">Learn More About Me</a>
            </div>
        </div>
    </section>

    <!-- Featured Projects Section -->
    <section class="container">
        <div class="section-title">
            <h2>Featured Projects</h2>
            <p>Showcase of my recent data analysis work</p>
        </div>
        
        <div class="projects-grid">
            <?php
            if ($featured_result && $featured_result->num_rows > 0) {
                while($project = $featured_result->fetch_assoc()) {
                    $thumbnail = $project['thumbnail'] ?: 'https://via.placeholder.com/400x300?text=' . urlencode($project['title']);
                    $icon = '';
                    
                    switch($project['project_type']) {
                        case 'powerbi':
                            $icon = 'fa-chart-bar';
                            break;
                        case 'github':
                            $icon = 'fa-github';
                            break;
                        case 'dashboard':
                            $icon = 'fa-tachometer-alt';
                            break;
                        case 'image':
                            $icon = 'fa-image';
                            break;
                        case 'pdf':
                            $icon = 'fa-file-pdf';
                            break;
                        case 'html':
                            $icon = 'fa-code';
                            break;
                        default:
                            $icon = 'fa-project-diagram';
                    }
                    ?>
                    <div class="project-card" data-type="<?php echo $project['project_type']; ?>" onclick="openModal(<?php echo $project['id']; ?>, '<?php echo $project['project_type']; ?>')">
                        <div class="project-thumbnail">
                            <?php if ($project['thumbnail']): ?>
                                <img src="<?php echo htmlspecialchars($project['thumbnail']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                            <?php else: ?>
                                <i class="fas <?php echo $icon; ?>"></i>
                            <?php endif; ?>
                        </div>
                        <div class="project-info">
                            <span class="project-type"><?php echo ucfirst($project['project_type']); ?></span>
                            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($project['description'], 0, 120)) . '...'; ?></p>
                            <div class="project-links">
                                <a href="#" class="project-link" onclick="event.stopPropagation(); openModal(<?php echo $project['id']; ?>, '<?php echo $project['project_type']; ?>')">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p>No featured projects available yet.</p>';
            }
            ?>
        </div>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="projects.php" class="btn btn-primary">View All Projects</a>
        </div>
    </section>

    <!-- Skills Preview Section -->
    <section class="container" style="background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%); padding: 4rem 2rem; margin-top: 4rem;">
        <div class="section-title">
            <h2>Core Competencies</h2>
            <p>Technical skills that drive results</p>
        </div>
        
        <div class="skills-grid" style="max-width: 900px; margin: 0 auto;">
            <?php
            $skills_query = "SELECT * FROM skills ORDER BY proficiency DESC LIMIT 4";
            $skills_result = $conn->query($skills_query);
            
            if ($skills_result && $skills_result->num_rows > 0) {
                while($skill = $skills_result->fetch_assoc()) {
                    ?>
                    <div class="skill-item">
                        <h4><?php echo htmlspecialchars($skill['skill_name']); ?></h4>
                        <div class="skill-bar">
                            <div class="skill-progress" data-width="<?php echo $skill['proficiency']; ?>" style="width: 0%;"></div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="about.php" class="btn btn-secondary">View Full Profile</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About</h3>
                <p><?php echo htmlspecialchars(substr($about_data['content'] ?? 'Data Analyst Portfolio', 0, 150)); ?></p>
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
                <?php
                $email_query = "SELECT content FROM about_content WHERE section_name = 'email'";
                $email_result = $conn->query($email_query);
                $email = $email_result->fetch_assoc()['content'] ?? 'email@example.com';
                ?>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($email); ?></p>
                
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

    <!-- Project Modal -->
    <div id="projectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Project Details</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Project content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="script.js"></script>
</body>
</html>
<?php $conn->close(); ?>