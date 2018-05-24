<?php include("include/include_html.php"); ?>
    <head>
        <?php include("include/include_head.php"); ?>
    </head>
    <?php 
    	$page_class="login-page form-page inner-page";
    	include_once "include/header.php";
    ?>
    <section class="container content-wrapper">
        <div class="row">
            <div class="col-md-4 hidden-sm hidden-xs left">
                <figure class="sub-title" data-aos="fade-right">
                    <img src="<?php echo asset_url("images/".langCode()."/memberzone_title.png")?>"  srcset="<?php echo asset_url("images/".langCode()."/memberzone_title@2x.png")?> 2x" />
                </figure>
            </div>
            <section class="col-md-8 col-sm-12 right" data-aos="fade-left">
                <figure class="sub-title"><img src="<?php echo asset_url("images/".langCode()."/member-login.png")?>"  srcset="<?php echo asset_url("images/".langCode()."/member-login@2x.png")?> 2x" /></figure>
                <?php echo form_open(base_lang_url('member/login'), array("role"=>"form", "id"=>"login-form")); ?>
                    <div class="error-wrapper">
                        <?php echo validation_errors(); ?>
                        <?php if(!empty($error)):?>
                            <?php foreach($error as $msg):?>
                                <li class="alert-message"><?php echo $msg;?></li>
                            <?php endforeach;?>
                        <?php endif;?>        
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" value="<?php echo set_value('email'); ?>" placeholder="<?php echo lang("email");?>" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="<?php echo lang("Password");?>" required>
                    </div>
                    <button type="submit" class="btn-style login-btn"><?php echo lang("member_login");?></button>
                    <a href="<?php echo $fb_url;?>"><img src="<?php echo asset_url("images/fb_btn.png");?>" class="fb-login-btn" /></a>
                    <a href="<?php echo base_lang_url("member/registration");?>" class="btn-style reg-btn"><?php echo lang("立即登記");?></a>
                </form>
                <ul class="option-list">
                    <li><a href="<?php echo base_lang_url("member/forget")?>"><?php echo lang("forget_password");?>？</a></li>
                    <li><a href="<?php echo base_lang_url("member/registration");?>"><?php echo lang("need_reg");?>？</a></li>
                    <li><a href="#"><?php echo lang("why_reg");?>？</a></li>
                </ul>
            </section>
        </div>
        <div class="bottom" data-aos="fade-up">
            <h2 class="title"><?php echo lang("why_reg");?>？</h2>
            <div class="desc"><?php echo lang("why_reg_desc");?></div>
        </div>
        
    </section>
<script>
    var localizedMessage = {
        required:"<?php echo lang("must_fill");?>",
        email:"<?php echo lang("email");?>",
        password:"<?php echo lang("password");?>",
        valid_email:"<?php echo lang("email_incorrect");?>",
    };
</script>
 <?php include_once "include/footer.php";?>
