<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include('conn.php');
include('freez.php');

$departments_query = "SELECT * FROM departments";
$departments_result = mysqli_query($conn, $departments_query);

$doctors_department = [];

while ($row = mysqli_fetch_assoc($departments_result)) {
    $department_id = $row['depid'];
    $doctors_query = "SELECT * FROM doctors WHERE department = $department_id";
    $doctors_result = mysqli_query($conn, $doctors_query);

    $doctors_department[$department_id] = [];
    while ($doctor_row = mysqli_fetch_assoc($doctors_result)) {
        $doctors_department[$department_id][] = $doctor_row;
    }
    }
    
?>

<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الأقسام</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="departments.css">
    <link rel="icon" href="icon.png" >
</head>
<body>
    <header>
        <img src="logo.jpeg" alt="Logo" onclick="window.location.href='patient.php'">
    </header>

   <br><a href="patient.php" class="home-btn">العودة الى الصفحة الرئيسية <i class="fa fa-chevron-circle-left"></i></a>

    <div class="departments-container">
    <h1><i class="fa fa-sitemap"></i> الأقسام </h1>

        <table class="departments-table">
            <thead>
                <tr>
                    <th>اسم القسم</th>
                    <th>أطباء القسم</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php
                if (!empty($doctors_department)) {
                    foreach ($doctors_department as $department_id => $doctors) {
                        $department_query = "SELECT depname FROM departments WHERE depid = $department_id";
                        $department_result = mysqli_query($conn, $department_query);
                        $department_row = mysqli_fetch_assoc($department_result);
                        
                        $department_name = $department_row['depname'];
                        echo '<tr><td class="t">' . $department_name . '</td>';
                        echo '<td>';
                        if (!empty($doctors)) {
                            echo '<button class="showbtn" onclick="showDoctors(' . $department_id . ')">عرض الأطباء <i class="fa fa-eye" aria-hidden="true"></i></button>';
                            echo '<td><ul id="doctors-list' . $department_id . '" style="display: none;">';
                            foreach ($doctors as $doctor) {
                                echo '<li>' . $doctor['doctorname'] . '</li>';
                            }
                            echo '</ul></td>';
                        } else {
                            echo 'لا يوجد أطباء مسجلين في هذا القسم.';
                        }
                        echo '</td></tr>';
                    }
                } else {
                    echo '<tr><td colspan="2">لا توجد أقسام مسجلة حتى الآن.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>
<script>
       function showDoctors(departmentId) {
            var doctorsList = document.getElementById('doctors-list' + departmentId);
            if (doctorsList.style.display === 'block') {
                doctorsList.style.display = 'none';
            } else {
                doctorsList.style.display = 'block';
            }
        }
    </script>