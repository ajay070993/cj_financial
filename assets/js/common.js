
  

var ajaxRequested = false;
$(document).ready(function() { 
	$('.ajax_form').submit(function(event){
	
	var $submitBTN = $(this).find('button[type="submit"]');
	var btnText = $submitBTN.text();
	$submitBTN.attr('disabled','disabled');
	var posturl=$(this).attr('action');
	var $this = $(this).closest('form');
	var formID =$(this).attr('id');
	var formClass =$(this).attr('class');
	var loadingHTML = '<i class="fa fa-spinner fa-spin fa-lg fa-fw"></i>'
	$submitBTN.text('');
	$submitBTN.append(loadingHTML);
	if(!formID)
	formID = formClass;
	window.ajaxRequested = true;
	$($this).find('.form-group').removeClass('has-error');
	$($this).find('.help-block').hide();
	thisform = $this;
	$.each($this.find('input'),function(key,value){
		if(!$(this).val())
			$(this).removeClass('edited');
	});
	$(this).ajaxSubmit({
					url: posturl,
					dataType: 'json',
					success: function(response){
						$submitBTN.text(btnText);
						checkTosterResponse(response);
						response.formID = formID;
						$submitBTN.removeAttr('disabled');
						$submitBTN.find('.fa-spin').remove();
						window.ajaxRequested = false;
						$($this).find('.ajax_alert').removeClass('alert-danger').removeClass('alert-success');
						$($this).find('.ajax_alert').fadeOut('fast')
						if(response.success)
						 {
							$($this).find('.ajax_alert').addClass('alert-success');
							window.madeChangeInForm = false;
						 }
						 else
						 {
							 $($this).find('.ajax_alert').addClass('alert-danger');
						 }
						if(response.message)
						{
							if(response.notify)
							{
								//showNotifyMessages(response);
								$($this).find('.ajax_alert').fadeIn('slow').children('.ajax_message').html(response.message);
							}
							else
							{
								$($this).find('.ajax_alert').fadeIn('slow').children('.ajax_message').html(response.message);
							}
						}
						if(response.redirectURL)
						window.location.href=response.redirectURL;
						if(response.scrollToThisForm)
						scrollToElement('#'+formID,1000);
						if(response.selfReload)
						window.location.reload();
						if(response.resetForm)
						$($this).resetForm();
						if(response.callBackFunction)
						callBackMe(response.callBackFunction,response);
						$(thisform).find('.form-group').removeClass('has-error');
					},
					error:function(response){
						$submitBTN.text(btnText);
						$submitBTN.removeAttr('disabled'); 
						$submitBTN.find('.fa-spin').remove();
					}
				});
	return false;
	});
});
function callBackMe(functionName,data)
{
	window[functionName](data);
}
function scrollToElement(element,speed)
{
	$('html, body').animate({scrollTop:$(element).position().top + 100},speed);
}
 function checkTosterResponse(data)
  {
    if(data)
    {
      if(data.message)
      {
        if(data.success)
        {
          toastr.success(data.message);
        }
        else
        {
          toastr.error(data.message);
        }
      }
    }    
  }
$(function(){
	$('.ajax_form').on('keypress','input',function(e){
		var key = e.which;
		if(key == 13)  // the enter key code
		{
			$(this).closest('form').submit();    
		}
	})
})





//renderAjaxList function for Select Sport and get data accoding to selected sport
function renderAjaxList(data)
{
	if(data.success)
	{
		$(".ajax_content").html(data.html);
	}
}

//data get according to pagination
$(document).on('click','.pagination a',function(e){
	var href = $(this).attr('href');
	e.preventDefault();
	if(href)
	{
		$.getJSON(href,function(data){
			if(data.success)
			{
				$(".ajax_content").html(data.html);
			}
		})
		return false;
	}
})

//Code for search data accoding to input box
$(document).on('click','#search_form .btn-range',function(e){
	e.preventDefault();
	var search_alpha = $(this).attr('data-value');
	$('.btn-range').removeClass('btn-info');

	if($(this).hasClass('active'))
	{
		$('.btn-range').removeClass('active');
		$("#search_alpha").val('');
	}
	else
	{
		$('.btn-range').removeClass('active');
		$(this).addClass('active');
		$(this).addClass('btn-info');
		$("#search_alpha").val(search_alpha);
	}
	submitSearchData();
	return false;
})
function submitSearchData()
{
	$(".add_link").attr('href',$("#search_form").attr('data-add_url'));
	$("#search_form").submit();
}

function changeLanguage()
{
	$(".add_link").attr('href',$("#language_form .lang_abbr option:selected").attr('data-add_url'));
	$("#language_form").submit();
}
//Code for select multiple checkbox for active,inactive and delete row
 jQuery(document).on('change','#sample_3 .group-checkable',function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");

            jQuery(set).each(function () {
                if (checked) {
                    $(this).attr("checked", true);
                } else {
                    $(this).attr("checked", false);
                }                    
            });
            jQuery.uniform.update(set);
        });