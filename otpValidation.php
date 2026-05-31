<?php
session_start();
require_once "dbaseconnection.php";

$fullname    = $_SESSION['reg_fullname'] ?? '';
$email       = $_SESSION['reg_email'] ?? '';
$password    = $_SESSION['reg_password'] ?? ''; 
$dob         = $_SESSION['reg_dob'] ?? '';
$gender      = $_SESSION['reg_gender'] ?? '';
$contact     = $_SESSION['reg_contact'] ?? '';
$address     = $_SESSION['reg_address'] ?? '';
$Target_Role = $_SESSION['reg_targetRole'] ?? '';
$Unique_User_ID = $_SESSION['reg_user_id'] ?? '';

$alertScript = "";

if (isset($_POST['ver'])) {
    // Escaping the OTP is still good practice before the first query
    $userotp = $conn->real_escape_string($_POST['otp']);

    $otpsql = "SELECT * FROM pending_applications WHERE otp = '$userotp'";
    $otpresult = $conn->query($otpsql); 

    if ($otpresult && $otpresult->num_rows == 1) {
        
        $querySuccess = false;

        if ($Target_Role == 'Employee') {
            
            $updateotp = "UPDATE pending_applications SET Status = 'Waiting for admin', otp = NULL WHERE otp = '$userotp'";
            $querySuccess = $conn->query($updateotp);
            
        } else if ($Target_Role == 'Resident') {

            $updateotp = "UPDATE `pending_applications` SET `Status` = 'Completed', `Current_Role` = 'Resident', `otp` = NULL WHERE `otp` = '$userotp'";
                        
            if ($conn->query($updateotp) === TRUE) {

                $stmt = $conn->prepare("INSERT INTO resident_table (Resident_Id, Resident_Name, Date_of_Birth, Gender, Contact_Information, Address, username, password, Date_Created) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                
                $stmt->bind_param("ssssssss", $Unique_User_ID, $fullname, $dob, $gender, $contact, $address, $email, $password);
                
                if ($stmt->execute()) {
                    $querySuccess = true;
                }
                $stmt->close();
            }
        }

        if ($querySuccess) {
            $alertScript = "Swal.fire({
                    icon: 'success',
                    title: 'OTP Verified',
                    text: 'Your email has been successfully verified. You can now log in.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d6efd'
                }).then(() => {
                    window.location.href = 'login.php';
                });";
        } else {
            $alertScript = "Swal.fire({
                    icon: 'error',
                    title: 'Verification Failed',
                    text: 'There was an error updating your verification status. Please try again.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d6efd'
                });";
        }
    } else {
        $alertScript = "Swal.fire({
                icon: 'error',
                title: 'Invalid OTP',
                text: 'The code you entered is incorrect or has expired.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#0d6efd'
            });";
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #cfe0f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .otp-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(26, 58, 110, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }

        .otp-card-header {
            background-color: #1a3a6e;
            padding: 28px 32px 22px;
            text-align: center;
        }

        .otp-card-header h4 {
            color: #ffffff;
            font-weight: 700;
            font-size: 20px;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .otp-card-header p {
            color: #a0bce0;
            font-size: 13px;
            margin: 6px 0 0;
        }

        .otp-card-body {
            padding: 30px 32px 32px;
        }

        .otp-info-box {
            background-color: #f0f5fc;
            border-left: 4px solid #d4a800;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .otp-info-box i {
            color: #d4a800;
            font-size: 18px;
            flex-shrink: 0;
        }

        .otp-info-box span {
            color: #1a3a6e;
            font-size: 13px;
            font-weight: 500;
        }

        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #1a3a6e;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1.5px solid #c5d8f0;
            border-radius: 8px;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 8px;
            color: #1a3a6e;
            padding: 12px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #1a3a6e;
            box-shadow: 0 0 0 3px rgba(26, 58, 110, 0.12);
        }

        .form-control::placeholder {
            font-size: 16px;
            letter-spacing: 2px;
            color: #b0c4de;
            font-weight: 400;
        }

        .btn-verify {
            background-color: #1a3a6e;
            border: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            letter-spacing: 0.5px;
            transition: background-color 0.2s, transform 0.1s;
        }

        .back-link {
            text-align: center;
            font-size: 12px;
            margin-top: 12px;
        }

        .back-link a {
            color: #6c757d;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .back-link a:hover {
            color: #1a3a6e;
        }
    </style>
</head>
<body>

    <div class="otp-card">
        <div class="otp-card-header">
            <h4>OTP Verification</h4>
            <p>Barangay Record Management System</p>
        </div>
        <div class="otp-card-body">
            <div class="otp-info-box">
                <i class="bi bi-envelope-fill"></i>
                <span>A One-Time Password (OTP) has been sent to your registered email address.</span>
            </div>

            <form action="OTEVerification.php" method="post">
                <div class="mb-4">
                    <label class="form-label" for="otpInput">Enter OTP Code</label>
                    <input
                        type="text"
                        name="otp"
                        id="otpInput"
                        class="form-control"
                        maxlength="6"
                        placeholder="· · · · · ·"
                        autocomplete="one-time-code"
                        required
                    />
                </div>

                <button type="submit" name="ver" class="btn-verify">
                    <i class="bi bi-shield-check me-2"></i>Verify OTP
                </button>
            </form>

            <div class="back-link">
                <a href="login.php"><i class="bi bi-arrow-left"></i> Back to Login</a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

<?php
require_once "FINALS_LABACT3_dbconnection.php";

if (isset($_POST['otp'])) {
    $userotp = $_POST['otp'];
    $otpsql = "Select * from tjs_info where otptjs = '".$conn->real_escape_string($userotp)."'";
    $result = $conn->query($otpsql);

    if ($result && $result->num_rows == 1) {
        $updateSql = "Update tjs_info set statustjs = 'Verified' where otptjs = '".$conn->real_escape_string($userotp)."'";
        if ($conn->query($updateSql) === TRUE) {
            ?>
            <script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "OTP Verified",
                text: "Your account has been activated",
                showConfirmButton: true
            }).then(() => {
                window.location.href = "login.php";
            });
            </script>
            <?php
        }
    } else {
        ?>
        <script>
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Invalid OTP",
            text: "Please try again",
            showConfirmButton: true
        });
        </script>
        <?php
    }
}
?>