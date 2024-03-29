jQuery(document).ready(function($) {
  $("#admin2020siteloader").fadeOut();

  $("#admin2020listView").on("click", function(e) {
    $(".admin2020_media_gallery").addClass("admin2020_list_view");
    $(".admin2020_media_gallery").addClass("uk-grid-small");
  });
  $("#admin2020gridView").on("click", function(e) {
    $(".admin2020_media_gallery").removeClass("admin2020_list_view");
    $(".admin2020_media_gallery").removeClass("uk-grid-small");
  });

  $("#admin2020mediaSearch").on("keyup", function(e) {
    searchterm = jQuery(this).val().toLowerCase();

    $(".admin2020_media_gallery > div").each(function() {
      content = "";
      if (jQuery(this).hasClass("admin2020_attachment")) {
        content = jQuery(this).text().toLowerCase();
      }
      if (content.indexOf(searchterm) !== -1) {
        jQuery(this).fadeIn(100);
      } else {
        jQuery(this).fadeOut(100);
      }
    });
  });

  $(".toggle_filters").on("click", function(e) {
    $(".tablenav.top").addClass("filter_open");
    $(".search-box").addClass("filter_open");
  });

  $(".close_filters").on("click", function(e) {
    $(".tablenav.top").removeClass("filter_open");
    $(".search-box").removeClass("filter_open");
  });

  //catch loader staying around too long
  setTimeout(function() {
    $("#admin2020siteloader").hide();
  }, 1000);

  $("body").on("click", function(e) {
    e = e || event;
    var from = findParent("a", e.target || e.srcElement);
    try {
      if (
        from &&
        $(from).attr("href").indexOf("#") === -1 &&
        !jQuery(from).hasClass("install-now")
      ) {
        $("#admin2020siteloader").fadeIn();
        setTimeout(function() {
          $("#admin2020siteloader").hide();
        }, 3000);
      }
    } catch (err) {
      //error
    }
  });

  ///TOGGLE ACTIONS DROPDOWN
  $(".post-action").on("click", function(event) {
    $(".row-actions").removeClass("ma-visible");
    $(this).parent().find(".row-actions").toggleClass("ma-visible");
  });

  /// HIDE MODALS ON OUTSIDE CLICKS
  $("body").on("click", function(event) {
    if (
      !$(event.target).closest(".post-action").length &&
      !$(event.target).is(".post-action") &&
      !$(event.target).is(".row-actions") &&
      !$(event.target).is(".row-actions span")
    ) {
      $(".row-actions").removeClass("ma-visible");
    }

    if (
      !$(event.target).closest(".ma-admin-filter").length &&
      !$(event.target).is(".ma-admin-filter") &&
      !$(event.target).is(".ma-visible") &&
      !$(event.target).is(".ma-visible *")
    ) {
      $(".alignleft.actions").removeClass("ma-visible");
    }
  });

  //OPEN SUBMENUS
  $("#adminmenu .wp-has-submenu a.wp-has-submenu").on("click", function(e) {
    e.preventDefault();
    $(this).parent().toggleClass("ma-open-sub");
  });

  $("#toggleAdmin2020").on("click", function(e) {
    var currentUrl = window.location.href;
    var url = new URL(currentUrl);
    url.searchParams.set("admin2020", "false"); // setting your param
    var newUrl = url.href;
    window.location.href = newUrl;
  });

  /// DISPLAY ACTIONS BASED ON SELECTED
  $(".check-column input").on("change", function() {
    var thecount = $(".check-column input:checked").length;

    if (thecount > 0) {
      $(".bulkactions").addClass("bulk-actions-flex");
    } else {
      $(".bulkactions").removeClass("bulk-actions-flex");
    }
  });

  /// TOGGLE VIEWS
  $(".upload-view-toggle").on("click", function() {
    $(".upload-theme").toggleClass("ma-admin-visible");
    $(".upload-plugin").toggleClass("ma-admin-visible");
  });

  /// TOGGLE MENU WIDTH
  $(".ma-admin-shrink-wrap a").on("click", function() {
    maAdminShrinkMenu();
  });
  /// TOGGLE SCREEN WRAP
  $("#maAdminToggleScreenOptions").on("click", function() {
    $("#screen-options-wrap").toggleClass("hidden");
  });
  /// APPLY SEARCH FILTERS
  $(".ma-admin-apply-filters").on("click", function() {
    ajaxSearch(jQuery("#ma-admin-search").val());
  });
  /// SWITCH DARK MODE
  $("#maAdminSwitchDarkMode").on("click", function() {
    maAdminSwitchDarkMode();
  });

  /// TOGGLES FILTERS ON TABLES
  $(".drawer-toggle").on("click", function() {
    $(".filter-drawer").toggleClass("ma-admin-visible");
  });

  /// ADMIN SEARCH
  $("#ma-admin-search").on("keyup", function() {
    if ($("#ma-admin-search").val().length > 0) {
      ajaxSearch($("#ma-admin-search").val());
    }
  });

  /// SEARCH ADMIN MENU
  $("#ma-admin-menu-search").on("keyup", function() {
    var searchString = $("#ma-admin-menu-search").val();
    filter = searchString.toUpperCase();
    var list = $("#ma-admin-menu-list");

    if (filter.length > 1) {
      var maAdminNav = UIkit.nav("#ma-admin-menu-list", {
        multiple: true,
      });

      $("#ma-admin-menu-list li")
        .not("#ma-admin-searchtab")
        .each(function() {
          if ($(this).text().toUpperCase().includes(filter)) {
            $(this).show();
            $(this).addClass("uk-open");
          } else {
            $(this).hide();
          }
        });
    } else {
      $("#ma-admin-menu-list li").show();
      $("#ma-admin-menu-list li").removeClass("uk-open");
      $("#ma-admin-menu-list uk-active").addClass("uk-open");
      var maAdminNav = UIkit.nav("#ma-admin-menu-list", {
        multiple: false,
      });
    }
  });
}); ////// END OF JQUERY AS $

