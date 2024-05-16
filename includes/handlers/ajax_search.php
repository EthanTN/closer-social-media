<?php
    include("../../config/config.php");
    include("../../includes/classes/User.php");

    $query = $_POST['query'];
    $userLoggedIn = $_POST['userLoggedIn'];
    $name = explode(" ",$query);
    //if query contains an underscore, user is searching for usernames
    if(strpos($query, '_') !== false){
        $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
    }

    //if there are 2 words, take them as first and last name
    else if (count($name) == 2){
        $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$name[0]%' AND last_name LIKE '$name[1]%') AND user_closed='no' LIMIT 8");
    }
    //if query has 1 word, search first or last name
    else{
        $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$name[0]%' OR last_name LIKE '$name[0]%')  AND user_closed='no' LIMIT 8");
    }

    if($query != ""){
        while($row = mysqli_fetch_array($usersReturnedQuery)){
            $user = new User($con, $userLoggedIn);
            if($row['username'] != $userLoggedIn){
                $mutual_friends = $user->getMutualFriends($row['username'])." friends in common";

            }else{
                $mutual_friends = "";
            
            }
            echo "<div class='resultDisplay'>
                    <a href='".$row['username']."' style='color: #1485BD' >
                        <div class='liveSearchProfilePic'>
                            <img src='".$row['profile_pic']."'>
                        </div>
                        <div class='liveSearchText'>
                            ".$row['first_name']. " ".$row['last_name']."
                            <p style='margin: 0;'>".$row['username']."</p>
                            <p id='grey'>".$mutual_friends."</p>
                        </div>
                    </a>
                  </div>";
                  
        }
    }
?>