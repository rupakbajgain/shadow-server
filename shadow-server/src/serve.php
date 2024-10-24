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

    $path = $_SERVER['REQUEST_URI'];
    $pos1 = strpos($path, "serve.php");
    $path = substr($path, $pos1+9);

    $pos = strpos($path, "?");
    if($pos)
        $path = substr($path, 0, $pos);
    if($path==''){
        $final = substr($_SERVER['REQUEST_URI'],0,$pos1+10);
        header('Location: '.$final."/");
        die();
    }

    $db = new SQLite3('blob.db');
    if(!$db) {
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    };

    $sql = "SELECT * from B where P = ? LIMIT 1";
    $statement = $db->prepare($sql);
    $statement->bindParam(1, $path, SQLITE3_TEXT);
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
            header('Content-Type: '.get_mime($path, $row['T']));
        echo $row['C'];
        exit();
    }else{
        $res->success = FALSE;
        $res->err = $path." Not Found ";
        return;
    }

    $db->close();
}

main();
header('Content-Type: application/json');
echo json_encode($res);
?> 
