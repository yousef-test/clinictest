<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('conn.php');
include('freez.php');
include('moveapp.php');

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


if(isset($_GET['read_message'])) {
    $message_id = $_GET['read_message'];
    header("Location: message.php?message_id=$message_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>واجهة المريض</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="patient.css">
    <link rel="icon" href="icon.png" >

</head>
<body>

<?php
date_default_timezone_set('Asia/Gaza');

// حصول على رقم اليوم في الأسبوع
$day_number = date('w');

$arabic_days = array("الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت");

$day_name = $arabic_days[$day_number];

$today = date('Y-m-d');
$time = date('h:i A');
?>
        <div class="date-time">
            <span>اليوم: <?php echo "<a> $day_name </a>"?></span>
            <span>التاريخ: <?php echo "<a> $today</a>" ?></span>
            <span>الساعة: <?php echo "<a>$time</a>" ?></span>
        </div>
<header>
    <img src="logo.jpeg" alt="Logo" onclick="window.location.href='patient.php'">
</header>
<img src="logo.jpeg" alt="Logo" class="pic">

<div class="notifications">
    <i class="fa fa-envelope" onclick="showMessage()">
    <?php
        //  للحصول على عدد الرسائل غير المقروءة
        $unread_messages = "SELECT COUNT(*) AS unread_count FROM messages WHERE user_id = '$user_id' AND is_read = 0";
        $result_unread= $conn->query($unread_messages);

        if ($result_unread->num_rows > 0) {
            $row_unread_messages = $result_unread->fetch_assoc();
            $unread_count = $row_unread_messages['unread_count'];

            if ($unread_count > 0) {
                echo "<span class='unread-count'>$unread_count</span>";
            }
        }
?>
</i>
</div>

<div id="messageBox" class="notification-body" style="display: none;">
<div class = "message">
    <?php
$sql_message = "SELECT message_id, msg_address FROM messages WHERE user_id = '$user_id' AND is_read = 0 ORDER BY sent_at DESC LIMIT 3";
$result_message = $conn->query($sql_message);

     if ($unread_count > 0) {
        echo "<a class='unread-messages'>لديك $unread_count رسالة غير مقروءة</a><br>";
    }
    if ($result_message->num_rows > 0) {
                while($row_message = $result_message->fetch_assoc()) {
            echo '<a class="message" href="?read_message=' . $row_message['message_id'] .'">' . $row_message["msg_address"] . '</a><hr>';
        }

    } else {
        echo '<div class="message">لا توجد رسائل غير مقروءة';
    }
    echo '<p class="show_all_msg" onclick="window.location.href=\'messages.php\'">عرض جميع الرسائل</p></div>';

    ?>
    
</div>
</div>

<div class="personal-info">
    <div class="user-section">
        <i class="fas fa-user"></i>
        <?php
        $get_name="SELECT username FROM users WHERE userid=$user_id";
        $get_name_query = $conn->query($get_name);
        $get_name_result = $get_name_query->fetch_assoc();
        if($get_name_result==true) {
            echo '<div style="margin-top: 10px;">' . $get_name_result['username'] . '</div>';
        }
        ?>
        <a class="logout-btn" href="?logout">تسجيل الخروج</a>

        <?php
$sql_apps_count = "SELECT COUNT(*) AS total_appointments FROM appointments WHERE userid = '$user_id' AND appdate = '$today' AND TIMEDIFF(CONCAT(appdate, ' ', apptime), NOW()) < '01:00:00'";
$result_apps_count = $conn->query($sql_apps_count);

if ($result_apps_count->num_rows > 0) {
    $row_apps_count = $result_apps_count->fetch_assoc();
    $apps_count = $row_apps_count['total_appointments'];

    if ($apps_count > 0) {
        echo"<div class='apps_count'>";
        echo" لديك $apps_count حجوزات هذه الساعة </div>";
    }
}

$sql_user_appointments = "SELECT * FROM appointments JOIN doctors ON doctors.doctorid = appointments.doctorid WHERE userid = '$user_id' AND appdate = '$today' AND TIMEDIFF(CONCAT(appdate, ' ', apptime), NOW()) < '01:00:00' ORDER BY apptime ASC";
$user_appointments = $conn->query($sql_user_appointments);

if ($user_appointments->num_rows > 0) {
    echo "<table class='appointments-table'>";
    echo "<thead><tr><th>اسم الطبيب</th>
    <th>ساعة الموعد</th>
    <th>الزمن المتبقي</th>
    </tr></thead>";

    while ($row = $user_appointments->fetch_assoc()) {
        $appointment_datetime = strtotime($row['appdate'] . ' ' . $row['apptime']);
        $current_datetime = strtotime(date('Y-m-d H:i:s'));
        $time_diff = $appointment_datetime - $current_datetime;
        $minutes_left = floor($time_diff / 60); // الزمن المتبقي بالدقائق

        if ($minutes_left < 60) {
            $appointment_time = date('h:i A', $appointment_datetime);
            echo "<tr><td>".$row['doctorname']."</td>
            <td>$appointment_time</td>";
            if($minutes_left<0){
                echo "<td style='background-color:#05ae38;'>الموعد قائم</td></tr>";
            }
            else{
                echo"<td>$minutes_left دقيقة</td></tr>";
            }
        }
    }
    echo "</table>";
}
?>

    <div class="dash">القائمة الرئيسية</div>
    </div>

    <div class="buttons-section">
        <a class="list-btn" type="submit" href="userapp.php"> <i class="fas fa-calendar-check"></i> حجوزاتي </a>
        <a class="list-btn" type="submit" href="doctorspage.php"><i class="fas fa-user-md"></i> الأطباء </a>
        <a class="list-btn" type="submit" href="departments.php"><i class="fa fa-sitemap"></i> الأقسام </a>
        <a class="list-btn" type="submit" href="personal-info.php"><i class="fas fa-info-circle"></i> المعلومات الشخصية </a>
        <a class="list-btn" type="submit" href="userhistory.php"><i class="fas fa-chart-line"></i> سجل حجوزاتك </a>
        <a class="list-btn" type="submit" onclick="showConn()"><i class="fa fa-phone" ></i> تواصل معنا </a>

</div>

<div class="conn-div" id="conn" style="display: none;">
    <a>اضغط على تواصل لارسال رسالة عبر الواتس أب</a><br><br>
    <a class="conn" href="https://wa.me/970592990280" target="_blank">تواصل</a>
    <a class="cancel" onclick="showConn()">الغاء</a>
</div>

<button class="appointment-btn" onclick="location.href='get-appointment.php'"> <i style="padding : 5%" class="fas fa-calendar-plus"></i>  حجز موعد </button>

<script>
    function showMessage() {
        var messageBox = document.getElementById('messageBox');
        if (messageBox.style.display === 'none') {
            messageBox.style.display = 'block';
        } else {
            messageBox.style.display = 'none';
        }
    }

    function showConn() {
        var connbox = document.getElementById('conn');
        if (connbox.style.display === 'none') {
            connbox.style.display = 'block';
        } else {
            connbox.style.display = 'none';
        }

    }
</script>

</body>
</html>
