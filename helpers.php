<?php

function sendAPIOutput(string $status, string $message, array $data = []) : void {
    die(json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]));
}

function goIfNotLoggedInMgmt(string $path) : void {
    if(!isset($_SESSION['management'])) {
        header('Location: ' . $path);
        exit;
    }
}

function goIfLoggedInMgmt(string $path) : void {
    if(isset($_SESSION['management'])) {
        header('Location: ' . $path);
        exit;
    }
}

function goIfNotLoggedInAdmin(string $path) : void {
    if(!isset($_SESSION['admin'])) {
        header('Location: ' . $path);
        exit;
    }
}

function goIfLoggedInAdmin(string $path) : void {
    if(isset($_SESSION['admin'])) {
        header('Location: ' . $path);
        exit;
    }
}

function goIfNotLoggedIn(string $path) : void {
    if(!isset($_SESSION['user'])) {
        header('Location: ' . $path);
        exit;
    }
}

function goIfLoggedIn(string $path) : void {
    if(isset($_SESSION['user'])) {
        header('Location: ' . $path);
        exit;
    }
}

function generateUPCCode() {
    $number = str_pad(rand(0, pow(10, 11) - 1), 11, "0", STR_PAD_LEFT);
    
    $checkDigit = 0;
    for($i = 0; $i <= 10; $i += 2) {
        $checkDigit += intval(substr($number, $i, 1));
    }
    $checkDigit *= 3;

    for($i = 1; $i <= 11; $i += 2) {
        $checkDigit += intval(substr($number, $i, 1));
    }

    $checkDigit = (($checkDigit % 10) == 0) ? '0' : strval((10 - ($checkDigit % 10)));

    return $number . $checkDigit;
}

function generateBarcode($drawers) {
    $number = generateUPCCode();
    $found = false;
    foreach($drawers as $d) {
        if($d['barcode'] == $number) $found = true;
    }

    if($found == true) {
        return generateBarcode($drawers);
    } else {
        return $number;
    }
}