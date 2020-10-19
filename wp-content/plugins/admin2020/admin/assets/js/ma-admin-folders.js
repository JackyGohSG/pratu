function admin2020newfolder() {

  foldername = jQuery("#foldername").val();
  foldertag = jQuery("#admin2020_foldertag input[name=color_tag]:checked").val();
  ///CHECK FOR INPUT
  if (foldername.length > 0) {


    jQuery.ajax({
      url: ma_admin_folder_ajax.ajax_url,
      type: 'post',
      data: {
        action: 'admin2020_create_folder',
        security: ma_admin_folder_ajax.security,
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

function admin2020deletefolder(folderid) {


  jQuery.ajax({
    url: ma_admin_folder_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_delete_folder',
      security: ma_admin_folder_ajax.security,
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
      url: ma_admin_folder_ajax.ajax_url,
      type: 'post',
      data: {
        action: 'admin2020_rename_folder',
        security: ma_admin_folder_ajax.security,
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


function move_folder_to_folder(folderid, origin_folder_id) {

  var new_folder_id = folderid;
  var moving_folder = origin_folder_id;

  var page_id = getUrlParameter('page');


  jQuery.ajax({
    url: ma_admin_folder_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_move_folder_into_folder',
      security: ma_admin_folder_ajax.security,
      destination_id: folderid,
      origin_id: origin_folder_id,
      page_id: page_id
    },
    success: function(response) {

      if (response) {

        data = JSON.parse(response);
        if (data.error) {
          admin2020_notification(data.error, 'danger');
        } else {
          admin2020_notification(data.message, 'primary');


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

function movetofolder(folderid, theids) {

  var page_id = ma_admin_folder_ajax.page_now;

  jQuery.ajax({
    url: ma_admin_folder_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_move_to_folder',
      security: ma_admin_folder_ajax.security,
      theids: theids,
      folderid: folderid,
      page_id: page_id
    },
    success: function(response) {
      admin2020_notification('Files added to folder', 'primary');
      jQuery('.admin2020_media_gallery').removeClass('multiple');
      jQuery('.admin2020_delete_multiple').addClass('hidden');

      //jQuery('.admin2020_media_gallery').html(response);
      //destination = document.getElementById("folder" + folderid);
      //jQuery(destination).replaceWith(response);
      media_filter_change(get_active_filters());

      update_folders();
    }
  });

}

function update_folders() {

  var page_id = ma_admin_folder_ajax.page_now;

  jQuery.ajax({
    url: ma_admin_folder_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_refresh_all_folders',
      security: ma_admin_folder_ajax.security,
      page_id: page_id
    },
    success: function(response) {

      theids = [];

      jQuery('.admin2020folder.sub_open').each(function() {

        the_id = jQuery(this).attr('folder-id');
        theids.push(the_id);

      })

      jQuery('#admin2020folderswrap').html(response);

      jQuery.each(theids, function(j, id) {

        string = '#folder' + id;
        jQuery(string).addClass('sub_open');

      })


    }
  });

}


var getUrlParameter = function getUrlParameter(sParam) {
  var sPageURL = window.location.search.substring(1),
    sURLVariables = sPageURL.split('&'),
    sParameterName,
    i;

  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split('=');

    if (sParameterName[0] === sParam) {
      return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
    }
  }
};


function admin2020_edit_folder(folder_id) {

  folderstring = "#folder" + folder_id;
  the_folder = jQuery(folderstring);
  folder_name = jQuery(folderstring).find('.folder_title').html();
  color_tag = jQuery(folderstring).find('.folder_tag').attr('value');

  jQuery("#admin2020_edit_folder").find('#foldername_update').val(folder_name);
  jQuery('#admin2020_folder_tag_update input').each(function() {

    value = jQuery(this).val();
    if (value == color_tag) {
      jQuery(this).prop('checked', true);
    }

  })

  UIkit.modal(jQuery("#admin2020_edit_folder")).show();

  jQuery("#delete_the_folder").off('click').on("click", function() {
    admin2020deletefolder(folder_id);
  });

  jQuery("#update_the_folder").off('click').on("click", function() {
    admin2020renamefolder(folder_id);
  });

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
function admin2020mediadrag(ev) {
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
function admin2020mediadrop(ev) {

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


  movetofolder(folderid, theids);

  //var data = ev.dataTransfer.getData("text");
  //ev.target.appendChild(document.getElementById(data));
}


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


  movetofolder(folderid, theids);

  //var data = ev.dataTransfer.getData("text");
  //ev.target.appendChild(document.getElementById(data));
}


function admin2020folderdrag(ev) {

  if (jQuery(ev.target).hasClass('admin2020folder')) {
    var theid = ev.target.id;
  } else {
    var theid = jQuery(ev.target).closest('.admin2020folder').attr('id');
  }

  ev.dataTransfer.setData("text", theid);

}