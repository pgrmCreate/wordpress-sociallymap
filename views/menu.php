<link rel='stylesheet' id='configuration-style.css'
 href='<?php echo basename(dirname( __FILE__ )); ?>/../../wp-content/plugins/sociallymap/views/styles/configuration.css?ver=20120208' type='text/css' media='all' />

<ul class="subsubsub">
	<li>
		<?php
			if($_GET["page"] == "sociallymap-configuration") {
			?>
			 	<span>
			 		Configuration générale
			 	</span>
			<?php 
		}
		else {
			?>
		 		<a href="?page=sociallymap-configuration">
		 			Configuration générale
		 		</a>
			<?php 
		}
		?>
	</li> 
	|
	<li>
		<?php
			if($_GET["page"] == "sociallymap-rss-list") {
			?>
			 	<span>
			 		Mes entités
			 	</span>
			<?php 
		}
		else {
			?>
		 		<a href="?page=sociallymap-rss-list">
		 			Mes entités
		 		</a>
			<?php 
		}
		?>
	</li> 
</ul>

