$(function(){
  var lang = $("html").attr("lang");
  if($("#video-player").length){
    videojs('video-player', { controls: true })
  }

    $('.news-list').slick({
        slidesPerRow: 3,
        rows:2,
        infinite:false,
        nextArrow: '<div data-aos="fade-right" class="slider-arrow right-arrow"><i class="fas fa-chevron-circle-right"></i></div>',
        prevArrow: '<div data-aos="fade-left" class="slider-arrow left-arrow"><i class="fas fa-chevron-circle-left"></i></div>',
        responsive: [
            {
              breakpoint: 992,
              settings: {
                slidesPerRow: 2,
                arrows:false,
                dots:true
              }
            },
            {
              breakpoint: 768,
              settings: {
                slidesPerRow: 1,
                arrows:false,
                dots:true
              }
            },
            // {
            //   breakpoint: 480,
            //   settings: {
            //     arrows:false
            //   }
            // }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
          ]
      });

    $('.article-sub-list').slick({
        slidesPerRow: 2,
        rows:3,
        infinite:false,
        nextArrow: '<div data-aos="fade-right" class="slider-arrow right-arrow"><i class="fas fa-chevron-circle-right"></i></div>',
        prevArrow: '<div data-aos="fade-left" class="slider-arrow left-arrow"><i class="fas fa-chevron-circle-left"></i></div>',
        responsive: [
            {
              breakpoint: 992,
              settings: {
                slidesPerRow: 1,
                arrows:false,
                dots:true
              }
            },
            // {
            //   breakpoint: 480,
            //   settings: {
            //     arrow:false
            //   }
            // }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
          ]
    });
    $('.training-sub-list').slick({
        slidesToShow: 4,
        infinite:false,
        nextArrow: '<div data-aos="fade-right" class="slider-arrow right-arrow"><i class="fas fa-chevron-circle-right"></i></div>',
        prevArrow: '<div data-aos="fade-left" class="slider-arrow left-arrow"><i class="fas fa-chevron-circle-left"></i></div>',
        responsive: [
            {
              breakpoint: 992,
              settings: {
                arrows:false,
                dots:true
              }
            },
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 2,
                arrows:false,
                dots:true
              }
            },
            // {
            //   breakpoint: 480,
            //   settings: {
            //     arrow:false
            //   }
            // }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
          ]
    });
    $('.life-book-sub-list').slick({
        slidesPerRow: 2,
        rows:3,
        infinite:false,
        nextArrow: '<div data-aos="fade-right" class="slider-arrow right-arrow"><i class="fas fa-chevron-circle-right"></i></div>',
        prevArrow: '<div data-aos="fade-left" class="slider-arrow left-arrow"><i class="fas fa-chevron-circle-left"></i></div>',
        responsive: [
            {
              breakpoint: 992,
              settings: {
                slidesPerRow: 1,
                arrows:false,
                dots:true
              }
            },
            // {
            //   breakpoint: 480,
            //   settings: {
            //     arrow:false
            //   }
            // }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
          ]
    });



    $('#calendar').fullCalendar({
      header: {
        left: '',
        center: '',
        right: ''
      },
      navLinks: false, // can click day/week names to navigate views
      eventLimit: false, // allow "more" link when too many events,
      // showNonCurrentDates:true,
      eventBorderColor:'transparent',
      googleCalendarApiKey: 'AIzaSyBuG0a28G8I0bNoX8zqBL8FJScnPgRB7TA',
      eventSources:[
        'zh-CN.hong_kong#holiday@group.v.calendar.google.com',
        function(start, end, timezone, callback) {
          $.ajax({
            url: '/'+lang+"/api/event/events",
            dataType:"json",
            data: {
              from: start.format("YYYY-MM-DD"),
              to: end.format("YYYY-MM-DD")
            },
            success: function(response) {
              var events = [];
              if(response.status=="success"){
                for(var i in response.data){
                  event = response.data[i];
                  var type = $(".event-list[data-id='"+event.event_type_id+"']");
                  events.push({
                    event_id: event.event_id,
                    title: event.title,
                    start: event.start,
                    backgroundColor: type.data("color"),
                    type:type.text(),
                    place: event.place,
                    content: event.content,
                    target: event.target,
                    participant: event.participant,
                    max_people: event.max_people,
                    stop_date: event.stop_date,
                  });
                }
              }
              callback(events);
            }
          });
        },
      ],
      eventRender: function(event, element) {
        if (event.url) {
           element.addClass("fc-holiday");
           if(lang == "zh-hans"){
            element.text(event.title.tran());
           }
         }
      },
      eventClick: function(event) {
        if (event.url) {
            return false;
        }
        var wrapper = $("#event-detail");
        wrapper.find(".title").html(event.title);
        wrapper.find(".type").html(event.type);
        wrapper.find(".content").html(event.content);
        wrapper.find(".target").html(event.target);
        wrapper.find(".participant").html(event.participant);
        wrapper.find(".place").html(event.place);
        var endDate = new moment(event.stop_date), canReg = true;
        if(endDate.isBefore(new moment())){
          canReg = false;
        }

        wrapper.find(".applied-people").load("/zh-hant/api/event/count/"+event.event_id, function(){
          // console.log(wrapper.find(".max_people").html());
          // console.log(wrapper.find(".applied-people").html());
          if(parseInt(wrapper.find(".applied-people").html()) >= parseInt(wrapper.find(".max_people").html())){
            $(".event2form-btn").hide(0);
          }else{
            if(canReg){
              $(".event2form-btn").show(0);
            }else{
              $(".event2form-btn").hide(0);
            }
          }
        });
        wrapper.find(".max_people").html(event.max_people);
        wrapper.find(".end-date").html(event.stop_date);
        

        
        var form = $("#event-form");
        form.find(".title").html(event.title);
        form.find("input[name='event_id']").val(event.event_id);
        $("#event-submit-form").show(0);
        $("#event-form .error-msg").hide(0);
        $("#event-form .success").hide(0);
        $.magnificPopup.open({
          items: {
            src: '#event-detail', // can be a HTML string, jQuery object, or CSS selector
            type: 'inline'
          }
        });
      }
    });

    $(".event2form-btn").on("click", function(){
        $.magnificPopup.close();
        setTimeout(function(){
            $.magnificPopup.open({
              items: {
                src: '#event-form', // can be a HTML string, jQuery object, or CSS selector
                type: 'inline'
              }
            });
        }, 300);
    });

    var today = new moment();

    $(".schedule-header .month").html(today.format("MMMM"));
    $(".schedule-header .month-num").html(today.format("M"));
    $(".schedule-header .year").html(today.format("YYYY"));

    $(".schedule-header .next-month").on("click", function(){
        $('#calendar').fullCalendar("next");
        var date = $("#calendar").fullCalendar('getDate');
        $(".schedule-header .month").html(date.format("MMMM"));
        $(".schedule-header .month-num").html(date.format("M"));
        $(".schedule-header .year").html(date.format("YYYY"));
    });
    $(".schedule-header .prev-month").on("click", function(){
        $('#calendar').fullCalendar("prev");
        var date = $("#calendar").fullCalendar('getDate');
        $(".schedule-header .month").html(date.format("MMMM"));
        $(".schedule-header .month-num").html(date.format("M"));
        $(".schedule-header .year").html(date.format("YYYY"));
    });

    $(".menu-btn").on("click", function(){
        $("#mobile-menu").toggleClass("opened");
    });
    $("#mobile-menu .close-btn").on("click", function(){
        $("#mobile-menu").toggleClass("opened");
    });

    $(".down-arrow > a").on("click", function(){
        $('html, body').animate({
            scrollTop: $("#new-video").offset().top  - 70
         }, 800);
        return false;
    });

    if($(".home").length){
        new Waypoint.Inview({
          element: $('#main-kv')[0],
          entered: function(direction) {
            $(".main-nav > a").removeClass("active");
            $(".main-nav > a[data-id='main-kv']").addClass("active");
          },
        });

        new Waypoint.Inview({
          element: $('.down-arrow')[0],
          entered: function(direction) {
            $(".main-nav > a").removeClass("active");
            $(".main-nav > a[data-id='main-kv']").addClass("active");
          },
        });

        if($('#new-video').length){
          new Waypoint.Inview({
            element: $('#new-video')[0],
            entered: function(direction) {
              if($(window).scrollTop() > 100){
                  $(".main-nav > a").removeClass("active");
                  $(".main-nav > a[data-id='new-video']").addClass("active");
              }
            },
          });
        }

        if($('#new-photo').length){
          new Waypoint.Inview({
            element: $('#new-photo')[0],
            entered: function(direction) {
              $(".main-nav > a").removeClass("active");
              $(".main-nav > a[data-id='new-photo']").addClass("active");
            },
          });
        }

        if($('#new-article').length){
          new Waypoint.Inview({
            element: $('#new-article')[0],
            entered: function(direction) {
              $(".main-nav > a").removeClass("active");
              $(".main-nav > a[data-id='new-article']").addClass("active");
            },
          });
        }

        if($('#event').length){
          new Waypoint.Inview({
            element: $('#event')[0],
            entered: function(direction) {
              $(".main-nav > a").removeClass("active");
              $(".main-nav > a[data-id='event']").addClass("active");
            },
          });
        }

        if($('#training').length){
          new Waypoint.Inview({
            element: $('#training')[0],
            entered: function(direction) {
              $(".main-nav > a").removeClass("active");
              $(".main-nav > a[data-id='training']").addClass("active");
            },
          });
        }

        if($('#life-book').length){
          new Waypoint.Inview({
            element: $('#life-book')[0],
            entered: function(direction) {
              $(".main-nav > a").removeClass("active");
              $(".main-nav > a[data-id='life-book']").addClass("active");
            },
          });
        }

        if($('#about-us').length){
          new Waypoint.Inview({
            element: $('#about-us')[0],
            entered: function(direction) {
              $(".main-nav > a").removeClass("active");
              $(".main-nav > a[data-id='about-us']").addClass("active");
            },
          });
        }
    }

    $(".home:not(.member-home) .main-nav > a").on("click", function(){
        var id = $(this).data("id");
        $("#mobile-menu").removeClass("opened");
        if(id != null){
            $('html, body').animate({
                scrollTop: $("#"+id).offset().top  - 70
             }, 800);
            return false;
        }
    });

    if(window.location.hash && window.location.hash != "#_=_" && $(window.location.hash).length) {
      $('html, body').animate({
          scrollTop: $(window.location.hash).offset().top  - 70
       }, 800);
    }


    $(".file-input").find("input").on("change", function(){
      if($(this)[0].files.length > 0){
        var lbl = $(this).siblings(".file-name");
        lbl.text($(this)[0].files[0].name);
      }
    });

    $(".file-input").find("input").each(function(){
      if($(this)[0].files.length > 0){
        var lbl = $(this).siblings(".file-name");
        lbl.text($(this)[0].files[0].name);
      }
    });

    AOS.init({
        offset: 100
    });
});

$(window).on('load', function () {
    AOS.refresh(true);
});