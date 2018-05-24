<?php
include_once"include/html-header.php";
include_once"include/header.php";
?>
<div id="list-page" class="page-wrapper">
    <form action="" class="form-horizontal <?php echo $this->form_class; ?>" role="form" method="post" enctype="multipart/form-data" id="general-content-form">
        <input type="hidden" name="post_back" value="true" />
        <?php echo $warning_msg; ?>
        <div class="action-bar float">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-6 col-md-8">
                        <h3><span class="glyphicon glyphicon-tag"></span>
                            <?php echo $this->page_name ?></h3>
                    </div>
                    <div class="col-xs-6 col-md-4 button-box">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-container">
            <?php if (!empty($list)): ?>
                <?php 
                    $group_name = "";
                    $tabs = "";
                    $contents = "";
                    foreach ($list as $key => $l): ?>
                        <?php 
                            if($group_name != $l["group"]){
                                $group_name = $l["group"];
                                $tabs.='<li class="'.($key==0?"active":"").'"><a href="#tab-'.$group_name.'" data-toggle="tab">'. ucfirst(str_replace('_', ' ', $group_name)).'</a></li>';
                                
                                if($key!=0){
                                    $contents.='</div>';
                                }
                                
                                $contents.= '<div class="tab-pane ' . ($key == 0 ? "active" : "") . '" id="tab-' . $group_name . '"><br />';
                            }
                            
                            if ($l["field_type"] == "inputbox"){
                                $field = new textbox_field($l["name"], $l["name"], $l["value"], false, array("display_name"=>$l["title"]));
                                
                            }else if($l["field_type"] == "selectbox"){
                                $options = explode("|", $l["options"]);
                                $options = array_combine($options, $options);
                                $field = new select_field($l["name"], $l["name"], $l["value"], $options, array("display_name"=>$l["title"]));
                            }else{
                                $field = new editor_field($l["name"], $l["name"], $l["value"], false, array("display_name"=>$l["title"]));
                            }
                            $contents.=$field->html();
                        ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <ul class="nav nav-tabs">
                <?php echo $tabs;?>
            </ul>
            <div class="tab-content">
                <?php echo $contents;?>
            </div>
        </div>
    </form>
</div>
<?php
include_once"include/footer.php";
include_once"include/html-footer.php";
?>