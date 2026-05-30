<?php
include "../koneksi.php";

if(isset($_POST["tambah"])){

    $nama =
    $_POST["nama"];

    $role =
    $_POST["role"];

    mysqli_query(
      $conn,
      "
      INSERT INTO agents
      VALUES(
      NULL,
      '$nama',
      '$role'
      )
      "
    );
}