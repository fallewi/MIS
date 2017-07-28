// Admin Category upload Media
jQuery(function($){
  $('.upload_image_button').click(function(){
    var send_attachment_bkp = wp.media.editor.send.attachment;
    var button = $(this);
    wp.media.editor.send.attachment = function(props, attachment) {
      $(button).parent().prev().attr('src', attachment.url);
      $(button).prev().val(attachment.url);
      wp.media.editor.send.attachment = send_attachment_bkp;
    }
    wp.media.editor.open(button);
    return false;
  });
  /*
   * Delete value on input type="hidden"
   */
  $('.remove_image_button').click(function(){
    var r = confirm("Are you sure?");
    if (r == true) {
      var src = $(this).parent().prev().attr('data-src');
      $(this).parent().prev().attr('src', src);
      $(this).prev().prev().val('');
    }
    return false;
  });
});