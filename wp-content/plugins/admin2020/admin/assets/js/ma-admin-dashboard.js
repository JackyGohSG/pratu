jQuery(document).ready(function($) {

  $('#admin2020-date-range').daterangepicker({
    "autoApply": true,
    "maxSpan": {
      "days": 60
    },
    "locale": {
      'format': 'DD/MM/YYYY'
    },
    ranges: {
      'Today': [moment(), moment()],
      'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'This Month': [moment().startOf('month'), moment().endOf('month')],
      'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    "alwaysShowCalendars": true,
    "startDate": moment().subtract(1, 'month'),
    "endDate": moment(),
    "opens": "left"
  }, function(start, end, label) {


    refresh_dash();
  });


  //UIkit.sortable($("#admin2020_overview"), array());

  $(document).on('moved', '#admin2020_overview', function(e) {

    function_array = [];
    $("#admin2020_overview").children().each(function(index, element) {

      var function_name = $(element).attr('id');
      if (!function_array.includes(function_name)) {

        function_array.push(function_name);

      }

    });

    //console.log(function_array);

    jQuery.ajax({
      url: ma_admin_dash_ajax.ajax_url,
      type: 'post',
      data: {
        action: 'admin2020_save_dash_order',
        security: ma_admin_dash_ajax.security,
        order: function_array,
      },
      success: function(response) {

      }
    });

  })


});


function admin2020_save_visibility() {

  function_array = [];

  jQuery("#admin2020-visible-cards input").each(function(index, element) {

    var function_name = jQuery(element).attr('name');


    if (!jQuery(element).prop('checked')) {
      function_array.push(function_name);
    }

  });

  jQuery.ajax({
    url: ma_admin_dash_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_save_visibility',
      security: ma_admin_dash_ajax.security,
      visibility: function_array,
    },
    success: function(response) {

      message = response;
      admin2020_notification(message, "success");
      refresh_dash();

    }
  });

}





function admin_2020_build_charts() {

  jQuery('#gverror').hide();

  get_ga_data_combined();

}


function get_ga_data_combined() {

  startdate = jQuery("#admin2020-date-range").data('daterangepicker').startDate.format('YYYY-MM-DD');
  enddate = jQuery("#admin2020-date-range").data('daterangepicker').endDate.format('YYYY-MM-DD');

  jQuery("#admin2020siteloader").show();

  jQuery.ajax({
    url: ma_admin_dash_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_get_analytics',
      security: ma_admin_dash_ajax.security,
      sd: startdate,
      ed: enddate,
    },
    success: function(response) {


      data = JSON.parse(response);


      if (data.error) {
        message = 'Something went wrong, please try again later';
        admin2020_notification(message, "danger");
        return;
      }

      jQuery("#admin2020siteloader").hide();

      sort_ga_data(data);

    }
  });

}


function refresh_dash() {

  startdate = jQuery("#admin2020-date-range").data('daterangepicker').startDate.format('YYYY-MM-DD');
  enddate = jQuery("#admin2020-date-range").data('daterangepicker').endDate.format('YYYY-MM-DD');

  jQuery("#admin2020siteloader").show();


  jQuery.ajax({
    url: ma_admin_dash_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'admin2020_rebuild_dash',
      security: ma_admin_dash_ajax.security,
      sd: startdate,
      ed: enddate,
    },
    success: function(response) {

      jQuery('#admin2020_overview').html(response);

      admin_2020_build_charts();

    }
  });

}


function sort_ga_data(response) {

  startdate = jQuery("#admin2020-date-range").data('daterangepicker').startDate.format('YYYY-MM-DD');
  enddate = jQuery("#admin2020-date-range").data('daterangepicker').endDate.format('YYYY-MM-DD');
  var b = moment(startdate);
  var a = moment(enddate);
  days_between = a.diff(b, 'days');

  country_data = response.country;
  generic_data = response.generic;
  device_data = response.device;
  path_data = response.path;
  source_data = response.source;

  //////
  var total_users = generic_data.totals.users;
  var total_sessions = generic_data.totals.sessions;
  var total_page_views = generic_data.totals.pageviews;
  var total_page_speed = generic_data.totals.pageLoadTime;
  var total_bounce_rate = generic_data.totals.bounceRate;
  var sessionduration = generic_data.totals.avgSessionDuration;
  //////
  var total_users_comparison = generic_data.totals_comparison.users;
  var total_sessions_comparison = generic_data.totals_comparison.sessions;
  var total_page_views_comparison = generic_data.totals_comparison.pageviews;
  var total_page_speed_comparison = generic_data.totals_comparison.pageLoadTime;
  var total_bounce_rate_comparison = generic_data.totals_comparison.bounceRate;
  var sessionduration_comparison = generic_data.totals_comparison.avgSessionDuration;
  //////
  timeline_users = generic_data.timeline.data.users;
  timeline_sessions = generic_data.timeline.data.sessions;
  timeline_page_views = generic_data.timeline.data.pageviews;
  timeline_page_speed = generic_data.timeline.data.pageLoadTime;
  timeline_bounce_rate = generic_data.timeline.data.bounceRate;
  timeline_bounce_rate = generic_data.timeline.data.avgSessionDuration;

  timeline_users_comparison = generic_data.timeline_comparison.data.users;
  timeline_sessions_comparison = generic_data.timeline_comparison.data.sessions;
  timeline_page_views_comparison = generic_data.timeline_comparison.data.pageviews;
  timeline_bounce_rate_comparison = generic_data.timeline_comparison.data.bounceRate;
  timeline_bounce_rate_comparison = generic_data.timeline_comparison.data.avgSessionDuration;
  /////
  date_timeline = generic_data.timeline.dates;

  //////
  keyNames = Object.keys(device_data.totals);
  device_totals = [];
  device_names = [];

  for (i = 0; i < keyNames.length; i++) {

    var name = keyNames[i];
    var value = device_data.totals[name];

    device_names.push(jsUcfirst(name));
    device_totals.push(value);

  }

  /////
  minutes = parseInt((sessionduration / 60)).toFixed(0);
  seconds = (sessionduration - (minutes * 60)).toFixed(2);
  human_readable_session = minutes + "m " + seconds + "s";
  ///COMPARISON
  minutes = parseInt((sessionduration_comparison / 60)).toFixed(0);
  seconds = (sessionduration_comparison - (minutes * 60)).toFixed(2);
  human_readable_session_comparison = minutes + "m " + seconds + "s";



  admin2020_build_ga_users(timeline_users, date_timeline, total_users, days_between);
  admin2020_build_ga_page_views(timeline_page_views, date_timeline, total_page_views, days_between);
  admin2020_build_ga_page_speed(timeline_page_speed, date_timeline, total_page_speed, days_between);
  admin2020_build_ga_total_page_views(total_page_views, total_page_views_comparison, days_between);
  admin2020_build_ga_total_page_speed(total_page_speed, total_page_speed_comparison, days_between);
  admin2020_build_ga_device_breakdown(device_totals, device_names);
  admin2020_build_ga_session_duration(human_readable_session, days_between, human_readable_session_comparison, sessionduration, sessionduration_comparison);
  admin2020_build_ga_session_by_country(country_data);
  admin2020_build_ga_session_by_page(path_data);
  admin2020_build_ga_bounce_rate(total_bounce_rate, days_between, total_bounce_rate_comparison);
  admin2020_build_ga_referal(source_data);

}

