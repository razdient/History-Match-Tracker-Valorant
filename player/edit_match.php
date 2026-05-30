<?php
include "../koneksi.php";

$id = $_POST["id"];

$result =
$_POST["result"];

mysqli_query(
    $conn,
    "
    UPDATE matches
    SET result='$result'
    WHERE id='$id'
    "
);

header(
 "Location: dashboard.php"
);