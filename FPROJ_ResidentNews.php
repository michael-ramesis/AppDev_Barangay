<?php
session_start();
require_once 'dbaseconnection.php';


$current_resident_id = $_SESSION['Resident_Id'];

$resident = null;

$query = "SELECT Resident_Id, Resident_Name, Date_of_birth, Gender, Contact_information, Address 
          FROM resident_table 
          WHERE Resident_Id = ?";

$stmt_resident = $conn->prepare($query);

if ($stmt_resident) {
    $stmt_resident->bind_param("s", $current_resident_id);
    $stmt_resident->execute();
    $result_resident = $stmt_resident->get_result();
    
    if ($result_resident->num_rows > 0) {
        $db_row = $result_resident->fetch_assoc();

        $resident = [
            'resident_id'         => $db_row['Resident_Id'],
            'resident_name'       => $db_row['Resident_Name'],
            'date_of_birth'       => $db_row['Date_of_birth'],
            'gender'              => $db_row['Gender'],
            'contact_information' => $db_row['Contact_information'],
            'address'             => $db_row['Address'],
            'role'                => 'Resident' 
        ];
    }
    $stmt_resident->close();
}

if (!$resident) {
    $resident = [
        'resident_id' => '', 'resident_name' => 'Resident Not Found', 'date_of_birth' => '',
        'gender' => '', 'contact_information' => '', 'address' => '', 'role' => 'Guest'
    ];
}

$household_data = null;
$current_household_id = 1;

$stmt_house = $conn->prepare("SELECT household_Id, household_head, address, contact_information, number_of_members FROM household_table WHERE household_Id = ?");
if ($stmt_house) {
    $stmt_house->bind_param("i", $current_household_id);
    $stmt_house->execute();
    $result_house = $stmt_house->get_result();
    if ($result_house->num_rows > 0) {
        $household_data = $result_house->fetch_assoc();
    }
    $stmt_house->close();
}

$active_page = $_GET['page'] ?? 'profile';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS2609 Final Project - Resident Portal</title>
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
    <span class="role-badge bg-success"><?= htmlspecialchars($resident['role']) ?></span>
    <span class="user-name"><?= htmlspecialchars($resident['resident_name']) ?></span>
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
            <a href="FPROJ_ResidentIncidentReport.php" class="nav-link">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Report an Incident
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_ResidentNews.php" class="nav-link active">
                <i class="bi bi-newspaper me-2"></i> News & Projects
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5><i class="bi bi-newspaper me-2 text-primary"></i>Barangay News & Updates</h5>
            <p class="text-muted mb-0">Stay informed about community projects and important public advisories.</p>
        </div>
        <div class="text-muted" style="font-size:12px;"><?= date('F d, Y') ?></div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <h6 class="fw-bold mb-3"><i class="bi bi-megaphone-fill me-2 text-danger"></i>Public Advisories & Incidents</h6>
            
            <?php
            $advisories = [
                [
                    'type' => 'Security Alert',
                    'title' => 'Increase in Theft Incidents in Blk 3',
                    'date' => '2024-05-18',
                    'content' => 'Please be advised that there have been two reported incidents of theft in Block 3 over the past week. Residents are reminded to lock their gates and ensure their properties are secure.',
                    'icon' => 'bi-shield-exclamation',
                    'bg' => 'bg-danger'
                ],
                [
                    'type' => 'Community Advisory',
                    'title' => 'Noise Regulations Reminder',
                    'date' => '2024-05-14',
                    'content' => 'Following several complaints, we would like to remind all residents that the use of karaoke and loud music must be stopped by 10:00 PM as per the barangay ordinance.',
                    'icon' => 'bi-volume-up-fill',
                    'bg' => 'bg-warning text-dark'
                ]
            ];
            
            foreach ($advisories as $adv):
            ?>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge <?= $adv['bg'] ?>"><i class="bi <?= $adv['icon'] ?> me-1"></i><?= $adv['type'] ?></span>
                        <small class="text-muted"><?= date('M d, Y', strtotime($adv['date'])) ?></small>
                    </div>
                    <h6 class="fw-bold"><?= $adv['title'] ?></h6>
                    <p class="text-muted small mb-0"><?= $adv['content'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="col-12 col-lg-6">
            <h6 class="fw-bold mb-3"><i class="bi bi-cone-striped me-2 text-warning"></i>Infrastructure Projects</h6>
            
            <?php
            $projects = [];
            // Assuming table name matches your requirement
            $query = "SELECT project_name, project_description, start_date, end_date, project_status FROM infrastructure_project_table ORDER BY start_date DESC";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $projects[] = $row;
                }
            } else {
                echo '<p class="text-muted small">No infrastructure projects to display at this time.</p>';
            }
            
            foreach ($projects as $proj):
                $statusBadge = 'bg-secondary';
                if (strcasecmp($proj['project_status'], 'Completed') == 0) $statusBadge = 'bg-success';
                if (strcasecmp($proj['project_status'], 'Ongoing') == 0) $statusBadge = 'bg-primary';
                if (strcasecmp($proj['project_status'], 'Planned') == 0) $statusBadge = 'bg-info text-dark';
            ?>
            <div class="card border-0 shadow-sm mb-3 border-start border-4 <?= str_replace('bg-', 'border-', $statusBadge) ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0"><?= htmlspecialchars($proj['project_name']) ?></h6>
                        <span class="badge <?= $statusBadge ?>"><?= htmlspecialchars($proj['project_status']) ?></span>
                    </div>
                    <p class="text-muted small mb-2"><?= htmlspecialchars($proj['project_description']) ?></p>
                    <div class="d-flex text-muted" style="font-size: 0.75rem;">
                        <span class="me-3"><i class="bi bi-calendar-check me-1"></i><strong>Start:</strong> <?= date('M d, Y', strtotime($proj['start_date'])) ?></span>
                        <span><i class="bi bi-flag-fill me-1"></i><strong>Target:</strong> <?= date('M d, Y', strtotime($proj['end_date'])) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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