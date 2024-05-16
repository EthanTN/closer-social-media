<?php
    include("../../config/config.php");
    include("../classes/User.php");
    include("../classes/Notification.php");

    $limit = 10; //number of message to load
    $notification = new Notification($con, $_REQUEST['userLoggedIn']);

    echo $notification->getNotifications($_REQUEST, $limit);
?>