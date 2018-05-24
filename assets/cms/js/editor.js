$(function(){
    tinymce.init({
        selector: "textarea.editor",
        theme: "modern",
        plugins: [
             "advlist autolink link lists charmap preview hr anchor pagebreak spellchecker",
             "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
             "save table contextmenu directionality template paste textcolor media image"
       ],
       //print media emoticons image
       content_css: globalVar.domain+"/assets/css/cms-editor.css",
       toolbar: " undo redo | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link insertfile media image | forecolor backcolor ", //styleselect
       //| preview  fullpage
       //insertfile image print media emoticons
       inline_styles : true,
        style_formats: [
            {title: 'Bold text', inline: 'b'},
            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
            {title: 'Example 1', inline: 'span', classes: 'example1'},
            {title: 'Example 2', inline: 'span', classes: 'example2'},
            {title: 'Table styles'},
            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
        ]
     });
});