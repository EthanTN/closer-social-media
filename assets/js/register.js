$(document).ready(function() {
    //on click signup, hide login an show registration form
    $("#signup").click(function(){
        $("#first").slideUp("slow", function(){
            $("#second").slideDown("slow");
        });
    });

    $("#signin").click(function(){
        $("#second").slideUp("slow", function(){
            $("#first").slideDown("slow");
        });
    });


});