function open_icon_chooser(item) {
  UIkit.modal(jQuery("#icon-list")).show();

  jQuery('#icon_selected').one("click", function() {
    icon = jQuery("#admin2020_icon_select .iconselected span").attr('uk-icon');

    jQuery(item).find('.admin2020_icon_display').attr('uk-icon', icon);

    if (icon == "noicon") {
      icon = "";
    }

    jQuery(item).find('.admin2020_icon_value').attr('value', icon);



    UIkit.modal(jQuery("#icon-list")).hide();
  })
}

function admin2020_set_prefs(pref_name, value = null) {

  if (pref_name == null) {
    return;
  }

  if (value == null && pref_name == 'folders') {

    open = jQuery('#admin2020_mediapanel').attr('aria-hidden');
    if (open == 'false') {
      value = 'closed';
    } else {
      value = 'open';
    }
  }

  jQuery.ajax({
    url: ma_admin_ajax.ajax_url,
    type: "post",
    data: {
      action: "admin2020_set_user_prefs",
      security: ma_admin_ajax.security,
      pref_name: pref_name,
      value: value,
    },
    success: function(response) {


    },
  });
}

function admin2020_export_settings_json() {
  jQuery.ajax({
    url: ma_admin_ajax.ajax_url,
    type: "post",
    data: {
      action: "admin2020_export_settings",
      security: ma_admin_ajax.security,
    },
    success: function(response) {
      data = response;

      var today = new Date();
      var dd = String(today.getDate()).padStart(2, "0");
      var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
      var yyyy = today.getFullYear();

      date_today = mm + "_" + dd + "_" + yyyy;
      filename = "admin2020_settings_" + date_today + ".json";

      var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(data);
      var dlAnchorElem = document.getElementById("admin2020_download_settings");
      dlAnchorElem.setAttribute("href", dataStr);
      dlAnchorElem.setAttribute("download", filename);
      dlAnchorElem.click();
    },
  });
}

function admin2020_export_settings_json_network() {
  jQuery.ajax({
    url: ma_admin_ajax.ajax_url,
    type: "post",
    data: {
      action: "admin2020_export_settings_network",
      security: ma_admin_ajax.security,
    },
    success: function(response) {
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, "0");
      var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
      var yyyy = today.getFullYear();

      date_today = mm + "_" + dd + "_" + yyyy;
      filename = "admin2020_settings_" + date_today + ".json";

      var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(data);
      var dlAnchorElem = document.getElementById("admin2020_download_settings");
      dlAnchorElem.setAttribute("href", dataStr);
      dlAnchorElem.setAttribute("download", filename);
      dlAnchorElem.click();
    },
  });
}

function admin2020_import_settings() {
  var thefile = jQuery("#admin2020_export_settings")[0].files[0];

  if (thefile.type != "application/json") {
    window.alert("Please select a valid JSON file.");
    return;
  }

  if (thefile.size > 10000) {
    window.alert("File is to big.");
    return;
  }

  var file = document.getElementById("admin2020_export_settings").files[0];
  var reader = new FileReader();
  reader.readAsText(file, "UTF-8");

  reader.onload = function(evt) {
    json_settings = evt.target.result;
    parsed = JSON.parse(json_settings);

    if (parsed != null) {
      jQuery.ajax({
        url: ma_admin_ajax.ajax_url,
        type: "post",
        data: {
          action: "admin2020_import_settings",
          security: ma_admin_ajax.security,
          admin2020_settings: parsed,
        },
        success: function(response) {
          message = response;
          admin2020_notification(message, "success");
          location.reload();
        },
      });
    }
  };
}

