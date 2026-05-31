<?php
require_once 'dbaseconnection.php';
$active_page = basename($_SERVER['PHP_SELF']);

$menu = [
    'FPROJ_Admin.php'                  => ['icon' => 'bi-speedometer2',              'label' => 'Dashboard'],
    'FPROJ_AdminPending.php'         => ['icon' => 'bi-people-fill',               'label' => 'Residents'],
    'FPROJ_AdminHouseholds.php'        => ['icon' => 'bi-house-fill',                'label' => 'Households'],
    'FPROJ_AdminBarangayOfficials.php' => ['icon' => 'bi-person-badge-fill',         'label' => 'Barangay Officials'],
    'FPROJ_AdminIncidentReports.php'   => ['icon' => 'bi-exclamation-triangle-fill', 'label' => 'Incident Reports'],
    'FPROJ_AdminInfrastructure.php'    => ['icon' => 'bi-cone-striped',              'label' => 'Infrastructure Projects'],
    'FPROJ_AdminPending.php'    => ['icon' => 'bi-person-gear',               'label' => 'Users Management'],
    'FPROJ_AdminSysLog.php'            => ['icon' => 'bi-journal-text',              'label' => 'System Logs'],
];
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
            <a href="FPROJ_AdminPending.php" class="nav-link">
                <i class="bi bi-person-gear me-2"></i> Users Management
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_AdminSysLog.php" class="nav-link active">
                <i class="bi bi-journal-text me-2"></i> System Logs
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
<!-- Header -->
  <div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5><i class="bi bi-journal-text me-2 text-secondary"></i>System Logs</h5>
        <p class="text-muted mb-0">Monitor system activity, user actions, and security events.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary">
            <i class="bi bi-download me-1"></i> Export CSV
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Audit Trail</h6>
        <div class="d-flex gap-2">
            <input type="date" class="form-control form-control-sm" style="max-width: 150px;" title="Filter by Date">
            <div class="input-group" style="width: 250px;">
                <input type="text" class="form-control form-control-sm" placeholder="Search logs...">
                <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </div>
    <div class="table-responsive p-3">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Log ID</th>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Action Performed</th>
                    <th class="text-center">Details</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $sql = "select log_id, action, datetime, Application_ID, Full_Name from tbl_logs order by datetime DESC";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0):
                    // 3. Loop through the actual database rows
                    while($row = $result->fetch_assoc()):
                        // Handle empty values since your screenshot shows some NULLs
                        $logId = htmlspecialchars($row['log_id']);
                        $timestamp = htmlspecialchars($row['datetime']);
                        
                        // If Full_Name is null/empty, label it as 'System' or 'Unknown'
                        $user = !empty($row['Full_Name']) ? htmlspecialchars($row['Full_Name']) : 'System';
                        
                        // Action formatting (Attach Application ID if it exists)
                        $action = htmlspecialchars($row['action']);
                        if (!empty($row['Application_ID'])) {
                            $action .= " (App ID: " . htmlspecialchars($row['Application_ID']) . ")";
                        }

                        // Note: Role is not in tbl_logs in your screenshot, so setting a default badge
                        $role = ($user === 'System') ? 'System' : 'User';
                        $roleBadge = ($role === 'System') ? 'bg-secondary' : 'bg-primary';
                ?>
                <tr>
                    <td class="text-muted" style="font-size: 0.9em;"><?= $logId ?></td>
                    <td class="text-muted" style="font-size: 0.9em;"><?= $timestamp ?></td>
                    <td class="fw-bold"><?= $user ?></td>
                    <td>
                        <span class="badge <?= $roleBadge ?>"><?= $role ?></span>
                    </td>
                    <td><?= $action ?></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewLogModal" title="View Technical Details">
                            <i class="bi bi-info-circle"></i>
                        </button>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No system logs found.</td>
                </tr>
                <?php 
                endif; 
                
                // Close connection
                $conn->close();
                ?>
                </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3 border-top">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm justify-content-end mb-0">
                <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav>
    </div>
</div>

<div class="modal fade" id="viewLogModal" tabindex="-1" aria-labelledby="viewLogModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="viewLogModalLabel"><i class="bi bi-terminal me-2"></i>Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="fw-bold text-muted small text-uppercase">Action Data (JSON representation)</label>
                    <div class="p-3 bg-dark text-success rounded mt-1" style="font-family: monospace; font-size: 0.85em;">
                        {<br>
                        &nbsp;&nbsp;"action_type": "CREATE",<br>
                        &nbsp;&nbsp;"table_affected": "Resident Table",<br>
                        &nbsp;&nbsp;"record_id": "RES-004",<br>
                        &nbsp;&nbsp;"ip_address": "192.168.1.105",<br>
                        &nbsp;&nbsp;"browser": "Chrome/124.0.0.0"<br>
                        }
                    </div>
                </div>
                <div class="text-muted" style="font-size: 0.85em;">
                    <strong>Note:</strong> Log records are immutable and cannot be deleted individually.
                </div>
            </div>
            <div class="modal-footer pb-0 px-0 mt-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Official Modal -->
<div class="modal fade" id="addOfficialModal" tabindex="-1" aria-labelledby="addOfficialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOfficialModalLabel">Add New Official</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="process_official.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Official Name</label>
                            <input type="text" class="form-control" name="official_name" placeholder="Full Name" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Position</label>
                            <select class="form-select" name="position" required>
                                <option value="" selected disabled>Select Position...</option>
                                <option value="Barangay Chairman">Barangay Chairman</option>
                                <option value="Barangay Kagawad">Barangay Kagawad</option>
                                <option value="Barangay Secretary">Barangay Secretary</option>
                                <option value="Barangay Treasurer">Barangay Treasurer</option>
                                <option value="SK Chairperson">SK Chairperson</option>
                                <option value="SK Kagawad">SK Kagawad</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Contact Information</label>
                            <input type="text" class="form-control" name="contact_information" placeholder="e.g. 09123456789" required>
                        </div>
                    </div>
                    <div class="modal-footer mt-4 px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Official</button>
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