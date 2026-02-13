<?php
require_once 'config.php';
$conn = getDBConnection();

// Fetch all projects
$projects_query = "SELECT * FROM projects ORDER BY display_order ASC, created_at DESC";
$projects_result = $conn->query($projects_query);

// Get unique project types for filters
$types_query = "SELECT DISTINCT project_type FROM projects";
$types_result = $conn->query($types_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - Data Analyst Portfolio</title>
    
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
                <li><a href="about.php">About</a></li>
                <li><a href="projects.php" class="active">Projects</a></li>
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
            <h1>My Projects</h1>
            <p class="subtitle">Explore my data analysis portfolio</p>
            <p>From interactive dashboards to comprehensive reports, see how I transform data into actionable insights.</p>
        </div>
    </section>

    <!-- Projects Section -->
    <section class="container">
        <!-- Project Filters -->
        <div class="projects-filter">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-th"></i> All Projects
            </button>
            <?php
            $filter_icons = [
                'powerbi' => 'fa-chart-bar',
                'dashboard' => 'fa-tachometer-alt',
                'github' => 'fa-github',
                'image' => 'fa-image',
                'pdf' => 'fa-file-pdf',
                'html' => 'fa-code'
            ];
            
            if ($types_result && $types_result->num_rows > 0) {
                while($type = $types_result->fetch_assoc()) {
                    $type_name = $type['project_type'];
                    $icon = $filter_icons[$type_name] ?? 'fa-project-diagram';
                    ?>
                    <button class="filter-btn" data-filter="<?php echo $type_name; ?>">
                        <i class="fas <?php echo $icon; ?>"></i> <?php echo ucfirst($type_name); ?>
                    </button>
                    <?php
                }
            }
            ?>
        </div>

        <!-- Projects Grid -->
        <div class="projects-grid">
            <?php
            if ($projects_result && $projects_result->num_rows > 0) {
                while($project = $projects_result->fetch_assoc()) {
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
                            <p><?php echo htmlspecialchars(substr($project['description'], 0, 120)) . (strlen($project['description']) > 120 ? '...' : ''); ?></p>
                            
                            <div class="project-links">
                                <?php if ($project['project_type'] === 'github' && $project['external_link']): ?>
                                    <a href="<?php echo htmlspecialchars($project['external_link']); ?>" target="_blank" class="project-link" onclick="event.stopPropagation();">
                                        <i class="fab fa-github"></i> View on GitHub
                                    </a>
                                <?php elseif (in_array($project['project_type'], ['powerbi', 'dashboard']) && $project['external_link']): ?>
                                    <a href="#" class="project-link" onclick="event.stopPropagation(); openModal(<?php echo $project['id']; ?>, '<?php echo $project['project_type']; ?>')">
                                        <i class="fas fa-external-link-alt"></i> View Dashboard
                                    </a>
                                <?php else: ?>
                                    <a href="#" class="project-link" onclick="event.stopPropagation(); openModal(<?php echo $project['id']; ?>, '<?php echo $project['project_type']; ?>')">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                    <i class="fas fa-folder-open" style="font-size: 4rem; color: var(--medium-gray); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--dark-gray);">No Projects Yet</h3>
                    <p style="color: var(--medium-gray);">Projects will appear here once they are added through the admin panel.</p>
                    <a href="admin/login.php" class="btn btn-primary" style="margin-top: 1rem;">Go to Admin Panel</a>
                </div>
                <?php
            }
            ?>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="container" style="background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue)); padding: 4rem 2rem; margin-top: 4rem; border-radius: 20px; color: white; text-align: center;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
            <div>
                <h2 style="font-size: 3rem; color: var(--primary-orange); margin-bottom: 0.5rem;">
                    <?php echo $projects_result ? $conn->query("SELECT COUNT(*) as count FROM projects")->fetch_assoc()['count'] : 0; ?>+
                </h2>
                <p style="color: white; font-size: 1.2rem;">Projects Completed</p>
            </div>
            <div>
                <h2 style="font-size: 3rem; color: var(--primary-green); margin-bottom: 0.5rem;">100+</h2>
                <p style="color: white; font-size: 1.2rem;">Datasets Analyzed</p>
            </div>
            <div>
                <h2 style="font-size: 3rem; color: var(--light-orange); margin-bottom: 0.5rem;">50+</h2>
                <p style="color: white; font-size: 1.2rem;">Dashboards Created</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About</h3>
                <p>Passionate data analyst specializing in transforming complex data into actionable insights.</p>
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
                <p><i class="fas fa-envelope"></i> your.email@example.com</p>
                
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