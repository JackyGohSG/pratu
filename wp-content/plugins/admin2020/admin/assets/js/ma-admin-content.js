var imageEditor;



jQuery(document).ready(function($) {






  refreshFolderCount();

  $('#admin2020upload').on('change', function(e) {
    e.preventDefault;
    preupload(jQuery('#admin2020upload')[0]);
  })

  $('.admin2020filterControlmonth').on('click', function(e) {
    filtertext = jQuery(this).text();
    if (filtertext == 'All') {
      jQuery("#selectedfilters #month").html('');

      if (jQuery("#selectedfilters #month").html() == "" && jQuery("#selectedfilters #year").html() == "" && jQuery("#selectedfilters #user").html() == "" && jQuery("#selectedfilters #status").html() == "") {
        jQuery("#selectedfilters #clearall").hide();
      }
    } else {
      jQuery("#selectedfilters #month").html('<span class="uk-label">' + filtertext + '</span>');
      jQuery("#selectedfilters #clearall").show();
    }
  });

  $('.admin2020filterControlstatus').on('click', function(e) {
    filtertext = jQuery(this).text();
    if (filtertext == 'All') {
      jQuery("#selectedfilters #status").html('');

      if (jQuery("#selectedfilters #month").html() == "" && jQuery("#selectedfilters #year").html() == "" && jQuery("#selectedfilters #user").html() == "" && jQuery("#selectedfilters #status").html() == "") {
        jQuery("#selectedfilters #clearall").hide();
      }
    } else {
      jQuery("#selectedfilters #status").html('<span class="uk-label">' + filtertext + '</span>');
      jQuery("#selectedfilters #clearall").show();
    }
  });

  $('.admin2020filterControlyear').on('click', function(e) {
    filtertext = jQuery(this).text();
    if (filtertext == 'All') {
      jQuery("#selectedfilters #year").html('');

      if (jQuery("#selectedfilters #month").html() == "" && jQuery("#selectedfilters #year").html() == "" && jQuery("#selectedfilters #user").html() == "" && jQuery("#selectedfilters #status").html() == "") {
        jQuery("#selectedfilters #clearall").hide();
      }
    } else {
      jQuery("#selectedfilters #year").html('<span class="uk-label">' + filtertext + '</span>');
      jQuery("#selectedfilters #clearall").show();
    }
  });

  $('.admin2020filterControluser').on('click', function(e) {
    filtertext = jQuery(this).text();
    if (filtertext == 'All') {
      jQuery("#selectedfilters #user").html('');

      if (jQuery("#selectedfilters #month").html() == "" && jQuery("#selectedfilters #year").html() == "" && jQuery("#selectedfilters #user").html() == "" && jQuery("#selectedfilters #status").html() == "") {
        jQuery("#selectedfilters #clearall").hide();
      }
    } else {
      jQuery("#selectedfilters #user").html('<span class="uk-label">' + filtertext + '</span>');
      jQuery("#selectedfilters #clearall").show();
    }
  });

  $('.admin2020filterControlalll').on('click', function(e) {
    jQuery("#selectedfilters #year").html('');
    jQuery("#selectedfilters #month").html('');
    jQuery("#selectedfilters #user").html('');
    jQuery("#selectedfilters #status").html('');
    jQuery("#selectedfilters #clearall").hide();
  });


  $('#admin2020listView').on('click', function(e) {
    $('.admin2020_media_gallery').addClass('admin2020_list_view');
    $('.admin2020_media_gallery').addClass('uk-grid-small');
  });
  $('#admin2020gridView').on('click', function(e) {
    $('.admin2020_media_gallery').removeClass('admin2020_list_view');
    $('.admin2020_media_gallery').removeClass('uk-grid-small');
  });



  $('#admin2020postsSearch').on('keyup', function(e) {
    searchterm = jQuery(this).val().toLowerCase();

    $('.admin2020_media_gallery > div').each(function() {
      content = "";
      if (jQuery(this).hasClass('admin_2020_content_item')) {
        content = jQuery(this).text().toLowerCase();
      }
      if (content.indexOf(searchterm) !== -1) {
        jQuery(this).fadeIn(100);
      } else {
        jQuery(this).fadeOut(100);
      }
    })

  })

})







