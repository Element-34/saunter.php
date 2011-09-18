<?php

function randomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}

function randomNumber($length = 1) {
    $characters = '0123456789';
    $string = '';

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}

function randomCharacter($length = 1) {
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $string = '';

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}

function generateEmailAddress() {
    return randomString() . '@' . randomString() . '.com';
}

function generatePostalCode($selected) {
    if (strcmp($selected, "United Kingdom") == 0) {
        // this isn't exactly correct as there are rules about which letters can be used in which format, but structurally
        // these cover it -- well, according to wikipedia at least http://en.wikipedia.org/wiki/Postcodes_in_the_United_Kingdom#Format
        $variant = rand(0, 5);
        switch ($variant) {
            case 0:
                // A9 9AA
                $postalCode = randomCharacter() . randomNumber() . ' ' . randomNumber() . randomCharacter(2);
                break;
            case 1:
                // A99 9AA
                $postalCode = randomCharacter() . randomNumber(2). ' ' . randomNumber() . randomCharacter(2);
                break;
            case 2:
                // AA9 9AA
                $postalCode = randomCharacter(2). randomNumber() . ' ' . randomNumber() . randomCharacter(2);
                break;
            case 3:
                // AA99 9AA
                $postalCode = randomCharacter(2). randomNumber(2) . ' ' . randomNumber() . randomCharacter(2);
                break;
            case 4:
                // A9A 9AA
                $postalCode = randomCharacter() . randomNumber() . randomCharacter() . ' ' . randomNumber() . randomCharacter(2);
                break;
            case 5:
                // AA9A 9AA
                $postalCode = randomCharacter(2). randomNumber() . randomCharacter() . ' ' . randomNumber() . randomCharacter(2);
                break;
        }
    } elseif (strcmp($selected, "United States") == 0) {
        // zip5 and zip9
        $variant = rand(0, 1);
        // only does zip 5
        $variant = 0;
        switch ($variant) {
            case 0:
                // 12345
                $postalCode = randomNumber(5);
                break;
            case 1:
                // 12345-1234
                $postalCode = randomNumber(5) . '-' . randomNumber(4);
                break;
        }
    } elseif (strcmp($selected, "Canada") == 0) {
        // A1A 1A1
        $postalCode = randomCharacter() . randomNumber() . randomCharacter() . ' ' . randomNumber() . randomCharacter() . randomNumber();
    }
    return $postalCode;
}
?>