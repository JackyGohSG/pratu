///UPLOAD SCRIPT FOR ADMIN OPTIONS
jQuery(document).ready(function($){
  var mediaUploader;
  var mediaUploaderDark;
  var mediaUploaderBackground;
  $('.ma-admin-backend-logo').click(function(e) {
    e.preventDefault();
      if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: 'Choose Image',
      button: {
      text: 'Choose Image'
    }, multiple: false });
    mediaUploader.on('select', function() {
      var attachment = mediaUploader.state().get('selection').first().toJSON();
      $('.ma-admin-backend-logo').attr('src',attachment.url);
      $('#background_image').val(attachment.url);
    });
    mediaUploader.open();
  });

  $('.ma-admin-backend-logo-dark').click(function(e) {
    e.preventDefault();
      if (mediaUploaderDark) {
      mediaUploaderDark.open();
      return;
    }
    mediaUploaderDark = wp.media.frames.file_frame = wp.media({
      title: 'Choose Image',
      button: {
      text: 'Choose Image'
    }, multiple: false });
    mediaUploaderDark.on('select', function() {
      var attachment = mediaUploaderDark.state().get('selection').first().toJSON();
      $('.ma-admin-backend-logo-dark').attr('src',attachment.url);
      $('#background_image_dark').val(attachment.url);
    });
    mediaUploaderDark.open();
  });

  $('.ma-admin-backend-background-image').click(function(e) {
    e.preventDefault();
      if (mediaUploaderBackground) {
      mediaUploaderBackground.open();
      return;
    }
    mediaUploaderBackground = wp.media.frames.file_frame = wp.media({
      title: 'Choose Image',
      button: {
      text: 'Choose Image'
    }, multiple: false });
    mediaUploaderBackground.on('select', function() {
      var attachment = mediaUploaderBackground.state().get('selection').first().toJSON();
      $('.ma-admin-backend-background-image').attr('src',attachment.url);
      $('#login_background_image').val(attachment.url);
    });
    mediaUploaderBackground.open();
  });
});
