<?php
	$data = get_query_var('articleData');
	$id = $data['id'];
	$link = $data['link'];
	$displayType = $data['display_type'];
?>
<?php
	// MODAL VERSION
	if($displayType == "modal") {
?>
	<a class="sm-readmore-link" data-article-id="<?php echo $id ?>" data-article-link="<?php echo $link ?>"
	data-fancybox-type="iframe" href="<?php echo $link ?>">
			Lire la suite
	</a>
<?php
	}
	// EXTERNE VERSION
	elseif($displayType == "tab") {
?>
	<a class="sm-readmore-link" data-article-id="<?php echo $id ?>" href="<?php echo $link ?>"
	data-article-link="<?php echo $link ?>" target="_blank"> 
			Lire la suite
	</a>
<?php
	}
?>

<div class="sm-modal-container">
	<div class="sm-modal-overlay" data-article-id="<?php echo $id ?>"></div>

	<div class="sm-modal-content">
		<p class="sm-modal-close">
			<i class="dashicons-before dashicons-dismiss sociallymap-icon-button"></i>
		</p>
	</div>
</div>


