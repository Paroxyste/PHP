function getNumMsg(user,type){if($('.dropdown_data_window').css('height')=='0px'){let file;if(type=='message'){file='ajax_load_messages.php';$('span').remove('#unread_message')};let ajaxreq=$.ajax({url:'controller/handlers/'+file,type:'POST',data:'page=1&userLoggedIn='+user,cache:!1,success:function(response){$('.dropdown_data_window').html(response);$('.dropdown_data_window').css({'max-height':'280px'});$('#dropdown_data_type').val(type)}})}else{$('.dropdown_data_window').html('')
$('.dropdown_data_window').css({'max-height':'0px'})}};function getLiveSearchUsers(value,user){let path='controller/handlers/';let file='ajax_search.php';let pageName=path+file;$.post(pageName,{query:value,userLoggedIn:user},function(data){$('.search_results').html(data)})};function getTchatFriend(value,user){let path='controller/handlers/';let file='ajax_friend_search.php';let pageName=path+file;$.post(pageName,{query:value,userLoggedIn:user},function(data){$('.results').html(data)})};function isElementInView(el){if(el==null){return};let rect=el.getBoundingClientRect();return(rect.top>=0&&rect.left>=0&&rect.bottom<=(window.innerHeight||document.documentElement.clientHeight)&&rect.right<=(window.innerWidth||document.documentElement.clientWidth))};function loadMsgData(){if(dropdownInProgress){return};dropdownInProgress=!0;let page=$('.dropdown_data_window').find('.nextPageDropdownData').val()||1;let type=$('#dropdown_data_type').val();let file;if(type=='message'){file="ajax_load_messages.php"};$.ajax({url:'controller/handlers/'+file,type:'POST',data:'page='+page+'&userLoggedIn='+userLoggedIn,cache:!1,success:function(response){$('.dropdown_data_window').find('.nextPageDropdownData').remove();$('.dropdown_data_window').find('.noMoreDropdownData').remove();$('.dropdown_data_window').append(response);dropdownInProgress=!1}})}