/////BUILD USER
function admin2020_build_ga_users(data, dates, total_users, days_between) {

  if (!jQuery("#traffic_visits").length) {
    return;
  }

  var temp = [];

  temp.label = 'Users';
  temp.data = data;
  temp.backgroundColor = "rgba(30, 135, 240, 0.2)";
  temp.borderColor = "rgb(30, 135, 240)";
  temp.pointBackgroundColor = "rgba(30, 135, 240, 0.2)";
  temp.pointBorderColor = "rgb(30, 135, 240)";


  jQuery("#total-vists").text(Number(total_users).toLocaleString() + " in the last " + days_between + " days");
  newchart('traffic_visits', 'line', dates, temp);


}
/////BUILD USER
function admin2020_build_ga_page_views(data, dates, total_page_views, days_between) {

  if (!jQuery("#total-sessions").length) {
    return;
  }

  var temp = [];

  temp.label = 'Page Views';
  temp.data = data;
  temp.backgroundColor = "rgba(255, 102, 236, 0.2)";
  temp.borderColor = "rgb(255, 102, 236)";
  temp.pointBackgroundColor = "rgba(255, 102, 236, 0.2)";
  temp.pointBorderColor = "rgb(255, 102, 236)";


  jQuery("#total-sessions").text(Number(total_page_views).toLocaleString() + " page views in the last " + days_between + " days");
  newchart('session_visits', 'line', dates, temp);

}
////SITE SPEED
function admin2020_build_ga_page_speed(data, dates, total_page_speed, days_between) {

  if (!jQuery("#site_speed").length) {
    return;
  }
  formated = [];
  for (i = 0; i < data.length; i++) {
    formated.push(Number(data[i]).toFixed(2));
  }

  var temp = [];

  temp.label = 'Page Views';
  temp.data = formated;
  temp.backgroundColor = "rgba(50, 210, 150, 0.2)";
  temp.borderColor = "rgb(50 210 150)";
  temp.pointBackgroundColor = "rgba(50, 210, 150, 0.2)";
  temp.pointBorderColor = "rgb(50 210 150)";

  jQuery("#site_speed_average").text(Number(total_page_speed).toFixed(2) + "s average page speed");
  newchart('site_speed', 'line', dates, temp);

}
/////TOTOAL PAGE Views
function admin2020_build_ga_total_page_views(total_page_views, total_page_views_comparison, days_between) {

  if (!jQuery("#totalsessions_text").length) {
    return;
  }

  difference = total_page_views / total_page_views_comparison * 100 - 100;
  difference = difference.toFixed(2);

  if (difference < 0) {
    jQuery("#total_page_views_change").removeClass("good");
    jQuery("#total_page_views_change").addClass("bad");
    jQuery("#total_page_views_change .change-text").text(difference + "%");
  } else if (difference > 0) {
    jQuery("#total_page_views_change").removeClass("bad");
    jQuery("#total_page_views_change").addClass("good");
    jQuery("#total_page_views_change .change-text").text(difference + "%");
  } else {
    jQuery("#total_page_views_change").removeClass("bad");
    jQuery("#total_page_views_change").addClass("good");
    jQuery("#total_page_views_change .change-text").text("No change");
  }

  jQuery('#admin2020_total_sessions').html(Number(total_page_views).toLocaleString());
  jQuery("#totalsessions_text").text("vs. Previous " + days_between + " days (" + Number(total_page_views_comparison).toLocaleString() + ")");

}

function admin2020_build_ga_total_page_speed(total_page_speed, total_page_speed_comparison, days_between) {

  if (!jQuery("#admin2020_total_site_speed_change").length) {
    return;
  }


  difference = total_page_speed / total_page_speed_comparison * 100 - 100;
  difference = difference.toFixed(2);

  if (difference > 0) {
    jQuery("#admin2020_total_site_speed_change").removeClass("good");
    jQuery("#admin2020_total_site_speed_change").addClass("bad");
    jQuery("#admin2020_total_site_speed_change .change-text").text(difference + "%");
  } else if (difference < 0) {
    jQuery("#admin2020_total_site_speed_change").removeClass("bad");
    jQuery("#admin2020_total_site_speed_change").addClass("good");
    jQuery("#admin2020_total_site_speed_change .change-text").text(difference + "%");
  } else {
    jQuery("#admin2020_total_site_speed_change").removeClass("bad");
    jQuery("#admin2020_total_site_speed_change").addClass("good");
    jQuery("#admin2020_total_site_speed_change .change-text").text("No change");
  }

  jQuery('#admin2020_total_site_speed').html(Number(total_page_speed).toFixed(2) + "s");
  jQuery("#admin2020_total_site_speed_text").text("vs. Previous " + days_between + " days (" + Number(total_page_speed_comparison).toFixed(2) + "s)");

}


/////DEVICE Breakdown
function admin2020_build_ga_device_breakdown(device_totals, device_names) {

  if (!jQuery("#device_visits").length) {
    return;
  }

  var temp = [];

  temp.label = 'Devices';
  temp.data = device_totals;
  temp.backgroundColor = ["rgba(30, 135, 240,0.2)", "rgba(255, 159, 243,0.2)", "rgba(29, 209, 161, 0.2)"];
  temp.borderColor = ["rgb(30, 135, 240)", "rgb(255, 159, 243)", "rgba(29, 209, 161, 1)"];
  temp.pointBackgroundColor = "rgba(255, 102, 236, 0.2)";
  temp.pointBorderColor = "rgb(95, 39, 205)";

  newchart('device_visits', 'doughnut', device_names, temp);

}

