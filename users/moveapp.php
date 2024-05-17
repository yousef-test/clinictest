<?php

$sql_transfer = "INSERT INTO history (doctorid, userid, apptime, appdate)
SELECT doctorid, userid, apptime, appdate FROM appointments
WHERE TIMESTAMP(appdate, apptime) < TIMESTAMP(NOW()) - INTERVAL 20 MINUTE";

if ($conn->query($sql_transfer) === TRUE) {

$sql_delete = "DELETE FROM appointments 
  WHERE TIMESTAMP(appdate, apptime) < TIMESTAMP(NOW()) - INTERVAL 20 MINUTE";

if (!$conn->query($sql_delete)) {
$message = "حدث خطأ أثناء حذف المواعيد من جدول المواعيد: " . $conn->error;
}

} else {
$message = "حدث خطأ أثناء نقل المواعيد إلى جدول السجلات: " . $conn->error;
}

?>