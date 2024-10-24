<?php
$res = new StdClass;
$res->success = TRUE;

function get_mime($path,$mime){
	$ext = pathinfo($path, PATHINFO_EXTENSION);
	if($mime=='text/plain' && $ext=='js')return 'text/javascript';
	else return $mime;
}

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

    $sql = "SELECT * from B where ID = ? LIMIT 1";
    $statement = $db->prepare($sql);
    $statement->bindParam(1, $id, SQLITE3_INTEGER);
    $ret = $statement->execute();
    if(!$ret) {
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    };
    if($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
        if(isset($_GET['m']))
            header('Content-Type: '.$_GET['m']);
        else
            header('Content-Type: '.get_mime($row['P'],$row['T']));
        echo $row['C'];
        exit();
    }else{
        $res->success = FALSE;
        $res->err = "Not Found";
        return;
    }

    $db->close();
}

main();
header('Content-Type: application/json');
echo json_encode($res);
?> 