////SESSION Duration
function admin2020_build_ga_session_duration(human_readable_session, days_between, human_readable_session_comparison, sessionduration, sessionduration_comparison) {

  if (!jQuery("#admin2020_average_session_duration").length) {
    return;
  }

  difference = sessionduration / sessionduration_comparison * 100 - 100;
  difference = difference.toFixed(2);

  if (difference < 0) {
    jQuery("#total_session_duration_change").removeClass("good");
    jQuery("#total_session_duration_change").addClass("bad");
    jQuery("#total_session_duration_change .change-text").text(difference + "%");
  } else if (difference > 0) {
    jQuery("#total_session_duration_change").removeClass("bad");
    jQuery("#total_session_duration_change").addClass("good");
    jQuery("#total_session_duration_change .change-text").text(difference + "%");
  } else {
    jQuery("#total_session_duration_change").removeClass("bad");
    jQuery("#total_session_duration_change").addClass("good");
    jQuery("#total_session_duration_change .change-text").text("No change");
  }

  jQuery("#total_session_duration_text").text("vs. Previous " + days_between + " days (" + human_readable_session_comparison + ")");
  jQuery("#admin2020_average_session_duration").text(human_readable_session);
}

////SESSIONS BY COUNTRY
function admin2020_build_ga_session_by_country(country_rows) {

  if (!jQuery("#total-sessions-counntry").length) {
    return;
  }

  table = jQuery('#total-sessions-counntry tbody');
  jQuery(table).html('');

  totals = country_rows.totals;
  totals_comparison = country_rows.totals_comparison;
  keyNames = Object.keys(totals);

  for (i = 0; i < keyNames.length; i++) {

    country = keyNames[i];
    value = totals[country];
    comparison = totals_comparison[country];

    percent_changed = (value / comparison * 100 - 100).toFixed(2);
    precent = "%";

    theclass = "uk-text-success";
    if (percent_changed < 0) {
      theclass = "uk-text-danger";
    }

    if (!isFinite(percent_changed)) {
      percent_changed = "NA";
      theclass = "";
      precent = "";
    }

    flag = getCountryCode(country);
    countryicon = "";

    if (flag != "") {
      countryicon = '<img class="admin2020_country_icon" src="' + flag + '" alt="' + country + '"> ';
    }

    jQuery(table).append('<tr><td>' + countryicon + country + '</td><td>' + value + '</td><td class="uk-text-right ' + theclass + '">' + percent_changed + precent + '</td></tr>');

  }
}

///SESSIONS BY PAGE
function admin2020_build_ga_session_by_page(sessions_by_page_data) {

  if (!jQuery("#total-sessions-page").length) {
    return;
  }

  table = jQuery('#total-sessions-page tbody');
  jQuery(table).html('');

  totals = sessions_by_page_data.totals;
  keyNames = Object.keys(totals);
  totals_comparison = sessions_by_page_data.totals_comparison;

  for (i = 0; i < keyNames.length; i++) {

    country = keyNames[i];
    value = totals[country];

    comparison = totals_comparison[country];

    percent_changed = (value / comparison * 100 - 100).toFixed(2);
    precent = "%";


    theclass = "uk-text-success";
    if (percent_changed < 0) {
      theclass = "uk-text-danger";
    }

    if (!isFinite(percent_changed)) {
      percent_changed = "NA";
      theclass = "";
      precent = "";
    }


    jQuery(table).append('<tr><td>' + country + '</td><td>' + value + '</td><td class="uk-text-right ' + theclass + '">' + percent_changed + precent + '</td></tr>');

  }
}

///SESSIONS BY REFERALS
function admin2020_build_ga_referal(sessions_by_referal) {

  if (!jQuery("#total-sessions-referer").length) {
    return;
  }

  table = jQuery('#total-sessions-referer tbody');
  jQuery(table).html('');

  totals = sessions_by_referal.totals;
  keyNames = Object.keys(totals);
  totals_comparison = sessions_by_referal.totals_comparison;

  for (i = 0; i < keyNames.length; i++) {

    referer = keyNames[i];
    value = totals[referer];

    comparison = totals_comparison[referer];

    percent_changed = (value / comparison * 100 - 100).toFixed(2);
    precent = "%";


    theclass = "uk-text-success";
    if (percent_changed < 0) {
      theclass = "uk-text-danger";
    }

    if (!isFinite(percent_changed)) {
      percent_changed = "NA";
      theclass = "";
      precent = "";
    }

    iconurl = "https://s2.googleusercontent.com/s2/favicons?domain=" + referer;
    icon = '<img class="admin2020_country_icon" src="' + iconurl + '" alt="' + referer + '"> ';

    jQuery(table).append('<tr><td>' + icon + referer + '</td><td>' + value + '</td><td class="uk-text-right ' + theclass + '">' + percent_changed + precent + '</td></tr>');

  }
}

///TOTAL BOUNCE RATE
function admin2020_build_ga_bounce_rate(total_bounce_rate, days_between, total_bounce_rate_comparison) {

  if (!jQuery("#total_bounce_rate_change").length) {
    return;
  }

  difference = total_bounce_rate / total_bounce_rate_comparison * 100 - 100;
  difference = difference.toFixed(2);

  if (difference > 0) {
    jQuery("#total_bounce_rate_change").removeClass("good");
    jQuery("#total_bounce_rate_change").addClass("bad");
    jQuery("#total_bounce_rate_change .change-text").text(difference + "%");
  } else if (difference < 0) {
    jQuery("#total_bounce_rate_change").removeClass("bad");
    jQuery("#total_bounce_rate_change").addClass("good");
    jQuery("#total_bounce_rate_change .change-text").text(difference + "%");
  } else {
    jQuery("#total_bounce_rate_change").removeClass("bad");
    jQuery("#total_bounce_rate_change").addClass("good");
    jQuery("#total_bounce_rate_change .change-text").text("No change");
  }



  shortened = Number(total_bounce_rate).toFixed(2);
  shortened_comparison = Number(total_bounce_rate_comparison).toFixed(2);
  jQuery("#total_bounce_rate_text").text("vs. Previous " + days_between + " days (" + shortened_comparison + "%)");
  jQuery("#admin2020_total_bounce_rate").text(shortened + '%');
}









function admin_2020_date_convert(unix_timestamp) {
  var date = new Date(unix_timestamp * 1000);
  var hours = date.getHours();
  var minutes = "0" + date.getMinutes();
  var seconds = "0" + date.getSeconds();
  var formattedTime = hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
  return formattedTime;
}