function get_active_folder_id() {

  activerfolder = jQuery('#admin2020folderswrap .admin2020folder .uk-active');
  if (jQuery(activerfolder).length > 0) {
    activefolderid = jQuery(activerfolder).parent().attr('folder-id');
  } else {
    activefolderid = 0;
  }

  return activefolderid;

}


function admin2020newfolder() {

  foldername = jQuery("#foldername").val();
  foldertag = jQuery("#admin2020_foldertag input[name=color_tag]:checked").val();
  ///CHECK FOR INPUT
  if (foldername.length > 0) {


    jQuery.ajax({
      url: ma_admin_media_ajax.ajax_url,
      type: 'post',
      data: {
        action: 'admin2020_create_posts_folder',
        security: ma_admin_media_ajax.security,
        title: foldername,
        foldertag: foldertag,
      },
      success: function(response) {
        jQuery('#admin2020folderswrap').prepend(response);
        admin2020_notification('Folder Created!', 'success');
      }
    });


  }

}


function admin2020createSubfolder(parentid) {

  activeid = get_active_folder_id();

  jQuery.ajax({
    url: ma_admin_content_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_create_posts_sub_folder',
      security: ma_admin_content_ajax.security,
      parentid: parentid,
      activeid: activeid,
    },
    success: function(response) {
      jQuery('#admin2020folderswrap').html(response);
      admin2020_notification('Folder Created!', 'success');
      refreshFolderCount();
    }
  });




}


function admin2020deletefolder(folderid) {

  jQuery.ajax({
    url: ma_admin_media_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_delete_posts_folder',
      security: ma_admin_media_ajax.security,
      folderid: folderid,
    },
    success: function(response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          admin2020_notification(data.error, 'danger');
          UIkit.modal(jQuery("#admin2020_edit_folder")).hide();
        } else {
          toremove = document.getElementById("folder" + folderid);
          jQuery(toremove).remove();
          admin2020_notification(data.message, 'success');
          UIkit.modal(jQuery("#admin2020_edit_folder")).hide();
        }

      }
    }
  });


}



function admin2020renamefolder(folderid) {

  newfoldername = jQuery("#admin2020_edit_folder").find('#foldername_update').val();
  foldertag = jQuery("#admin2020_folder_tag_update input[name=color_tag]:checked").val();

  ///CHECK FOR INPUT
  if (newfoldername.length > 0) {


    jQuery.ajax({
      url: ma_admin_media_ajax.ajax_url,
      type: 'post',
      data: {
        action: 'admin2020_rename_posts_folder',
        security: ma_admin_media_ajax.security,
        title: newfoldername,
        folderid: folderid,
        foldertag: foldertag,
      },
      success: function(response) {

        if (response) {
          data = JSON.parse(response);
          if (data.error) {
            admin2020_notification(data.error, 'danger');
            UIkit.modal(jQuery("#admin2020_edit_folder")).hide();
          } else {
            toremove = document.getElementById("folder" + folderid);
            jQuery(toremove).replaceWith(data.html);
            admin2020_notification(data.message, 'success');
            UIkit.modal(jQuery("#admin2020_edit_folder")).hide();
          }

        }

      }
    });


  }


}





//////DRAG AND DROP

/// DRAG OVER
function admin2020mediaAllowDrop(ev) {
  ev.preventDefault();
  drop = ev.target;
  if (jQuery(drop).hasClass('admin2020folder')) {
    jQuery(drop).addClass('folderdrag');
  } else {
    jQuery(drop).closest('.admin2020folder').addClass('folderdrag');
  }
  //ev.target.classList.add("folderdrag");
}

/// DRAG LEAVE
function admin2020mediaDropOut(ev) {
  ev.preventDefault();
  drop = ev.target;
  if (jQuery(drop).hasClass('admin2020folder')) {
    jQuery(drop).removeClass('folderdrag');
  } else {
    jQuery(drop).closest('.admin2020folder').removeClass('folderdrag');
  }
}

