$("#choose_files_btn_picture").click(function(){
    altair_form_file_upload.choose_files_modal("picture", "learning/posts");
});

$("#choose_files_btn_audio").click(function(){
    altair_form_file_upload.choose_files_modal("audio", "learning/posts");
});

$("#choose_files_btn_video").click(function(){
    altair_form_file_upload.choose_files_modal("video", "learning/posts");
});

$("#choose_files_btn_document").click(function(){
    altair_form_file_upload.choose_files_modal("document", "learning/posts");
});

$(".toggle-source").click(function(e){
    e.preventDefault();
    var id = $(this).attr('id');
    
    if ($("#source-" + id).val() == "local") {
        $('.alternate-' + id).removeClass('uk-hidden');
        $("#local-" + id).addClass('uk-hidden');
        $("#source-" + id).val('remote');
    }else {
       $('.alternate-' + id).addClass('uk-hidden');
        $("#local-" + id).removeClass('uk-hidden');
        $("#source-" + id).val('local');
    }
});