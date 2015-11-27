<?php
    $data = get_query_var('articleData');
    $url = $data['url'];
    $display_type = $data['display_type'];
?>

<p>
	<link rel="canonical" href="<?php echo ($url); ?>" />
	
	<a class="sm-readmore-link sm-display-modal" data-article-link=""
	data-fancybox-type="iframe" href="<?php echo ($url); ?>" data-display-type="modal">
			Lire la suite
	</a>

	<a class="sm-readmore-link sm-display-tab" href="<?php echo ($url); ?>"
	data-article-link="" target="_blank" data-display-type="tab"> 
			Lire la suite
	</a>
</p>


