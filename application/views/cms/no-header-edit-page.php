<?php
include_once"include/html-header.php";
include_once"include/header.php";

?>
<style>
    .main-header, .main-sidebar, .main-footer{
        display:none;
    }
    .fixed .content-wrapper, .fixed .right-side{
        padding-top:0;
    }
    .content-wrapper, .right-side, .main-footer{
        margin-left: 0;
    }
</style>
<section class="content">
    <?php echo $warning_msg; ?>
    <div id="edit-page" class="page-wrapper box">
        <form action="" class="form-horizontal <?php echo $this->form_class; ?>" role="form" method="post" enctype="multipart/form-data" id="general-content-form">
            <input type="hidden" name="post_back" value="true" />
            <input type="hidden" name="return" value="<?php echo $this->input->get("return"); ?>" />
            <div class="box-header">
                <h2 class="box-title"><?php echo langc($this->page_name); ?></h2>
                <div class="box-tools">
                    <?php if(!isset($no_save_btn) || !$no_save_btn):?>
                        <button type="submit" class="btn btn-success btn-raised"><?php echo langc("Save");?></button>
                    <?php endif;?>
                </div>
            </div>
            <div class="content-container box-body">
                <?php echo $form; ?>
            </div>
        </form>
        <div class="box-footer no-border">
            <?php echo @$bottom_addon;?>
        </div>
    </div>
</div>
<?php
include_once"include/footer.php";
include_once"include/html-footer.php";
?>