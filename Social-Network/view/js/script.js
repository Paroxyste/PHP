// User search 
function getLiveSearchUsers(value, user) {
  let ajax_search = 'controller/handlers/ajax_search.php'

  $.post(ajax_search, {query:value, userLoggedIn:user}, function(data) {
    $('.search_results').html(data);
  });
};

// Search friend for new conversation
function getTchatFriend(value, user) {
  let ajax_tchat_search = 'controller/handlers/ajax_friend_search.php'

  $.post(ajax_tchat_search, {query:value, userLoggedIn:user}, function(data) {
    $(".results").html(data);
  });
};

// Messages & notifications data
function getDropdownData(user, type) {
  if ($('.dropdown_data_window').css('height') == '0px') {
    let pageName;

    if (type == 'notification') {
      pageName = 'ajax_load_notifications.php';
      $('span').remove('#unread_notification');
    } else if (type == 'message') {
      pageName = 'ajax_load_messages.php';
      $('span').remove('#unread_message');
    };

    let ajaxreq = $.ajax({
      url: 'controller/handlers/' + pageName,
      type: 'POST',
      data: 'page=1&userLoggedIn=' + user,
      cache: false,

      success: function(response) {
        $('.dropdown_data_window').html(response);
        $('.dropdown_data_window').css({"padding" : "0px", "max-height": "280px", "border" : "1px solid #DADADA"});
        $('#dropdown_data_type').val(type);
      }
    });
  } else {
    $('.dropdown_data_window').html('');
     ('.dropdown_data_window').css({"padding" : "0px", "height": "0px", "border" : "1px solid red"});
  };
};