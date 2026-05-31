<?php
// Mock Admin Login
$admin = [
    'first_name' => 'System',
    'last_name'  => 'Administrator',
    'role'       => 'Admin',
];

$full_name   = $admin['first_name'] . ' ' . $admin['last_name'];
$active_page = $_GET['page'] ?? 'dashboard';


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
            <a href="FPROJ_AdminHouseholds.php" class="nav-link active">
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
            <a href="FPROJ_AdminPending.php" class="nav-link">
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
        <h5><i class="bi bi-house-fill me-2"></i>Household Management</h5>
        <p class="text-muted mb-0">View, add, and manage household records within the barangay.</p>
    </div>
    <!-- Admin Privilege: Add new record -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHouseholdModal">
        <i class="bi bi-plus-lg me-1"></i> Add Household
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Household Masterlist</h6>
        <div class="input-group" style="width: 250px;">
            <input type="text" class="form-control form-control-sm" placeholder="Search households...">
            <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-search"></i></button>
        </div>
    </div>
    <div class="table-responsive p-3">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Household ID</th>
                    <th>Head of Household</th>
                    <th>Address</th>
                    <th>Contact Info</th>
                    <th>Total Members</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mock data representing the Household Table structure
                $households = [
                    ['HH-1001', 'Roberto Villafuerte', 'Blk 1 Lot 2, Sampaguita St.', '09123456789', 4],
                    ['HH-1002', 'Elena Zorilla', 'Blk 3 Lot 4, Rosal St.', '09987654321', 3],
                    ['HH-1003', 'Jose Clara', 'Blk 2 Lot 1, Ilang-Ilang St.', '09456781234', 5]
                ];
                
                foreach ($households as $hh):
                ?>
                <tr>
                    <td class="fw-bold text-muted"><?= $hh[0] ?></td>
                    <td class="fw-bold"><?= $hh[1] ?></td>
                    <td><?= $hh[2] ?></td>
                    <td><?= $hh[3] ?></td>
                    <td>
                        <span class="badge bg-info text-dark rounded-pill px-3"><?= $hh[4] ?> Members</span>
                    </td>
                    <td class="text-center">
                        <!-- Admin Privileges: Edit and Delete -->
                        <button class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Household Modal -->
<div class="modal fade" id="addHouseholdModal" tabindex="-1" aria-labelledby="addHouseholdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addHouseholdModalLabel">Add New Household</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="process_household.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Head of Household</label>
                            <input type="text" class="form-control" name="household_head" placeholder="Full Name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Number of Members</label>
                            <input type="number" class="form-control" name="number_of_members" min="1" value="1" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Contact Information</label>
                            <input type="text" class="form-control" name="contact_information" placeholder="e.g. 09123456789">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer mt-4 px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Household</button>
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