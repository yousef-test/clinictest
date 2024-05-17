<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

include('conn.php');
include('freez.php');

?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض الرسائل</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="messages.css" >
    <link rel="icon" href="icon.png" >

</head>
<body>
<header>
    <img src="logo.jpeg" alt="Logo" onclick="window.location.href='patient.php'">
</header>
<div class="container">
    <div class="title">
    <h2><i class="fa fa-envelope"></i> الرسائل المستلمة</h2>
    </div>
<a class="back-btn" href="patient.php"><i class="fa fa-arrow-right"></i> عودة </a>
    <table>
        <thead>
        <tr>
            <th>الموضوع</th>
            <th>التاريخ</th>
            <th>المرسل</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sql_messages = "SELECT * FROM messages WHERE user_id = $user_id ORDER BY sent_at DESC";
        $result_messages = $conn->query($sql_messages);
        
        if ($result_messages->num_rows > 0){
            while ($message = $result_messages->fetch_assoc()){ ?>
                <tr>
                    <td class="message">
                        <a href="message.php?message_id=<?php echo $message['message_id']; ?>">
                            <?php echo $message['msg_address']; ?>
                        </a>
                    </td>
                    <td><?php echo date('Y-m-d', strtotime($message['sent_at'])); ?></td>
                    <td>admin</td>
                </tr>
        
        <?php }} else{ ?>
            <tr>
                <td colspan="3" style="text-align: center;">لا توجد رسائل مرسلة.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
