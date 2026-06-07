<?php
session_start();
include "../koneksi.php";

// Auth check
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'player') {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['id'];

if (!isset($_GET['id_match']) || !is_numeric($_GET['id_match'])) {
    header("Location: matchhistory.php");
    exit;
}

$matchId = intval($_GET['id_match']);

// Hapus hanya jika match milik player ini (security check)
mysqli_query($conn, "
    DELETE FROM matches
    WHERE id_match = '$matchId'
    AND player_id  = '$id'
");

header("Location: matchhistory.php?success=Match+berhasil+dihapus");
exit;
