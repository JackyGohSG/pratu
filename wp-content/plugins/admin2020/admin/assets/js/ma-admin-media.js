var imageEditor;



jQuery(document).ready(function($) {


  $('#admin2020upload').on('change', function(e) {
    e.preventDefault;
    preupload(jQuery('#admin2020upload')[0]);
  })

  $(document).on('moved', '#batch_rename_builder', function(e) {

    build_batch_rename_preview();

  })

  $('.admin2020filterControlmonth').on('click', function(e) {
    filtertext = jQuery(this).text();
    if (filtertext == 'All') {
      jQuery("#selectedfilters #month").html('');

      if (jQuery("#selectedfilters #month").html() == "" && jQuery("#selectedfilters #year").html() == "" && jQuery("#selectedfilters #user").html() == "") {
        jQuery("#selectedfilters #clearall").hide();
      }
    } else {
      jQuery("#selectedfilters #month").html('<span class="uk-label">' + filtertext + '</span>');
      jQuery("#selectedfilters #clearall").show();
    }
    media_filter_change(get_active_filters());
  });

  $('.admin2020filterControlyear').on('click', function(e) {
    filtertext = jQuery(this).text();
    if (filtertext == 'All') {
      jQuery("#selectedfilters #year").html('');

      if (jQuery("#selectedfilters #month").html() == "" && jQuery("#selectedfilters #year").html() == "" && jQuery("#selectedfilters #user").html() == "") {
        jQuery("#selectedfilters #clearall").hide();
      }
    } else {
      jQuery("#selectedfilters #year").html('<span class="uk-label">' + filtertext + '</span>');
      jQuery("#selectedfilters #clearall").show();
    }
    media_filter_change(get_active_filters());
  });

  $('.admin2020filterControluser').on('click', function(e) {
    filtertext = jQuery(this).text();
    if (filtertext == 'All') {
      jQuery("#selectedfilters #user").html('');

      if (jQuery("#selectedfilters #month").html() == "" && jQuery("#selectedfilters #year").html() == "" && jQuery("#selectedfilters #user").html() == "") {
        jQuery("#selectedfilters #clearall").hide();
      }
    } else {
      jQuery("#selectedfilters #user").html('<span class="uk-label">' + filtertext + '</span>');
      jQuery("#selectedfilters #clearall").show();
    }
    media_filter_change(get_active_filters());
  });

  $('.admin2020filterControlalll').on('click', function(e) {
    jQuery("#selectedfilters #year").html('');
    jQuery("#selectedfilters #month").html('');
    jQuery("#selectedfilters #user").html('');
    jQuery("#selectedfilters #clearall").hide();
    media_filter_change(get_active_filters());
  });


  $('#admin2020listView').on('click', function(e) {
    $('.admin2020_media_gallery').addClass('admin2020_list_view');
    $('.admin2020_media_gallery').addClass('uk-grid-small');
  });
  $('#admin2020gridView').on('click', function(e) {
    $('.admin2020_media_gallery').removeClass('admin2020_list_view');
    $('.admin2020_media_gallery').removeClass('uk-grid-small');
  });



  $('#admin2020mediaSearch').on('keyup', function(e) {

    media_filter_change(get_active_filters());

  })

})


function edit_image(imagelink, imageTitle) {


  imageEditor = new tui.ImageEditor('#admin2020_image_edit_area', {
    includeUI: {
      loadImage: {
        path: imagelink,
        name: 'Blank'
      },
      theme: blackTheme, // or whiteTheme
      initMenu: 'filter',
      menuBarPosition: 'right'
    },
    cssMaxWidth: 700,
    cssMaxHeight: 500,
    usageStatistics: false
  });

  //imageEditor.loadImageFromURL(imagelink, imageTitle);
  window.onresize = function() {
    imageEditor.ui.resizeEditor();
  }

  jQuery('.tui-image-editor-header-buttons .tui-image-editor-download-btn').
  replaceWith('<span class="uk-padding-small" style="float:left"><a onclick="admin2020_save_edited_as_copy(this);" href="#" class="uk-button uk-button-primary uk-margin-right" >Save as copy</a><a href="#" onclick="admin2020_save_edited(this);" class="uk-button uk-button-primary" >Save</a></span>');
  jQuery('.tui-image-editor-header-logo').hide();
  // $('.tui-image-editor-menu').hide();

  // $('.tui-image-editor-header-buttons div:first').hide();
  var loadBtn = jQuery('.tui-image-editor-header-buttons div:first');
  loadBtn.hide();


  jQuery('.admin2020_image_edit_wrap').show();
};


