<?php

function winRate($win, $total) {
    if ($total == 0) {
        return 0;
    }
    return round(($win / $total) * 100, 1);
}

function kda($kill, $death, $assist) {
    if ($death == 0) {
        $death = 1;
    }
    return round(($kill + $assist) / $death, 2);
}

/**
 * Hitung rata-rata KDA dari semua match player
 */
function avgKda($conn, $playerId) {
    $query = mysqli_query($conn, "
        SELECT kill_count, death_count, assist_count
        FROM matches
        WHERE player_id = '$playerId'
    ");

    if (!$query || mysqli_num_rows($query) == 0) {
        return 0;
    }

    $totalKda = 0;
    $count = 0;

    while ($row = mysqli_fetch_assoc($query)) {
        $totalKda += kda($row['kill_count'], $row['death_count'], $row['assist_count']);
        $count++;
    }

    return $count > 0 ? round($totalKda / $count, 2) : 0;
}

/**
 * Ambil username berdasarkan session id
 */
function getUsername($conn, $id) {
    $query = mysqli_query($conn, "SELECT username FROM users WHERE id = '$id'");
    if ($query && mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        return $row['username'];
    }
    return 'Unknown';
}

/**
 * Escape string untuk keamanan
 */
function esc($conn, $str) {
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($str)));
}
