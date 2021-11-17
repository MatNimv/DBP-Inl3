<?php
header( "Content-type: application/json" );
require_once "functions.php";

$allUsers = loadJson("users.json");
$allPets = loadJson("../pets.json")

?>

<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
//first_name
if (isset($_GET["first_name"])) {
    foreach ($allUsers as $user => $user) {
        if ($user["first_name"] == $_GET["first_name"]) {
            sendJson($allUsers[$user]);
        }
    }
}

?>