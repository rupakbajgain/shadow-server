<?php
// params
// c = channel name
// i = msg id
$res = new StdClass;
$res->success = TRUE;

function sanitize($name) {
    return str_replace(array_merge(
        array_map('chr', range(0, 31)),
        array('<', '>', ':', '"', '/', '\\', '|', '?', '*')
    ), '', $name);
}

function main(){
    global $res;
    $channel = $_GET['c'];
    $id = $_GET['i'];

    if(!isset($channel)){
        $res->success = FALSE;
        $res->err = "Channel name required";
        return;
    }

    if(!isset($id)){
        $res->success = FALSE;
        $res->err = "Message ID required";
        return;
    }

    $db = new SQLite3('db/'.sanitize($channel).'.db');
    if(!$db) {
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    };

    $sql=$db->prepare("DELETE from MSG where ID = ?");
    $sql->bindParam(1, $id, SQLITE3_INTEGER);
    
    if(!$sql->execute()){
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    }

    $db->close();
}

main();
header('Content-Type: application/json');
echo json_encode($res);
?> 