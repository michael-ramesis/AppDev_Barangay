<?php
session_start();
require_once "dbaseconnection.php";

// Mock Admin Login / Session
$admin = $_SESSION['admin'] ?? [
    'first_name' => 'System',
    'last_name'  => 'Administrator',
    'role'       => 'Admin',
];

$full_name   = $admin['first_name'] . ' ' . $admin['last_name'];
$active_page = $_GET['page'] ?? 'dashboard';

$alert_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. HANDLE VERIFY / DELETE FOR PENDING APPLICATIONS
    if (isset($_POST['action']) && isset($_POST['target_id'])) {
        $targetId = $conn->real_escape_string($_POST['target_id']);

        if ($_POST['action'] === 'verify') {
            $fetchOrigSql = "SELECT * FROM `pending_applications` WHERE `Application_ID` = '$targetId'";
            $origResult = $conn->query($fetchOrigSql);
            $origRow = $origResult->fetch_assoc();

            $dbRole = !empty($origRow['Current_Role']) ? $origRow['Current_Role'] : 'Resident';
            $dbPos = !empty($origRow['Target_Role']) ? $origRow['Target_Role'] : 'Resident';

            $rawRole = trim($_POST['role'] ?? '');
            $rawPosition = trim($_POST['position'] ?? '');
            
            $selectedRole = !empty($rawRole) ? $conn->real_escape_string($rawRole) : $conn->real_escape_string($dbRole);
            $selectedPosition = !empty($rawPosition) ? $conn->real_escape_string($rawPosition) : $conn->real_escape_string($dbPos);
            
            $status = 'Verified by Admin'; 

            $updatesql = "UPDATE `pending_applications` SET `Current_Role` = '$selectedRole', `Target_Role` = '$selectedPosition', `Status` = '$status' WHERE `Application_ID` = '$targetId'";
            
            if ($conn->query($updatesql) === TRUE) {
                $fetchSql = "SELECT * FROM `pending_applications` WHERE `Application_ID` = '$targetId'";
                $fetchResult = $conn->query($fetchSql);
                
                if ($fetchResult && $fetchResult->num_rows > 0) {
                    $row = $fetchResult->fetch_assoc();
                    
                    $appName = $conn->real_escape_string($row['Applicant_Name'] ?? '');
                    $appContact = $conn->real_escape_string($row['Contact_Information'] ?? '');
                    $email = $conn->real_escape_string($row['Email'] ?? '');
                    $pass = $conn->real_escape_string($row['Password'] ?? '');

                    if ($selectedRole === 'Admin' || $selectedRole === 'Employee') {
                        // Added Official_ID to match your database exactly
                        $insertsql = "INSERT INTO `barangay_official_table` (`Official_ID`, `Official_Name`, `Position`, `Contact_Information`, `email`, `password`, `date_verified`) 
                                    VALUES ('$targetId', '$appName', '$selectedPosition', '$appContact', '$email', '$pass', NOW())";
                        
                        if ($conn->query($insertsql) === TRUE) {
                            $alert_message = "<div class='alert alert-success alert-dismissible fade show container mt-3'>Applicant verified! Status changed and added to Barangay Officials.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
                        } else {
                            $alert_message = "<div class='alert alert-warning alert-dismissible fade show container mt-3'>Verified, but error adding to officials table: " . $conn->error . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
                        }
                    } else {
                        $alert_message = "<div class='alert alert-success alert-dismissible fade show container mt-3'>Resident verified successfully!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
                    }
                }
            } else {
                $alert_message = "<div class='alert alert-danger alert-dismissible fade show container mt-3'>Error updating record: " . $conn->error . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
            }
            
        } 
        else if ($_POST['action'] === 'delete') {
            $deletesql = "DELETE FROM pending_applications WHERE Application_ID = '$targetId'";
            if ($conn->query($deletesql) === TRUE) {
                $alert_message = "<div class='alert alert-danger alert-dismissible fade show container mt-3'>Applicant removed successfully.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
            }
        }
    }

    // 2. HANDLE DELETE FOR BARANGAY OFFICIALS TABLE
    if (isset($_POST['action']) && $_POST['action'] === 'delete_official') {
        $delId = $conn->real_escape_string($_POST['delete_official_id']);
        
        $deleteOffSql = "DELETE FROM barangay_official_table WHERE Official_ID = '$delId'";
        if ($conn->query($deleteOffSql) === TRUE) {
            $alert_message = "<div class='alert alert-danger alert-dismissible fade show container mt-3'>Barangay Official removed successfully.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } else {
            $alert_message = "<div class='alert alert-warning alert-dismissible fade show container mt-3'>Error removing official: " . $conn->error . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    }
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
    <div class="logo-circle d-flex align-items-center justify-content-center flex-shrink-0">?</div>
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
        <li class="nav-item"><a href="FPROJ_AdminBarangayOfficials.php" class="nav-link active"><i class="bi bi-person-badge-fill me-2"></i> Barangay Officials</a></li>
        <li class="nav-item"><a href="FPROJ_AdminIncidentReports.php" class="nav-link"><i class="bi bi-exclamation-triangle-fill me-2"></i> Incident Reports</a></li>
        <li class="nav-item"><a href="FPROJ_AdminInfrastructure.php" class="nav-link"><i class="bi bi-cone-striped me-2"></i> Infrastructure Projects</a></li>
        <li class="nav-item"><a href="FPROJ_AdminPending.php" class="nav-link"><i class="bi bi-person-gear me-2"></i> Users Management</a></li>
        <li class="nav-item"><a href="FPROJ_AdminSysLog.php" class="nav-link"><i class="bi bi-journal-text me-2"></i> System Logs</a></li>
    </ul>
</div>

<div class="main-content">
  <?= $alert_message ?>

  <div class="page-header d-flex align-items-center justify-content-between mb-4 mt-3">
    <div>
        <h5><i class="bi bi-person-badge-fill me-2"></i>Barangay Officials Management</h5>
        <p class="text-muted mb-0">View, add, and manage the roster of barangay officials.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfficialModal">
        <i class="bi bi-plus-lg me-1"></i> Add Official
    </button>
  </div>

  <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-bold">Officials Directory</h6>
          <div class="input-group" style="width: 250px;">
              <input type="text" class="form-control form-control-sm" placeholder="Search officials...">
              <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-search"></i></button>
          </div>
      </div>
      <div class="table-responsive p-3">
          <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                  <tr>
                      <th>Official ID</th>
                      <th>Official Name</th>
                      <th>Position</th>
                      <th>Contact Information</th>
                      <th class="text-center">Actions</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                  // Fetch REAL data from the database
                  $officialsQuery = "SELECT * FROM barangay_official_table ORDER BY date_verified DESC";
                  $officialsResult = $conn->query($officialsQuery);

                  if ($officialsResult && $officialsResult->num_rows > 0) {
                      while ($off = $officialsResult->fetch_assoc()) {
                          
                          // Handle missing or null IDs nicely
                          $off_id = !empty($off['Official_ID']) ? htmlspecialchars($off['Official_ID']) : 'N/A';
                          $position = htmlspecialchars($off['Position']);
                          
                          // Set Badge colors dynamically based on position
                          $badgeClass = 'bg-secondary';
                          if (strpos(strtolower($position), 'captain') !== false || strpos(strtolower($position), 'chairman') !== false) {
                              $badgeClass = 'bg-success';
                          } elseif (strpos(strtolower($position), 'kagawad') !== false) {
                              $badgeClass = 'bg-primary';
                          }

                          echo "<tr>
                                  <td class='fw-bold text-muted'>{$off_id}</td>
                                  <td class='fw-bold'>" . htmlspecialchars($off['Official_Name']) . "</td>
                                  <td>
                                      <span class='badge {$badgeClass}'>{$position}</span>
                                  </td>
                                  <td>" . htmlspecialchars($off['Contact_Information']) . "</td>
                                  <td class='text-center'>
                                      <form method='POST' action='' style='display: inline;'>
                                          <input type='hidden' name='delete_official_id' value='{$off_id}'>
                                          
                                          <button type='button' class='btn btn-sm btn-outline-primary' title='Edit'>
                                              <i class='bi bi-pencil-square'></i>
                                          </button>
                                          
                                          <button type='submit' name='action' value='delete_official' class='btn btn-sm btn-outline-danger' title='Delete' onclick=\"return confirm('Are you sure you want to remove this official?');\">
                                              <i class='bi bi-trash'></i>
                                          </button>
                                      </form>
                                  </td>
                                </tr>";
                      }
                  } else {
                      echo "<tr><td colspan='5' class='text-center text-muted'>No officials found.</td></tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
</div>

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
                                <option value="Barangay Captain">Barangay Captain</option>
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
            main.style.marginLeft = '190px'; 
        } else {
            sidebar.style.display = 'none';
            main.style.marginLeft = '0';
        }
    });
</script>
</body>
</html>