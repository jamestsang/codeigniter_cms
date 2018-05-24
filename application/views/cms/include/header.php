<?php
    $admin_list = $this->config->item("cms");
    if($this->session->userdata("is_admin")!=1){
        unset($admin_list["admin_list"]["Admin"]);
    }
?>
<div class="wrapper">
<header class="main-header">
    <!-- Logo -->
    <a href="../../index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>LifeTube</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>LifeTube</b> CMS</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!--<li class="">
            <a href="<?php echo base_url("/cms/home/language");?>">
              <i class="fa fa-globe"></i> 
              <?php 
                if($this->session->userdata('language') == "english"){
                  echo "中文";
                }else{
                  echo "English";
                }
                
              ?>
            </a>
          </li>-->
          <li class="">
            <a href="<?php echo base_url("/cms/admin/profile");?>">
              <i class="fa fa-user"></i> <?php echo $this->session->userdata('username');?>
            </a>
          </li>
          <li class="">
            <a href="<?php echo base_url("/cms/index/logout");?>">
              <i class="fa fa-sign-out"></i> <?php echo lang("logout");?>
            </a>
          </li>
        </ul>
      </div>
    </nav>
  </header>

<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
      <?php if(!empty($admin_list["admin_list"])):?>
        <?php foreach($admin_list["admin_list"] as $name=>$link):?>
          <?php if(!empty($link["sub"])):?>
            <li class="treeview <?php echo in_array($link["class"], $this->link_path)?"active":"";?>">
              <a href="#">
                <i class="fa <?php echo $link["icon"];?>"></i>
                <span><?php echo lang($name);?></span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <?php foreach($link["sub"] as $sub_name=>$sub_link):?>
                <li class="<?php echo in_array($sub_link["class"], $this->link_path)?"active":"";?>">
                  <a href="<?php echo $sub_link["url"]?>"><i class="fa fa-circle-o"></i> <?php echo lang($sub_name);?></a>
                </li>
                <?php endforeach;?>
              </ul>
            </li>
          <?php else:?>
            <li class="<?php echo in_array($link["class"], $this->link_path)?"active":"";?>">
              <a href="<?php echo $link["url"]?>">
                <i class="fa <?php echo $link["icon"];?>"></i> <span><?php echo lang($name);?></span>
              </a>
            </li>
          <?php endif;?>
        <?php endforeach;?>
      <?php endif;?>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- =============================================== -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    

