<?php
    include("includes/header.php");
    if(isset($_GET['q'])){
        $query = $_GET['q'];

    }
    else{
        $query = '';
    }
    if(isset($_GET['type'])){
        $type = $_GET['type'];

    }
    else{
        $type = 'name';
    }
?>
<div class="main_column column" id="main_column">
    <?php
        if($query == ""){
            echo "You must enter something in the search box!";
        }else{
            
            //if query contains an underscore, user is searching for usernames
            if($type == "username"){
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
            }else{
                $name = explode(" ",$query);
                //if there are 2 words, take them as first and last name
                if (count($name) == 3){
                    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$name[0]%' AND last_name LIKE '$name[2]%') AND user_closed='no'");
                }
                //if query has 1 word, search first or last name
                else if (count($name) == 2){
                    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$name[0]%' AND last_name LIKE '$name[1]%') AND user_closed='no'");
                   
                }else{
                    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$name[0]%' OR last_name LIKE '$name[0]%')  AND user_closed='no'");
                }
            }
            if(mysqli_num_rows($usersReturnedQuery) == 0){
                echo "We can't find anyone with a ".$type." like: ".$query;
            }else{
                echo mysqli_num_rows($usersReturnedQuery). " results found: <br><br>";
            }
            echo "<p id='grey'>Try searching for:</p>";
            echo "<a href='search.php?q=".$query."&type=name'>Names</a>, <a href='search.php?q=".$query."&type=username'>Username</a><br><br><hr id='search_hr'>";
            while ($row = mysqli_fetch_array($usersReturnedQuery)){
                $user_obj = new User($con, $user['username']);
                $button = "";
                $mutual_friends = "";

                if($user['username'] != $row['username']){
                    if($user_obj->isFriend($row['username'])){
                        $button = "<input type='submit' name='".$row['username']."' class='danger' value='Stay Away'>";
                    }else if($user_obj->didReceiveRequest( $row['username'])){
                        $button = "<input type='submit' name='".$row['username']."' class='warning' value='Respond to request'>";
                    }else if($user_obj->didSendRequest( $row['username'])){
                        $button = "<input type='submit' name=''  class='default' value='Request Sent'>";
                    }else{
                        $button = "<input type='submit' name='".$row['username']."' class='success' value='Get Closer'>";
                    }

                    $mutual_friends = $user_obj->getMutualFriends($row['username'])." friends in common";

                    //Button form
                    if(isset($_POST[$row['username']])){
                        if($user_obj->isFriend($row['username'])){
                            $user_obj->removeFriend($row['username']);
                            header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                        }else if ($user_obj->didReceiveRequest($row['username'])){
                            header("Location: requests.php");
                        }else if ($user_obj->didSendRequest($row['username'])){

                        }else{
                            $user_obj->sendRequest($row['username']);
                            header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                        }
                    }
                }
                echo "
                    <div class='search_result'>
                        <div class='searchPageFriendButtons'>
                            <form action='' method='POST'>
                                ".$button."
                                <br>
                            </form>
                        </div>

                        <div class='result_profile_pic'>
                            <a href='". $row['username']."'>
                                <img src='".$row['profile_pic']."' style='height: 100px;'>
                            </a>
                        </div>
                        <a href='".$row['username']."'>".$row['first_name']." ".$row['last_name']."
                            <p style='margin: 0;' id='grey'>".$row['username']."</p>
                        </a>
                        <br>
                        ".$mutual_friends."<br>
                    </div>
                    <hr id='search_hr'>
                ";
            }//end while loop
        }
    ?>
</div>