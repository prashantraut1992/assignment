jQuery(document).ready(function(){
 if(localStorage.userdata){
    var editData = JSON.parse(localStorage.userdata);
    editData = editData[0];
    if (editData) {
        $("#full_name").val(editData.full_name);
        $("#user_name").val(editData.user_name);                
        $("#email").val(editData.email);
        localStorage.clear();
    }
}

});

//SET all data into user form
$( document ).ready(function() {

    $('.edit_user').on('click',function() {

        var user_id = $(this).attr('rel');
        if(!user_id || user_id == '') {
            alert('Error in processing .');
            return false;
        }

        var detailsurl = '/edit-user'; 

        $.ajax({
            url: detailsurl,
            method :'post',
            data: 'user_id='+user_id,
            dataType: 'json',
            success: function (data) {
                if (data.status == 'OK') {
                    localStorage.clear();
                    if(data.userdata){
                        localStorage.userdata = JSON.stringify(data.userdata); 
                    }                                        
                    window.location.replace("/edit-user-form");
                } else {
                    alert('Error in processing .');
                }

            },
            error: function(){
                alert('Error in processing ...');
            },
        });
    });
});


// on reset btn click
$('#reset_btn').on('click',function() {
    location.reload();
});

// on cancel btn click
$('#cancel_btn').on('click',function() {
    window.location.replace("/users-list");
    localStorage.clear();
});

//user form validation
$("#adduserform").validate({      
    rules: {
      full_name: "required",
      user_name: "required",
      email: "required",
      password: "required",
  },
  messages: {
    full_name: "Please enter full name",
    user_name: "Please enter user name",
    email: "Please enter email",
    password: "Please enter password",
},   
});

// check subject is unique or not
$('#user_name').on('change',function() {

    var userNameVal = $(this).val();
    if(userNameVal == '') {
        return false;
    }   

    var checkurl = '/check-user-exist';

    $.ajax({
        url: checkurl,
        method :'post',
        data: 'user_name='+userNameVal,
        dataType: 'json',
        success: function (data) {
            if (data.status == 'OK') {
                $('#user_name').css("border", "1px solid green");
                $('#usernameMessage').hide();
                return true;
            } else {
                $('#usernameMessage').show();
                    $('#usernameMessage').html('User name already exists.');                    
                    $('#user_name').css("border", "1px solid red");
                    $('#user_name').focus();
                }
            },
            error: function(){
                alert('Error in processing !');
            },
        });
});



// Add user on submit btn click
$('#submit_btn').on('click',function() {

    if($("#adduserform").valid()) {

        var formData = $('#adduserform').serialize();

        var addurl = '/add-user';

        $.ajax({
            url: addurl,
            method :'post',
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data.status == 'OK') {
                    alert('Added successfully !');
                    window.location.replace("/users-list");
                } else {
                    alert('Error in processing !');
                }
            },
            error: function(){
                alert('Error in processing !');
            },
        });
    }
});


// on delete btn click delete single or multiusers *soft-delete
$('.delete_user').on('click',function() {
    var user_id = [];

    $.each($(".user_id_chk:checked"), function(){
        user_id.push($(this).attr('rel'));
    });

    if(user_id.length == 0) {
        alert('Please select user for action. ');
    } 

    if(user_id.length > 0 && confirm("Are you sure !")) { 

        userids = user_id.join(", ");

        var deleteurl = '/delete-user'; 

        $.ajax({
            url: deleteurl,
            method :'post',
            data: 'userids='+userids,
            dataType: 'json',
            success: function (data) {

                if (data.status == 'OK') {
                    alert('Deleted successfully .');
                    location.reload();
                } else {
                    alert('Error in processing .');
                }
            },
            error: function(){
                alert('Error in processing .');
            },
        });
    } 
});

// on delete btn click activate single or multiusers 
$('.activate_user').on('click',function() {
    var user_id = [];

    $.each($(".user_id_chk:checked"), function(){
        user_id.push($(this).attr('rel'));
    });

    if(user_id.length == 0) {
        alert('Please select user for action. ');
    } 

    if(user_id.length > 0 && confirm("Are you sure !")) { 

        userids = user_id.join(", ");

        var deleteurl = '/activate-user'; 

        $.ajax({
            url: deleteurl,
            method :'post',
            data: 'userids='+userids,
            dataType: 'json',
            success: function (data) {

                if (data.status == 'OK') {
                    alert('Activated successfully .');
                    location.reload();
                } else {
                    alert('Error in processing .');
                }
            },
            error: function(){
                alert('Error in processing .');
            },
        });
    } 
});

// get form data by ajax
$('.edit_user').on('click',function() {

    var userid = $(this).attr('rel');
    if(!userid || userid == '') {
        alert('Error in processing .');
        return false;
    }

    var detailsurl = '/edit-user'; 

    $.ajax({
        url: detailsurl,
        method :'post',
        data: 'user_id='+userid,
        dataType: 'json',
        success: function (data) {
            if (data.status == 'OK') {                    
                return true;
            } else {
                alert('Error in processing .');
                return false;
            }
        },
        error: function(){
            alert('Error in processing ...');
        },
    });        
});


// update user on submit btn click
$('#update_btn').on('click',function() {

    if($("#adduserform").valid()) {

        var formData = $('#adduserform').serialize();

        var addurl = '/update-user';

        $.ajax({
            url: addurl,
            method :'post',
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data.status == 'OK') {
                    alert('Updated successfully !');
                    window.location.replace("/users-list");
                } else {
                    alert('Error in processing !');
                }
            },
            error: function(){
                alert('Error in processing !');
            },
        });    
    }
});



$('#email').change(function(e){
    var email = $("#email").val();
    const options = {method: 'GET', headers: {Accept: 'application/json'}};
    fetch('https://api.debounce.io/v1/?api=API-KEY-HERE&email='+email, options)
    .then(response => response.json())
    .then(response => {console.log('Success:', response);
        if(response.debounce.send_transactional != 1) {            
            $('#emailMessage').html('Email not valid.');
        }else{            
            $('#emailMessage').html();
        }
    })
    .catch(err => {console.log('err:', err);
     $('#emailMessage').html('Valid Email.');
 });

  //cancel the submit event
  e.preventDefault();
});


$(document).ready(function () {  
    $('#password').keyup(function () {  
        $('#strengthMessage').html(checkStrength($('#password').val()))  
    })  
    function checkStrength(password) {  
        var strength = 0  
        if (password.length < 6) {  
            $('#strengthMessage').removeClass()  
            $('#strengthMessage').addClass('Short')  
            return 'Too short'  
        }  
        if (password.length > 7) strength += 1  
        // If password contains both lower and uppercase characters, increase strength value.  
    if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1  
        // If it has numbers and characters, increase strength value.  
    if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1  
        // If it has one special character, increase strength value.  
    if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1  
        // If it has two special characters, increase strength value.  
    if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1  
        // Calculated strength value, we can return messages  
        // If value is less than 2  
        if (strength < 2) {  
            $('#strengthMessage').removeClass()  
            $('#strengthMessage').addClass('Weak')  
            return 'Weak'  
        } else if (strength == 2) {  
            $('#strengthMessage').removeClass()  
            $('#strengthMessage').addClass('Good')  
            return 'Good'  
        } else {  
            $('#strengthMessage').removeClass()  
            $('#strengthMessage').addClass('Strong')  
            return 'Strong'  
        }  
    }  
});  