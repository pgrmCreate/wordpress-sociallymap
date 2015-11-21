<?php
	$data = get_query_var('articleData');
	$id = $data['id'];
	$link = $data['link'];
?>
<p>
	<link rel="canonical" href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
	
	<a class="sm-readmore-link sm-display-modal" data-article-id="<?php echo $id ?>" data-article-link="<?php echo $link ?>"
	data-fancybox-type="iframe" href="<?php echo $link ?>" data-display-type="modal">
			Lire la suite
	</a>

	<a class="sm-readmore-link sm-display-tab" data-article-id="<?php echo $id ?>" href="<?php echo $link ?>"
	data-article-link="<?php echo $link ?>" target="_blank" data-display-type="tab"> 
			Lire la suite
	</a>
</p>


