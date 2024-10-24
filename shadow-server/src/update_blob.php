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

    if (count($_FILES) > 0) {
        if (is_uploaded_file($_FILES['f']['tmp_name'])) {
            $imgData = file_get_contents($_FILES['f']['tmp_name']);
            $imgMime = mime_content_type($_FILES['f']['tmp_name']);
            $sql = "UPDATE B SET C=?,T=? WHERE ID=?";
            $statement = $db->prepare($sql);
            $statement->bindParam(1, $imgData, SQLITE3_BLOB);
            $statement->bindParam(2, $imgMime, SQLITE3_TEXT);
            $statement->bindParam(3, $id, SQLITE3_INTEGER);
            $ret = $statement->execute();
            if(!$ret) {
                $res->success = FALSE;
                $res->err = $db->lastErrorMsg();
                return;
            };
        }
    }

    $db->close();
}

main();
header('Content-Type: application/json');
echo json_encode($res);
?> 
