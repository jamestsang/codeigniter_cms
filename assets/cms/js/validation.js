(function($) {
    var publicMethod = $.fn.validation =$.validation = function(options){
        var opts=$.extend({}, $.fn.validation.defaults, options);
        publicMethod.opts=opts;
        init($(this));
        opts.before(this, opts);
        var msg=$('<ul>').addClass('msg-list');
        $(this).submit(function(){
            var result=true;
            msg.empty();
            opts.before(this, opts);
            $(this).find('.'+opts.class_name).each(function(){
                changeStatus($(this), true, opts);
                var warning='';
                if(jQuery.trim($(this).val())==''){
                    result=false;
                    changeStatus($(this), false, opts);
                    warning=$(this).attr('title');
                }
                if($(this).attr('type')=='checkbox'){
					name = $(this).attr("name");
                    if($("input[name='"+name+"']:checked").length==0){
                        result=false;
                        changeStatus($(this), false, opts);
                        warning=$(this).attr('title');
                    }
                }
                if($(this).attr('type')=='radio'){
                    var name = $(this).attr('name');
                    if($('input[name="'+name+'"]:checked').val()==null){
                        result=false;
                        changeStatus($(this), false, opts);
                        warning=$(this).attr('title');
                    }
                }
                if(($(this).attr('id')=='email'||$(this).attr('name')=='email')&&!emailCheck($(this).val())){
                    result=false;
                    changeStatus($(this), false, opts);
                    warning=$(this).attr('title');
                }
                var extend_return=opts.extend_function($(this), opts);
                if(extend_return!=undefined){
                    warning=extend_return;
                    changeStatus($(this), false, opts);
                    result=false;
                }
                msg.append($('<li>').text(warning));
            });
            $(this).find('.'+opts.alt_class_name).each(function(){
                changeStatus($(this), true, opts);
                var warning='';
                var extend_return=opts.extend_function($(this),opts);
                if(extend_return!=undefined){
                    warning=extend_return;
                    changeStatus($(this), false, opts);
                    result=false;
                }
                msg.append($('<li>').text(warning));
            });
            if(opts.show_msg&&result==false){
                $(opts.msg_class).append(msg).show(0);
            }
            opts.after(result, opts);
            return result;
        });

        function init(form){
            $(opts.msg_class).empty().hide(0);
            if(opts.all_field){
                form.find('input, textarea, select').each(function(){
                    if($(this).attr('type')!='submit'&&$(this).attr('type')!='button'&&$(this).attr('type')!='reset'){
                        $(this).removeClass('alt_valid');
                        $(this).addClass('valid');
                    }
                });
            }

            form.find('.'+opts.class_name).each(function(){
                changeStatus($(this), true, opts);
                if(opts.compulsory_tag!=false){
                    var compul=$('<'+opts.compulsory_tag+'>').addClass(opts.compulsory_class).text(opts.compulsory);
					if($(this).parents(".form-group").find("."+opts.compulsory_class).length==0){
						$(this).parents(".form-group").find(".control-label").prepend(compul);
					}
                }
            });
            form.find('.'+opts.alt_class_name).each(function(){
                changeStatus($(this), true, opts);
            });
        }
    };

    function changeStatus(element, status, opts){
        if(status){
            if((element.attr('type')=='text'||element.attr('type')=='password'||element.prop('tagName')=='TEXTAREA')&&element.css('background-image')!='none'){
                element.closest(".form-group").removeClass("has-warning");
            }else if(element.attr('type')=='radio'||element.attr('type')=='checkbox'){
                element.closest(".form-group").removeClass("has-warning");
            }else if(element.prop('tagName')=='SELECT'){
                element.closest(".form-group").removeClass("has-warning");
            }else{
                element.closest(".form-group").removeClass("has-warning");
            }
        }else{
            if((element.attr('type')=='text'||element.attr('type')=='password'||element.prop('tagName')=='TEXTAREA')&&element.css('background-image')!='none'){
                element.closest(".form-group").addClass("has-warning");
            }else if(element.attr('type')=='radio'||element.attr('type')=='checkbox'){
                element.closest(".form-group").addClass("has-warning");
            }else if(element.prop('tagName')=='SELECT'){
                element.closest(".form-group").addClass("has-warning");
            }else{
                element.closest(".form-group").addClass("has-warning");
            }
        }

    }

    function emailCheck (emailStr) {
        var emailPat=/^(.+)@(.+)$/
        var specialChars="\\(\\)<>@,;:\\\\\\\"\\.\\[\\]"
        var validChars="\[^\\s" + specialChars + "\]"
        var quotedUser="(\"[^\"]*\")"
        var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/
        var atom=validChars + '+'
        var word="(" + atom + "|" + quotedUser + ")"
        var userPat=new RegExp("^" + word + "(\\." + word + ")*$")
        var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$")

        var matchArray=emailStr.match(emailPat)
        if (matchArray==null)
            return false

        var user=matchArray[1]
        var domain=matchArray[2]

        if (user.match(userPat)==null)
            return false

        var IPArray=domain.match(ipDomainPat)
        if (IPArray!=null) {
            for (var i=1;i<=4;i++) {
                if (IPArray[i]>255)
                    return false
            }
            return true
        }
        var domainArray=domain.match(domainPat)
        if (domainArray==null)
            return false

        var atomPat=new RegExp(atom,"g")
        var domArr=domain.match(atomPat)
        var len=domArr.length
        if (domArr[domArr.length-1].length<2 || domArr[domArr.length-1].length>3)
            return false

        if (len<2)
            return false

        return true;
    }

    publicMethod.reset=function(){
        var ot=publicMethod.opts;
        $('.'+publicMethod.opts.class_name).each(function(){
            changeStatus($(this), true, ot);
        });

    }

    $.fn.validation.defaults={
        all_field:false,
        class_name:'valid',
        alt_class_name:'alt_valid',
        show_msg:false,
        msg_class:'.msg_area',
        compulsory:'*',
        compulsory_class:'compulsory',
        compulsory_tag:'span',
        extend_function:function(element, opts){},
        after:function(result, opts){},
        before:function(form, opts){}
    };
})(jQuery);