function newchart(target, type, labels, chart_data) {

  senddata = [];

  var temp = {
    label: chart_data.label,
    data: chart_data.data,
    backgroundColor: chart_data.backgroundColor,
    borderColor: chart_data.borderColor,
    pointBorderWidth: 0,
    borderWidth: 2,
    pointBackgroundColor: chart_data.pointBackgroundColor,
    pointBorderColor: chart_data.pointBorderColor,
    clip: 100,
    lineTension: 0.3,
    spanGaps: true,
    pointRadius: 5,
    pointHoverRadius: 7
  }

  senddata.push(temp);

  if (chart_data.label == "Devices") {
    the_labels = true;
  } else {
    the_labels = false;
  }
  //var canvas = document.getElementById(target);
  var ctx = document.getElementById(target).getContext("2d");

  var myChart = new Chart(ctx, {
    type: type,
    data: {
      labels: labels,
      datasets: senddata,
    },
    options: {

      cutoutPercentage: 80,
      elements: {
        arc: {
          borderWidth: 12
        }
      },

      legend: {
        display: the_labels,
        position: 'right',
        labels: {
          align: "start",
          boxWidth: 5,
          fontColor: "#999",
          usePointStyle: true,
          padding: 10,
          fontSize: 14
        }
      },
      plugins: {
        datalabels: {
          display: false,
          backgroundColor: ["#8300ad"]
        }
      },
      maintainAspectRatio: true,
      scales: {
        yAxes: [{

          stacked: true,
          ticks: {
            display: false,
            padding: 20,
            fontColor: "#999",
            autoSkip: true,
            maxTicksLimit: 5,
            beginAtZero: true
          },
          gridLines: {
            display: false,
            drawBorder: false,
            tickMarkLength: 0

          }
        }],
        xAxes: [{
          stacked: true,
          gridLines: {
            display: false,
            drawBorder: false,
          },
          ticks: {
            display: false,
            padding: 0,
            fontColor: "#999",
            beginAtZero: true
          }
        }]
      }
    }
  });
}



function new_double_chart(target, type, labels, chart_data) {

  senddata = [];
  chart_one = chart_data[0];

  var temp = {
    label: chart_one.label,
    data: chart_one.data,
    backgroundColor: chart_one.backgroundColor,
    borderColor: chart_one.borderColor,
    pointBorderWidth: 0,
    borderWidth: 2,
    pointBackgroundColor: chart_one.pointBackgroundColor,
    pointBorderColor: chart_one.pointBorderColor,
    clip: 100,
    lineTension: 0.3,
    spanGaps: true,
    pointRadius: 5,
    pointHoverRadius: 7
  }

  senddata.push(temp);
  chart_two = chart_data[1];

  var temp = {
    label: chart_two.label,
    data: chart_two.data,
    backgroundColor: chart_two.backgroundColor,
    borderColor: chart_two.borderColor,
    pointBorderWidth: 0,
    borderWidth: 2,
    pointBackgroundColor: chart_two.pointBackgroundColor,
    pointBorderColor: chart_two.pointBorderColor,
    clip: 100,
    lineTension: 0.3,
    spanGaps: true,
    pointRadius: 5,
    pointHoverRadius: 7
  }

  senddata.push(temp);
  //var canvas = document.getElementById(target);
  var ctx = document.getElementById(target).getContext("2d");

  var myChart = new Chart(ctx, {
    type: type,
    data: {
      labels: labels,
      datasets: senddata,
    },
    options: {

      cutoutPercentage: 80,
      elements: {
        arc: {
          borderWidth: 12
        }
      },

      legend: {
        display: true,
        position: 'top',
        labels: {
          align: "start",
          boxWidth: 5,
          fontColor: "#999",
          usePointStyle: true,
          padding: 10,
          fontSize: 14
        }
      },
      plugins: {
        datalabels: {
          display: false,
          backgroundColor: ["#8300ad"]
        }
      },
      maintainAspectRatio: true,
      scales: {
        yAxes: [{

          stacked: false,
          ticks: {
            display: false,
            padding: 20,
            fontColor: "#999",
            autoSkip: true,
            maxTicksLimit: 5,
            beginAtZero: true
          },
          gridLines: {
            display: false,
            drawBorder: false,
            tickMarkLength: 0

          }
        }],
        xAxes: [{
          stacked: true,
          gridLines: {
            display: false,
            drawBorder: false,
          },
          ticks: {
            display: false,
            padding: 0,
            fontColor: "#999",
            beginAtZero: true
          }
        }]
      }
    }
  });
}

