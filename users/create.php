<?php
require_once "functions.php";

//Ladda in vår JSON-data från fil
$users = loadJSON("users.json");

//Vilken HTTP-metod vi tog emot samt vilken sorts content det är
$rqstMethod = $_SERVER["REQUEST_METHOD"];
$contentType = $_SERVER["CONTENT_TYPE"];

//Hämtar ut det som skickades till vår server
$data = file_get_contents("php://input");
$rqstData = json_decode($data_true, true);

if ($rqstMethod == "POST") {
    if (isset($rqstData["first_name"], $rqstData["last_name"], $rqstData["gender"], $rqstData["pet"])) {
    }
} else {
    $json = json_encode(["message" => "Method is not allowed!"]);
    sendJson($data, 405);
}
