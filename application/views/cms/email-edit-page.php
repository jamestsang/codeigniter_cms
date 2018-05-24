<?php
    include_once"include/html-header.php";
    include_once"include/header.php";
?>
<section class="content">
    <div id="edit-page" class="page-wrapper box">
        <form action="" class="form-horizontal <?php echo $this->form_class; ?>" role="form" method="post" enctype="multipart/form-data" id="general-content-form">
            <input type="hidden" name="post_back" value="true" />
            <input type="hidden" name="return" value="<?php echo $this->input->get("return"); ?>" />
            <div class="box-header">
                <h2 class="box-title"><?php echo langc($this->page_name); ?></h2>
                <div class="box-tools">
                    <?php if ($this->input->get("return")): ?>
                        <a href="<?php echo $this->input->get("return"); ?>"><button type="button" class="btn btn-link btn-raised"><span class="glyphicon glyphicon-chevron-left"></span><?php echo langc("Back");?></button></a>
                    <?php endif; ?>
                    <?php if($not_send < $email_total):?>
                        <a href="" class="btn btn-warning send-btn" role="button">Send Email</a>
                    <?php endif;?>
                </div>
            </div>
            <div class="content-container box-body">
                <?php echo $form; ?>
            </div>
            <?php if($not_send < $email_total):?>
            <div class="info-box bg-yellow sending-box">
                <span class="info-box-icon"><i class="fas fa-envelope"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Sending Email</span>
                  <div class="progress">
                    <div class="progress-bar" style="width: 0%"></div>
                  </div>
                  <span class="progress-description"></span>
                  <a href="" class="btn btn-danger stop-btn" role="button">Stop</a>
                </div>
                <!-- /.info-box-content -->
              </div>
             <?php endif;?>
        </form>
    </div>
</section>
<script>
//10 of 30 email sent
    $(function(){
        var emailList = [];
        var sentItem = <?php echo $not_send;?>;
        var total = <?php echo $email_total;?>;
        var sentingKey = <?php echo $not_send;?>;
        var percent = sentItem/total* 100;
        $(".progress-bar").css("width", percent+"%");
        $(".progress-description").html(sentItem+" of "+total+" email sent");
        $(".stop-btn").on("click", function(){
            location.reload();
            return false;
        });
        $(".send-btn").on("click", function(){
            function sendEmail(){
                $.ajax({ url: "cms/email/sendEmail/<?php echo $id;?>", 
                    async: true, 
                    success: function(msg) { 
                        sentItem++;
                        $(".progress-description").html(sentItem + " of "+total+" email sent");
                        var percent = sentItem/total * 100;
                        $(".progress-bar").css("width", percent+"%");
                        if(sentItem < total){
                            sentingKey++;
                            setTimeout(function(){
                                sendEmail();
                            }, 500);
                        }else{
                            $(".stop-btn").hide(100);
                            setTimeout(function(){
                                location.reload();
                            }, 800);
                        }
                    }
                  });
            }
            if(total > 0){
                $(".progress-description").html(sentItem+" of "+total+" email sent");
                $(".stop-btn").show(100);
                sendEmail();
            }else{
                $(".sending-box").hide(500);
            }
            return false;
        });

        
    });

</script>
<?php
    include_once"include/footer.php";
    include_once"include/html-footer.php";
?>