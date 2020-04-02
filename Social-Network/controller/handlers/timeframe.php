<?php

declare(strict_types=1);

$dateTimeNow = date('Y-m-d  H:i:s');

$startDate  = new DateTime($dateTime);
$endDate    = new DateTime($dateTimeNow);

$interval   = $startDate->diff($endDate);

// ---------------------------------------------------------------------- Years

if (
    $interval->y >= 1
) {
    if (
        $interval == 1
    ) {
        $timeMsg = $interval->y . ' year ago';
    } else {
        $timeMsg = $interval->y . ' years ago';
    }
}

// --------------------------------------------------------------------- Months

elseif (
    $interval->m >= 1
) {
    if (
        $interval->m == 1
    ) {
        $timeMsg = $interval->m . ' month';
    } else {
        $timeMsg = $interval->m . ' months';
    }
}

// ----------------------------------------------------------------------- Days

elseif (
    $interval->d >= 1
) {
    if (
        $interval->d == 1
    ) {
        $timeMsg = 'Yesterday';
    } else {
        $timeMsg = $interval->d . ' days ago';
    }
}

// ---------------------------------------------------------------------- Hours

elseif (
    $interval->h >= 1
) {
    if (
        $interval->h == 1
    ) {
        $timeMsg = $interval->h . ' hour ago';
    } else {
        $timeMsg = $interval->h . ' hours ago';
    }
}

// -------------------------------------------------------------------- Minutes

else {
    if (
        $interval->i <= 1
    ) {
        $timeMsg = 'Just Now';
    } else {
        $timeMsg = $interval->i . ' minutes ago';
    }
}

?>
