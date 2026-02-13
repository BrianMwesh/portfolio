<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$conn = getDBConnection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $project_type = sanitize_input($_POST['project_type']);
    $external_link = sanitize_input($_POST['external_link'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $display_order = intval($_POST['display_order'] ?? 0);
    
    $file_path = '';
    $thumbnail = '';
    
    // Handle file uploads
    if ($project_type === 'image' && isset($_FILES['project_file']) && $_FILES['project_file']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['project_file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = UPLOAD_DIR . 'images/' . $new_filename;
            
            if (move_uploaded_file($_FILES['project_file']['tmp_name'], $upload_path)) {
                $file_path = UPLOAD_URL . 'images/' . $new_filename;
                $thumbnail = $file_path;
            }
        }
    }
    
    if ($project_type === 'pdf' && isset($_FILES['project_file']) && $_FILES['project_file']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['project_file']['name'], PATHINFO_EXTENSION));
        
        if ($ext === 'pdf') {
            $new_filename = uniqid() . '.pdf';
            $upload_path = UPLOAD_DIR . 'pdfs/' . $new_filename;
            
            if (move_uploaded_file($_FILES['project_file']['tmp_name'], $upload_path)) {
                $file_path = UPLOAD_URL . 'pdfs/' . $new_filename;
            }
        }
    }
    
    if ($project_type === 'html' && isset($_FILES['project_file']) && $_FILES['project_file']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['project_file']['name'], PATHINFO_EXTENSION));
        
        if ($ext === 'html') {
            $new_filename = uniqid() . '.html';
            $upload_path = UPLOAD_DIR . 'html/' . $new_filename;
            
            if (move_uploaded_file($_FILES['project_file']['tmp_name'], $upload_path)) {
                $file_path = UPLOAD_URL . 'html/' . $new_filename;
            }
        }
    }
    
    // Handle thumbnail upload
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'thumb_' . uniqid() . '.' . $ext;
            $upload_path = UPLOAD_DIR . 'thumbnails/' . $new_filename;
            
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_path)) {
                $thumbnail = UPLOAD_URL . 'thumbnails/' . $new_filename;
            }
        }
    }
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO projects (title, description, project_type, file_path, external_link, thumbnail, is_featured, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiii", $title, $description, $project_type, $file_path, $external_link, $thumbnail, $is_featured, $display_order);
    
    if ($stmt->execute()) {
        $message = 'Project added successfully!';
        // Clear form
        $_POST = array();
    } else {
        $error = 'Error adding project: ' . $conn->error;
    }
    
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project - Admin</title>
    
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
            <h1><i class="fas fa-plus-circle"></i> Add New Project</h1>
        </div>
        <div>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            <a href="manage_projects.php" style="float: right; color: inherit;">View all projects →</a>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="admin-card">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Project Title *</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" class="form-control" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="project_type">Project Type *</label>
                <select id="project_type" name="project_type" class="form-control" required onchange="toggleFields()">
                    <option value="">Select type...</option>
                    <option value="image">Image</option>
                    <option value="pdf">PDF</option>
                    <option value="html">HTML</option>
                    <option value="powerbi">Power BI Dashboard</option>
                    <option value="dashboard">Dashboard (iframe)</option>
                    <option value="github">GitHub Link</option>
                </select>
            </div>
            
            <div class="form-group" id="file_upload_group" style="display: none;">
                <label for="project_file">Upload File</label>
                <input type="file" id="project_file" name="project_file" class="form-control" accept="">
                <small style="color: var(--dark-gray);">For image, PDF, or HTML projects</small>
            </div>
            
            <div class="form-group" id="external_link_group" style="display: none;">
                <label for="external_link">External Link/URL</label>
                <input type="url" id="external_link" name="external_link" class="form-control" placeholder="https://...">
                <small style="color: var(--dark-gray);">For Power BI, Dashboard, or GitHub links</small>
            </div>
            
            <div class="form-group">
                <label for="thumbnail">Thumbnail Image (Optional)</label>
                <input type="file" id="thumbnail" name="thumbnail" class="form-control" accept="image/*">
                <small style="color: var(--dark-gray);">Upload a custom thumbnail for the project card</small>
            </div>
            
            <div class="form-group">
                <label for="display_order">Display Order</label>
                <input type="number" id="display_order" name="display_order" class="form-control" value="0" min="0">
                <small style="color: var(--dark-gray);">Lower numbers appear first</small>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="is_featured" id="is_featured" style="margin-right: 0.5rem; width: auto;">
                    <span>Feature this project on homepage</span>
                </label>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Project
                </button>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        function toggleFields() {
            const projectType = document.getElementById('project_type').value;
            const fileUploadGroup = document.getElementById('file_upload_group');
            const externalLinkGroup = document.getElementById('external_link_group');
            const projectFile = document.getElementById('project_file');
            
            // Reset
            fileUploadGroup.style.display = 'none';
            externalLinkGroup.style.display = 'none';
            projectFile.removeAttribute('required');
            
            if (projectType === 'image') {
                fileUploadGroup.style.display = 'block';
                projectFile.setAttribute('accept', 'image/*');
            } else if (projectType === 'pdf') {
                fileUploadGroup.style.display = 'block';
                projectFile.setAttribute('accept', '.pdf');
            } else if (projectType === 'html') {
                fileUploadGroup.style.display = 'block';
                projectFile.setAttribute('accept', '.html');
            } else if (projectType === 'powerbi' || projectType === 'dashboard' || projectType === 'github') {
                externalLinkGroup.style.display = 'block';
            }
        }
    </script>
    
    <script src="../script.js"></script>
</body>
</html>
<?php $conn->close(); ?>