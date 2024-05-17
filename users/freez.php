<?php
$sql = "SELECT userid, activity FROM users WHERE userid = '{$_SESSION['user_id']}'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if($row['activity'] === '0') {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }
}
?>