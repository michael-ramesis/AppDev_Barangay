<?php
session_start();
$fullname    = $_SESSION['reg_fullname'] ?? '';
$email       = $_SESSION['reg_email'] ?? '';
$password    = $_SESSION['reg_password'] ?? ''; 
$contact     = $_SESSION['reg_contact'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS2609 Final Project</title>
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
        <li class="nav-item">
            <a href="FPROJ_Admin.php" class="nav-link">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="FPROJ_AdminPending.php" class="nav-link">
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

<div class="main-content p-4">

<?php
require_once "dbaseconnection.php";

$alert_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['target_id'])) {

        $targetId = $conn->real_escape_string($_POST['target_id']);

        $fetchOrigSql = "SELECT * FROM `pending_applications` WHERE `Application_ID` = '$targetId'";
        $origResult = $conn->query($fetchOrigSql);
        $origRow = $origResult->fetch_assoc();

        $dbRole = !empty($origRow['Current_Role']) ? $origRow['Current_Role'] : 'Resident';
        $dbPos = !empty($origRow['Target_Role']) ? $origRow['Target_Role'] : 'Resident';

        $rawRole = trim($_POST['role'] ?? '');
        $rawPosition = trim($_POST['position'] ?? '');
        
        $selectedRole = !empty($rawRole) ? $conn->real_escape_string($rawRole) : $conn->real_escape_string($dbRole);
        $selectedPosition = !empty($rawPosition) ? $conn->real_escape_string($rawPosition) : $conn->real_escape_string($dbPos);

        if ($_POST['action'] === 'verify') {
            
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
                    // Added `Official_Id` to columns and `'$targetId'` to the values
                    $insertsql = "INSERT INTO `barangay_official_table` (`Official_Id`, `Official_Name`, `Position`, `Contact_Information`, `email`, `password`, `date_verified`) 
                                VALUES ('$targetId', '$appName', '$selectedPosition', '$appContact', '$email', '$pass', NOW())";
                        
                        if ($conn->query($insertsql) === TRUE) {
                            $alert_message = "<div class='alert alert-success container mt-3'>Applicant verified! Status changed to 'Verified by Admin' and added to the Barangay Officials table.</div>";
                        } else {
                            $alert_message = "<div class='alert alert-warning container mt-3'>Applicant verified, but error adding to officials table: " . $conn->error . "</div>";
                        }
                    } else {
                        $alert_message = "<div class='alert alert-success container mt-3'>Resident verified successfully! Status changed to 'Verified by Admin'.</div>";
                    }
                } else {
                    $alert_message = "<div class='alert alert-danger container mt-3'>Error: Could not retrieve applicant data.</div>";
                }

            } else {
                $alert_message = "<div class='alert alert-danger container mt-3'>Error updating record: " . $conn->error . "</div>";
            }
            
        } 
        else if ($_POST['action'] === 'delete') {

            $deletesql = "DELETE FROM pending_applications WHERE Application_ID = '$targetId'";
            
            if ($conn->query($deletesql) === TRUE) {
                $alert_message = "<div class='alert alert-danger container mt-3'>Applicant removed successfully.</div>";
            }
        }
    }
}

if (isset($_POST['btnsearch']) && !empty(trim($_POST['searchinput']))) {
    $safeSearchInput = $conn->real_escape_string(trim($_POST['searchinput']));
    $searchinput = "%" . $safeSearchInput . "%";
    $displaysql = "select * from pending_applications where Applicant_Name like '$searchinput'";
    $result = $conn->query($displaysql);
} else {
    $displaysql = "select * from pending_applications";
    $result = $conn->query($displaysql);
}

echo $alert_message;
?>

    <table class="table table-light table-bordered align-middle text-center">
        <thead>
            <tr class="table-dark">
                <th>Full Name</th>
                <th>Date of Application</th>
                <th>Gender</th>
                <th>Contact Information</th>
                <th>Address</th>
                <th>Current Role</th>
                <th>Target Role</th>
                <th>Change Role</th>
                <th>Set Position</th>
                <th>Status</th>
                <th>Actions</th>  
            </tr>
        </thead>
        <tbody>
