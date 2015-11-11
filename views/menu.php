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

