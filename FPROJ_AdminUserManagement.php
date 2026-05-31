<?php
// Mock Admin Login
$admin = [
    'first_name' => 'System',
    'last_name'  => 'Administrator',
    'role'       => 'Admin',
];

$full_name   = $admin['first_name'] . ' ' . $admin['last_name'];
$active_page = $_GET['page'] ?? 'dashboard';

// Admin Menu includes all tables, specifically adding 'users' and 'logs' 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS2609 Final Project - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<!-- Topbar -->
<nav class="topbar d-flex align-items-center px-3 gap-3">
    <div class="logo-circle d-flex align-items-center justify-content-center flex-shrink-0">?</div>
    <button class="btn btn-sm btn-link text-white p-0" id="sidebarToggle">
        <i class="bi bi-list fs-4"></i>
    </button>
    <span class="topbar-title flex-grow-1">Barangay Record Management System</span>
    <span class="role-badge bg-danger"><?= htmlspecialchars($admin['role'] ?? 'Admin') ?></span>
    <span class="user-name"><?= htmlspecialchars($full_name ?? 'System Administrator') ?></span>
    <i class="bi bi-caret-down-fill text-white" style="font-size:10px;"></i>
</nav>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-label text-uppercase pb-2 text-white px-3 mt-3" style="font-size: 1.25rem; font-weight: bold;">
        Admin Menu
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="FPROJ_Admin.php" class="nav-link">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_AdminResidents.php" class="nav-link">
                <i class="bi bi-people-fill me-2"></i> Residents
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_AdminHouseholds.php" class="nav-link">
                <i class="bi bi-house-fill me-2"></i> Households
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_AdminBarangayOfficials.php" class="nav-link">
                <i class="bi bi-person-badge-fill me-2"></i> Barangay Officials
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_AdminIncidentReports.php" class="nav-link">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Incident Reports
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_AdminInfrastructure.php" class="nav-link">
                <i class="bi bi-cone-striped me-2"></i> Infrastructure Projects
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_AdminPending.php" class="nav-link active">
                <i class="bi bi-person-gear me-2"></i> Users Management
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_AdminSysLog.php" class="nav-link">
                <i class="bi bi-journal-text me-2"></i> System Logs
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
<!-- Header -->
   <div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5><i class="bi bi-person-gear me-2 text-purple" style="color: #6b21a8;"></i>System Users Management</h5>
        <p class="text-muted mb-0">Control system access, manage employee accounts, and assign roles.</p>
    </div>
    <button class="btn" style="background-color: #6b21a8; color: white;" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus-fill me-1"></i> Add New User
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Registered Users</h6>
        <div class="input-group" style="width: 250px;">
            <input type="text" class="form-control form-control-sm" placeholder="Search users...">
            <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-search"></i></button>
        </div>
    </div>
    <div class="table-responsive p-3">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mock data representing the Users Table structure
                $users = [
                    ['USR-001', 'System Administrator', 'admin_sys', 'Admin', 'Active'],
                    ['USR-002', 'Tyron James Sia', 'tyron.sia', 'Employee', 'Active'],
                    ['USR-003', 'Jane Doe', 'jane.doe', 'Employee', 'Inactive'],
                    ['USR-004', 'Mark Reyes', 'mark.reyes', 'Employee', 'Active']
                ];
                
                foreach ($users as $usr):
                    // Determine Role Badge
                    $roleBadge = $usr[3] === 'Admin' ? 'bg-danger' : 'bg-primary';
                    // Determine Status Badge
                    $statusBadge = $usr[4] === 'Active' ? 'bg-success' : 'bg-secondary';
                ?>
                <tr>
                    <td class="fw-bold text-muted"><?= $usr[0] ?></td>
                    <td class="fw-bold"><?= $usr[1] ?></td>
                    <td><code><?= $usr[2] ?></code></td>
                    <td>
                        <span class="badge <?= $roleBadge ?>"><?= $usr[3] ?></span>
                    </td>
                    <td>
                        <span class="badge <?= $statusBadge ?>"><?= $usr[4] ?></span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-warning" title="Reset Password">
                            <i class="bi bi-key-fill"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary" title="Edit User">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" title="Disable/Delete User">
                            <i class="bi bi-person-x-fill"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #6b21a8;">
                <h5 class="modal-title" id="addUserModalLabel">Create User Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="process_user.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" class="form-control" name="username" placeholder="e.g. juan.delacruz" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Temporary Password</label>
                            <input type="password" class="form-control" name="password" required>
                            <div class="form-text" style="font-size: 11px;">User will be prompted to change this upon first login.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assign Role</label>
                            <select class="form-select" name="role" required>
                                <option value="" selected disabled>Select Role...</option>
                                <option value="Employee">Employee (Mid-Level)</option>
                                <option value="Admin">Admin (Top-Level)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer mt-4 px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white" style="background-color: #6b21a8;">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark" id="addProjectModalLabel">Add New Infrastructure Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="process_project.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" name="project_name" placeholder="e.g. Road Widening, Bridge Repair" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Project Status</label>
                            <select class="form-select" name="project_status" required>
                                <option value="" selected disabled>Select Status...</option>
                                <option value="Planned">Planned</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                                <option value="On Hold">On Hold</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Project Description</label>
                            <textarea class="form-control" name="project_description" rows="3" placeholder="Provide details about the infrastructure project..." required></textarea>
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
                    <div class="modal-footer mt-4 px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-dark fw-bold">Save Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addIncidentModal" tabindex="-1" aria-labelledby="addIncidentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="addIncidentModalLabel">Log New Incident</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="process_incident.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Reported By (Resident)</label>
                            <select class="form-select" name="resident_id" required>
                                <option value="" selected disabled>Select Resident...</option>
                                <option value="1">Michael Villafuerte</option>
                                <option value="2">Franz Zorilla</option>
                                <option value="3">Maria Clara</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Handled By (Official)</label>
                            <select class="form-select" name="official_id" required>
                                <option value="" selected disabled>Select Official...</option>
                                <option value="1">Arturo Dela Cruz</option>
                                <option value="2">Maria Santos</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Incident Type</label>
                            <input type="text" class="form-control" name="incident_type" placeholder="e.g. Theft, Noise, Altercation" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date Reported</label>
                            <input type="date" class="form-control" name="date_reported" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Incident Details</label>
                            <textarea class="form-control" name="incident_details" rows="4" placeholder="Provide a detailed description of the incident..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer mt-4 px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Submit Report</button>
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
        const main    = document.querySelector('.main-content');
        if (sidebar.style.display === 'none' || sidebar.style.display === '') {
            sidebar.style.display = 'block';
            main.style.marginLeft = '190px'; // Adjust based on your style.css sidebar width
        } else {
            sidebar.style.display = 'none';
            main.style.marginLeft = '0';
        }
    });
</script>
</body>
</html>