<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Remove spaces from ID to ensure the HTML ID attribute is valid
        $appId = trim($row['Application_ID']); 
        
        $currentRole = !empty($row['Current_Role']) ? $row['Current_Role'] : 'Pending'; 
        $targetRole = !empty($row['Target_Role']) ? $row['Target_Role'] : 'Resident';
        $suggestedRole = ($currentRole === 'Pending') ? $targetRole : $currentRole;
        
        $status = $row['Status'] ?? 'Pending';
        // Check if already verified
        $isCompleted = ($status === 'Verified by Admin' || $status === 'Verified');

        // Logic for Badge Color
        $roleBadgeClass = 'bg-secondary';
        if ($currentRole === 'Resident') $roleBadgeClass = 'bg-success';
        elseif ($currentRole === 'Employee' || $currentRole === 'Admin') $roleBadgeClass = 'bg-primary';
        ?>
        
        <tr>
            <td><?= htmlspecialchars($row['Applicant_Name']) ?></td>
            <td><?= htmlspecialchars($row['Date_of_Application']) ?></td>
            <td><?= htmlspecialchars($row['Gender']) ?></td>
            <td><?= htmlspecialchars($row['Contact_Information']) ?></td>
            <td><?= htmlspecialchars($row['Address']) ?></td>
            <td><span class='badge <?= $roleBadgeClass ?>'><?= htmlspecialchars($currentRole) ?></span></td> 
            <td><?= htmlspecialchars($targetRole) ?></td>

            <form method="POST" action="">
                <input type="hidden" name="target_id" value="<?= $appId ?>">
                
                <td>
                    <select name='role' class='form-select form-select-sm' <?= $isCompleted ? 'disabled' : '' ?>>
                        <option value='Admin' <?= $suggestedRole == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        <option value='Employee' <?= $suggestedRole == 'Employee' ? 'selected' : '' ?>>Employee</option>
                        <option value='Resident' <?= ($suggestedRole == 'Resident' || $suggestedRole == 'Pending') ? 'selected' : '' ?>>Resident</option>
                    </select>
                </td>
                
                <td>
                    <select name='position' class='form-select form-select-sm' <?= $isCompleted ? 'disabled' : '' ?>>
                        <option value='Resident' <?= $targetRole == 'Resident' ? 'selected' : '' ?>>Resident</option>
                        <option value='Barangay Captain' <?= $targetRole == 'Barangay Captain' ? 'selected' : '' ?>>Barangay Captain</option>
                        <option value='Kagawad' <?= $targetRole == 'Kagawad' ? 'selected' : '' ?>>Kagawad</option>
                        <option value='Secretary' <?= $targetRole == 'Secretary' ? 'selected' : '' ?>>Secretary</option>
                        <option value='Treasurer' <?= $targetRole == 'Treasurer' ? 'selected' : '' ?>>Treasurer</option>
                        <option value='SK Chairman' <?= $targetRole == 'SK Chairman' ? 'selected' : '' ?>>SK Chairman</option>
                        <option value='Tanod' <?= $targetRole == 'Tanod' ? 'selected' : '' ?>>Tanod</option>
                    </select>
                </td>
                
                <td>
                    <?php if ($isCompleted): ?>
                        <span class='badge bg-success'><?= htmlspecialchars($status) ?></span>
                    <?php else: ?>
                        <span class='badge bg-warning text-dark'><?= htmlspecialchars($status) ?></span>
                    <?php endif; ?>
                </td>
                
                <td>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="submit" name="action" value="verify" class="btn btn-sm btn-success" <?= $isCompleted ? 'disabled' : '' ?>>
                            Verify
                        </button>
                        <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
                            Delete
                        </button>
                    </div>
                </td>
            </form>
            </tr>
        <?php
    }
} else {
    echo "<tr><td colspan='11' class='text-center'>0 results found</td></tr>";
}
?>
</tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        const sidebar = document.getElementById('sidebar');
        const main    = document.querySelector('.main-content');
        if (sidebar.style.display === 'none') {
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