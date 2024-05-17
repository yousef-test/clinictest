<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('conn.php');
include('freez.php');


    $user_id = $_SESSION['user_id'];
    $message = "";
    $message2 = "";

$checkapp="SELECT COUNT(*) as userapp_count FROM appointments WHERE userid=$user_id";
$app_count=$conn->query($checkapp);
$count= $app_count->fetch_assoc();

if($count['userapp_count']==0){
$message2= "ليس لديك مواعيد محجوزة";
}

if (isset($_POST['doctor']) && !empty($_POST['doctor'])) {
    
    $doctor_id = $_POST['doctor'];

    // التحقق من عدم وجود موعد سابق مع الطبيب
    $check_appointment = "SELECT COUNT(*) as app_count FROM appointments WHERE doctorid = '$doctor_id' AND userid = '$user_id'";
    $app_result = $conn->query($check_appointment);
    $app_data = $app_result->fetch_assoc();

    if ($app_data['app_count'] == 0) {
        // جلب ساعات دوام الطبيب
        $doctor_work_sql = "SELECT from_hours, to_hours, for_day, to_day FROM doctors WHERE doctorid = '$doctor_id'";
        $doctor_work_result = $conn->query($doctor_work_sql);

            $doctor_work = $doctor_work_result->fetch_assoc();
            $next_day = date('Y-m-d', strtotime('+1 day'));
            $next_day_of_week = date('N', strtotime($next_day));
            $next_day_converted = ($next_day_of_week % 7) + 2; // تحويل لليوم التالي
            $start_time = strtotime($doctor_work['from_hours']); // بداية دوام الطبيب
            $end_time = strtotime($doctor_work['to_hours']);
            $time_interval = 20 * 60; // فاصل زمني للمواعيد (20 دقيقة)

            if ($next_day_converted==8){
                $next_day_converted=1;
            }
            else{
                $next_day_converted=$next_day_converted;
            }
            if ($next_day_converted >= $doctor_work['for_day'] && $next_day_converted <= $doctor_work['to_day']) {
                // حجز الموعد في الوقت المتاح
                while ($start_time < $end_time) {

                    $check_appointment_sql = "SELECT COUNT(*) as user_app_count FROM appointments WHERE userid = '$user_id' AND apptime = '$start_time'";
                    $check_appointment_result = $conn->query($check_appointment_sql);
                    $check_appointment_data = $check_appointment_result->fetch_assoc();

                    if ($check_appointment_data['user_app_count'] == 0) {
                        $formatted_time = date('H:i:s', $start_time);

                        //كي لا يكون الموعد بنفس وقت موعد اخر لنفس المستخدم
                        $same_app = "SELECT COUNT(*) as same_app FROM appointments WHERE userid = '$user_id' AND apptime = '$formatted_time'";
                        $same_app_result = $conn->query($same_app);
                        $same_app_data = $same_app_result->fetch_assoc();
                        
                        if($same_app_data['same_app']==0){
                            
                        $insert_sql = "INSERT INTO appointments (appdate, apptime, doctorid, userid, day_of_week) VALUES ('$next_day', '$formatted_time', '$doctor_id', '$user_id', '$next_day_converted')";
                        if ($conn->query($insert_sql) === TRUE) {
                            $message = "<p style='color: green;'>تم حجز الموعد بنجاح</p>";
                            header("refresh:1;url='get-appointment.php'");
                            break;
                        } else {
                            $message = "حدث خطأ أثناء الحجز: " . $conn->error;
                        }
                    }
                    }
                    $start_time += $time_interval; // الانتقال إلى الوقت التالي
                }                    

                if ($start_time >= $end_time) {
                    $message = "عذراً، الحجوزات مع الطبيب ممتلئة في اليوم التالي.";
                }
            } else {
                $message = "الطبيب غير مداوم في الغد.";
            }

    } else {
        $message = "<p style='color: tomato;'>لقد قمت بالفعل بحجز موعد مع هذا الطبيب.</p>";
    }
} else {
    $message = "اضغط على زر حجز موعد عند الطبيب الذي تريده وستتم عملية الحجز.";
}