///DRAG START
function admin2020postdrag(ev) {
  ev.dataTransfer.setData("text", ev.target.id);

  thefiles = jQuery('.admin2020_media_select:checkbox:checked').length
  if (thefiles < 1) {
    thefiles = 1 + " file";
  } else {
    thefiles = thefiles + " files";
  }

  var elem = document.createElement("div");
  elem.id = "admin2020dragHandle";
  elem.innerHTML = thefiles;
  elem.style.position = "absolute";
  elem.style.top = "-1000px";
  document.body.appendChild(elem);
  ev.dataTransfer.setDragImage(elem, 0, 0);
}

///DROP
function admin2020postdrop(ev) {

  ev.stopImmediatePropagation();

  ev.preventDefault();
  drop = ev.target;


  folder_check = ev.dataTransfer.getData("text");
  origin_folder_id = null;

  if (folder_check) {

    if (folder_check.includes("folder")) {

      folder_chunks = folder_check.split("folder");
      origin_folder_id = folder_chunks[1];

    }

  }

  if (jQuery(drop).hasClass('admin2020folder')) {
    jQuery(drop).removeClass('folderdrag');
    folder = ev.target;
  } else {
    jQuery(drop).closest('.admin2020folder').removeClass('folderdrag');
    folder = jQuery(drop).closest('.admin2020folder');
  }

  if (jQuery(folder).hasClass('admin2020allFolders')) {
    folderid = "";
  } else {
    folderid = jQuery(folder).attr('folder-id');
  }


  if (origin_folder_id) {
    if (origin_folder_id != folderid) {
      move_folder_to_folder(folderid, origin_folder_id);
    }
    return;
  }

  theids = [];
  amount = jQuery('.admin2020_media_select:checkbox:checked').length

  if (amount > 0) {
    jQuery('.admin2020_media_select:checkbox:checked').each(function(index, element) {

      theid = jQuery(element).attr('admin2020_attachmentid');
      theids.push(theid);

    });
  } else {
    data = ev.dataTransfer.getData("text");
    lonedragger = document.getElementById(data);
    theid = jQuery(lonedragger).attr('admin2020_attachmentid');
    theids.push(theid);
  }


  movePostTofolder(folderid, theids);

  //var data = ev.dataTransfer.getData("text");
  //ev.target.appendChild(document.getElementById(data));
}

function move_folder_to_folder(folderid, origin_folder_id) {

  var new_folder_id = folderid;
  var moving_folder = origin_folder_id;


  jQuery.ajax({
    url: ma_admin_content_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_move_folder_to_folder',
      security: ma_admin_content_ajax.security,
      destination_id: folderid,
      origin_id: origin_folder_id,
    },
    success: function(response) {

      if (response) {

        data = JSON.parse(response);
        if (data.error) {
          admin2020_notification(data.error, 'danger');
        } else {
          admin2020_notification(data.message, 'primary');

          console.log(new_folder_id);
          console.log(moving_folder);

          string = '#folder' + new_folder_id;
          string_remove = '#folder' + moving_folder;

          if (jQuery(string).hasClass('admin2020_top_level_folder')) {
            toplevel = string;
          } else {
            toplevel = jQuery(string).closest('admin2020_top_level_folder').attr('attr');
          }

          open = null;
          if (jQuery(toplevel).hasClass('sub_open')) {
            open = true;
          }

          destination = document.getElementById("folder" + new_folder_id);
          toremove = document.getElementById("folder" + moving_folder);
          //jQuery("#admin2020folderswrap").find(string).empty();
          if (new_folder_id == 'false') {
            jQuery('#admin2020folderswrap').prepend(data.html);
          } else {

            jQuery(destination).replaceWith(data.html);

          }

          jQuery(toremove).remove();

          if (open) {
            jQuery(toplevel).addClass('sub_open');
          }

        }

      }

    }
  });

}

function admin2020folderdrag(ev) {

  if (jQuery(ev.target).hasClass('admin2020folder')) {
    var theid = ev.target.id;
  } else {
    var theid = jQuery(ev.target).closest('.admin2020folder').attr('id');
  }

  ev.dataTransfer.setData("text", theid);

}

