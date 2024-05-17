<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('conn.php');
include('freez.php');

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الأطباء</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="doctorspage.css">
    <link rel="icon" href="icon.png" >

</head>
<body>

<header>
    <img src="logo.jpeg" alt="Logo" onclick="window.location.href='patient.php'">
</header>
<br><a href="patient.php" class="home-btn">العودة الى الصفحة الرئيسية <i class="fa fa-chevron-circle-left"></i></a>

<div class="container">
    <h2><i class="fas fa-user-md"></i> الأطباء </h2>
    <div class="doctors-list">
<div class="search-container">
<input type="text" class="search" id="searchInput" onkeyup="searchDoctors()" placeholder="بحث عن طبيب...">
<label for="searchInput" class="fa fa-search" ></label>

</div>
        <table>
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>التخصص</th>
                    <th>القسم</th>
                    <th>أيام الدوام</th>
                    <th>ساعات العمل</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $sql = "SELECT doctorid, doctorname, speciality, departments.depname, from_hours, to_hours, for_day, to_day 
                FROM doctors 
                INNER JOIN departments ON doctors.department = departments.depid";
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
                        // تحويل الوقت إلى نظام 12 ساعة
                        $from_hours = date("h:i A", strtotime($row["from_hours"]));
                        $to_hours = date("h:i A", strtotime($row["to_hours"]));
                        
                        echo "<tr class='doctor'>
                                <td>".$row["doctorname"]."</td>
                                <td>".$row["speciality"]."</td>
                                <td>".$row["depname"]."</td>
                                <td class='work_days'>".$for_day." -> ".$to_day."</td>
                                <td>"."من الـ".$from_hours." الى الـ ".$to_hours."</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>لا يوجد أطباء لعرضهم</td></tr>";
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div>
    </div>

<script>
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
</body>
</html>