function admin2020_save_edited(item) {

  jQuery(item).html('Saving <div class="image_save_spinner" uk-spinner></div>');

  imageurl = jQuery("#admin2020_viewer_fullLink").val();
  parts = imageurl.split("/");
  filename = parts[parts.length - 1];
  imageid = jQuery("#admin2020_viewer_currentid").text();

  img = imageEditor.toDataURL();
  blob = dataURLtoBlob(img);


  fd = new FormData();
  fd.append("ammended_image", blob, filename);
  fd.append("attachmentid", imageid);
  fd.append('security', ma_admin_ajax.security);
  fd.append('action', 'admin2020_upload_edited_image');

  jQuery.ajax({
    url: ma_admin_ajax.ajax_url,
    type: 'post',
    data: fd,
    async: true,
    cache: false,
    contentType: false,
    processData: false,
    success: function(response) {




      jQuery(item).html('Image Saved');
      jQuery('.admin2020_image_edit_wrap').hide();
      admin2020_notification('Image Saved!', 'success');
      jQuery('.admin2020_media_gallery').html(response);

      press = [];
      press.target = '';
      press.shiftKey = false;
      target = '#attachment' + imageid;

      admin2020_attachment_info(jQuery(target), press);

    },
    error: function(error) {
      console.log(error);
    }
  });

}


function admin2020_save_edited_as_copy(item) {

  jQuery(item).html('Saving <div class="image_save_spinner" uk-spinner></div>');

  imageurl = jQuery("#admin2020_viewer_fullLink").val();
  parts = imageurl.split("/");
  filename = parts[parts.length - 1];
  imageid = jQuery("#admin2020_viewer_currentid").text();

  img = imageEditor.toDataURL();
  blob = dataURLtoBlob(img);


  fd = new FormData();
  fd.append("ammended_image", blob, filename);
  fd.append("attachmentid", imageid);
  fd.append("file_name", filename);
  fd.append('security', ma_admin_ajax.security);
  fd.append('action', 'admin2020_upload_edited_image_as_copy');

  jQuery.ajax({
    url: ma_admin_ajax.ajax_url,
    type: 'post',
    data: fd,
    async: true,
    cache: false,
    contentType: false,
    processData: false,
    success: function(response) {

      jQuery(item).html('Image Saved');
      jQuery('.admin2020_image_edit_wrap').hide();
      admin2020_notification('Image Saved!', 'success');
      jQuery('.admin2020_media_gallery').html(response);

      press = [];
      press.target = '';
      press.shiftKey = false;
      target = '#attachment' + imageid;

      admin2020_attachment_info(jQuery(".admin2020_attachment").first(), press);

    },
    error: function(error) {
      console.log(error);
    }
  });

}

function dataURLtoBlob(dataurl) {
  var arr = dataurl.split(','),
    mime = arr[0].match(/:(.*?);/)[1],
    bstr = atob(arr[1]),
    n = bstr.length,
    u8arr = new Uint8Array(n);
  while (n--) {
    u8arr[n] = bstr.charCodeAt(n);
  }
  return new Blob([u8arr], {
    type: mime
  });
}

function copythis(item) {

  var copyText = document.getElementById("admin2020_viewer_fullLink");



  copyText.select();
  copyText.setSelectionRange(0, 99999);
  document.execCommand("copy");
  jQuery('#linkcopied').show().delay(1000).fadeOut("slow");


}