function movePostTofolder(folderid, theids) {

  jQuery.ajax({
    url: ma_admin_content_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_move_to_posts_folder',
      security: ma_admin_content_ajax.security,
      theids: theids,
      folderid: folderid,
    },
    success: function(response) {

      if (response) {
        data = JSON.parse(response);
        if (data.error) {

        } else {

          admin2020_notification(data.message, 'primary');
          jQuery('.admin2020_media_gallery').removeClass('multiple');
          jQuery('.admin2020_delete_multiple').addClass('hidden');

          jQuery('.admin2020_media_gallery').html(data.posts);
          destination = document.getElementById("folder" + folderid);
          jQuery(destination).replaceWith(data.folder);
        }

      }
    }
  });

}

function refreshFolderCount() {

  total = jQuery(".admin2020_media_gallery .admin_2020_content_item").length;
  jQuery('.admin2020totalCount').text(total);

  jQuery('#admin2020folderswrap .admin2020folder').each(function(index, element) {

    folderid = jQuery(element).attr('folder-id');
    infolder = jQuery(".admin2020_media_gallery [admin2020_folders='" + folderid + "'").length;
    jQuery(element).find('.admin2020folderCount').text(infolder);


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

function get_posts_later(media_index) {

  jQuery.ajax({
    url: ma_admin_content_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_get_posts_later',
      security: ma_admin_content_ajax.security,
      media_index: media_index,
    },
    success: function(response) {
      jQuery('.admin2020_media_gallery').append(response);
      refreshFolderCount();
    }
  });

}


function admin2020_save_post() {

  title = jQuery("#admin2020_viewer_title").val();
  content = get_tinymce_content("post_preview_editor");
  postid = jQuery("#admin2020_viewer_currentid").text();


  jQuery.ajax({
    url: ma_admin_content_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_save_post',
      security: ma_admin_content_ajax.security,
      title: title,
      content: content,
      postid: postid,
    },
    success: function(response) {
      //console.log(response);
      admin2020_notification('Post Updated', 'success');
      jQuery('.admin2020_media_gallery').html(response);

      item = jQuery("#attachment" + postid);
      press = [];
      press.target = '';
      press.shiftKey = false;
      admin2020_get_post_info(item, press);
    }
  });

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

function admin2020_delete_post() {


  imgid = jQuery("#admin2020_viewer_currentid").text();

  jQuery.ajax({
    url: ma_admin_content_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_delete_post',
      security: ma_admin_content_ajax.security,
      imgid: imgid,
    },
    success: function(response) {
      //console.log(response);
      admin2020_notification('File Deleted', 'primary');
      UIkit.toggle("#admin2020MediaViewer").toggle();
      jQuery('.admin2020_media_gallery').html(response);
    }
  });



}


function admin2020_duplicate_post() {


  postid = jQuery("#admin2020_viewer_currentid").text();

  jQuery.ajax({
    url: ma_admin_content_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_duplicate_post',
      security: ma_admin_content_ajax.security,
      postid: postid,
    },
    success: function(response) {
      //console.log(response);
      admin2020_notification('Post Duplicated', 'primary');
      UIkit.toggle("#admin2020MediaViewer").toggle();
      jQuery('.admin2020_media_gallery').html(response);

      press = [];
      press.target = '';
      press.shiftKey = false;
      target = '#attachment' + postid;

      admin2020_get_post_info(jQuery(".admin_2020_content_item").first(), press);
    }
  });



}

function admin2020_delete_multiple_posts() {

  if (jQuery('.admin2020_media_select:checkbox:checked').length < 1) {
    admin2020_notification('No Items Selected', 'warning');
  } else {

    theids = [];
    jQuery('.admin2020_media_select:checkbox:checked').each(function(index, element) {

      theid = jQuery(element).attr('admin2020_attachmentid');
      candelete = jQuery(element).closest('.admin_2020_content_item').attr('admin2020_user_can_delete');
      if (candelete == 1) {
        theids.push(theid);
      }

    });

    jQuery.ajax({
      url: ma_admin_content_ajax.ajax_url,
      type: 'post',
      data: {
        action: 'admin2020_delete_multiple_posts',
        security: ma_admin_content_ajax.security,
        theids: theids,
      },
      success: function(response) {
        //console.log(response);
        admin2020_notification('Files Deleted', 'primary');
        jQuery('.admin2020_media_gallery').removeClass('multiple');
        jQuery('.admin2020_delete_multiple').addClass('hidden');
        jQuery('.admin2020_media_gallery').html(response);
      }
    });

  }

}

