<!DOCTYPE html>
<html lang="ar">
<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

include('conn.php');
include('freez.php');

$message="";

$sql = "SELECT username, email, phone_number, id_number FROM users WHERE userid = $userId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row["username"];
    $email = $row["email"];
    $phone = $row["phone_number"];
    $id_number = $row["id_number"];
} else {
    echo "0 results";
}

//لتغيير كلمة المرور
if (isset($_POST["change_pass"])) {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    
    $sql = "SELECT password FROM users WHERE userid = $userId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $getPassword = $row["password"];

        if ($getPassword == $oldPassword) {
            // تحديث كلمة المرور في قاعدة البيانات
            $sql = "UPDATE users SET password='$newPassword' WHERE userid=$userId";
            if ($conn->query($sql) === TRUE) {
                header("refresh:2;url='personal-info.php'");
                $message="<p style='color: green;'>تم تحديث كلمة المرور بنجاح</p>";
            } else {
                $message="<p style='color: red;'>حدث خطأ أثناء تحديث كلمة المرور ،اعد المحاولة"  . $conn->error;
            }
        } else {
            header("refresh:2;url='personal-info.php'");
            $message="<p style='color: red;'>كلمة المرور الحالية غير صحيحة</p>";
        }
    } else {
        header("refresh:2;url='personal-info.php'");
        $message="<p style='color: red;'>خطأ في تحديث كلمة المرور</p>";
    }
}

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المعلومات الشخصية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="personal-info.css">
    <link rel="icon" href="icon.png" >
</head>
<body>

<header>
    <img src="logo.jpeg" alt="Logo" onclick="window.location.href='patient.php'">
</header>
<br><a href="patient.php" class="home-btn">العودة الى الصفحة الرئيسية <i class="fa fa-chevron-circle-left"></i></a>
<div class="container">
    <h1><i class="fas fa-info-circle"></i> المعلومات الشخصية </h1>
    <div class="info-group">
        <label>الاسم:</label>
        <p><?php echo $name; ?></p>
    </div>
    <div class="info-group">
        <label>البريد الإلكتروني:</label>
        <p><?php echo $email; ?></p>
    </div>
    <div class="info-group">
        <label>رقم الهاتف:</label>
        <p><?php echo $phone; ?></p>
    </div>
    <div class="info-group">
        <label>رقم الهوية:</label>
        <p><?php echo $id_number; ?></p>
    </div>
    <form method="post">
        <button type="button" class="change-btn" onclick="togglePasswordChange()">تغيير كلمة المرور</button>
        <div id="passwordChange" style="display: none;">
            <input type="password" name="oldPassword" placeholder="كلمة المرور الحالية" required>
            <input type="password" name="newPassword" placeholder="كلمة المرور الجديدة" required>
            <button type="submit" name="change_pass" onclick="return confirmDelete()">تحديث</button>
        </div>
    </form>
            <?php echo $message; ?>
</div>

<script>
    function togglePasswordChange() {
        var passwordChange = document.getElementById('passwordChange');
        if (passwordChange.style.display === 'none') {
            passwordChange.style.display = 'block';
        } else {
            passwordChange.style.display = 'none';
        }
    }

    function confirmDelete() {
    return confirm("هل أنت متأكد من أنك تريد تحديث كلمة المرور؟");
}
</script>
</body>
</html>