function preupload(files_data) {


  jQuery(".admin2020uploadItems").html('');


  count = 0;

  jQuery.each(files_data.files, function(j, file) {


    name = file.name;
    filesize = file.size;
    filetype = file.type;
    src = URL.createObjectURL(file);

    if (filetype.includes("image")) {
      quickdisplay = '<img width="40" src="' + src + '">';
    } else if (filetype.includes("video")) {
      quickdisplay = '<div class="uk-flex uk-flex-center" style="min-width:40px;"><span uk-icon="icon:video-camera"></span></div>';
    } else if (filetype.includes("application")) {
      quickdisplay = '<div class="uk-flex uk-flex-center" style="min-width:40px;"><span uk-icon="icon:file-pdf"></span></div>';
    } else if (filetype.includes("audio")) {
      quickdisplay = '<div class="uk-flex uk-flex-center" style="min-width:40px;"><span uk-icon="icon:microphone"></span></div>';
    }


    content = '<div class="admin2020uploaditem uk-grid-small uk-flex-middle uk-animation-slide-right" style="animation-delay:' + count + 's"uk-grid><div class="uk-width-auto">' + quickdisplay + '</div><div class="uk-width-expand"><div class="uk-margin-remove-bottom">' + name + '</div><p class="uk-text-meta uk-margin-remove-top">' + formatfilesize(filesize) + '</p></div><div class="uk-width-auto" uk-spinner></div></div>';

    jQuery(".admin2020uploadItems").append(content);

    count = count + 0.05;
  })


  thefiles = files_data.files;
  currentstat = '0/' + thefiles.length;
  jQuery('.admin2020upstat').text(currentstat);
  jQuery('.admin2020_loader_wrapper').show();

  checkfile(thefiles, 0);


}

function formatfilesize(size) {
  var i = Math.floor(Math.log(size) / Math.log(1024));
  return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
};

function uploadcallback(thefiles, index) {

  currentstat = index + '/' + thefiles.length;
  jQuery('.admin2020upstat').text(currentstat);

  uploaditem = jQuery('.admin2020uploadItems .admin2020uploaditem')[index - 1];
  jQuery(uploaditem).empty().append('<div class="uk-text-success">Item Uploaded!</div>');

  //jQuery(uploaditem).fadeOut(600, function(uploaditem){jQuery(uploaditem).remove()});

  if (index >= thefiles.length) {

    admin2020uploadfinished();
    return;

  } else {

    checkfile(thefiles, index);

  }

}

function checkfile(thefiles, index) {

  allowedTypes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/x-icon',
    "application/pdf",
    "application/msword",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "application/mspowerpoint", "application/powerpoint", "application/vnd.ms-powerpoint", "application/x-mspowerpoint",
    "application/vnd.openxmlformats-officedocument.presentationml.presentation",
    "application/mspowerpoint", "application/vnd.ms-powerpoint",
    "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
    "application/vnd.oasis.opendocument.text",
    "application/excel", "application/vnd.ms-excel", "application/x-excel", "application/x-msexcel",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    "application/octet-stream",
    "audio/mpeg3", "audio/x-mpeg-3", "video/mpeg", "video/x-mpeg",
    "audio/m4a",
    "audio/ogg",
    "audio/wav", "audio/x-wav",
    "video/mp4",
    "video/x-m4v",
    "video/quicktime",
    "video/x-ms-asf", "video/x-ms-wmv",
    "application/x-troff-msvideo", "video/avi", "video/msvideo", "video/x-msvideo",
    "audio/mpeg", " video/mpeg",
    "video/ogg",
    "video/3gpp", "audio/3gpp",
    "video/3gpp2", "audio/3gpp2",
    "application/zip",
    "application/octet-stream",
    "application/x-zip-compressed",
    "multipart/x-zip"
  ];



  filetype = thefiles[index].type;
  filesize = thefiles[index].size;
  maxfilesize = jQuery('#max-upload-size').text();


  if (filesize < maxfilesize) {
    if (allowedTypes.includes(filetype)) {
      processupload(thefiles, index);
    } else {
      uploaditem = jQuery('.admin2020uploadItems .admin2020uploaditem')[index];
      jQuery(uploaditem).empty().append('<div class="uk-text-danger">' + thefiles[index].name + ' not uploaded. Unsupported File Type</div>');
      processupload(thefiles, index + 1);
    }
  } else {
    uploaditem = jQuery('.admin2020uploadItems .admin2020uploaditem')[index];
    jQuery(uploaditem).empty().append('<div class="uk-text-danger">' + thefiles[index].name + ' not uploaded. File size exceeds limit</div>');
    processupload(thefiles, index + 1);
  }



}

function admin2020uploadfinished() {

  media_filter_change(get_active_filters());


}


