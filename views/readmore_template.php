<?php
	$data = get_query_var('articleData');
	$id = $data['id'];
	$link = $data['link'];
?>

<div>
	<a href="<?php echo $link ?>" data-article-id="<?php echo $id ?>" data-article-link="<?php echo $link ?>">
		Lire la suite
	</a>
</div>