function switchinfo(direction) {

  currentid = jQuery("#admin2020_viewer_currentid").text();
  target = '#attachment' + currentid;
  press = [];
  press.target = '';
  press.shiftKey = false;

  if (direction == "right") {
    nextitem = jQuery(target).next();
    if (!jQuery(nextitem).hasClass('admin_2020_content_item')) {
      nextitem = jQuery(nextitem).next();
    }
    if (jQuery(nextitem).index() < jQuery('.admin2020_media_gallery > div').length - 1) {
      admin2020_get_post_info(nextitem, press);
    }

  } else if (direction == "left") {
    nextitem = jQuery(target).prev();
    if (!jQuery(nextitem).hasClass('admin_2020_content_item')) {
      nextitem = jQuery(nextitem).prev();
    }
    if (jQuery(nextitem).index() >= 1) {
      admin2020_get_post_info(nextitem, press);
    }
  }

}


function admin2020_get_post_info(item, press) {

  clickarea = press.target;

  if (jQuery(clickarea).hasClass('admin2020_media_select')) {
    return;
  }


  if (jQuery('.admin2020_media_select:checkbox:checked').length > 0) {

    if (press.shiftKey) {

      currentindex = jQuery(item).index();

      jQuery.each(jQuery('.admin2020_media_gallery .admin_2020_content_item'), function(i, obj) {

        if (jQuery(obj).find('.admin2020_media_select').prop("checked") == true) {

          lastindex = jQuery(obj).index();

        }

      })

      if (lastindex < currentindex) {
        start = lastindex;
        end = currentindex - 1;
      } else {
        start = currentindex - 1;
        end = lastindex;
      }

      for (var n = start; n <= end; ++n) {

        attachment = jQuery('.admin2020_media_gallery > div')[n];
        if (jQuery(attachment).is(':visible')) {

          checkbox = jQuery(attachment).find('.admin2020_media_select');
          jQuery(checkbox).prop('checked', true);

        }

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

    type = jQuery(item).attr("admin2020_type");
    posturl = jQuery(item).attr("admin2020_post_url");
    editposturl = jQuery(item).attr("admin2020_edit_url");
    filename = jQuery(item).attr("admin2020_filename");
    uploadedon = jQuery(item).attr("admin2020_uploaded_on");
    attachmentid = jQuery(item).attr("admin2020_attachmentid");
    content = jQuery(item).attr("admin2020_file_description");
    datemeta = jQuery(item).attr("admin2020_metadata");


    canedit = jQuery(item).attr("admin2020_user_can_edit");
    candelete = jQuery(item).attr("admin2020_user_can_delete");

    if (canedit != 1) {
      jQuery('#admin2020_edit_post').hide();
      jQuery('#admin2020_save_post').hide();
    } else {
      jQuery('#admin2020_edit_post').show();
      jQuery('#admin2020_save_post').show();
    }

    if (candelete != 1) {
      jQuery('#admin2020_delete_post').hide();
    } else {
      jQuery('#admin2020_delete_post').show();
    }

    if (candelete != 1) {
      jQuery('#admin2020_delete_post').hide();
    } else {
      jQuery('#admin2020_delete_post').show();
    }


    jQuery("#admin2020_viewer_currentid").text(attachmentid);
    jQuery("#admin2020_post_preview").attr("src", posturl);
    jQuery("#admin2020_view_post").attr("href", posturl);
    jQuery("#admin2020_edit_post").attr("href", editposturl);
    jQuery("#admin_2020_post_preview").html(content);
    jQuery("#admin2020_viewer_title").val(filename);
    jQuery("#admin2020_viewer_meta").text(datemeta);

    jQuery("#admin_2020_post_preview").show();
    jQuery("#wp-post_preview_editor-wrap").removeClass('show_post_preview_editor');


    UIkit.offcanvas('#admin2020MediaViewer').show();

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