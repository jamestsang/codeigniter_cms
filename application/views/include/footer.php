<footer id="footer">
        	<div class="bg-color"></div>
        	<div class="container">
        		<div class="col-sm-6 col-xs-12">
    				<p class="copyright"><?php echo lang("copy_right")?></p>
    				<a href="" class="ig-icon"><i class="fab fa-instagram"></i></a>
    				<a href="" class="fb-icon"><i class="fab fa-facebook-f"></i></a>
    			</div>
    			<div class="col-sm-6  col-xs-12 right">
    				<p class="funded"><?php echo lang("funded");?></p>
    				<img src="<?php echo asset_url("images/HKJC_logo.png")?>" srcset="<?php echo asset_url("images/HKJC_logo@2x.png")?> 2x">
    				<img src="<?php echo asset_url("images/HKJC_logo2.png")?>" srcset="<?php echo asset_url("images/HKJC_logo2@2x.png")?> 2x">
    			</div>
        	</div>
        </footer>
    </body>
    <?php Resource::getCSS(true);?>
    <?php Resource::getJS(true);?>
</html>