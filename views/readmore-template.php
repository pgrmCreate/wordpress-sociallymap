<?php
	$data = get_query_var('articleData');
?>

<p>
	<link rel="canonical" href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
	
	<a class="sm-readmore-link sm-display-modal" data-article-link=""
	data-fancybox-type="iframe" href="" data-display-type="modal">
			Lire la suite
	</a>

	<a class="sm-readmore-link sm-display-tab" href=""
	data-article-link="" target="_blank" data-display-type="tab"> 
			Lire la suite
	</a>
</p>


