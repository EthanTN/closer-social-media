<?php
    require 'config/config.php';
    include("includes/classes/User.php");
    include("includes/classes/Post.php");
    include("includes/classes/Message.php");
    include("includes/classes/Notification.php");

    if(isset($_SESSION['username'])){
        $userLoggedIn = $_SESSION['username'];
        $user_detail_query = mysqli_query($con, "SELECT * FROM users WHERE username = '$userLoggedIn'");
        $user = mysqli_fetch_array($user_detail_query);
    }
    else{
        header("Location: register.php");
    }
    
?>
<html>
    <!-- <head>
        <title>
            Welcome to Closer
        </title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/closer.js"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <link href="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
        <link href="https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
        <link rel="stylesheet"  type='text/css' href="assets/css/style.css" >

    </head> -->
    <head>
        <title>Welcome to Closer</title>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- <script src="assets/js/bootstrap.js"></script> -->
        <script src="assets/js/closer.js"></script>
       
        <script src="assets/js/bootbox.min.js"></script>
        <script src="assets/js/jquery.Jcrop.js"></script>
	    <script src="assets/js/jcrop_bits.js"></script>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
         <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        
      
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />
       
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <link rel="shortcut icon" type="image/x-icon" href="assets/images/background/logo.jpg" />
    </head>
    <body>
        <div class="top_bar">
            <div class="logo">
                <a href="index.php">Closer</a>
            </div>
            <div class="search">
                <form action="search.php" method="GET" name="search_form">
                    <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">
                    <div class="button-holder">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <div class="search_results">

                    </div>
                    <div class="search_results_footer_empty">

                    </div>
                </form>
            </div>
            <nav>

                <?php 
                    $message = new Message($con, $userLoggedIn);
                    $num_messages = $message->getUnreadNum();

                    $notifications  = new Notification($con, $userLoggedIn);
                    $num_notifications = $notifications->getUnreadNum();

                    $user_obj  = new User($con, $userLoggedIn);
                    $num_requests = $user_obj->getNumberOfFriendRequests();
                ?>

                <a href="#">
                    <?php echo $user['first_name']?>
                </a>
                <a href="#">
                    <i class="fa-solid fa-house"></i>
                </a>
                <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'messages')">
                    <i class="fa-regular fa-envelope"></i>
                    <?php
                        if($num_messages > 0)
                        echo 
                        '<span class="notification_badge" id="unread_message">'.$num_messages.'</span>';
                    ?>
                </a>
                <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
                    <i class="fa-regular fa-bell"></i>
                    <?php
                        if($num_notifications > 0)
                        echo 
                        '<span class="notification_badge" id="unread_notification">'.$num_notifications.'</span>';
                    ?>
                </a>
                <a href="requests.php">
                    <i class="fa-solid fa-people-group"></i>
                    <?php
                        if($num_requests > 0)
                        echo 
                        '<span class="notification_badge" id="unread_requests">'.$num_requests.'</span>';
                    ?>
                </a>
                <a href="settings.php">
                    <i class="fa-solid fa-gear"></i>
                </a>
                <a href="includes/handlers/logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>

            </nav>
            <div class='dropdown_data_window' style='height: 0px; border: none;'>

            </div>
            <input type="hidden" id='dropdown_data_type' value=''>
        </div>
        <script>
                var userLoggedIn = '<?php echo $userLoggedIn; ?>';
                $(document).ready(function() {

                  


                    $('.dropdown_data_window').scroll(function() {
                        var inner_height = $('.dropdown_data_window').innerHeight(); //Div containing posts
                        var scroll_top = $('.dropdown_data_window').scrollTop();
                        var page = $('.dropdown_data_window').find('.nextPageDrowpdownData').val();
                        var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();
                       
                        if ((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false') {

                            var pageName; //name of page to send to ajax request to
                            var type = $('#dropdown_data_type').val();

                            if(type=='notification'){
                                pageName = "ajax_load_notifications.php";

                            }else if (type == 'message'){
                                pageName = 'ajax_load_messages.php';
                            }
                            
                            var ajaxReq = $.ajax({
                                url: "includes/handlers/"+pageName,
                                type: "POST",
                                data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                                cache:false,

                                success: function(response) {
                                    $('.dropdown_data_window').find('.nextPageDrowpdownData').remove(); //Removes current .nextpage 
                                    $('.dropdown_data_window').find('.noMoreDropdownData').remove(); //Removes current .nextpage 

                                    $('.dropdown_data_window').append(response);
                                }
                            });

                        } //End if 
                    
                        return false;

                    }); //End (window).scroll(function())


                });
            </script>
        <div class='wrapper'>
            