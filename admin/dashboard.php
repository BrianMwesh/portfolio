<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$conn = getDBConnection();

// Get statistics
$total_projects = $conn->query("SELECT COUNT(*) as count FROM projects")->fetch_assoc()['count'];
$total_skills = $conn->query("SELECT COUNT(*) as count FROM skills")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../styles.css">
</head>
<body class="admin-panel">
    <div class="admin-header">
        <div>
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>
        </div>
        <div>
            <a href="../index.php" class="btn btn-secondary" style="margin-right: 1rem;">
                <i class="fas fa-eye"></i> View Site
            </a>
            <a href="logout.php" class="btn" style="background: #E74C3C; color: white;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="admin-card" style="background: linear-gradient(135deg, var(--primary-orange), var(--light-orange)); color: white;">
            <h2 style="font-size: 3rem; color: white; margin-bottom: 0.5rem;"><?php echo $total_projects; ?></h2>
            <p style="font-size: 1.2rem; color: white;"><i class="fas fa-project-diagram"></i> Total Projects</p>
        </div>
        
        <div class="admin-card" style="background: linear-gradient(135deg, var(--primary-green), var(--light-green)); color: white;">
            <h2 style="font-size: 3rem; color: white; margin-bottom: 0.5rem;"><?php echo $total_skills; ?></h2>
            <p style="font-size: 1.2rem; color: white;"><i class="fas fa-tools"></i> Skills Listed</p>
        </div>
        
        <div class="admin-card" style="background: linear-gradient(135deg, var(--primary-blue), var(--light-blue)); color: white;">
            <h2 style="font-size: 3rem; color: white; margin-bottom: 0.5rem;">Active</h2>
            <p style="font-size: 1.2rem; color: white;"><i class="fas fa-check-circle"></i> Status</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-card">
        <h2 style="color: var(--primary-blue); margin-bottom: 1.5rem;">
            <i class="fas fa-bolt"></i> Quick Actions
        </h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <a href="manage_projects.php" class="btn btn-primary" style="text-align: center; padding: 1.5rem;">
                <i class="fas fa-project-diagram" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                Manage Projects
            </a>
            
            <a href="add_project.php" class="btn" style="background: var(--primary-green); color: white; text-align: center; padding: 1.5rem;">
                <i class="fas fa-plus-circle" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                Add New Project
            </a>
            
            <a href="manage_skills.php" class="btn" style="background: var(--primary-blue); color: white; text-align: center; padding: 1.5rem;">
                <i class="fas fa-tools" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                Manage Skills
            </a>
            
            <a href="manage_about.php" class="btn" style="background: var(--dark-blue); color: white; text-align: center; padding: 1.5rem;">
                <i class="fas fa-user-edit" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                Edit About Page
            </a>
        </div>
    </div>

    <!-- Recent Projects -->
    <div class="admin-card">
        <h2 style="color: var(--primary-blue); margin-bottom: 1.5rem;">
            <i class="fas fa-clock"></i> Recent Projects
        </h2>
        
        <?php
        $recent_projects = $conn->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5");
        
        if ($recent_projects && $recent_projects->num_rows > 0):
        ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Created</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($project = $recent_projects->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['title']); ?></td>
                        <td><span class="project-type"><?php echo ucfirst($project['project_type']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($project['created_at'])); ?></td>
                        <td>
                            <?php if ($project['is_featured']): ?>
                                <i class="fas fa-star" style="color: var(--primary-orange);"></i>
                            <?php else: ?>
                                <i class="far fa-star" style="color: var(--medium-gray);"></i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn-edit">Edit</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="manage_projects.php" class="btn btn-secondary">View All Projects</a>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: var(--dark-gray); padding: 2rem;">
                No projects yet. <a href="add_project.php">Add your first project!</a>
            </p>
        <?php endif; ?>
    </div>

    <script src="../script.js"></script>
</body>
</html>
<?php $conn->close(); ?>