$(function() {
    $("#general-content-form").validation();
    $("#general-content-form input[type=password]").val("");
    $("#general-content-form.admin").validation({
        extend_function: function(element, opts) {
            if (element.attr("name") == "old_password") {
                if ($("input[name='password']").val() == "" && element.val() != "") {
                    return false;
                }
            }
            if (element.attr("name") == "confirm_password") {
                if (element.val() != $("input[name='password']").val() && $("input[name='password']").val() != "") {
                    return false;
                }
            }
        }
    });

    $("#general-content-form.member input[type=password]").val("");
    $("#general-content-form.member").validation({
        extend_function: function(element, opts) {
            if (element.attr("name") == "confirm_password") {
                if (element.val() != $("input[name='password']").val() && $("input[name='password']").val() != "") {
                    return false;
                }
            }
        }
    });

    $(".per-page-select").change(function() {
        location.href = $(this).val();
    });

    $(document).off("tap", ".glyphicon-remove:not(.exclude)");
    $(document).on("tap", ".glyphicon-remove:not(.exclude)", function() {
        return confirm("Confirm delete this record?");
    });
    
    $(".datetimepicker").each(function() {
        var format = $(this).attr("data-format");
        $("#" + $(this).attr("id")).AnyTime_picker({
            format: "%Y-%m-%d %H:%i:%s"
        });
    });

    if($(".datepicker").length){
        $(".datepicker").datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true
        });
    }
    if($(".datepicker-from").length && $(".datepicker-to").length){
        var dateFormat = "yy-mm-dd",
              from = $(".datepicker-from")
                .datepicker({
                    dateFormat: "yy-mm-dd",
                    changeMonth: true,
                    changeYear: true
                })
                .on( "change", function() {
                  to.datepicker( "option", "minDate", getDate( this ) );
                }),
              to = $(".datepicker-to").datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true
              })
              .on( "change", function() {
                from.datepicker( "option", "maxDate", getDate( this ) );
              });
         
        function getDate( element ) {
          var date;
          try {
            date = $.datepicker.parseDate( dateFormat, element.value );
          } catch( error ) {
            date = null;
          }
     
          return date;
        }
    }

    if($(".birth-datepicker").length){
        $(".birth-datepicker").datepicker({
            "dateFormat": "yy-mm-dd",
            yearRange: "-80:-10",
            changeMonth: true,
            changeYear: true
        });
    }

    if($(".upload-frame").length){
        $(".upload-frame").colorbox({
            iframe: true,
            width: "95%",
            height: "80%",
            onCleanup:function(){
                var iframe = $("#colorbox iframe");
                if(iframe.contents().find("#uploadify").length){
                    iframe[0].contentWindow.destroyUploadify();
                }
            }
        });
    }
    
    if($(".color-field").length){
        $(".color-field").ColorPicker({
                onSubmit: function(hsb, hex, rgb, el) {
                        $(el).val(hex);
                        $(el).ColorPickerHide();
                },
                onBeforeShow: function () {
                        $(this).ColorPickerSetColor(this.value);
                }
        })
        .bind('keyup', function(){
                $(this).ColorPickerSetColor(this.value);
        });
    }
    
    $(document).off("tap", ".mobile-menu-btn");
    $(document).on("tap", ".mobile-menu-btn", function(){
        if(!$("#left-menu").hasClass("open")){
            $("#left-menu").addClass("open");
        }else{
            $("#left-menu").removeClass("open");
        }
    });
    
    $(document).off("tap", ".close-menu-btn");
    $(document).on("tap", ".close-menu-btn", function(){
        $("#left-menu").removeClass("open");
    });
    
    $(".list-filter-form").off("submit");
    $(".list-filter-form").on("submit", function(){
        var params = $("input, select", this);
        var filterStr = "";
        params.each(function(){
            if($(this).val().length){
                filterStr+=$(this).attr("id")+":"+$(this).val()+"|";
            }
        });
        var url = $(this).attr("action")+"&filter="+filterStr.substring(0, filterStr.length-1);
        location.href = url;
        return false;
    });

    $(".file-input").find("input").on("change", function(){
      if($(this)[0].files.length > 0){
        var lbl = $(this).siblings(".file-name");
        lbl.text($(this)[0].files[0].name);
      }
    });
    
    //$(".form-control").removeClass("form-control");
    /*if($.material){
        $.material.init();
    }*/
});

/*$(window).load(function() {
    if($(window).width() > 768){
        if($("#right-container").height() > $("#left-container").height()){
            $("#left-menu").css("min-height", $("#right-container").height() + "px");
        }else{
            $("#right-menu").css("min-height", $("#left-container").height() + "px");
        }
    }
});*/
