<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "Ev@nGR06",
    "valorant_tracker"
);

if(!$conn){
    die("Koneksi gagal");
}