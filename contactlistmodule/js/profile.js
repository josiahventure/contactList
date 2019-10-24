(function($) {
  $('.control-label').mousedown(function(event) {
    $.ajax({
      type: "post",
      url: 'contacts',
      data: "somedata",
      cache: false,
      success: function(html) {
        $('#contact_table').html(html);
      }
    });
  });

  $('.link').mousedown(function(event) {
    var profile = ".profile_";
    profile += event.target.id;
    var loading = ".loading_";
    loading += event.target.id;
    var id = "id=";
    id += event.target.id;
    if ($(profile).is(':hidden')) {
      $("tr").removeClass("active_content");

      $(loading).slideDown("fast");
      $.ajax({
        type: "post",
        url: 'profile', // profile url
        data: id, // contact id
        cache: false,
        success: function(html) {
          $(profile).html(html);
          $(loading).hide();
          $(profile).addClass("active_content");
        }
      });
    } else {
      $("tr").removeClass("active_content");
      $(loading).show();
      $(loading).slideUp("fast");
    }
  });
})(jQuery)
