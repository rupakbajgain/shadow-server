<?php
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

    if(!isset($channel)){
        $res->success = FALSE;
        $res->err = "Channel name required";
        return;
    }

    $db = new SQLite3('db/'.sanitize($channel).'.db');
    if(!$db) {
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    };

    $json = file_get_contents('php://input');
    $data = json_decode($json);
    $data->s = json_encode($data->m);

    $sql=$db->prepare("INSERT INTO MSG (F,T,M) VALUES(?,?,?)");
    $sql->bindParam(1, $data->f, SQLITE3_TEXT);
    $sql->bindParam(2, $data->t, SQLITE3_TEXT);
    $sql->bindParam(3, $data->s, SQLITE3_TEXT);
    
    if(!$sql->execute()){
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    }

    $res->id = $db->lastInsertRowID();

    $db->close();
}

main();
header('Content-Type: application/json');
echo json_encode($res);
?> 