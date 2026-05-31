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

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-label text-uppercase pb-2 text-white px-3 mt-3" style="font-size: 1.25rem; font-weight: bold;">
        Resident Menu
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="FPROJ_ResidentDashboard.php" class="nav-link active">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_ResidentIncidentReport.php" class="nav-link">
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
            <h5><i class="bi bi-person-lines-fill me-2 text-success"></i>My Profile</h5>
            <p class="text-muted mb-0">Update and manage your personal resident information.</p>
        </div>
        <div class="text-muted" style="font-size:12px;"><?= date('F d, Y') ?></div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-7 col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold">Personal Information</h6>
                    </div>
                        <div class="card-body p-4">
                         <form action="process_resident_update.php" method="POST">
                            <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Resident ID</label>
                                <input type="text" class="form-control bg-light" name="resident_id" value="<?= htmlspecialchars($resident['resident_id']) ?>" readonly>
                            </div>
                            
                            <div class="col-md-8">
                                <label class="form-label text-muted small fw-bold text-uppercase">Full Name</label>
                                <input type="text" class="form-control" name="resident_name" value="<?= htmlspecialchars($resident['resident_name']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Date of Birth</label>
                                <input type="date" class="form-control" name="date_of_birth" value="<?= htmlspecialchars($resident['date_of_birth']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Gender</label>
                                <select class="form-select" name="gender" required>
                                    <option value="Male" <?= $resident['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= $resident['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= $resident['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold text-uppercase">Contact Information</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="text" class="form-control" name="contact_information" value="<?= htmlspecialchars($resident['contact_information']) ?>" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold text-uppercase">Current Address</label>
                                <textarea class="form-control" name="address" rows="3" required><?= htmlspecialchars($resident['address']) ?></textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light">Discard Changes</button>
                            <button type="submit" class="btn btn-success px-4"><i class="bi bi-floppy me-2"></i>Save Details</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

       <div class="col-12 col-lg-5 col-xl-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-house-door-fill me-2 text-success"></i>Household Information</h6>
                </div>
                <div class="card-body">
                    
                    <?php if ($household_data): ?>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Household Number</label>
                            <span class="fs-6 fw-bold text-success"><?= htmlspecialchars($household_data['household_Id']) ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Household Head</label>
                            <span class="fs-6"><?= htmlspecialchars($household_data['household_head']) ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Registered Address</label>
                            <span class="fs-6"><?= htmlspecialchars($household_data['address']) ?></span>
                        </div>
                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Primary Contact</label>
                            <span class="fs-6"><?= htmlspecialchars($household_data['contact_information']) ?></span>
                        </div>
                        
                        <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                            <label class="text-muted small fw-bold text-uppercase mb-0">Total Members</label>
                            <span class="badge bg-light text-dark border px-3 py-2 fs-6">
                                <?= htmlspecialchars($household_data['number_of_members']) ?> Members
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning text-center mb-0" role="alert">
                            <i class="bi bi-exclamation-circle me-1"></i> No household data found in the database for this ID.
                        </div>
                    <?php endif; ?>

                    <div class="mt-4 text-muted" style="font-size: 11px;">
                        * Contact the barangay administration to update your household headcount or details.
                    </div>
                </div>
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