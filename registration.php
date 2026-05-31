<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – Barangay RMS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f0f5fc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }

        .wrapper {
            width: 100%;
            max-width: 500px;
            padding: 16px;
        }

        .header {
            background-color: #1a3a6e;
            border-radius: 8px 8px 0 0;
            padding: 28px 32px 24px;
            text-align: center;
        }

        .logo {
            width: 52px;
            height: 52px;
            background: #d4a800;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #1a3a6e;
            margin: 0 auto 14px;
        }

        .header h5 { color: #fff; font-size: 16px; font-weight: 600; margin: 0; }
        .header p { color: #a0bce0; font-size: 12px; margin: 4px 0 0; }
        
        .body {
            background: #fff;
            border-radius: 0 0 8px 8px;
            padding: 28px 32px;
            box-shadow: 0 2px 12px rgba(0,0,0,.1);
        }

        .form-label { font-size: 13px; font-weight: 600; color: #1a3a6e; margin-bottom: 5px; }
        .form-control, .form-select {
            font-size: 13px;
            border: 1px solid #c8d8ee;
            border-radius: 6px;
            padding: 9px 12px;
            color: #1a3a6e;
        }
        .form-control:focus, .form-select:focus {
            border-color: #1a3a6e;
            box-shadow: 0 0 0 3px rgba(26,58,110,.1);
        }

        .input-group-text { background: #f0f5fc; border: 1px solid #c8d8ee; color: #4a6a9a; font-size: 14px; }

        .btn-register {
            background: #d4a800;
            border: none;
            color: #122a54;
            font-weight: 700;
            font-size: 14px;
            padding: 10px;
            border-radius: 6px;
            width: 100%;
            transition: background .2s;
            margin-top: 10px;
        }
        .btn-register:hover { background: #b89000; color: #122a54; }

        .footer { text-align: center; margin-top: 18px; font-size: 13px; color: #6c757d; }
        .footer a { color: #1a3a6e; font-weight: 600; text-decoration: none; }
        .footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="header">
        <div class="logo">BRGY</div>
        <h5>Barangay Record Management System</h5>
        <p>Create a new account</p>
    </div>

    <div class="body">
        <form method="POST" action="registration.php">
            
            <div class="mb-3">
                <label class="form-label" for="role">Register As</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="Resident">Resident</option>
                    <option value="Employee">Employee</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" class="form-control" placeholder="Enter your full name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="contact">Contact Number</label>
                    <input type="tel" id="contact" name="contact" class="form-control" placeholder="09xxxxxxxxx" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter a valid email" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                    <button type="button" class="input-group-text" id="togglePassword" style="cursor:pointer;">
                        <i class="bi bi-eye-fill" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="gender">Gender</label>
                    <select id="gender" name="gender" class="form-select" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="address">Complete Address</label>
                <textarea id="address" name="address" class="form-control" rows="2" placeholder="Enter your complete address" required></textarea>
            </div>

            <button type="submit" name="register" class="btn-register">
                <i class="bi bi-person-plus-fill me-1"></i> Register & Send OTP
            </button>
        </form>

        <div class="footer">
            Already have an account? <a href="login.php">Sign in here</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const pwField = document.getElementById('password');
        const icon    = document.getElementById('toggleIcon');
        if (pwField.type === 'password') {
            pwField.type = 'text';
            icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
        } else {
            pwField.type = 'password';
            icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
        }
    });
</script>
</body>
</html>

<?php
require_once "dbaseconnection.php";
require_once "verifyEmail.php";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $fullname   = trim($_POST['fullname'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $dob        = $_POST['dob'] ?? '';
    $gender     = $_POST['gender'] ?? '';
    $contact    = trim($_POST['contact'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $targetRole = $_POST['role'] ?? '';

    $_SESSION['reg_fullname']   = $fullname;
    $_SESSION['reg_email']      = $email;
    $_SESSION['reg_password']   = $password;
    $_SESSION['reg_dob']        = $dob;
    $_SESSION['reg_gender']     = $gender;
    $_SESSION['reg_contact']    = $contact;
    $_SESSION['reg_address']    = $address;
    $_SESSION['reg_targetRole'] = $targetRole;

    if (empty($fullname) || empty($email) || empty($password) || empty($dob) || empty($gender) || empty($contact) || empty($address)) {
        $error = 'Please fill in all fields.';
    } else {
        $otp = rand(100000, 999999);
        
        $unique_user_id = 'BRGY-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
        
        $_SESSION['reg_user_id'] = $unique_user_id;

        $insert_sql = "INSERT INTO pending_applications (`Application_ID`, `Applicant_Name`, `Date_of_Application`, `Target_Role`, `Status`, `Date_of_Birth`, `Gender`, `Contact_Information`, `Address`, `Current_Role`, `Email`, `Password`, `otp`) 
                       VALUES ('$unique_user_id', '$fullname', NOW(), '$targetRole', 'Waiting for email confirmation', '$dob', '$gender', '$contact', '$address', 'Pending', '$email', '$password', '$otp')";

        $result = $conn->query($insert_sql);

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        if ($result === TRUE) {
            $logsql = "INSERT INTO tbl_logs (action, datetime, Application_ID, Full_Name) VALUES ('Registered', NOW(), '$unique_user_id', '$fullname')";
            $conn->query($logsql);

            send_verification($fullname, $email, $otp);

            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful',
                        text: 'Your application has been submitted. Please check your email for the OTP to verify your account.',
                        confirmButtonColor: '#1a3a6e',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'otpValidation.php';
                    });
                });
            </script>";
        } else {
            $error = 'Error during registration. Please try again.';
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: " . json_encode($error) . ",
                        confirmButtonColor: '#1a3a6e',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
        }
    }
}
?>