function admin2020_import_settings_network() {
  var thefile = jQuery("#admin2020_export_settings")[0].files[0];

  if (thefile.type != "application/json") {
    window.alert("Please select a valid JSON file.");
    return;
  }

  if (thefile.size > 10000) {
    window.alert("File is to big.");
    return;
  }

  var file = document.getElementById("admin2020_export_settings").files[0];
  var reader = new FileReader();
  reader.readAsText(file, "UTF-8");

  reader.onload = function(evt) {
    json_settings = evt.target.result;
    parsed = JSON.parse(json_settings);

    if (parsed != null) {
      jQuery.ajax({
        url: ma_admin_ajax.ajax_url,
        type: "post",
        data: {
          action: "admin2020_import_settings_network",
          security: ma_admin_ajax.security,
          admin2020_settings: parsed,
        },
        success: function(response) {
          message = response;
          admin2020_notification(message, "success");
          location.reload();
        },
      });
    }
  };
}

function admin2020_set_google_data(view, token) {
  if (view == "" || token == "") {
    return;
  }

  jQuery.ajax({
    url: ma_admin_ajax.ajax_url,
    type: "post",
    data: {
      action: "admin2020_set_google_data",
      security: ma_admin_ajax.security,
      view: view,
      token: token,
    },
    success: function(response) {

      jQuery("#admin2020_token").attr("value", token);
      jQuery("#admin2020_view").attr("value", view);

      message = response;
      admin2020_notification(message, "success");
    },
  });
}

/// DEBOUNCE TO PREVENT SERVER OVERLOAD ON SEARCH
function debounce(func, wait, immediate) {
  var timeout;
  return function() {
    var context = this,
      args = arguments;
    var later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
}

var ajaxSearch = debounce(function(searchKey) {
  /// GET SELECTED FILTERS
  var postTypeFilters = [];
  jQuery("#ma-admin-search-filters")
    .find(".ma-admin-post-types")
    .each(function(index) {
      if (jQuery(this).is(":checked")) {
        var value = jQuery(this).attr("ma-admin-filter");
        postTypeFilters.push(value);
      }
    });

  // GET SELECTED CATEGORIES
  var categortFilters = [];
  jQuery("#ma-admin-search-filters")
    .find(".ma-admin-categories")
    .each(function(index) {
      if (jQuery(this).is(":checked")) {
        var value = jQuery(this).attr("ma-admin-filter");
        categortFilters.push(value);
      }
    });

  //SUBMIT AJAX
  jQuery.ajax({
    url: ma_admin_ajax.ajax_url,
    type: "post",
    data: {
      action: "ma_admin_search_admin_posts",
      security: ma_admin_ajax.security,
      searchterm: searchKey,
      jsonfilters: postTypeFilters,
      categoryfilters: categortFilters,
    },
    success: function(response) {
      jQuery("#admin_search_results").html(response);
      jQuery(".ma-admin-search-results").show();
    },
  });
  //250 indicates the minimum tie interval between the series of events being fired
}, 250);

/// SAVE MENU SHRINK SETTINGS
function maAdminShrinkMenu() {
  jQuery("body").toggleClass("ma-admin-menu-shrink");

  jQuery.ajax({
    url: ma_admin_ajax.ajax_url,
    type: "post",
    data: {
      action: "ma_admin_shrink_menu",
      security: ma_admin_ajax.security,
    },
    success: function(response) {
      // NOTHING TO DO
    },
  });
}

function admin2020_reset_menu_settings() {
  console.log("started");

  jQuery.ajax({
    url: ma_admin_ajax.ajax_url,
    type: "post",
    data: {
      action: "admin2020_reset_menu_settings",
      security: ma_admin_ajax.security,
    },
    success: function(response) {
      admin2020_notification("Menu Styles reset!", "success");
      location.reload();
    },
  });
}

/// SWITCH AND SAVE DARK MODE PREF
function maAdminSwitchDarkMode() {
  jQuery("body").toggleClass("ma-admin-dark uk-light");

  jQuery.ajax({
    url: ma_admin_ajax.ajax_url,
    type: "post",
    data: {
      action: "ma_admin_switch_dark_mode",
      security: ma_admin_ajax.security,
    },
    success: function(response) {
      //location.reload();
    },
  });
}

//find first parent with tagName [tagname]
function findParent(tagname, el) {
  while (el) {
    if ((el.nodeName || el.tagName).toLowerCase() === tagname.toLowerCase()) {
      return el;
    }
    el = el.parentNode;
  }
  return null;
}

function admin2020_notification(message, status) {
  UIkit.notification({
    message: message,
    status: status,
    pos: "bottom-center",
    timeout: 4000,
  });
}