function processupload(thefiles, index) {


  if (index >= thefiles.length) {

    admin2020uploadfinished();
    return;

  }

  activerfolder = jQuery('#admin2020folderswrap .admin2020folder .uk-active');
  if (jQuery(activerfolder).length > 0) {
    activefolderid = jQuery(activerfolder).parent().attr('folder-id');
  } else {
    activefolderid = "";
  }



  fd = new FormData();
  name = thefiles[index].name;
  fd.append(name, thefiles[index]);
  fd.append('security', ma_admin_ajax.security);
  fd.append('action', 'admin2020_upload_attachment');
  fd.append('folderid', activefolderid);

  jQuery.ajax({
    url: ma_admin_ajax.ajax_url,
    type: 'post',
    data: fd,
    async: true,
    cache: false,
    contentType: false,
    processData: false,
    success: function(response) {


      uploadcallback(thefiles, (index + 1));

    },
    error: function(error) {
      console.log(error);
    }
  });


}


function admin2020searchAtachments(item) {

  searchterm = jQuery(item).val().toLowerCase();

  jQuery('.attachments > .attachment').each(function() {

    content = jQuery(this).html().toLowerCase();

    if (content.indexOf(searchterm) !== -1) {
      jQuery(this).fadeIn(100);
    } else {
      jQuery(this).fadeOut(100);
    }
  })

}



function admin2020_save_attachment(imgid) {

  title = jQuery("#admin2020_viewer_input_title").val();
  imgalt = jQuery("#admin2020_viewer_altText").val();
  caption = jQuery("#admin2020_viewer_caption").val();
  description = jQuery("#admin2020_viewer_description").val();


  jQuery.ajax({
    url: ma_admin_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_save_attachment',
      security: ma_admin_ajax.security,
      title: title,
      imgalt: imgalt,
      caption: caption,
      description: description,
      imgid: imgid,
    },
    success: function(response) {
      //console.log(response);
      admin2020_notification('File Updated', 'success');
      var theid = "#attachment" + imgid;
      jQuery(theid).replaceWith(response);
    }
  });

}

function admin2020_delete_attachment() {


  imgid = jQuery("#admin2020_viewer_currentid").text();

  jQuery.ajax({
    url: ma_admin_media_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_delete_attachment',
      security: ma_admin_media_ajax.security,
      imgid: imgid,
    },
    success: function(response) {
      //console.log(response);
      admin2020_notification('File Deleted', 'primary');
      UIkit.offcanvas('#admin2020MediaViewer').hide();

      var theid = "#attachment" + imgid;
      jQuery(theid).remove();

      update_folders();
    }
  });



}


function admin2020_save_post() {

  title = jQuery("#admin2020_viewer_title").val();
  content = get_tinymce_content("post_preview_editor");
  postid = jQuery("#admin2020_viewer_currentid").text();


  jQuery.ajax({
    url: ma_admin_media_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_save_post',
      security: ma_admin_media_ajax.security,
      title: title,
      content: content,
      postid: postid,
    },
    success: function(response) {
      //console.log(response);
      admin2020_notification('File Updated', 'success');
      var theid = "#attachment" + postid;
      jQuery(theid).replaceWith(response);

    }
  });

}

function admin2020_delete_multiple_attachment() {

  if (jQuery('.admin2020_media_select:checkbox:checked').length < 1) {
    admin2020_notification('No Items Selected', 'warning');
  } else {



    theids = [];
    jQuery('.admin2020_media_select:checkbox:checked').each(function(index, element) {

      theid = jQuery(element).attr('admin2020_attachmentid');
      theids.push(theid);

    });

    UIkit.modal.confirm('Are you sure you want to delete ' + theids.length + ' items?').then(function() {

      jQuery.ajax({
        url: ma_admin_media_ajax.ajax_url,
        type: 'post',
        data: {
          action: 'admin2020_delete_multiple_attachment',
          security: ma_admin_media_ajax.security,
          theids: theids,
        },
        success: function(response) {
          //console.log(response);
          admin2020_notification('Files Deleted', 'primary');
          jQuery('.admin2020_media_gallery').removeClass('multiple');
          jQuery('.admin2020_delete_multiple').addClass('hidden');

          for (var n = 0; n <= theids.length; ++n) {
            var theid = "#attachment" + theids[n];
            jQuery(theid).remove();
          }

          update_folders();
        }
      });


    }, function() {
      ///CANCELLED
    });



  }

}

