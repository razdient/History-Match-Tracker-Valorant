<?php
session_start();
include "../koneksi.php";

if(isset($_POST["simpan"])){

    $player =
    $_SESSION["id"];

    $agent =
    $_POST["agent_id"];

    $map =
    $_POST["map_id"];

    $result =
    $_POST["result"];

    $kill =
    $_POST["kill"];

    $death =
    $_POST["death"];

    $assist =
    $_POST["assist"];

    mysqli_query(
        $conn,
        "
        INSERT INTO matches
        (
          player_id,
          agent_id,
          map_id,
          result,
          kill_count,
          death_count,
          assist_count
        )
        VALUES
        (
          '$player',
          '$agent',
          '$map',
          '$result',
          '$kill',
          '$death',
          '$assist'
        )
        "
    );

    header(
      "Location: dashboard.php"
    );
}