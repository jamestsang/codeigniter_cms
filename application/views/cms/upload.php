<?php
	include_once"include/html-header.php";
	echo js(array(
		"assets/plugin/uploadify/swfobject.js",
		"assets/plugin/uploadify/uploadify.js?ver=".rand(0,9999),
                "assets/plugin/dropzone/dropzone.js"
	));
	echo css(array(
		"assets/plugin/uploadify/uploadify.css",
            "assets/plugin/dropzone/dropzone.css"
	));
	$ordring_list = array();
	if($limit > 0){
		if($total >= $limit){
			$this_limit = 0;
		}else{
			$this_limit = ($total - $limit > 5)?5:$limit - $total;
			if($this_limit<0){
				$this_limit = 0;
			}
		}
	}else{
		$this_limit = 5;
	}
?>
	<div id="media-uploader">
		<?php if($this_limit>0):?>
		<div id="uploader-wrapper" class="clearfix">
			<input type="file" name="uploader[]" id="uploadify"/>
		</div>
                <div id="dropzone">
                    <form action="<?php echo base_url();?>cms/upload/add/<?php echo $section."/".$id."/".$folder;?>" class="dropzone" id="my-awesome-dropzone">
                          <div class="dz-message">
                            Drop files here or click to upload.
                          </div>
                    </form>
                </div>
		<?php endif;?>
		<div class="file-list">
			<div class="container-fluid image-list">
			  <?php if(!empty($image_list)):?>
				<?php foreach($image_list as $image):
					$ordring_list[] = $image["ordering"];
				?>
					<div class="row" data-id="<?php echo $image["image_id"];?>"  id="<?php echo $image["image_id"];?>">
						<div class="move-icon col-md-1 col-sm-1 hidden-xs"><span class="glyphicon glyphicon-resize-vertical"></span></div>
						<div class="thumb col-md-2 col-sm-2 col-xs-4"><img src="<?php echo base_url().$image["path"]?>" alt="" /></div>
						<div class="title col-md-7 col-sm-7 col-xs-6">
							<div><?php echo $image["filename"];?></div>
							<?php if($caption=="yes"):?><label>Caption:&nbsp;</label><input type="text" value="<?php echo $image["caption"];?>" class="caption"/><?php endif;?>
						</div>
						<div class="delete-icon col-md-2 col-sm-2 col-xs-2"><span class="glyphicon glyphicon-remove"></span></div>
					</div>
				<?php endforeach;?>
			  <?php endif;?>
			</div>
			<!--<ul class="temp">
				<li class="list-group-item clearfix">
					<div class="move-icon"><span class="glyphicon glyphicon-resize-vertical"></span></div>
					<div class="thumb"><img src="" alt="" /></div>
					<div class="title">
						<div></div>
						<?php if($caption=="yes"):?><label>Caption:&nbsp;</label><input type="text" class="caption"/><?php endif;?>
					</div>
					<div class="delete-icon"><span class="glyphicon glyphicon-remove"></span></div>
				</li>
			</ul>-->
		</div>
	</div>
	<script>
		var ordering = <?php echo json_encode($ordring_list);?>;
		var inited = false;
                function destroyUploadify(){
                	if(inited){
                    	$("#uploadify").uploadify("destroy");
                	}
                }
		$(function(){
                    if(Modernizr.input["multiple"]){
                        $("#uploader-wrapper").hide(0);
                    }else{
                        $("#dropzone").hide(0);
                    }
                    
                    Dropzone.options.myAwesomeDropzone = {
                        paramName: "uploader[]", // The name that will be used to transfer the file
                        maxFilesize: 500, // MB
                        maxFiles: <?php echo $this_limit;?>,
                        //uploadMultiple:true,
                        queuecomplete: function(){
                            location.reload(true);
                        }
                    };
                    
                        var browser_cookies = "<?= trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->config->item('encryption_key'), $_COOKIE[$this->config->item('sess_cookie_name')], MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))); ?>";
			$("#uploadify").uploadify({
				'swf'            : 'assets/plugin/uploadify/uploadify.swf',
				'uploader'       : '<?php echo base_url();?>cms/upload/add/<?php echo $section."/".$id."/".$folder;?>',
				'cancelImage'    : '<?php echo base_url();?>assets/plugin/uploadify/uploadify-cancel.png',
				'fileObjName'    : 'uploader[]',
				'auto'           : true,
				'multi'          : true,
				'checkExisting'  :'<?php echo base_url();?>cms/upload/check',
				'queueSizeLimit' : <?php echo $this_limit;?>,
                                'formData'       : {"browser_cookie": browser_cookies},
                                //'debug': true,
				'onQueueComplete':function(stats){
				   location.reload(true);
				},
				onInit   : function(instance) {
		            alert('The queue ID is ' + instance.settings.queueID);
		            inited = true;
		        }
			});
			
			$( ".image-list" ).sortable({
				update:updateSort
			}).disableSelection();
			
			function updateSort(){
				var image_list = $( ".image-list" ).sortable( "toArray" );
				$.post("<?php echo base_url();?>cms/upload/updateOrdering", {image:image_list, ordering:ordering});
			}
			
			$(".glyphicon-remove").off("tap");
			$(".glyphicon-remove").on('tap' ,function(){
				var con=confirm('Confirm Delete This Image?');
				if(!con) return false;
				var id=$(this).parents(".row").attr('data-id');
				$.get('<?php echo base_url();?>cms/upload/delete/'+id, function(response){
					location.reload(true);
				});
				return false;
			});
			
			$(document).off("blur", ".caption");
			$(document).on("blur", ".caption", function(){
				var id=$(this).parents(".row").attr('data-id');
				var field = $(this);
				var caption = field.val();
				field.val("Process......");
				$.post('<?php echo base_url();?>cms/upload/updateCaption/'+id, {caption:caption}, function(){
					field.val(caption);
				});
			});
		});
	</script>
<?php
	include_once"include/html-footer.php";
?>