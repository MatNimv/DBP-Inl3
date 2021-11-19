<?php
require_once "../functions.php";

$allUsers = loadJson("users.json");
$allPets = loadJson("../pets.json");
?>
<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod === "GET") {
    //first_name
    if (isset($_GET["first_name"])) {
        foreach ($allUsers as $user => $user) {
            if ($user["first_name"] == $_GET["first_name"]) {
                sendJson($allUsers[$user]);
                exit();
            }
        }
    }
}
if (isset($_GET["limit"])) {
    $limit = $_GET["limit"];
    $slicedUsers = array_slice($allUsers, 0, $limit);
    sendJson($slicedUsers);
    exit();
}


if (isset($_GET["id"])) {
    $ids = explode(",", $_GET["id"]);
    $usersById = [];

    foreach ($allUsers as $user) {
        if (in_array($user["id"], $ids)) {
            $usersById[] = $user;
        }
    }
    if (count($usersById) == 0) {
        $json = json_encode(["message" => "User does not exist"]);
        sendJson($json, 400);
    }
}

    //om det finns AGE i URL.
    if(isset($_GET["age"])){
        $age = $_GET["age"];
        $ageArray = [];

        foreach($allUsers as $user){
            if ($user["age"] == $age){
                array_push($ageArray, $user);
            }
        } //om det inte finns användare i den specifika åldern.
        if (count($ageArray)== 0){
            sendJson(["message" => "No users at this age."]);
        } else { //annars skickar den ut alla med den specifika åldern.
            sendJson($ageArray);
        }
    } else { //om inte det finns AGE, skickas alla användare ut ändå.
        sendJson($allUsers);
    }
?>