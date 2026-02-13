<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$conn = getDBConnection();
$message = '';

// Handle add skill
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_skill'])) {
    $skill_name = sanitize_input($_POST['skill_name']);
    $proficiency = intval($_POST['proficiency']);
    $category = sanitize_input($_POST['category']);
    
    $stmt = $conn->prepare("INSERT INTO skills (skill_name, proficiency, category) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $skill_name, $proficiency, $category);
    
    if ($stmt->execute()) {
        $message = 'Skill added successfully!';
    }
    $stmt->close();
}

// Handle delete skill
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM skills WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    header('Location: manage_skills.php?deleted=1');
    exit();
}

// Fetch all skills
$skills = $conn->query("SELECT * FROM skills ORDER BY category, proficiency DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills - Admin</title>
    
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
            <h1><i class="fas fa-tools"></i> Manage Skills</h1>
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
    
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Skill deleted successfully!
        </div>
    <?php endif; ?>

    <!-- Add New Skill Form -->
    <div class="admin-card">
        <h2 style="color: var(--primary-blue); margin-bottom: 1.5rem;">
            <i class="fas fa-plus-circle"></i> Add New Skill
        </h2>
        
        <form method="POST" action="">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="skill_name">Skill Name</label>
                    <input type="text" id="skill_name" name="skill_name" class="form-control" required placeholder="e.g., Python, SQL, Power BI">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="proficiency">Proficiency (%)</label>
                    <input type="number" id="proficiency" name="proficiency" class="form-control" required min="0" max="100" value="75">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="Programming">Programming</option>
                        <option value="Database">Database</option>
                        <option value="Visualization">Visualization</option>
                        <option value="Tools">Tools</option>
                        <option value="Statistics">Statistics</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <button type="submit" name="add_skill" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>
        </form>
    </div>

    <!-- Skills List -->
    <div class="admin-card">
        <h2 style="color: var(--primary-blue); margin-bottom: 1.5rem;">
            <i class="fas fa-list"></i> Your Skills
        </h2>
        
        <?php if ($skills->num_rows > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Skill Name</th>
                        <th>Category</th>
                        <th style="width: 200px;">Proficiency</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $current_category = '';
                    while($skill = $skills->fetch_assoc()): 
                        if ($current_category !== $skill['category']):
                            $current_category = $skill['category'];
                    ?>
                    <tr style="background: var(--light-gray);">
                        <td colspan="4" style="font-weight: 700; color: var(--primary-blue);">
                            <?php echo htmlspecialchars($current_category); ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($skill['category']); ?></td>
                        <td>
                            <div class="skill-bar">
                                <div class="skill-progress" style="width: <?php echo $skill['proficiency']; ?>%;"></div>
                            </div>
                            <small><?php echo $skill['proficiency']; ?>%</small>
                        </td>
                        <td>
                            <button onclick="deleteSkill(<?php echo $skill['id']; ?>)" class="btn-delete" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem 2rem;">
                <i class="fas fa-tools" style="font-size: 3rem; color: var(--medium-gray); margin-bottom: 1rem;"></i>
                <h3 style="color: var(--dark-gray);">No Skills Added Yet</h3>
                <p style="color: var(--medium-gray);">Use the form above to add your first skill!</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function deleteSkill(id) {
            if (confirm('Are you sure you want to delete this skill?')) {
                window.location.href = 'manage_skills.php?delete=' + id;
            }
        }
    </script>
    
    <script src="../script.js"></script>
</body>
</html>
<?php $conn->close(); ?>