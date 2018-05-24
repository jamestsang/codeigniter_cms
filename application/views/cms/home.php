<?php
	include_once"include/html-header.php";
	include_once"include/header.php";
?>
<section class="content">
  <div class="home-page page-wrapper">
      <div class="row">
          <?php if(!empty($list)):?>
              <?php foreach($list as $name=>$link):?>
                  <div class="col-lg-3 col-xs-6">
                      <!--<div class="panel panel-default mdl-card mdl-shadow--2dp demo-card-event">
                          <a href="<?php echo $link["url"];?>">
                              <div class="panel-body mdl-card__title mdl-card--expand">
                                  <?php echo $name;?>
                              </div>
                              <div class="panel-footer mdl-card__actions mdl-card--border">View Detail.</div>
                          </a>
                      </div>-->
                      <div class="small-box <?php echo $link["bg_color"];?>">
                        <div class="inner">
                          <h3><?php echo lang($name);?></h3>

                          <p>&nbsp;</p>
                        </div>
                        <div class="icon">
                          <i class="ion <?php echo $link["dashboard_icon"];?>"></i>
                        </div>
                        <a href="<?php echo $link["url"];?>" class="small-box-footer"><?php echo lang("more_info");?><i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                  </div>
              <?php endforeach;?>
          <?php endif;?>
      </div>
  </div>
</section>
  

<?php
	include_once"include/footer.php";
	include_once"include/html-footer.php";
?>