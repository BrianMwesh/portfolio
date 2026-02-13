<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$conn = getDBConnection();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if ($key !== 'submit') {
            $content = sanitize_input($value);
            $stmt = $conn->prepare("UPDATE about_content SET content = ? WHERE section_name = ?");
            $stmt->bind_param("ss", $content, $key);
            $stmt->execute();
            $stmt->close();
        }
    }
    $message = 'About content updated successfully!';
}

// Fetch about content
$about_result = $conn->query("SELECT * FROM about_content");
$about_data = [];
while($row = $about_result->fetch_assoc()) {
    $about_data[$row['section_name']] = $row['content'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About - Admin</title>
    
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
            <h1><i class="fas fa-user-edit"></i> Manage About Content</h1>
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
        </div>
    <?php endif; ?>

    <div class="admin-card">
        <form method="POST" action="">
            <div class="form-group">
                <label for="bio">Biography / About Me</label>
                <textarea id="bio" name="bio" class="form-control" rows="6"><?php echo htmlspecialchars($about_data['bio'] ?? ''); ?></textarea>
                <small style="color: var(--dark-gray);">Tell visitors about yourself and your expertise</small>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($about_data['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($about_data['phone'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" class="form-control" value="<?php echo htmlspecialchars($about_data['location'] ?? ''); ?>" placeholder="City, Country">
            </div>
            
            <button type="submit" name="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>
    </div>

    <script src="../script.js"></script>
</body>
</html>
<?php $conn->close(); ?>