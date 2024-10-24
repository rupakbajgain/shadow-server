<?php
$res = new StdClass;
$res->success = TRUE;

function main(){
    global $res;
    $p = isset($_POST['p'])?$_POST['p']:'';

    $db = new SQLite3('blob.db');
    if(!$db) {
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    };
    $done=FALSE;
    //var_dump($_FILES);
    if (count($_FILES) > 0) {
        if (is_uploaded_file($_FILES['f']['tmp_name'])) {
            $imgMime = mime_content_type($_FILES['f']['tmp_name']);
            $imgData = file_get_contents($_FILES['f']['tmp_name']);
            $sql = "INSERT INTO B(C,T,P) VALUES(?,?,?)";
            $statement = $db->prepare($sql);
            $statement->bindParam(1, $imgData, SQLITE3_BLOB);
            $statement->bindParam(2, $imgMime, SQLITE3_TEXT);
            $statement->bindParam(3, $p, SQLITE3_TEXT);
            $ret = $statement->execute();
            if(!$ret) {
                $res->success = FALSE;
                $res->err = $db->lastErrorMsg();
                return;
            };
            $done=TRUE;
        }
    }
    if(!$done){
        $res->success = FALSE;
        $res->err = "No file(f) found";
        return;
    };
    $res->id = $db->lastInsertRowID();
    $db->close();
}

main();
header('Content-Type: application/json');
echo json_encode($res);
?> 
