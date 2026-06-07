<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "Ev@nGR06",
    "valorant_tracker"
);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
