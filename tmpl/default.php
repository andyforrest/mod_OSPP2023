<?php
defined('_JEXEC') or die;

if (!$list) {
    return;
}

?>
<ul class="mod-articleslatest latestnews mod-list">
<?php foreach ($list as $item) : ?>
    <li itemscope itemtype="https://schema.org/Article">
        <a href="<?php echo $item->link; ?>" itemprop="url">
            <span itemprop="name">

                <?php echo $item->title; ?>
                <br>
            </span>
        </a>
    <?php echo $item->author; ?>
    <br>
    <?php echo $item->modified; ?>
    <br>
    <?php echo $item->introtext; ?>
    </li>
<?php endforeach; ?>
</ul>
