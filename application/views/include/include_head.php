    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
    <meta HTTP-EQUIV="CACHE-CONTROL" content="NO-CACHE">
    <meta charset="utf-8">
    <title><?php echo Meta::getTitle();?></title>
    <?php if(Meta::getDescription() != ""):?>
    	<meta property="description" content="<?php echo Meta::getDescription();?>"></meta>
    <?php endif;?>
    <meta property="fb:app_id" content="483775458672225"></meta>
    <?php if(!empty(Meta::getFB())):?>
    	<?php foreach(Meta::getFB() as $name=>$value):?>
    		<meta property="og:<?php echo $name?>" content="<?php echo $value?>"></meta>
    	<?php endforeach;?>
    <?php endif;?>
    <meta property="og:url" content="<?php echo base_current_url();?>"></meta>

    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <?php Resource::getCSS(false);?>
    <?php Resource::getJS(false);?>