<!DOCTYPE html>
<html>
	<head>
		<title><?php echo (isset($this->organizationObj)?$this->organizationObj["title"]."-":"") . $this->config->item("site_name","cms");?></title>
		<meta charset="utf-8" />
		<base href="<?php echo base_url();?>" />
		<!--[if IE]><meta http-equiv="imagetoolbar" content="no" /><![endif]-->
		<meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		
                <script>
                    var globalVar = {};
                    globalVar.domain = "<?php echo DOMAIN;?>";
                </script>
                <?php Resource::getCSS(false);?>
                <?php Resource::getJS(false);?>
		<!--[if lte IE 8]>
			<?php echo js(array(
				"assets/plugin/selectivizr-min.js",
				"assets/plugin/html5.js",
			));?>
		<![endif]-->
	</head>
	<body class="<?php echo isset($body_class)?$body_class:"hold-transition skin-yellow fixed sidebar-mini";?>">
		