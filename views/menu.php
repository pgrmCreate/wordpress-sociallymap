<ul class="subsubsub">
    <li>
        <?php
        if ($_GET["page"] == "sociallymap-rss-list") {
        ?>
             <span>
                 Mes entités
             </span>
        <?php
        } else {
        ?>
             <a href="?page=sociallymap-rss-list">
                 Mes entités
             </a>
        <?php
        }
        ?>
    </li>
    <li>
        <?php
        if ($_GET["page"] == "sociallymap-rss-add") {
        ?>
             <span>
                Ajouter une entité
             </span>
        <?php
        } else {
        ?>
             <a href="?page=sociallymap-rss-add">
                Ajouter une entité
             </a>
        <?php
        }
    ?>
    </li>
</ul>
