<?php
session_start();
require_once 'dbaseconnection.php';

// 1. SESSION CHECK
if (!isset($_SESSION['Resident_Id'])) {
    header("Location: login.php");
    exit();
}

$current_resident_id = $_SESSION['Resident_Id'];
$resident_data = null; 

// 2. FETCH RESIDENT DATA (For the Topbar Profile)
$query_res = "SELECT Resident_Id, Resident_Name FROM resident_table WHERE Resident_Id = ?";
$stmt_resident = $conn->prepare($query_res);

if ($stmt_resident) {
    $stmt_resident->bind_param("s", $current_resident_id);
    $stmt_resident->execute();
    $result_resident = $stmt_resident->get_result();
    
    if ($result_resident->num_rows > 0) {
        $resident_data = $result_resident->fetch_assoc();
        $resident_data['role'] = 'Resident';
    }
    $stmt_resident->close();
}

// Fallback to prevent UI crash
if (!$resident_data) {
    $resident_data = ['Resident_Id' => $current_resident_id, 'Resident_Name' => 'Resident', 'role' => 'Guest'];
}

// 3. SUBMISSION LOGIC
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_report'])) {
    $inc_type = $_POST['incident_type'] ?? '';
    $date_rep = $_POST['date_reported'] ?? '';
    $details  = $_POST['incident_details'] ?? '';
    $res_id   = $resident_data['Resident_Id'];

    if (!empty($inc_type) && !empty($date_rep) && !empty($details)) {
        // MATCHING DB EXACTLY
        $insert_query = "INSERT INTO incident_report_table (Resident_Id, Incident_type, Date_reported, Incident_details, status) VALUES (?, ?, ?, ?, 'Pending')";
        $stmt_ins = $conn->prepare($insert_query);
        
        if ($stmt_ins) {
            $stmt_ins->bind_param("ssss", $res_id, $inc_type, $date_rep, $details);
            if ($stmt_ins->execute()) {
                $_SESSION['system_message'] = "Your incident report has been submitted successfully.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['system_message'] = "Database Error: " . $stmt_ins->error;
                $_SESSION['message_type'] = "danger";
            }
            $stmt_ins->close();
        }
        header("Location: FPROJ_ResidentIncidentReport.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Reporting - Resident Portal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<nav class="topbar d-flex align-items-center px-3 gap-3">
    <div class="logo-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-success text-white">R</div>
    <button class="btn btn-sm btn-link text-white p-0" id="sidebarToggle">
        <i class="bi bi-list fs-4"></i>
    </button>
    <span class="topbar-title flex-grow-1">Barangay Resident Portal</span>
    <span class="role-badge bg-success"><?= htmlspecialchars($resident_data['role']) ?></span>
    <span class="user-name"><?= htmlspecialchars($resident_data['Resident_Name']) ?></span>
    <i class="bi bi-caret-down-fill text-white" style="font-size:10px;"></i>
</nav>

<div class="sidebar" id="sidebar">
    <div class="sidebar-label text-uppercase pb-2 text-white px-3 mt-3" style="font-size: 1.25rem; font-weight: bold;">
        Resident Menu
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="FPROJ_ResidentDashboard.php" class="nav-link">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_ResidentIncidentReport.php" class="nav-link active">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Report an Incident
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_ResidentNews.php" class="nav-link">
                <i class="bi bi-newspaper me-2"></i> News & Projects
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5><i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Incident Reporting</h5>
            <p class="text-muted mb-0">File a new report or view all community reports.</p>
        </div>
        <button class="btn btn-danger px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newIncidentModal">
            <i class="bi bi-plus-lg me-2"></i>File New Report
        </button>
    </div>

    <?php if (isset($_SESSION['system_message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['system_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['system_message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 fw-bold">Community Incident Reports</h6>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Report ID</th>
                        <th>Incident Type</th>
                        <th>Date Reported</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 🔴 FIXED: Removed the 'WHERE Resident_Id = ?' clause so it grabs ALL reports for EVERYONE.
                    $query_list = "SELECT report_id, Incident_type, Incident_details, Date_reported, status 
                                   FROM incident_report_table 
                                   ORDER BY Date_reported DESC";
                    
                    // We can use a simpler query execution since there are no parameters anymore
                    $result_list = $conn->query($query_list);

                    if ($result_list && $result_list->num_rows > 0):
                        while ($rep = $result_list->fetch_assoc()):
                            $statusBadge = 'bg-secondary';
                            if (strcasecmp($rep['status'], 'Resolved') == 0) $statusBadge = 'bg-success';
                            if (strcasecmp($rep['status'], 'Under Review') == 0) $statusBadge = 'bg-warning text-dark';
                            if (strcasecmp($rep['status'], 'Pending') == 0) $statusBadge = 'bg-danger';
                    ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= htmlspecialchars($rep['report_id']) ?></td>
                        <td><?= htmlspecialchars($rep['Incident_type']) ?></td>
                        <td><?= date('M d, Y', strtotime($rep['Date_reported'])) ?></td>
                        <td style="max-width: 250px;">
                            <div class="text-truncate" title="<?= htmlspecialchars($rep['Incident_details']) ?>">
                                <?= htmlspecialchars($rep['Incident_details']) ?>
                            </div>
                        </td>
                        <td><span class="badge <?= $statusBadge ?>"><?= htmlspecialchars($rep['status']) ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> View</button>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i> No incident reports found.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="newIncidentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">File a New Incident Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Incident Type</label>
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
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Date of Incident</label>
                            <input type="date" class="form-control" name="date_reported" max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-bold">Incident Details</label>
                            <textarea class="form-control" name="incident_details" rows="5" placeholder="Provide specific details..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer mt-4 px-0 pb-0 border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="submit_report" class="btn btn-danger px-4">Submit Report</button>
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
        sidebar.classList.toggle('d-none');
        main.style.marginLeft = sidebar.classList.contains('d-none') ? '0' : '190px';
    });
</script>
</body>
</html>