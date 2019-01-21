<?php
    if(isset($_SESSION['user'])){
        echo json_encode(array("user"=>strval($_SESSION['user'])));
    }else{
        echo json_encode(array("user"=>'unset'));
    }
?>