function add_batch_rename_item() {

  itemtoadd = jQuery('#batch_name_chooser').val();

  if (itemtoadd == "") {
    return;
  }

  jQuery.ajax({
    url: ma_admin_media_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_add_batch_rename_item',
      security: ma_admin_media_ajax.security,
      itemtoadd: itemtoadd,
    },
    success: function(response) {
      if (response) {
        jQuery('#batch_rename_builder').append(response);
        build_batch_rename_preview();
      }

    }
  });

}

function build_batch_rename_preview() {

  name_preview = "";

  jQuery("#batch_rename_builder .rename_item").each(function() {

    type = jQuery(this).find(".batch_rename_option").attr('name');

    if (type == 'date' || type == 'text') {
      value = jQuery(this).find("input").val();;
    } else {
      value = "{" + type + "}"
    }

    name_preview = name_preview + value;

  });

  jQuery("#batch_rename_preview").text(name_preview);
}

function batch_rename_process() {

  if (jQuery('.admin2020_media_select:checkbox:checked').length < 1) {
    admin2020_notification('No Items Selected', 'warning');
    return;
  }

  if (jQuery("#batch_rename_builder .rename_item").length < 1) {
    admin2020_notification('You must add at least one naming item', 'warning');
    return;
  }

  theids = [];
  jQuery('.admin2020_media_select:checkbox:checked').each(function(index, element) {

    theid = jQuery(element).attr('admin2020_attachmentid');
    theids.push(theid);

  });

  var temp_type = [];
  var temp_value = [];

  jQuery("#batch_rename_builder .rename_item").each(function() {

    type = jQuery(this).find(".batch_rename_option").attr('name');


    if (type == 'date' || type == 'text' || type == 'sequence' || type == 'meta') {
      value = jQuery(this).find("input").val();;
    } else {
      value = "";
    }

    temp_type.push(type);
    temp_value.push(value);

  });



  item_to_rename = jQuery('#form-stacked-select').val();

  jQuery.ajax({
    url: ma_admin_media_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_batch_rename',
      security: ma_admin_media_ajax.security,
      structure: temp_type,
      values: temp_value,
      ids: theids,
      item_to_rename: item_to_rename,
    },
    success: function(response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          admin2020_notification(data.error, 'danger');
        } else {
          admin2020_notification(data.message, 'primary');
        }
      }

    }
  });

}

function switchinfo(direction) {

  currentid = jQuery("#admin2020_viewer_currentid").text();
  target = '#attachment' + currentid;
  press = [];
  press.target = '';
  press.shiftKey = false;

  if (direction == "right") {
    nextitem = jQuery(target).next();
    if (!jQuery(nextitem).hasClass('admin2020_attachment')) {
      nextitem = jQuery(nextitem).next();
    }
    if (jQuery(nextitem).index() < jQuery('.admin2020_media_gallery > div').length - 1) {
      admin2020_attachment_info(nextitem, press);
    }

  } else if (direction == "left") {
    nextitem = jQuery(target).prev();
    if (!jQuery(nextitem).hasClass('admin2020_attachment')) {
      nextitem = jQuery(nextitem).prev();
    }
    if (jQuery(nextitem).index() >= 1) {
      admin2020_attachment_info(nextitem, press);
    }
  }

}


function admin2020_attachment_info(item, press) {

  clickarea = press.target;

  if (jQuery(clickarea).hasClass('admin2020_media_select')) {
    return;
  }


  if (jQuery('.admin2020_media_select:checkbox:checked').length > 0) {

    if (press.shiftKey) {

      currentindex = jQuery(item).index();

      jQuery.each(jQuery('.admin2020_media_gallery .admin2020_attachment'), function(i, obj) {

        if (jQuery(obj).find('.admin2020_media_select').prop("checked") == true) {

          lastindex = jQuery(obj).index();

        }

      })

      if (lastindex < currentindex) {
        start = lastindex;
        end = currentindex;
      } else {
        start = currentindex;
        end = lastindex;
      }

      for (var n = start; n <= end; ++n) {

        attachment = jQuery('.admin2020_media_gallery > div')[n];
        if (jQuery(attachment).is(':visible')) {

          checkbox = jQuery(attachment).find('.admin2020_media_select');
          jQuery(checkbox).prop('checked', true);

        }
        //.find('.admin2020_media_select');
        //checkbox.prop('checked', true);

      }

    } else {

      checkbox = jQuery(item).find('.admin2020_media_select');
      checkbox.prop('checked', !checkbox.prop("checked"));

    }

    if (jQuery('.admin2020_media_select:checkbox:checked').length < 1) {
      jQuery('.admin2020_media_gallery').removeClass('multiple');
      jQuery('.admin2020_delete_multiple').addClass('hidden');
    }




  } else {

    attachmentid = jQuery(item).attr('admin2020_attachmentid');
    jQuery('#admin2020_media_loader').show();

    jQuery.ajax({
      url: ma_admin_media_ajax.ajax_url,
      type: 'post',
      data: {
        action: 'admin2020_get_attachment_view',
        security: ma_admin_media_ajax.security,
        id: attachmentid,
      },
      success: function(response) {
        jQuery("#admin2020MediaViewer_content").html(response);
        jQuery('#admin2020_media_loader').hide();
        jQuery("#wp-post_preview_editor-wrap").removeClass('show_post_preview_editor');
        UIkit.offcanvas('#admin2020MediaViewer').show();
      }
    });

    return;

  }

}


