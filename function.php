<?php

function winRate($win,$total){

    if($total == 0){
        return 0;
    }

    return round(
        ($win / $total) * 100,
        2
    );
}

function kda(
    $kill,
    $death,
    $assist
){

    if($death == 0){
        $death = 1;
    }

    return round(
        ($kill + $assist) / $death,
        2
    );
}