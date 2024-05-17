<!DOCTYPE html>
<html>

<?php
include('conn.php');

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: patient.php");
    exit;
}

$message = '';

if (isset ($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT userid, activity FROM users WHERE (email='$email' OR id_number='$email') AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if ($row['activity'] == false) {
            $message = "<div style='color: red; text-align: center; font-size: 80%;'>تم تجميد حسابك ،الرجاء مراجعة المركز الصحي</div>";
        } else {
            $_SESSION['user_id'] = $row['userid'];
            header("Location: patient.php");
            exit();
        }
    } else {
        $message = "<div style='color: red; text-align: center; font-size: 80%;'>البريد الإلكتروني أو كلمة المرور غير صحيحة</div>";
    }
}

mysqli_close($conn);
?>




<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="loginb.css">
    <link rel="icon" href="icon.png" >

   
</head>

<body>
    <header>
        <img src="logo.jpeg" alt="Logo" onclick="window.location.href='main.php';" style="cursor: pointer;">
    </header>
    <div class="full-height">
        <div class="input-div">
            <p class="header-text">تسجيل الدخول</p>
            <form action="" method="POST">
                    <?php echo $message; ?>
                    <input type="text" name="email" class="input-field" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" placeholder=" البريد الإلكتروني او رقم الهوية" required>
                    <input type="password" name="password" class="input-field" placeholder="كلمة المرور" required>
                 <center><button type="submit" name="login" class="login-btn">تسجيل الدخول <i class="fas fa-sign-in-alt"></i></button>
              <p class="login-link">ليس لديك حساب؟ <a href="signup.php">انشئ حساب</a></p></center>
            </form>
            
        </div>
    </div>
</body>

</html>
