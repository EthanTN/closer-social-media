<?php
    include("../../config/config.php");
    include("../classes/User.php");
    include("../classes/Message.php");

    $limit = 10; //number of message to load
    $message = new Message($con, $_REQUEST['userLoggedIn']);

    echo $message->getConvosDropdown($_REQUEST, $limit);
?>