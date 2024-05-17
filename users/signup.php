<!DOCTYPE html>
<html lang="ar">
<?php

include('conn.php');

$message = '';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $id_number = $_POST['id-number'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $re_password = $_POST['re-password'];

    if ($password !== $re_password) {
        $message = "<div style='color: red; text-align: center;'>تأكد من تطابق كلمة المرور</div>";
    } elseif (count(explode(' ', $username)) < 4) {
        $message = "<div style='color: red; text-align: center;'>ادخل الاسم الرباعي</div>";
    }
    elseif (strlen($id_number) != 9) {
        $message = "<div style='color: red; text-align: center;'>تأكد من رقم الهوية</div>";
    }
    elseif (strlen($phone_number) != 10) {
        $message = "<div style='color: red; text-align: center;'>تأكد من رقم الهاتف</div>";
    } else {
        $checkEmail = "SELECT email AND id_number AND phone_number FROM users WHERE email = '$email' OR id_number= '$id_number' OR phone_number='$phone_number'";
        $result = $conn->query($checkEmail);
        if ($result !== false && $result->num_rows > 0) {
            $message = "<div style='color: red; text-align: center;'>البيانات مسجلة مسبقا</div>";
        } else {
            $sql = "INSERT INTO users (username, email, id_number, phone_number, password) VALUES ('$username', '$email', '$id_number', '$phone_number', '$password')";
            if ($conn->query($sql) === TRUE) {
                $message = "<div style='color: green; text-align: center;'>تم إنشاء الحساب بنجاح ، سيتم توجيهك إلى صفحة تسجيل الدخول</div>";
                header('refresh:2;url=login.php');
            } else {
                $message = "<div style='color: red; text-align: center;'>خطأ: " . $sql . "<br>" . $conn->error . "</div>";
            }
        }
    }
}


$conn->close();
?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="sign-up.css">
    <link rel="icon" href="icon.png" >

</head>
<body>
    <header>
        <img src="logo.jpeg" alt="Logo" onclick="window.location.href='main.php';" style="cursor: pointer;">
    </header>
    <div class="input-div">
        <p class="header-text">إنشاء حساب جديد</p>
        <?php echo $message; ?>
        <form action="" method="POST">
        <input type="text" name="username" placeholder="الاسم الرباعي" class="input-field" value="<?php echo $_POST['username'] ?? ''; ?>" required><br>
        <input type="email" name="email" placeholder="البريد الإلكتروني" class="input-field" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required><br>
        <input type="number" name="id-number" placeholder="رقم الهوية" class="input-field" value="<?php echo isset($_POST['id-number']) ? $_POST['id-number'] : ''; ?>" required><br>
        <input type="number" name="phone_number" placeholder="رقم الهاتف" class="input-field" value="<?php echo isset($_POST['phone_number']) ? $_POST['phone_number'] : ''; ?>" required><br>
        <input type="password" name="password" placeholder="كلمة المرور" class="input-field" required><br>
        <input type="password" name="re-password" placeholder="اعد كتابة كلمة المرور" class="input-field" required><br>

            <center><button type="submit" name="submit" class="signup-btn"><i class="fas fa-user-plus"></i> إنشاء الحساب</button></center>
        </form>
        <center><p class="login-link">لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p></center>
    </div>
</body>
</html>
