<?php
	include_once"include/html-header.php";
	include_once"include/header.php";
?>
<section class="content">
	<div id="list-page" class="<?php echo $this->form_class;?>  no-padding">
		<?php echo $warning_msg;?>
		
		<div class="content-container box">
			<div class="box-header">
				<h2 class="box-title"><?php echo langc("$this->page_name");?></h2>
				<div class="box-tools">
					<?php if($this->input->get("return")):?>
						<a href="<?php echo $this->input->get("return");?>"><button type="button" class="btn btn-link btn-raised"><span class="glyphicon glyphicon-chevron-left"></span><?php echo langc("Back");?></button></a>
					<?php endif;?>
					<?php if($config["canAdd"]):?>
						<a href="<?php echo $this->alias."/".$this->add_page_link."?return=".urlencode($this->input->getFullPath());?>" class="btn btn-primary" role="button"><?php echo langc("New Record");?></a>
					<?php endif;?>
				</div>
			</div>
			<div class="box-body">
                <?php echo @$filterHtml;?>
                <?php echo $top_addon;?>
				<?php echo $table;?>
			</div>
			
			<div class="box-footer clearfix no-border page-contrainer">
			  <div class="row">
				<div class="col-md-8">
					<ul class="pagination"><?php echo $page_container;?></ul>
				</div>
				<div class="col-md-2 label-col">
					<label class="control-label"><?php echo langc("Display per page");?></label>
				</div>
				<div class="col-md-2">
					<?php echo CMS::perPageSelect($total, $record_per_page, $this->input->getFullPath(array("show_page")));?>
				</div>
			  </div>
			</div>
		</div>
	</div>
</section>
	<?php if($ordering):?>
		<script>
			var CMS = {};
			CMS.ordering = true;
			CMS.ordering_path = "<?php echo base_url("cms/".$this->ordering_link)?>";
		</script>
	<?php 
		echo js(array(
			"assets/cms/js/ordering.js",
		));
	endif;?>
<?php
	include_once"include/footer.php";
	include_once"include/html-footer.php";
?>