function admin2020_duplicate_post() {


  postid = jQuery("#admin2020_viewer_currentid").text();

  jQuery.ajax({
    url: ma_admin_media_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_duplicate_post',
      security: ma_admin_media_ajax.security,
      postid: postid,
    },
    success: function(response) {
      //console.log(response);
      admin2020_notification('Post Duplicated', 'primary');
      UIkit.toggle("#admin2020MediaViewer").toggle();
      jQuery('.admin2020_media_gallery').prepend(response);

      press = [];
      press.target = '';
      press.shiftKey = false;
      target = '#attachment' + postid;

      admin2020_attachment_info(jQuery(".admin_2020_content_item").first(), press);
    }
  });



}

function admin_2020_enable_post_edit() {
  content = jQuery("#admin_2020_post_preview").html();
  set_tinymce_content("post_preview_editor", content);
  jQuery("#admin_2020_post_preview").hide();
  jQuery("#wp-post_preview_editor-wrap").addClass('show_post_preview_editor');
}


function get_tinymce_content(id) {
  var content;
  var inputid = id;
  var editor = tinyMCE.get(inputid);
  var textArea = jQuery('textarea#' + inputid);
  if (textArea.length > 0 && textArea.is(':visible')) {
    content = textArea.val();
  } else {
    content = editor.getContent();
  }
  return content;
}

function set_tinymce_content(id, content) {
  var inputid = id;
  var editor = tinyMCE.get(inputid);
  var textArea = jQuery('textarea#' + inputid);
  if (textArea.length > 0 && textArea.is(':visible')) {
    textArea.val(content);
  } else {
    editor.setContent(content);
  }
}

function admin2020_multiple_select() {

  if (jQuery('.admin2020_media_select:checkbox:checked').length > 0) {
    jQuery('.admin2020_media_gallery').addClass('multiple');
    jQuery('.admin2020_delete_multiple').removeClass('hidden');
  } else {
    jQuery('.admin2020_media_gallery').removeClass('multiple');
    jQuery('.admin2020_delete_multiple').addClass('hidden');
  }
}






function media_folder_change(item) {
  filters = get_active_filters();
  filters.folderid = item;
  media_filter_change(filters);
}


function media_filter_change(filters) {

  page_id = ma_admin_media_ajax.page_now

  var cansend;
  if (cansend == false) {
    return;
  }


  jQuery.ajax({
    url: ma_admin_media_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_build_media_filter',
      security: ma_admin_media_ajax.security,
      filters: filters,
      page_id: page_id,
    },
    beforeSend: function(xhr) {
      jQuery(".admin2020loaderwrap").show();

      cansend = false;
    },
    success: function(response) {
      if (response) {
        ma_admin_media_ajax.current_page = 1;
        jQuery('.admin2020_media_gallery').html(response);
      }
      jQuery(".admin2020loaderwrap").hide();
      cansend = true;
    }
  });

}



function get_active_filters() {

  var filters = {};

  filters.folderid = jQuery(".admin2020folderTitle.uk-active").parent().attr('folder-id');
  filters.uploadmonth = jQuery("#selectedfilters #month").text();
  filters.uploadyear = jQuery("#selectedfilters #year").text();
  filters.uploaduser = jQuery("#selectedfilters #user").text();
  filters.searchterm = jQuery("#admin2020mediaSearch").val().toLowerCase();

  return filters;

}