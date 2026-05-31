<?php
session_start();
require_once "dbaseconnection.php";

// FIX 1: Safely initialize $admin to prevent undefined variable errors
$admin = $_SESSION['admin'] ?? [
    'first_name' => 'System', 
    'last_name'  => 'Admin', 
    'role'       => 'Admin'
];

$full_name     = $admin['first_name'] . ' ' . $admin['last_name'];
$alert_message = "";

// Only process if a POST request is made
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // FIX 2: HANDLE "DELETE" LOGIC (Separated from Add)
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $targetId = $_POST['resident_id'];
        
        // Raw delete query as requested
        $deleteSql = "DELETE FROM resident_table WHERE Resident_Id = '$targetId'";
        
        if ($conn->query($deleteSql) === TRUE) {
            $alert_message = "<div class='alert alert-danger alert-dismissible fade show container mt-3' role='alert'>
                                Resident removed successfully.
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                              </div>";
        } else {
            $alert_message = "<div class='alert alert-warning alert-dismissible fade show container mt-3' role='alert'>
                                Error deleting resident: " . $conn->error . "
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                              </div>";
        }
    }

    // FIX 3: HANDLE "ADD RESIDENT" LOGIC
    if (isset($_POST['save_resident'])) {
        
        // Generate unique ID: BRGY-2026-XXXXXX
        $unique_user_id = 'BRGY-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));

        // Raw POST data as requested (No security measures)
        $name    = $_POST['resident_name'];
        $dob     = $_POST['date_of_birth'];
        $gender  = $_POST['gender'];
        $contact = $_POST['contact_information'];
        $address = $_POST['address'];

        $insertSql = "INSERT INTO resident_table (Resident_Id, Resident_Name, Date_of_birth, Gender, Contact_information, Address) 
                      VALUES ('$unique_user_id', '$name', '$dob', '$gender', '$contact', '$address')";

        if ($conn->query($insertSql) === TRUE) {
            $alert_message = "<div class='alert alert-success alert-dismissible fade show container mt-3' role='alert'>
                                New resident added successfully! ID: <strong>$unique_user_id</strong>
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                              </div>";
        } else {
            $alert_message = "<div class='alert alert-danger alert-dismissible fade show container mt-3' role='alert'>
                                Error: " . $conn->error . "
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                              </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS2609 - Resident Management</title>
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
        <li class="nav-item">
            <a href="FPROJ_Admin.php" class="nav-link">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_AdminResidents.php" class="nav-link active"> 
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
            <a href="FPROJ_AdminSysLog.php" class="nav-link">
                <i class="bi bi-journal-text me-2"></i> System Logs
            </a>
        </li>
    </ul>
</div>

<div class="main-content p-4">
    <?= $alert_message ?>

    <div class="page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5><i class="bi bi-people-fill me-2"></i>Resident Management</h5>
            <p class="text-muted mb-0">View, add, and manage barangay resident records.</p>
        </div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addResidentModal">
            <i class="bi bi-plus-circle me-1"></i> Add Resident
        </button>
    </div>

    <form action="" method="POST" class="mb-3 d-flex gap-2">
        <input type="text" name="searchinput" class="form-control w-25" placeholder="Search Resident Name...">
        <button type="submit" name="btnsearch" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
    </form>

    <div class="table-responsive bg-white rounded shadow-sm p-3">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-dark text-center">
                <tr>
                    <th>Resident ID</th>
                    <th>Resident Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Contact Information</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    <?php
    $result = $conn->query("SELECT * FROM resident_table");
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='text-center'>
                <td>" . htmlspecialchars($row['Resident_Id']) . "</td>
                <td>" . htmlspecialchars($row['Resident_Name']) . "</td>
                <td>" . htmlspecialchars($row['Date_of_birth']) . "</td>
                <td>" . htmlspecialchars($row['Gender']) . "</td>
                <td>" . htmlspecialchars($row['Contact_information']) . "</td>
                <td>" . htmlspecialchars($row['Address']) . "</td>
                <td>
                    <form method='POST' action='' style='display: inline;'>
                        <input type='hidden' name='resident_id' value='" . htmlspecialchars($row['Resident_Id']) . "'>
                        
                        <div class='btn-group'>
                            <button type='button' class='btn btn-sm btn-primary'>Edit</button>
                            <button type='submit' name='action' value='delete' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this resident?');\">Delete</button>
                        </div>
                    </form>
                </td>
              </tr>";
    }
    ?>
</tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addResidentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Resident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Resident Name</label>
                            <input type="text" class="form-control" name="resident_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender" required>
                                <option value="" selected disabled>Select Gender...</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Information</label>
                            <input type="text" class="form-control" name="contact_information">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer mt-4 px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="save_resident" class="btn btn-primary">Save Record</button>
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