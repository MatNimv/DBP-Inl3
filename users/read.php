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


    sendJson($usersById);
    exit();
}

sendJson($allUsers);
?>