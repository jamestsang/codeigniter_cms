<?php
include_once"include/html-header.php";
include_once"include/header.php";
?>
<div id="edit-page" class="page-wrapper">
        <div class="action-bar float">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-6 col-md-8">
                        <h3>
                            <?php echo $this->page_name ?></h3>
                    </div>
                    <div class="col-xs-6 col-md-4 button-box">
                        <?php if ($this->input->get("return")): ?>
                            <a href="<?php echo $this->input->get("return"); ?>"><button type="button" class="btn btn-link btn-raised"><span class="glyphicon glyphicon-chevron-left"></span>Back</button></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-container">
            <?php if(!empty($ans)):?>
                <?php foreach($ans as $key=>$a):?>
                    <div class="panel <?php echo $a["correct"]==1?'panel-success':'panel-danger';?>">
                    <div class="panel-heading">
                      問題 <?php echo $key+1?>：
                      <div><?php echo $a["content"];?></div>
                    </div>
                    <div class="panel-body"><?php echo $a["title"];?></div>
                  </div>
                <?php endforeach;?>
            <?php endif;?>
        </div>
    </form>
    <?php echo @$bottom_addon;?>
</div>
<?php
include_once"include/footer.php";
include_once"include/html-footer.php";
?>