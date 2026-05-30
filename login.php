<?php
session_start();
include "koneksi.php";

if(isset($_POST["login"])){

    $username = $_POST["username"];
    $password = $_POST["password"];

    $query =
    mysqli_query(
        $conn,
        "
        SELECT * FROM users
        WHERE username='$username'
        AND password='$password'
        "
    );

    if(mysqli_num_rows($query)>0){

        $data =
        mysqli_fetch_assoc($query);

        $_SESSION["id"] =
        $data["id"];

        $_SESSION["role"] =
        $data["role"];

        if(
          $data["role"]=="admin"
        ){
            header(
              "Location: admin/dashboard.php"
            );
        } else {
            header(
              "Location: player/dashboard.php"
            );
        }

    } else {

        echo "Login gagal";
    }
}