<?php
$res = new StdClass;
$res->success = TRUE;

function main(){
    global $res;
    $id = $_GET['i'];

    if(!isset($id)){
        $res->success = FALSE;
        $res->err = "Old blob ID required";
        return;
    }

    $db = new SQLite3('blob.db');
    if(!$db) {
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    };

    $sql = "DELETE from B where ID = ?";
    $statement = $db->prepare($sql);
    $statement->bindParam(1, $id, SQLITE3_INTEGER);
    $ret = $statement->execute();
    if(!$ret) {
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    };
    $db->close();
}

main();
header('Content-Type: application/json');
echo json_encode($res);
?> 