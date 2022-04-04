$('#upload').change(function() {
    var filepath = this.value;
    var m = filepath.match(/([^\/\\]+)$/);
    var filename = m[1];
    $('#filename').html(filename);
});

$(".navigation ul li").click(function(){
    $(this).addClass("active").siblings().removeClass("active");
})  


// $(".menuIcon").click(function(){
//     $(".navigation").toggleClass("navSmall");
//     $(".main").toggleClass("mainSmall");
// })



$('#spreadDrop').change(function() {
    var filepath = this.value;
    var m = filepath.match(/([^\/\\]+)$/);
    var filename = m[1];
    $('#spreadFile').html(filename);
    $("#progress_bar_1").hide();
    $("#progress_bar_2").hide();
    $("#progress_bar_3").hide();
});

$('#exl_file_name').change(function() {
    var filepath = this.value;
    var m = filepath.match(/([^\/\\]+)$/);
    var filename = m[1];
    $('#exl_file_drop').html(filename);
    $("#progress_bar_1").hide();
    $("#progress_bar_2").hide();
    $("#progress_bar_3").hide();
});

$("#animationCall").click(function(){
    setTimeout(function(){ 
        $("#progress_bar_1").addClass("isProgress");
        $("#progress_bar_1 .progressBox .loader").fadeIn();
    }, 0);
    setTimeout(function(){ 
        $("#progress_bar_1").removeClass("isProgress");
        $("#progress_bar_1 .progressBox .loader").hide();
        $("#progress_bar_1").addClass("isRight");
        $("#progress_bar_1 .progressBox .success,#1 .content .text").fadeIn();
    }, 6000);
    setTimeout(function(){ 
        $("#progress_bar_2").addClass("isProgress");
        $("#progress_bar_2 .progressBox .loader").fadeIn();
    }, 9000);
    setTimeout(function(){ 
        $("#progress_bar_2").removeClass("isProgress");
        $("#progress_bar_2 .progressBox .loader").hide();
        $("#progress_bar_2").addClass("isRight");
        $("#progress_bar_2 .progressBox .success,#2 .content .downloadBox").fadeIn();
    }, 12000);
    setTimeout(function(){ 
        $("#progress_bar_3").addClass("isProgress");
        $("#progress_bar_3 .progressBox .loader").fadeIn();
    }, 15000);
    setTimeout(function(){ 
        $("#progress_bar_3").removeClass("isProgress");
        $("#progress_bar_3 .progressBox .loader").hide();
        $("#progress_bar_3").addClass("isWrong");
        $("#progress_bar_3 .progressBox .failure, #3 .content .secondStep").fadeIn();
    }, 18000);
})

$("textarea").focusin(function(){
  this.style.overflow = "hidden";
  this.style.height = (this.scrollHeight) + 'px';
})

$("textarea").focusout(function(){
  this.style.overflow = "auto";
  this.style.height = 'auto';
})