?>


<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز موعد</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="get-appointment.css">
    <link rel="icon" href="icon.png" >

    
</head>
<body>

<header>
    <img src="logo.jpeg" alt="Logo" onclick="window.location.href='patient.php'">
</header>

<div class="divhome-btn">
    <a href="patient.php" class="home-btn">العودة الى الصفحة الرئيسية <i class="fa fa-chevron-circle-left"></i></a>
</div>
<div class="container">
    <div class="appointment-section">
        <div class="title">
            <i class="fas fa-calendar-plus"></i>
            <h2>حجز موعد</h2>
            
        </div>
    </div>

<?php 

        date_default_timezone_set('Asia/Gaza');
        function arabic_day_name($day_number) {
            $arabic_days = array("الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت");
            // تعديل الفهرس ليكون دائماً ضمن حدود المصفوفة
            $day_number = ($day_number + 1) % 7;
            return $arabic_days[$day_number];
        }
        
        $day_number = date('w');
        $day_name = arabic_day_name($day_number);
                
        echo "<p style='color:tomato;'> ملاحظة هامة : الموعد سيكون يوم غدٍ ". $day_name ." ،يرجى الالتزام بالموعد </p>" ;

        ?>

<div class="message2"><?php echo $message2;?></div>
<?php
if($count['userapp_count'] > 0){

    echo "<a href='userapp.php' class='show-app' >اضغط هنا للاطلاع على حجوزاتك</a>";
}
    ?>
<div class="search-container">
<input type="text" class="search" id="searchInput" onkeyup="searchDoctors()" placeholder="بحث عن طبيب...">
<label for="searchInput" class="fa fa-search" ></label>
    <table>
        <thead>
            <tr>
                <th>اسم الطبيب</th>
                <th>التخصص</th>
                <th>أيام الدوام</th>
                <th>ساعات الدوام</th>
                <th>حجز موعد</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT doctorid, doctorname,from_hours, to_hours, for_day, to_day ,speciality FROM doctors ";
            $result = $conn->query($sql);

            $days = array(
                1 => 'السبت',
                2 => 'الأحد',
                3 => 'الاثنين',
                4 => 'الثلاثاء',
                5 => 'الأربعاء',
                6 => 'الخميس',
                7 => 'الجمعة'
            );

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {

                    $for_day = $days[$row["for_day"]];
                    $to_day = $days[$row["to_day"]];

                    echo "<tr class='doctor'>";
                    echo "<td>".$row['doctorname']."</td>";
                    echo "<td>".$row['speciality']."</td>";
                    echo "<td class='oclock'>".$for_day." - ".$to_day."</td>";

                    $from_hours = date("g:i a", strtotime($row['from_hours']));
                    $to_hours = date("g:i a", strtotime($row['to_hours']));
                    echo "<td class='oclock'>".$from_hours." - ".$to_hours."</td>";
                    
                    // حقل حجز الموعد
                    echo "<td><form action='' method='post'>
                    <input type='hidden' name='doctor' value='".$row['doctorid']."'>
                    <button type='submit' class='book-appointment-btn' onclick='return confirmAppointment()'>حجز موعد</button></form></td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
    <div class="message"><?php echo $message;?></div>
</div>

</body>
</html>

<script>
function confirmAppointment() {
    return confirm("هل أنت متأكد من أنك تريد حجز موعد؟");
}
function searchDoctors() {
        var input = document.getElementById('searchInput');
        var filter = input.value.toUpperCase();
        var rows = document.querySelectorAll('.doctor');

        for (var i = 0; i < rows.length; i++) {
    var row = rows[i];
    var doctorName = row.getElementsByTagName('td')[0];
    if (doctorName) {
        var txtValue = doctorName.textContent || doctorName.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    }
}
    }
</script>
