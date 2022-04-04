<div class="topBar">

<svg class="menuIcon" data-type="<?php if($this->session->userdata('data-type-collapse')){ echo $this->session->userdata('data-type-collapse'); } else { echo "0"; } ?>" viewBox="0 -53 384 384" xmlns="http://www.w3.org/2000/svg"><path d="M368 154.668H16c-8.832 0-16-7.168-16-16s7.168-16 16-16h352c8.832 0 16 7.168 16 16s-7.168 16-16 16zm0 0M368 32H16C7.168 32 0 24.832 0 16S7.168 0 16 0h352c8.832 0 16 7.168 16 16s-7.168 16-16 16zm0 0M368 277.332H16c-8.832 0-16-7.168-16-16s7.168-16 16-16h352c8.832 0 16 7.168 16 16s-7.168 16-16 16zm0 0"/></svg>
    
    
    <div class="userBox">
    	<?php if($_SERVER['SERVER_NAME']=='cjxtract.blucognition.ai'){ ?>
        <img src="<?php echo $this->config->item('assets'); ?>images/credijusto.png" class="img-fluid" style="margin-right: 20px;width: 114px;">
        <?php } ?>
        <div class="notification">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20.2 18.534"><path class="a" d="M15.9 9.767v-3a4.85 4.85 0 00-2.912-4.405 6.975 6.975 0 00-1.188-.404v-.191a1.707 1.707 0 00-3.414 0v.22a6.866 6.866 0 00-1.074.379 5.046 5.046 0 00-3.023 4.4v3A7.309 7.309 0 012 15.2a.328.328 0 00-.085.337.339.339 0 00.268.228l3.24.546c.6.1 1.159.185 1.688.254a3.41 3.41 0 006.088 0c.527-.068 1.08-.151 1.68-.253l3.24-.546a.339.339 0 00.268-.228.327.327 0 00-.087-.338 7.371 7.371 0 01-2.4-5.433zm-6.836-8a1.024 1.024 0 012.048 0v.061a6.949 6.949 0 00-2.048.016zm1.087 16a2.757 2.757 0 01-2.191-1.1 21.647 21.647 0 002.187.115 21.53 21.53 0 002.2-.116 2.758 2.758 0 01-2.192 1.101zm4.608-2.116c-.655.11-1.256.2-1.823.271-.25.031-.493.058-.731.081H12.2c-.218.021-.431.039-.642.053l-.118.008c-.183.012-.363.021-.543.028h-.11c-.425.014-.844.014-1.268 0h-.114a25.124 25.124 0 01-.534-.027l-.128-.008a21.79 21.79 0 01-.623-.052h-.023c-.236-.023-.477-.05-.725-.081a42.94 42.94 0 01-1.83-.271l-2.618-.443a7.912 7.912 0 002.048-5.443v-3a4.367 4.367 0 012.626-3.8 6.2 6.2 0 011.194-.394H8.8a6.353 6.353 0 012.588-.023h.013a6.28 6.28 0 011.306.416 4.23 4.23 0 012.513 3.8v3a8 8 0 002.155 5.444z"/><path class="a" d="M11.903 3.918a4.584 4.584 0 00-3.741 0A3.032 3.032 0 006.378 6.51a.3.3 0 00.3.3.3.3 0 00.3-.3 2.42 2.42 0 011.435-2.055 3.971 3.971 0 013.24 0 .3.3 0 10.25-.545zM1.541 7.152a.3.3 0 00-.424 0 3.478 3.478 0 000 4.914.3.3 0 00.424-.424 2.878 2.878 0 010-4.066.3.3 0 000-.424z"/><path class="a" d="M2.591 11.46a.3.3 0 00.212-.512 1.97 1.97 0 010-2.783.3.3 0 00-.424-.424 2.571 2.571 0 000 3.631.3.3 0 00.212.088zM19.084 7.152a.3.3 0 00-.424.424 2.878 2.878 0 010 4.066.3.3 0 10.424.424 3.478 3.478 0 000-4.914z"/><path class="a" d="M17.397 7.741a.3.3 0 000 .424 1.97 1.97 0 010 2.783.3.3 0 10.424.424 2.571 2.571 0 000-3.631.3.3 0 00-.424 0z"/></svg>
            <div class="number"></div>
            <div class="notificationBox" style="display: none">
                <div class="text">
                    <p>Request <span>#6485</span> is assigned to you.</p>
                    <p>Request <span>#6485</span> is completed by <span>Arun</span>.</p>
                    <p>Request <span>#6485</span> is rejected by xyz engine.</p>
                </div>
            </div> 
        </div>
        <div class="userImg">A</div>
        <span><?php echo $this->session->userdata('email');?></span>
        <!-- <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 14.496 8.521"><g fill="none" stroke-linecap="round" stroke-width="1.8"><path d="M1.273 1.273l5.97 5.975M13.223 1.273l-5.97 5.975"/></g></svg> -->
    </div>
</div>

<script>
    $(document).ready(function () {
        $(".notification").click(function () {
            $(".notification .notificationBox").toggle();
        })
        $(".notification .notificationBox").click(function(e) {
            e.stopPropagation();
        });
    });
    // <?php if($this->session->userdata('data-type-collapse')){ ?>

    //     <?php if($this->session->userdata('data-type-collapse')==1){ ?>
    //         $(function(){
    //             $(".navigation").removeClass("navSmall");
    //             $(".main").removeClass("mainSmall");
    //         });
    //     <?php } else { ?>
    //         $(function(){
    //             $(".navigation").addClass("navSmall");
    //             $(".main").addClass("mainSmall");
    //         });
    //     <?php } ?>

    // <?php } else { ?>
    //     $(function(){
    //         $(".navigation").addClass("navSmall");
    //         $(".main").toggleClass("mainSmall");
    //     });

    // <?php } ?>


    $(".menuIcon").click(function(){
        var data_type = $(this).attr('data-type');
        if(data_type==0){
            $(this).attr('data-type','1');
        }
        else if(data_type==1){
            $(this).attr('data-type','0');
        }
        $(".navigation").toggleClass("navSmall");
        $(".main").toggleClass("mainSmall");
        var surl = siteurl+'<?php echo ($this->session->userdata('application_type') == 'fs' ? "Fs_" : ""); ?>result/setCollapseIcon?data_type='+data_type; 

        $.getJSON(surl,function(response){
            if (response.success) {         
                   
            }        
        }); 
    })
</script>
