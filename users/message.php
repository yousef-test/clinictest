<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userid=$_SESSION['user_id'];
include('conn.php');
include('freez.php');
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="message.css" >
    <link rel="icon" href="icon.png" >

    <title>عرض الرسالة</title>

</head>
<body>
<header>
    <img src="logo.jpeg" alt="Logo" onclick="window.location.href='patient.php'">
</header>

<div class="container">
    <a class="back-btn" href="messages.php"><i class="fa fa-arrow-right"></i> عودة </a>
    <?php

        if (isset($_GET['message_id'])) {
            $message_id = $_GET['message_id'];
            $sql_message = "SELECT * FROM messages WHERE message_id = $message_id AND user_id=$userid ";
            $result_message = $conn->query($sql_message);

        if ($result_message->num_rows > 0) {
            
            $message = $result_message->fetch_assoc();
            echo '<div class="message-details">';
            echo '<a class="msg-address">' . $message['msg_address'] . '</a>';
            echo '<div class="msg-from"><a>من :admin</a>';
            echo '<p class="msg-time">' .($message['sent_at']) . '</p>';
            echo '<hr><p class="msg_text">' . nl2br($message['message_text']) . '</p>';
            echo '</div>';

            // تحديث حالة الرسالة إلى مقروءة 
            $update_sql = "UPDATE messages SET is_read = 1 WHERE message_id = $message_id";
            $conn->query($update_sql);
        } else {
            echo '<div class="message-details">';
            echo '<p>لم يتم العثور على الرسالة.</p>';
            echo '</div>';
        }
    }
    ?>
</div>
</body>
</html>
