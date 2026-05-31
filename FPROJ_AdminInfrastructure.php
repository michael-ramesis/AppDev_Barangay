<?php
session_start();
require_once 'dbaseconnection.php';

// Mock Admin Login - In a real app, you'd fetch this from a session
$admin = [
    'first_name' => 'System',
    'last_name'  => 'Administrator',
    'role'       => 'Admin',
];
$full_name = $admin['first_name'] . ' ' . $admin['last_name'];

// --- 1. HANDLE SEARCH ---
$search = $_GET['search'] ?? '';

// --- 2. HANDLE DELETE PROJECT ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_project_id'])) {
    $del_id = $_POST['delete_project_id'];
    $del_query = "DELETE FROM infrastructure_project_table WHERE project_id = ?";
    $stmt_del = $conn->prepare($del_query);
    if ($stmt_del) {
        $stmt_del->bind_param("i", $del_id);
        if ($stmt_del->execute()) {
            $_SESSION['sys_msg'] = "Project deleted successfully.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['sys_msg'] = "Error deleting project.";
            $_SESSION['msg_type'] = "danger";
        }
        $stmt_del->close();
    }
    header("Location: FPROJ_AdminInfrastructure.php");
    exit();
}

// --- 3. HANDLE ADD NEW PROJECT ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_project'])) {
    $p_name   = $_POST['project_name'];
    $p_status = $_POST['project_status'];
    $p_desc   = $_POST['project_description'];
    $p_start  = $_POST['start_date'];
    $p_end    = $_POST['end_date'];

    $ins_query = "INSERT INTO infrastructure_project_table (project_name, project_description, start_date, end_date, project_status) VALUES (?, ?, ?, ?, ?)";
    $stmt_ins = $conn->prepare($ins_query);
    
    if ($stmt_ins) {
        $stmt_ins->bind_param("sssss", $p_name, $p_desc, $p_start, $p_end, $p_status);
        if ($stmt_ins->execute()) {
            $_SESSION['sys_msg'] = "Project added successfully!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['sys_msg'] = "Error: " . $conn->error;
            $_SESSION['msg_type'] = "danger";
        }
        $stmt_ins->close();
    }
    header("Location: FPROJ_AdminInfrastructure.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infrastructure Projects - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<nav class="topbar d-flex align-items-center px-3 gap-3">
    <div class="logo-circle d-flex align-items-center justify-content-center flex-shrink-0">?</div>
    <button class="btn btn-sm btn-link text-white p-0" id="sidebarToggle">
        <i class="bi bi-list fs-4"></i>
    </button>
    <span class="topbar-title flex-grow-1">Barangay Record Management System</span>
    <span class="role-badge bg-danger"><?= htmlspecialchars($admin['role']) ?></span>
    <span class="user-name"><?= htmlspecialchars($full_name) ?></span>
    <i class="bi bi-caret-down-fill text-white" style="font-size:10px;"></i>
</nav>

<div class="sidebar" id="sidebar">
    <div class="sidebar-label text-uppercase pb-2 text-white px-3 mt-3" style="font-size: 1.25rem; font-weight: bold;">
        Admin Menu
    </div>
    <ul class="nav flex-column">
        <li class="nav-item"><a href="FPROJ_Admin.php" class="nav-link"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a href="FPROJ_AdminResidents.php" class="nav-link"><i class="bi bi-people-fill me-2"></i> Residents</a></li>
        <li class="nav-item"><a href="FPROJ_AdminHouseholds.php" class="nav-link"><i class="bi bi-house-fill me-2"></i> Households</a></li>
        <li class="nav-item"><a href="FPROJ_AdminBarangayOfficials.php" class="nav-link"><i class="bi bi-person-badge-fill me-2"></i> Barangay Officials</a></li>
        <li class="nav-item"><a href="FPROJ_AdminIncidentReports.php" class="nav-link"><i class="bi bi-exclamation-triangle-fill me-2"></i> Incident Reports</a></li>
        <li class="nav-item"><a href="FPROJ_AdminInfrastructure.php" class="nav-link active"><i class="bi bi-cone-striped me-2"></i> Infrastructure Projects</a></li>
        <li class="nav-item"><a href="FPROJ_AdminPending.php" class="nav-link"><i class="bi bi-person-gear me-2"></i> Users Management</a></li>
        <li class="nav-item"><a href="FPROJ_AdminSysLog.php" class="nav-link"><i class="bi bi-journal-text me-2"></i> System Logs</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5><i class="bi bi-cone-striped me-2 text-warning"></i>Infrastructure Projects</h5>
            <p class="text-muted mb-0">Monitor and manage public works and infrastructure projects.</p>
        </div>
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addProjectModal">
            <i class="bi bi-plus-lg me-1 text-dark"></i> Add Project
        </button>
    </div>

    <?php if (isset($_SESSION['sys_msg'])): ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['sys_msg'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['sys_msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Project Masterlist</h6>
            <form action="" method="GET" class="input-group" style="width: 250px;">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search projects..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Project ID</th>
                        <th>Project Name</th>
                        <th style="width: 25%;">Description</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM infrastructure_project_table";
                    if (!empty($search)) {
                        $sql .= " WHERE project_name LIKE '%$search%' OR status LIKE '%$search%'";
                    }
                    $sql .= " ORDER BY project_id DESC";
                    
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0):
                        while ($proj = $result->fetch_assoc()):
                            $p_status = $proj['status'] ?? 'Planned';
                            $badgeClass = 'bg-secondary';
                            if ($p_status === 'Completed') $badgeClass = 'bg-success';
                            if ($p_status === 'Ongoing') $badgeClass = 'bg-primary';
                            if ($p_status === 'Planned') $badgeClass = 'bg-info text-dark';
                            if ($p_status === 'On Hold') $badgeClass = 'bg-danger';
                    ?>
                    <tr>
                        <td class="fw-bold text-muted">PROJ-<?= $proj['project_id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($proj['project_name']) ?></td>
                        <td class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($proj['project_description']) ?>">
                            <?= htmlspecialchars($proj['project_description']) ?>
                        </td>
                        <td>
                            <div class="small"><strong>Start:</strong> <?= date('M d, Y', strtotime($proj['start_date'])) ?></div>
                            <div class="small"><strong>End:</strong> <?= date('M d, Y', strtotime($proj['end_date'])) ?></div>
                        </td>
                        <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($p_status) ?></span></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                <form method="POST" onsubmit="return confirm('Permanently delete this project?');">
                                    <input type="hidden" name="delete_project_id" value="<?= $proj['project_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No infrastructure projects found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addProjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark fw-bold">Add New Infrastructure Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" name="project_name" placeholder="e.g. Road Widening" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Project Status</label>
                            <select class="form-select" name="project_status" required>
                                <option value="Planned">Planned</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                                <option value="On Hold">On Hold</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Project Description</label>
                            <textarea class="form-control" name="project_description" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estimated End Date</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                    </div>
                    <div class="modal-footer mt-4 px-0 pb-0 border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="save_project" class="btn btn-warning text-dark fw-bold">Save Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        const sidebar = document.getElementById('sidebar');
        const main = document.querySelector('.main-content');
        sidebar.classList.toggle('active');
        if (window.innerWidth > 768) {
             main.style.marginLeft = sidebar.classList.contains('active') ? '0' : '250px';
        }
    });
</script>
</body>
</html>