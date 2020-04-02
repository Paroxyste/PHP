function getLiveSearchUsers(value, user) {
  let ajax_search = 'controller/handlers/ajax_search.php'

  $.post(ajax_search, {query:value, userLoggedIn:user}, function(data) {
    $('.search_results').html(data);
  });
}