Chart.elements.Rectangle.prototype.draw = function() {

  var ctx = this._chart.ctx;
  var vm = this._view;
  var left, right, top, bottom, signX, signY, borderSkipped, radius;
  var borderWidth = vm.borderWidth;
  // Set Radius Here
  // If radius is large enough to cause drawing errors a max radius is imposed
  var cornerRadius = 4;

  if (!vm.horizontal) {
    // bar
    left = vm.x - vm.width / 2;
    right = vm.x + vm.width / 2;
    top = vm.y;
    bottom = vm.base;
    signX = 1;
    signY = bottom > top ? 1 : -1;
    borderSkipped = vm.borderSkipped || 'bottom';
  } else {
    // horizontal bar
    left = vm.base;
    right = vm.x;
    top = vm.y - vm.height / 2;
    bottom = vm.y + vm.height / 2;
    signX = right > left ? 1 : -1;
    signY = 1;
    borderSkipped = vm.borderSkipped || 'left';
  }

  // Canvas doesn't allow us to stroke inside the width so we can
  // adjust the sizes to fit if we're setting a stroke on the line
  if (borderWidth) {
    // borderWidth shold be less than bar width and bar height.
    var barSize = Math.min(Math.abs(left - right), Math.abs(top - bottom));
    borderWidth = borderWidth > barSize ? barSize : borderWidth;
    var halfStroke = borderWidth / 2;
    // Adjust borderWidth when bar top position is near vm.base(zero).
    var borderLeft = left + (borderSkipped !== 'left' ? halfStroke * signX : 0);
    var borderRight = right + (borderSkipped !== 'right' ? -halfStroke * signX : 0);
    var borderTop = top + (borderSkipped !== 'top' ? halfStroke * signY : 0);
    var borderBottom = bottom + (borderSkipped !== 'bottom' ? -halfStroke * signY : 0);
    // not become a vertical line?
    if (borderLeft !== borderRight) {
      top = borderTop;
      bottom = borderBottom;
    }
    // not become a horizontal line?
    if (borderTop !== borderBottom) {
      left = borderLeft;
      right = borderRight;
    }
  }

  ctx.beginPath();
  ctx.fillStyle = vm.backgroundColor;
  ctx.strokeStyle = vm.borderColor;
  ctx.lineWidth = borderWidth;

  // Corner points, from bottom-left to bottom-right clockwise
  // | 1 2 |
  // | 0 3 |
  var corners = [
    [left, bottom],
    [left, top],
    [right, top],
    [right, bottom]
  ];

  // Find first (starting) corner with fallback to 'bottom'
  var borders = ['bottom', 'left', 'top', 'right'];
  var startCorner = borders.indexOf(borderSkipped, 0);
  if (startCorner === -1) {
    startCorner = 0;
  }

  function cornerAt(index) {
    return corners[(startCorner + index) % 4];
  }

  // Draw rectangle from 'startCorner'
  var corner = cornerAt(0);
  ctx.moveTo(corner[0], corner[1]);

  for (var i = 1; i < 4; i++) {
    corner = cornerAt(i);
    nextCornerId = i + 1;
    if (nextCornerId == 4) {
      nextCornerId = 0
    }

    nextCorner = cornerAt(nextCornerId);

    width = corners[2][0] - corners[1][0];
    height = corners[0][1] - corners[1][1];
    x = corners[1][0];
    y = corners[1][1];

    var radius = cornerRadius;

    // Fix radius being too large
    if (radius > height / 2) {
      radius = height / 2;
    }
    if (radius > width / 2) {
      radius = width / 2;
    }

    ctx.moveTo(x + radius, y);
    ctx.lineTo(x + width - radius, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
    ctx.lineTo(x + width, y + height - radius);
    ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
    ctx.lineTo(x + radius, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
    ctx.lineTo(x, y + radius);
    ctx.quadraticCurveTo(x, y, x + radius, y);

  }

  ctx.fill();
  if (borderWidth) {
    ctx.stroke();
  }
};


function jsUcfirst(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}


function getCountryCode(countryName) {

  var countrycodedict = [{
    "name": "Israel",
    "dial_code": "+972",
    "code": "IL"
  }, {
    "name": "Afghanistan",
    "dial_code": "+93",
    "code": "AF"
  }, {
    "name": "Albania",
    "dial_code": "+355",
    "code": "AL"
  }, {
    "name": "Algeria",
    "dial_code": "+213",
    "code": "DZ"
  }, {
    "name": "AmericanSamoa",
    "dial_code": "+1 684",
    "code": "AS"
  }, {
    "name": "Andorra",
    "dial_code": "+376",
    "code": "AD"
  }, {
    "name": "Angola",
    "dial_code": "+244",
    "code": "AO"
  }, {
    "name": "Anguilla",
    "dial_code": "+1 264",
    "code": "AI"
  }, {
    "name": "Antigua and Barbuda",
    "dial_code": "+1268",
    "code": "AG"
  }, {
    "name": "Argentina",
    "dial_code": "+54",
    "code": "AR"
  }, {
    "name": "Armenia",
    "dial_code": "+374",
    "code": "AM"
  }, {
    "name": "Aruba",
    "dial_code": "+297",
    "code": "AW"
  }, {
    "name": "Australia",
    "dial_code": "+61",
    "code": "AU"
  }, {
    "name": "Austria",
    "dial_code": "+43",
    "code": "AT"
  }, {
    "name": "Azerbaijan",
    "dial_code": "+994",
    "code": "AZ"
  }, {
    "name": "Bahamas",
    "dial_code": "+1 242",
    "code": "BS"
  }, {
    "name": "Bahrain",
    "dial_code": "+973",
    "code": "BH"
  }, {
    "name": "Bangladesh",
    "dial_code": "+880",
    "code": "BD"
  }, {
    "name": "Barbados",
    "dial_code": "+1 246",
    "code": "BB"
  }, {
    "name": "Belarus",
    "dial_code": "+375",
    "code": "BY"
  }, {
    "name": "Belgium",
    "dial_code": "+32",
    "code": "BE"
  }, {
    "name": "Belize",
    "dial_code": "+501",
    "code": "BZ"
  }, {
    "name": "Benin",
    "dial_code": "+229",
    "code": "BJ"
  }, {
    "name": "Bermuda",
    "dial_code": "+1 441",
    "code": "BM"
  }, {
    "name": "Bhutan",
    "dial_code": "+975",
    "code": "BT"
  }, {
    "name": "Bosnia and Herzegovina",
    "dial_code": "+387",
    "code": "BA"
  }, {
    "name": "Botswana",
    "dial_code": "+267",
    "code": "BW"
  }, {
    "name": "Brazil",
    "dial_code": "+55",
    "code": "BR"
  }, {
    "name": "British Indian Ocean Territory",
    "dial_code": "+246",
    "code": "IO"
  }, {
    "name": "Bulgaria",
    "dial_code": "+359",
    "code": "BG"
  }, {
    "name": "Burkina Faso",
    "dial_code": "+226",
    "code": "BF"
  }, {
    "name": "Burundi",
    "dial_code": "+257",
    "code": "BI"
  }, {
    "name": "Cambodia",
    "dial_code": "+855",
    "code": "KH"
  }, {
    "name": "Cameroon",
    "dial_code": "+237",
    "code": "CM"
  }, {
    "name": "Canada",
    "dial_code": "+1",
    "code": "CA"
  }, {
    "name": "Cape Verde",
    "dial_code": "+238",
    "code": "CV"
  }, {
    "name": "Cayman Islands",
    "dial_code": "+ 345",
    "code": "KY"
  }, {
    "name": "Central African Republic",
    "dial_code": "+236",
    "code": "CF"
  }, {
    "name": "Chad",
    "dial_code": "+235",
    "code": "TD"
  }, {
    "name": "Chile",
    "dial_code": "+56",
    "code": "CL"
  }, {
    "name": "China",
    "dial_code": "+86",
    "code": "CN"
  }, {
    "name": "Christmas Island",
    "dial_code": "+61",
    "code": "CX"
  }, {
    "name": "Colombia",
    "dial_code": "+57",
    "code": "CO"
  }, {
    "name": "Comoros",
    "dial_code": "+269",
    "code": "KM"
  }, {
    "name": "Congo",
    "dial_code": "+242",
    "code": "CG"
  }, {
    "name": "Cook Islands",
    "dial_code": "+682",
    "code": "CK"
  }, {
    "name": "Costa Rica",
    "dial_code": "+506",
    "code": "CR"
  }, {
    "name": "Croatia",
    "dial_code": "+385",
    "code": "HR"
  }, {
    "name": "Cuba",
    "dial_code": "+53",
    "code": "CU"
  }, {
    "name": "Cyprus",
    "dial_code": "+537",
    "code": "CY"
  }, {
    "name": "Czech Republic",
    "dial_code": "+420",
    "code": "CZ"
  }, {
    "name": "Denmark",
    "dial_code": "+45",
    "code": "DK"
  }, {
    "name": "Djibouti",
    "dial_code": "+253",
    "code": "DJ"
  }, {
    "name": "Dominica",
    "dial_code": "+1 767",
    "code": "DM"
  }, {
    "name": "Dominican Republic",
    "dial_code": "+1 849",
    "code": "DO"
  }, {
    "name": "Ecuador",
    "dial_code": "+593",
    "code": "EC"
  }, {
    "name": "Egypt",
    "dial_code": "+20",
    "code": "EG"
  }, {
    "name": "El Salvador",
    "dial_code": "+503",
    "code": "SV"
  }, {
    "name": "Equatorial Guinea",
    "dial_code": "+240",
    "code": "GQ"
  }, {
    "name": "Eritrea",
    "dial_code": "+291",
    "code": "ER"
  }, {
    "name": "Estonia",
    "dial_code": "+372",
    "code": "EE"
  }, {
    "name": "Ethiopia",
    "dial_code": "+251",
    "code": "ET"
  }, {
    "name": "Faroe Islands",
    "dial_code": "+298",
    "code": "FO"
  }, {
    "name": "Fiji",
    "dial_code": "+679",
    "code": "FJ"
  }, {
    "name": "Finland",
    "dial_code": "+358",
    "code": "FI"
  }, {
    "name": "France",
    "dial_code": "+33",
    "code": "FR"
  }, {
    "name": "French Guiana",
    "dial_code": "+594",
    "code": "GF"
  }, {
    "name": "French Polynesia",
    "dial_code": "+689",
    "code": "PF"
  }, {
    "name": "Gabon",
    "dial_code": "+241",
    "code": "GA"
  }, {
    "name": "Gambia",
    "dial_code": "+220",
    "code": "GM"
  }, {
    "name": "Georgia",
    "dial_code": "+995",
    "code": "GE"
  }, {
    "name": "Germany",
    "dial_code": "+49",
    "code": "DE"
  }, {
    "name": "Ghana",
    "dial_code": "+233",
    "code": "GH"
  }, {
    "name": "Gibraltar",
    "dial_code": "+350",
    "code": "GI"
  }, {
    "name": "Greece",
    "dial_code": "+30",
    "code": "GR"
  }, {
    "name": "Greenland",
    "dial_code": "+299",
    "code": "GL"
  }, {
    "name": "Grenada",
    "dial_code": "+1 473",
    "code": "GD"
  }, {
    "name": "Guadeloupe",
    "dial_code": "+590",
    "code": "GP"
  }, {
    "name": "Guam",
    "dial_code": "+1 671",
    "code": "GU"
  }, {
    "name": "Guatemala",
    "dial_code": "+502",
    "code": "GT"
  }, {
    "name": "Guinea",
    "dial_code": "+224",
    "code": "GN"
  }, {
    "name": "Guinea-Bissau",
    "dial_code": "+245",
    "code": "GW"
  }, {
    "name": "Guyana",
    "dial_code": "+595",
    "code": "GY"
  }, {
    "name": "Haiti",
    "dial_code": "+509",
    "code": "HT"
  }, {
    "name": "Honduras",
    "dial_code": "+504",
    "code": "HN"
  }, {
    "name": "Hungary",
    "dial_code": "+36",
    "code": "HU"
  }, {
    "name": "Iceland",
    "dial_code": "+354",
    "code": "IS"
  }, {
    "name": "India",
    "dial_code": "+91",
    "code": "IN"
  }, {
    "name": "Indonesia",
    "dial_code": "+62",
    "code": "ID"
  }, {
    "name": "Iraq",
    "dial_code": "+964",
    "code": "IQ"
  }, {
    "name": "Ireland",
    "dial_code": "+353",
    "code": "IE"
  }, {
    "name": "Israel",
    "dial_code": "+972",
    "code": "IL"
  }, {
    "name": "Italy",
    "dial_code": "+39",
    "code": "IT"
  }, {
    "name": "Jamaica",
    "dial_code": "+1 876",
    "code": "JM"
  }, {
    "name": "Japan",
    "dial_code": "+81",
    "code": "JP"
  }, {
    "name": "Jordan",
    "dial_code": "+962",
    "code": "JO"
  }, {
    "name": "Kazakhstan",
    "dial_code": "+7 7",
    "code": "KZ"
  }, {
    "name": "Kenya",
    "dial_code": "+254",
    "code": "KE"
  }, {
    "name": "Kiribati",
    "dial_code": "+686",
    "code": "KI"
  }, {
    "name": "Kuwait",
    "dial_code": "+965",
    "code": "KW"
  }, {
    "name": "Kyrgyzstan",
    "dial_code": "+996",
    "code": "KG"
  }, {
    "name": "Latvia",
    "dial_code": "+371",
    "code": "LV"
  }, {
    "name": "Lebanon",
    "dial_code": "+961",
    "code": "LB"
  }, {
    "name": "Lesotho",
    "dial_code": "+266",
    "code": "LS"
  }, {
    "name": "Liberia",
    "dial_code": "+231",
    "code": "LR"
  }, {
    "name": "Liechtenstein",
    "dial_code": "+423",
    "code": "LI"
  }, {
    "name": "Lithuania",
    "dial_code": "+370",
    "code": "LT"
  }, {
    "name": "Luxembourg",
    "dial_code": "+352",
    "code": "LU"
  }, {
    "name": "Madagascar",
    "dial_code": "+261",
    "code": "MG"
  }, {
    "name": "Malawi",
    "dial_code": "+265",
    "code": "MW"
  }, {
    "name": "Malaysia",
    "dial_code": "+60",
    "code": "MY"
  }, {
    "name": "Maldives",
    "dial_code": "+960",
    "code": "MV"
  }, {
    "name": "Mali",
    "dial_code": "+223",
    "code": "ML"
  }, {
    "name": "Malta",
    "dial_code": "+356",
    "code": "MT"
  }, {
    "name": "Marshall Islands",
    "dial_code": "+692",
    "code": "MH"
  }, {
    "name": "Martinique",
    "dial_code": "+596",
    "code": "MQ"
  }, {
    "name": "Mauritania",
    "dial_code": "+222",
    "code": "MR"
  }, {
    "name": "Mauritius",
    "dial_code": "+230",
    "code": "MU"
  }, {
    "name": "Mayotte",
    "dial_code": "+262",
    "code": "YT"
  }, {
    "name": "Mexico",
    "dial_code": "+52",
    "code": "MX"
  }, {
    "name": "Monaco",
    "dial_code": "+377",
    "code": "MC"
  }, {
    "name": "Mongolia",
    "dial_code": "+976",
    "code": "MN"
  }, {
    "name": "Montenegro",
    "dial_code": "+382",
    "code": "ME"
  }, {
    "name": "Montserrat",
    "dial_code": "+1664",
    "code": "MS"
  }, {
    "name": "Morocco",
    "dial_code": "+212",
    "code": "MA"
  }, {
    "name": "Myanmar",
    "dial_code": "+95",
    "code": "MM"
  }, {
    "name": "Namibia",
    "dial_code": "+264",
    "code": "NA"
  }, {
    "name": "Nauru",
    "dial_code": "+674",
    "code": "NR"
  }, {
    "name": "Nepal",
    "dial_code": "+977",
    "code": "NP"
  }, {
    "name": "Netherlands",
    "dial_code": "+31",
    "code": "NL"
  }, {
    "name": "Netherlands Antilles",
    "dial_code": "+599",
    "code": "AN"
  }, {
    "name": "New Caledonia",
    "dial_code": "+687",
    "code": "NC"
  }, {
    "name": "New Zealand",
    "dial_code": "+64",
    "code": "NZ"
  }, {
    "name": "Nicaragua",
    "dial_code": "+505",
    "code": "NI"
  }, {
    "name": "Niger",
    "dial_code": "+227",
    "code": "NE"
  }, {
    "name": "Nigeria",
    "dial_code": "+234",
    "code": "NG"
  }, {
    "name": "Niue",
    "dial_code": "+683",
    "code": "NU"
  }, {
    "name": "Norfolk Island",
    "dial_code": "+672",
    "code": "NF"
  }, {
    "name": "Northern Mariana Islands",
    "dial_code": "+1 670",
    "code": "MP"
  }, {
    "name": "Norway",
    "dial_code": "+47",
    "code": "NO"
  }, {
    "name": "Oman",
    "dial_code": "+968",
    "code": "OM"
  }, {
    "name": "Pakistan",
    "dial_code": "+92",
    "code": "PK"
  }, {
    "name": "Palau",
    "dial_code": "+680",
    "code": "PW"
  }, {
    "name": "Panama",
    "dial_code": "+507",
    "code": "PA"
  }, {
    "name": "Papua New Guinea",
    "dial_code": "+675",
    "code": "PG"
  }, {
    "name": "Paraguay",
    "dial_code": "+595",
    "code": "PY"
  }, {
    "name": "Peru",
    "dial_code": "+51",
    "code": "PE"
  }, {
    "name": "Philippines",
    "dial_code": "+63",
    "code": "PH"
  }, {
    "name": "Poland",
    "dial_code": "+48",
    "code": "PL"
  }, {
    "name": "Portugal",
    "dial_code": "+351",
    "code": "PT"
  }, {
    "name": "Puerto Rico",
    "dial_code": "+1 939",
    "code": "PR"
  }, {
    "name": "Qatar",
    "dial_code": "+974",
    "code": "QA"
  }, {
    "name": "Romania",
    "dial_code": "+40",
    "code": "RO"
  }, {
    "name": "Rwanda",
    "dial_code": "+250",
    "code": "RW"
  }, {
    "name": "Samoa",
    "dial_code": "+685",
    "code": "WS"
  }, {
    "name": "San Marino",
    "dial_code": "+378",
    "code": "SM"
  }, {
    "name": "Saudi Arabia",
    "dial_code": "+966",
    "code": "SA"
  }, {
    "name": "Senegal",
    "dial_code": "+221",
    "code": "SN"
  }, {
    "name": "Serbia",
    "dial_code": "+381",
    "code": "RS"
  }, {
    "name": "Seychelles",
    "dial_code": "+248",
    "code": "SC"
  }, {
    "name": "Sierra Leone",
    "dial_code": "+232",
    "code": "SL"
  }, {
    "name": "Singapore",
    "dial_code": "+65",
    "code": "SG"
  }, {
    "name": "Slovakia",
    "dial_code": "+421",
    "code": "SK"
  }, {
    "name": "Slovenia",
    "dial_code": "+386",
    "code": "SI"
  }, {
    "name": "Solomon Islands",
    "dial_code": "+677",
    "code": "SB"
  }, {
    "name": "South Africa",
    "dial_code": "+27",
    "code": "ZA"
  }, {
    "name": "South Georgia and the South Sandwich Islands",
    "dial_code": "+500",
    "code": "GS"
  }, {
    "name": "Spain",
    "dial_code": "+34",
    "code": "ES"
  }, {
    "name": "Sri Lanka",
    "dial_code": "+94",
    "code": "LK"
  }, {
    "name": "Sudan",
    "dial_code": "+249",
    "code": "SD"
  }, {
    "name": "Suriname",
    "dial_code": "+597",
    "code": "SR"
  }, {
    "name": "Swaziland",
    "dial_code": "+268",
    "code": "SZ"
  }, {
    "name": "Sweden",
    "dial_code": "+46",
    "code": "SE"
  }, {
    "name": "Switzerland",
    "dial_code": "+41",
    "code": "CH"
  }, {
    "name": "Tajikistan",
    "dial_code": "+992",
    "code": "TJ"
  }, {
    "name": "Thailand",
    "dial_code": "+66",
    "code": "TH"
  }, {
    "name": "Togo",
    "dial_code": "+228",
    "code": "TG"
  }, {
    "name": "Tokelau",
    "dial_code": "+690",
    "code": "TK"
  }, {
    "name": "Tonga",
    "dial_code": "+676",
    "code": "TO"
  }, {
    "name": "Trinidad and Tobago",
    "dial_code": "+1 868",
    "code": "TT"
  }, {
    "name": "Tunisia",
    "dial_code": "+216",
    "code": "TN"
  }, {
    "name": "Turkey",
    "dial_code": "+90",
    "code": "TR"
  }, {
    "name": "Turkmenistan",
    "dial_code": "+993",
    "code": "TM"
  }, {
    "name": "Turks and Caicos Islands",
    "dial_code": "+1 649",
    "code": "TC"
  }, {
    "name": "Tuvalu",
    "dial_code": "+688",
    "code": "TV"
  }, {
    "name": "Uganda",
    "dial_code": "+256",
    "code": "UG"
  }, {
    "name": "Ukraine",
    "dial_code": "+380",
    "code": "UA"
  }, {
    "name": "United Arab Emirates",
    "dial_code": "+971",
    "code": "AE"
  }, {
    "name": "United Kingdom",
    "dial_code": "+44",
    "code": "GB"
  }, {
    "name": "United States",
    "dial_code": "+1",
    "code": "US"
  }, {
    "name": "Uruguay",
    "dial_code": "+598",
    "code": "UY"
  }, {
    "name": "Uzbekistan",
    "dial_code": "+998",
    "code": "UZ"
  }, {
    "name": "Vanuatu",
    "dial_code": "+678",
    "code": "VU"
  }, {
    "name": "Wallis and Futuna",
    "dial_code": "+681",
    "code": "WF"
  }, {
    "name": "Yemen",
    "dial_code": "+967",
    "code": "YE"
  }, {
    "name": "Zambia",
    "dial_code": "+260",
    "code": "ZM"
  }, {
    "name": "Zimbabwe",
    "dial_code": "+263",
    "code": "ZW"
  }, {
    "name": "land Islands",
    "dial_code": "",
    "code": "AX"
  }, {
    "name": "Antarctica",
    "dial_code": null,
    "code": "AQ"
  }, {
    "name": "Bolivia, Plurinational State of",
    "dial_code": "+591",
    "code": "BO"
  }, {
    "name": "Brunei Darussalam",
    "dial_code": "+673",
    "code": "BN"
  }, {
    "name": "Cocos (Keeling) Islands",
    "dial_code": "+61",
    "code": "CC"
  }, {
    "name": "Congo, The Democratic Republic of the",
    "dial_code": "+243",
    "code": "CD"
  }, {
    "name": "Cote d'Ivoire",
    "dial_code": "+225",
    "code": "CI"
  }, {
    "name": "Falkland Islands (Malvinas)",
    "dial_code": "+500",
    "code": "FK"
  }, {
    "name": "Guernsey",
    "dial_code": "+44",
    "code": "GG"
  }, {
    "name": "Holy See (Vatican City State)",
    "dial_code": "+379",
    "code": "VA"
  }, {
    "name": "Hong Kong",
    "dial_code": "+852",
    "code": "HK"
  }, {
    "name": "Iran, Islamic Republic of",
    "dial_code": "+98",
    "code": "IR"
  }, {
    "name": "Isle of Man",
    "dial_code": "+44",
    "code": "IM"
  }, {
    "name": "Jersey",
    "dial_code": "+44",
    "code": "JE"
  }, {
    "name": "Korea, Democratic People's Republic of",
    "dial_code": "+850",
    "code": "KP"
  }, {
    "name": "Korea, Republic of",
    "dial_code": "+82",
    "code": "KR"
  }, {
    "name": "Lao People's Democratic Republic",
    "dial_code": "+856",
    "code": "LA"
  }, {
    "name": "Libyan Arab Jamahiriya",
    "dial_code": "+218",
    "code": "LY"
  }, {
    "name": "Macao",
    "dial_code": "+853",
    "code": "MO"
  }, {
    "name": "Macedonia, The Former Yugoslav Republic of",
    "dial_code": "+389",
    "code": "MK"
  }, {
    "name": "Micronesia, Federated States of",
    "dial_code": "+691",
    "code": "FM"
  }, {
    "name": "Moldova, Republic of",
    "dial_code": "+373",
    "code": "MD"
  }, {
    "name": "Mozambique",
    "dial_code": "+258",
    "code": "MZ"
  }, {
    "name": "Palestinian Territory, Occupied",
    "dial_code": "+970",
    "code": "PS"
  }, {
    "name": "Pitcairn",
    "dial_code": "+872",
    "code": "PN"
  }, {
    "name": "Réunion",
    "dial_code": "+262",
    "code": "RE"
  }, {
    "name": "Russia",
    "dial_code": "+7",
    "code": "RU"
  }, {
    "name": "Saint Barthélemy",
    "dial_code": "+590",
    "code": "BL"
  }, {
    "name": "Saint Helena, Ascension and Tristan Da Cunha",
    "dial_code": "+290",
    "code": "SH"
  }, {
    "name": "Saint Kitts and Nevis",
    "dial_code": "+1 869",
    "code": "KN"
  }, {
    "name": "Saint Lucia",
    "dial_code": "+1 758",
    "code": "LC"
  }, {
    "name": "Saint Martin",
    "dial_code": "+590",
    "code": "MF"
  }, {
    "name": "Saint Pierre and Miquelon",
    "dial_code": "+508",
    "code": "PM"
  }, {
    "name": "Saint Vincent and the Grenadines",
    "dial_code": "+1 784",
    "code": "VC"
  }, {
    "name": "Sao Tome and Principe",
    "dial_code": "+239",
    "code": "ST"
  }, {
    "name": "Somalia",
    "dial_code": "+252",
    "code": "SO"
  }, {
    "name": "Svalbard and Jan Mayen",
    "dial_code": "+47",
    "code": "SJ"
  }, {
    "name": "Syrian Arab Republic",
    "dial_code": "+963",
    "code": "SY"
  }, {
    "name": "Taiwan, Province of China",
    "dial_code": "+886",
    "code": "TW"
  }, {
    "name": "Tanzania, United Republic of",
    "dial_code": "+255",
    "code": "TZ"
  }, {
    "name": "Timor-Leste",
    "dial_code": "+670",
    "code": "TL"
  }, {
    "name": "Venezuela, Bolivarian Republic of",
    "dial_code": "+58",
    "code": "VE"
  }, {
    "name": "Viet Nam",
    "dial_code": "+84",
    "code": "VN"
  }, {
    "name": "Virgin Islands, British",
    "dial_code": "+1 284",
    "code": "VG"
  }, {
    "name": "Virgin Islands, U.S.",
    "dial_code": "+1 340",
    "code": "VI"
  }];

  thepath = "";


  for (n = 0; n < countrycodedict.length; n++) {

    name = countrycodedict[n]["name"];

    if (name == countryName) {
      code = countrycodedict[n]["code"].toLowerCase();
      thepath = "https://lipis.github.io/flag-icon-css/flags/4x3/" + code + ".svg";
      break;
    }

  }

  return thepath;


}