<?php

 include('./phpqrcode.php');

//    $param = $_GET['id']; // remember to sanitize that - it is user input!
   
    // we need to be sure ours script does not output anything!!!
    // otherwise it will break up PNG binary!
   
    ob_start("callback");
   
    // here DB request or some processing
    $codeText = $_GET['name']." : https://".$_SERVER['SERVER_NAME']."/pages/UI.php?operation=details&class=" . $_GET['class'] . "&id=" . $_GET['id'];
    //$codeText = "<a href='https://".$_SERVER['SERVER_NAME']."/pages/UI.php?operation=details&class=" . $_GET['class'] . "&id=" . $_GET['id'] . "&' >".$_GET['name']."</a>";
   
    // end of processing here
    $debugLog = ob_get_contents();
    ob_end_clean();

    // outputs image directly into browser, as PNG stream
    QRcode::png($codeText);

?>
