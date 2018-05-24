<?php
	$body_class="hold-transition login-page";
	include_once"include/html-header.php";
?>
	<div class="login-box">
	  <div class="login-logo">
	    <b>LifeTube</b> CMS
	  </div>
	  <!-- /.login-logo -->
	  <div class="login-box-body">
	    <p class="login-box-msg">Sign in to start your session</p>

	    <?php echo form_open(base_url('cms/index'), array("role"=>"form")); ?>
		  <?php echo validation_errors(); ?>
	      <div class="form-group has-feedback">
	        <input type="email" class="form-control" placeholder="Email" name="email" value="<?php echo set_value('email'); ?>" id="email">
	        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
	      </div>
	      <div class="form-group has-feedback">
	        <input type="password" class="form-control" placeholder="Password" name="password" value="<?php echo set_value('password'); ?>" class="form-control" id="password">
	        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
	      </div>
	      <div class="row">
	        <div class="col-xs-8">
	          
	        </div>
	        <!-- /.col -->
	        <div class="col-xs-4">
	          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
	        </div>
	        <!-- /.col -->
	      </div>
	    </form>
	  </div>
	  <!-- /.login-box-body -->
	</div>
	<!-- /.login-box -->

<?php
	include_once"include/html-footer.php";
?>