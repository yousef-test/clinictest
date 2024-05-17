<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('conn.php');
include('freez.php');
include('moveapp.php');

$message= "";
$userid= $_SESSION['user_id'];

if (isset($_POST['delete'])) {
    $appid = $_POST['appid'];

    $sql_delete = "DELETE FROM appointments WHERE appid = '$appid' AND userid =$userid ";
    if ($conn->query($sql_delete) === TRUE) {
        header("refresh:2;url='userapp.php'");
        $message = "<p style='color: green;' >تم حذف الموعد بنجاح</p>";
    } else {
        $message = "<p style='color: red;' >خطأ في الحذف</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجوزاتي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="userapp.css">
    <link rel="icon" href="icon.png" >

</head>
<body>

<header>
    <img src="logo.jpeg" alt="Logo" onclick="window.location.href='patient.php'">
</header>

    <div><br>
        <a href="get-appointment.php" class="home-btn"><i class="fas fa-calendar-plus"></i> حجز موعد جديد </a>
        <a href="patient.php" class="home-btn">العودة الى الصفحة الرئيسية <i class="fa fa-chevron-circle-left"></i></a>
    </div>
<div class="container">
    <div class="appointments-section">
        <div class="title">
            <i class="fas fa-calendar-check"></i>
            <h2>حجوزاتك</h2>
        </div>
        <div class="appointments-list">
        <?php echo $message; ?>

            <table>
                <thead>
                    <tr>
                        <th>إزالة الحجز</th>
                        <th>تاريخ الموعد</th>
                        <th>يوم الموعد</th>
                        <th>وقت الموعد</th>
                        <th>اسم الطبيب</th>
                    </tr>
                </thead>
                <tbody>
                <?php

                $sql = "SELECT * FROM appointments WHERE userid = '$userid' ORDER BY appdate ASC, apptime ASC";
                $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $days = array(
                            1 => 'السبت',
                            2 => 'الأحد',
                            3 => 'الاثنين',
                            4 => 'الثلاثاء',
                            5 => 'الأربعاء',
                            6 => 'الخميس',
                            7 => 'الجمعة'
                        );

                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>
                        <form action='' method='post'>
                            <input type='hidden' name='appid' value='".$row['appid']."'>
                            <button type='submit' name='delete'  onclick='return confirmDelete()' value='".$row["appid"]."'' class='btn btn-delete'><i class='fas fa-trash'></i></button>
                        </form></td>";
                            echo "<td>".$row['appdate']."</td>";
                            echo "<td>".$days[$row['day_of_week']]."</td>";
                            echo "<td>".date(' h:i A ', strtotime($row['apptime']))."</td>";

                            $doctor_id = $row['doctorid'];
                            $sql_doctor_name = "SELECT doctorname FROM doctors WHERE doctorid = '$doctor_id'";
                            $result_doctor_name = $conn->query($sql_doctor_name);
                            if ($result_doctor_name->num_rows > 0) {
                                $doctor_name = $result_doctor_name->fetch_assoc()['doctorname'];
                                echo "<td>".$doctor_name."</td>";
                            } else {
                                echo "<td>الطبيب غير متوفر</td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<th>لا توجد حجوزات حاليًا</th>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
<script>
    function confirmDelete() {
    return confirm("هل أنت متأكد من أنك حذف حجز موعد؟");
}
</script>
</html>
