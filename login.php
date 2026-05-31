<?php
session_start();
require_once 'dbaseconnection.php';

$user_id = $_SESSION['reg_user_id'];

if (isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $password = $conn->real_escape_string($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Fields',
                    text: 'Please fill in all fields.',
                });
            });
        </script>";
    } else {
        $loginsql = "SELECT * FROM pending_applications WHERE email = '$username' AND password = '$password'";
        $result = $conn->query($loginsql);

        if ($result->num_rows > 0) {
            $fieldnames = $result->fetch_assoc();
            $usertype = $fieldnames['Current_Role'];

            $_SESSION['Current_Role'] = $usertype;


            if ($usertype === 'Resident') {
                $res_sql = "SELECT Resident_Id FROM resident_table WHERE username = '$username'";
                $res_result = $conn->query($res_sql);
                
                if ($res_result->num_rows > 0) {
                    $res_data = $res_result->fetch_assoc();
                    $_SESSION['Resident_Id'] = $res_data['Resident_Id'];
                }
            }
            // -----------------------------------------------------

            $logsql = "INSERT INTO tbl_logs (action, datetime, Application_ID, Full_Name) VALUES ('logged in', NOW(), '$user_id', '$username')";
            $conn->query($logsql);
            $conn->query($logsql);

            $redirect_url = "FPROJ_ResidentDashboard.php";
            if ($usertype == "Admin") {
                $redirect_url = "FPROJ_Admin.php";
            } elseif ($usertype == "Employee") {
                $redirect_url = "FPROJ_Employee.php";
            }

            // Show SweetAlert, wait for it to finish, THEN redirect via JavaScript
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Welcome back!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = '$redirect_url';
                    });
                });
            </script>";

        } else { 
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Wrong username or password',
                        text: 'Please try again',
                        showConfirmButton: true
                    });
                });
            </script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Barangay RMS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f0f5fc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wrapper {
            width: 100%;
            max-width: 420px;
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

        .header h5 {
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }

        .header p {
            color: #a0bce0;
            font-size: 12px;
            margin: 4px 0 0;
        }

        .body {
            background: #fff;
            border-radius: 0 0 8px 8px;
            padding: 28px 32px;
            box-shadow: 0 2px 12px rgba(0,0,0,.1);
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #1a3a6e;
            margin-bottom: 5px;
        }

        .form-control {
            font-size: 13px;
            border: 1px solid #c8d8ee;
            border-radius: 6px;
            padding: 9px 12px;
            color: #1a3a6e;
        }

        .form-control:focus {
            border-color: #1a3a6e;
            box-shadow: 0 0 0 3px rgba(26,58,110,.1);
        }

        .input-group-text {
            background: #f0f5fc;
            border: 1px solid #c8d8ee;
            color: #4a6a9a;
            font-size: 14px;
        }

        .btn-login {
            background: #d4a800;
            border: none;
            color: #122a54;
            font-weight: 700;
            font-size: 14px;
            padding: 10px;
            border-radius: 6px;
            width: 100%;
            transition: background .2s;
        }

        .btn-login:hover {
            background: #b89000;
            color: #122a54;
        }

        .footer {
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
            color: #6c757d;
        }

        .footer a {
            color: #1a3a6e;
            font-weight: 600;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .alert-danger {
            font-size: 13px;
            padding: 9px 14px;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Header -->
    <div class="header">
        <div class="logo">BRGY</div>
        <h5>Barangay Record Management System</h5>
        <p>Sign in to your account</p>
    </div>

    <!-- Form -->
    <div class="body">
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label class="form-label" for="username">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" id="username" name="username" class="form-control"
                           placeholder="Enter your username"
                           value="<?=($_POST['username'] ?? '') ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="Enter your password" required>
                    <button type="button" class="input-group-text" id="togglePassword" style="cursor:pointer;">
                        <i class="bi bi-eye-fill" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" name="login" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </button>
        </form>

        <div class="footer">
            Don't have an account? <a href="registration.php">Register here</a>
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




