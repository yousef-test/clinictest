<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include('conn.php');
include('freez.php');

$user_id = $_SESSION['user_id'];

?>

    <!DOCTYPE html>
    <html lang="ar">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>سجل الحجوزات</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="userhistory.css">
        <link rel="icon" href="icon.png" >

    </head>
    <body>
        <header>
            <img src="logo.jpeg" alt="Logo" onclick="window.location.href='patient.php'">
        </header>
        <br><a href="patient.php" class="home-btn">العودة الى الصفحة الرئيسية <i class="fa fa-chevron-circle-left"></i></a>

        <div class="container">
        <h1>سجل حجوزاتي</h1>
            <br><br>
            <table>
                <thead>
                    <tr>
                        <th>اسم الطبيب</th>
                        <th>تاريخ الحجز</th>
                        <th>وقت الحجز</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

        $sql = "SELECT historyid, doctors.doctorname, appdate, apptime 
        FROM history 
        JOIN doctors ON history.doctorid = doctors.doctorid
        WHERE userid = '$user_id'
        ORDER BY historyid DESC";

        $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['doctorname']; ?></td>
                            <td><?php echo $row['appdate']; ?></td>
                            <td><?php echo $row['apptime']; ?></td>
                        </tr>
                    <?php }
                    } else {
    echo "لا توجد سجلات متاحة.";
}
                    ?>    
                </tbody>
            </table>
        </div>
    </body>
    </html>

