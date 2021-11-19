<?php
require_once "../functions.php";

$allUsers = loadJson("users.json");
$allPets = loadJson("../pets.json")

?>
<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod === "GET") {
    //first_name
    if (isset($_GET["first_name"])) {
        foreach ($allUsers as $user => $user) {
            if ($user["first_name"] == $_GET["first_name"]) {
                sendJson($allUsers[$user]);
            }
        }
    }
    if (isset($_GET["limit"])) {
        $limit = $_GET["limit"];
        $slicedUsers = array_slice($users, 0, $limit);
        sendJson($slicedUsers);
    }


    if (isset($_GET["ids"])) {
        $ids = explode(",", $_GET["ids"]);
        $usersById = [];

        foreach ($users as $user) {
            if (in_array($user["id"], $ids)) {
                $usersById[] = $user;
            }
        }

        sendJson($usersById);
    }

    sendJson($users);
}


?>