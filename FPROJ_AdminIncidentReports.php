<?php
session_start();
require_once 'dbaseconnection.php';

// Mock Admin Login (Retained as requested)
$admin = [
    'first_name' => 'System',
    'last_name'  => 'Administrator',
    'role'       => 'Admin',
];

$full_name   = $admin['first_name'] . ' ' . $admin['last_name'];
$active_page = $_GET['page'] ?? 'dashboard';

// --- HANDLE DELETE REQUEST ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_report_id'])) {
    $del_id = $_POST['delete_report_id'];
    
    $del_query = "DELETE FROM incident_report_table WHERE report_id = ?";
    $stmt_del = $conn->prepare($del_query);
    if ($stmt_del) {
        $stmt_del->bind_param("i", $del_id);
        if ($stmt_del->execute()) {
            $_SESSION['sys_msg'] = "Incident report #$del_id has been successfully deleted.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['sys_msg'] = "Error deleting report: " . $conn->error;
            $_SESSION['msg_type'] = "danger";
        }
        $stmt_del->close();
    }
    header("Location: FPROJ_AdminIncidentReports.php");
    exit();
}

// --- HANDLE LOG NEW INCIDENT ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['log_incident'])) {
    $res_id   = $_POST['resident_id'];
    $inc_type = $_POST['incident_type'];
    $date_rep = $_POST['date_reported'];
    $details  = $_POST['incident_details'];

    $insert_query = "INSERT INTO incident_report_table (Resident_Id, Incident_type, Date_reported, Incident_details, status) VALUES (?, ?, ?, ?, 'Pending')";
    $stmt_ins = $conn->prepare($insert_query);
    
    if ($stmt_ins) {
        $stmt_ins->bind_param("ssss", $res_id, $inc_type, $date_rep, $details);
        if ($stmt_ins->execute()) {
            $_SESSION['sys_msg'] = "New incident logged successfully.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['sys_msg'] = "Error logging incident: " . $conn->error;
            $_SESSION['msg_type'] = "danger";
        }
        $stmt_ins->close();
    }
    header("Location: FPROJ_AdminIncidentReports.php");
    exit();
}
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

<nav class="topbar d-flex align-items-center px-3 gap-3">
    <div class="logo-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-danger text-white">A</div>
    <button class="btn btn-sm btn-link text-white p-0" id="sidebarToggle">
        <i class="bi bi-list fs-4"></i>
    </button>
    <span class="topbar-title flex-grow-1">Barangay Record Management System</span>
    <span class="role-badge bg-danger"><?= htmlspecialchars($admin['role'] ?? 'Admin') ?></span>
    <span class="user-name"><?= htmlspecialchars($full_name ?? 'System Administrator') ?></span>
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
        <li class="nav-item"><a href="FPROJ_AdminIncidentReports.php" class="nav-link active"><i class="bi bi-exclamation-triangle-fill me-2"></i> Incident Reports</a></li>
        <li class="nav-item"><a href="FPROJ_AdminInfrastructure.php" class="nav-link"><i class="bi bi-cone-striped me-2"></i> Infrastructure Projects</a></li>
        <li class="nav-item"><a href="FPROJ_AdminPending.php" class="nav-link"><i class="bi bi-person-gear me-2"></i> Users Management</a></li>
        <li class="nav-item"><a href="FPROJ_AdminSysLog.php" class="nav-link"><i class="bi bi-journal-text me-2"></i> System Logs</a></li>
    </ul>
</div>

<div class="main-content">
<div class="page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5><i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Incident Reports Management</h5>
            <p class="text-muted mb-0">View, log, and manage incidents reported within the barangay.</p>
        </div>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addIncidentModal">
            <i class="bi bi-plus-lg me-1"></i> Log Incident
        </button>
    </div>

    <?php if (isset($_SESSION['sys_msg'])): ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['sys_msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['sys_msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Incident Log</h6>
            <div class="input-group" style="width: 250px;">
                <input type="text" class="form-control form-control-sm" placeholder="Search incidents...">
                <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-search"></i></button>
            </div>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Report ID</th>
                        <th>Reported By (Resident)</th>
                        <th>Incident Type</th>
                        <th>Status</th>
                        <th>Date Reported</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch LIVE data connecting incident table with resident table to get names
                    $query_list = "SELECT i.report_id, r.Resident_Name, i.Incident_type, i.Date_reported, i.status 
                                   FROM incident_report_table i
                                   LEFT JOIN resident_table r ON i.Resident_Id = r.Resident_Id
                                   ORDER BY i.Date_reported DESC";
                    $result_list = $conn->query($query_list);

                    if ($result_list && $result_list->num_rows > 0):
                        while ($inc = $result_list->fetch_assoc()):
                            $statusBadge = 'bg-secondary';
                            if (strcasecmp($inc['status'], 'Resolved') == 0) $statusBadge = 'bg-success';
                            if (strcasecmp($inc['status'], 'Under Review') == 0) $statusBadge = 'bg-warning text-dark';
                            if (strcasecmp($inc['status'], 'Pending') == 0) $statusBadge = 'bg-danger';
                    ?>
                    <tr>
                        <td class="fw-bold text-muted">#<?= htmlspecialchars($inc['report_id']) ?></td>
                        <td><?= htmlspecialchars($inc['Resident_Name'] ?? 'Unknown Resident') ?></td>
                        <td><?= htmlspecialchars($inc['Incident_type']) ?></td>
                        <td><span class="badge <?= $statusBadge ?>"><?= htmlspecialchars($inc['status']) ?></span></td>
                        <td><?= date('M d, Y', strtotime($inc['Date_reported'])) ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm btn-outline-info" title="View Details"><i class="bi bi-eye"></i></button>
                                
                                <form method="POST" action="" onsubmit="return confirm('Are you sure you want to completely delete this incident report? This action cannot be undone.');">
                                    <input type="hidden" name="delete_report_id" value="<?= htmlspecialchars($inc['report_id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No incident reports found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addIncidentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Log New Incident</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Reported By (Resident)</label>
                            <select class="form-select" name="resident_id" required>
                                <option value="" selected disabled>Select Resident...</option>
                                <?php
                                // Fetch real residents for the dropdown
                                $res_query = "SELECT Resident_Id, Resident_Name FROM resident_table ORDER BY Resident_Name ASC";
                                $res_result = $conn->query($res_query);
                                if ($res_result && $res_result->num_rows > 0) {
                                    while ($row = $res_result->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($row['Resident_Id']) . '">' . htmlspecialchars($row['Resident_Name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Incident Type</label>
                            <select class="form-select" name="incident_type" required>
                                <option value="" selected disabled>Select Category...</option>
                                <option value="Theft / Robbery">Theft / Robbery</option>
                                <option value="Noise Complaint">Noise Complaint</option>
                                <option value="Property Damage">Property Damage</option>
                                <option value="Stray Animals">Stray Animals</option>
                                <option value="Public Disturbance">Public Disturbance</option>
                                <option value="Waste / Sanitation">Waste / Sanitation Issue</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Date Reported</label>
                            <input type="date" class="form-control" name="date_reported" max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Incident Details</label>
                            <textarea class="form-control" name="incident_details" rows="4" placeholder="Provide a detailed description of the incident..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer mt-4 px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="log_incident" class="btn btn-danger">Submit Report</button>
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
            main.style.marginLeft = '190px'; 
        } else {
            sidebar.style.display = 'none';
            main.style.marginLeft = '0';
        }
    });
</script>
</body>
</html>