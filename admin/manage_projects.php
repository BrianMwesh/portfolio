<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$conn = getDBConnection();

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Get file paths before deleting
    $stmt = $conn->prepare("SELECT file_path, thumbnail FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();
    
    // Delete files
    if ($project['file_path'] && file_exists(str_replace(UPLOAD_URL, UPLOAD_DIR, $project['file_path']))) {
        unlink(str_replace(UPLOAD_URL, UPLOAD_DIR, $project['file_path']));
    }
    if ($project['thumbnail'] && file_exists(str_replace(UPLOAD_URL, UPLOAD_DIR, $project['thumbnail']))) {
        unlink(str_replace(UPLOAD_URL, UPLOAD_DIR, $project['thumbnail']));
    }
    
    // Delete from database
    $delete_stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $delete_stmt->bind_param("i", $id);
    $delete_stmt->execute();
    $delete_stmt->close();
    
    header('Location: manage_projects.php?deleted=1');
    exit();
}

// Fetch all projects
$projects = $conn->query("SELECT * FROM projects ORDER BY display_order ASC, created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - Admin</title>
    
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
            <h1><i class="fas fa-project-diagram"></i> Manage Projects</h1>
            <p>Total: <?php echo $projects->num_rows; ?> projects</p>
        </div>
        <div>
            <a href="add_project.php" class="btn" style="background: var(--primary-green); color: white; margin-right: 1rem;">
                <i class="fas fa-plus"></i> Add New
            </a>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Project deleted successfully!
        </div>
    <?php endif; ?>

    <div class="admin-card">
        <?php if ($projects->num_rows > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Thumbnail</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Order</th>
                        <th>Featured</th>
                        <th>Created</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($project = $projects->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if ($project['thumbnail']): ?>
                                <img src="<?php echo htmlspecialchars($project['thumbnail']); ?>" 
                                     alt="Thumbnail" 
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                            <?php else: ?>
                                <div style="width: 60px; height: 60px; background: var(--light-gray); border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image" style="color: var(--medium-gray);"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($project['title']); ?></strong></td>
                        <td>
                            <span class="project-type">
                                <?php echo ucfirst($project['project_type']); ?>
                            </span>
                        </td>
                        <td style="max-width: 300px;">
                            <?php echo htmlspecialchars(substr($project['description'], 0, 100)) . '...'; ?>
                        </td>
                        <td><?php echo $project['display_order']; ?></td>
                        <td>
                            <?php if ($project['is_featured']): ?>
                                <i class="fas fa-star" style="color: var(--primary-orange); font-size: 1.2rem;"></i>
                            <?php else: ?>
                                <i class="far fa-star" style="color: var(--medium-gray);"></i>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($project['created_at'])); ?></td>
                        <td>
                            <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button onclick="deleteProject(<?php echo $project['id']; ?>)" class="btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem 2rem;">
                <i class="fas fa-folder-open" style="font-size: 4rem; color: var(--medium-gray); margin-bottom: 1rem;"></i>
                <h3 style="color: var(--dark-gray);">No Projects Yet</h3>
                <p style="color: var(--medium-gray); margin-bottom: 2rem;">Start by adding your first project!</p>
                <a href="add_project.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Project
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function deleteProject(id) {
            if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
                window.location.href = 'manage_projects.php?delete=' + id;
            }
        }
    </script>
    
    <script src="../script.js"></script>
</body>
</html>
<?php $conn->close(); ?>