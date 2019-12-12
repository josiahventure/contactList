(function($) {   // contact list load
  $('#edit-name').keyup(function() {
    $('#contact_table').html("LOADING");
    var name=document.getElementById("edit-name").value;
    var dataString="name="+ name;
    $.ajax({
      type: "post",
      url: 'contacts',
      data: dataString,
      cache: false,
      success: function(html) {
        for(var i=0; i<11; i++) {
        html = html.replace("<script", "<!--");
        html = html.replace("/script>", "-->");
      }
        $('#contact_table').html(html);
      }
    });
  });

$( "#contact_table" ).on( "click", ".link", function(event) {  // profile load
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
        type: "get",
        url: 'profile', // profile url
        data: id, // contact id
        cache: false,

        success: function(html) {
          for(var i=0; i<11; i++) {
          html = html.replace("<script", "<!--");
          html = html.replace("/script>", "-->");
        }
          var result = '<td colspan=10 class="profile_hide">'+html+"</td>";
          $(profile).html(result);
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

  $( "#contact_table" ).on( "click", ".edit_icon", function() {
      var id = this.id.replace("open_", "");
      $("."+id).hide();
      $("#open_"+id).hide();
      $("#close_"+id).css('display', 'inline-block');
      $("#form_"+id).css('display', 'inline-block');
  });

  $( "#contact_table" ).on( "click", ".close_edit_icon", function() {
      var id = this.id.replace("close_", "");
      $("."+id).css('display', 'block');
      $("#open_"+id).css('display', 'inline-block');
      $("#close_"+id).hide();
      $("#form_"+id).hide();
  });

})(jQuery)
