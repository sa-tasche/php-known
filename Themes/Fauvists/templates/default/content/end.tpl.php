<?php

    /* @var \Idno\Common\Entity $vars ['object'] */

    $replies = $vars['object']->countAnnotations('reply');
    $likes = $vars['object']->countAnnotations('like');
    $has_liked = false;
    if ($like_annotations = $vars['object']->getAnnotations('like')) {
        foreach ($like_annotations as $like) {
            if (\Idno\Core\site()->session()->isLoggedOn()) {
                if ($like['owner_url'] == \Idno\Core\site()->session()->currentUser()->getDisplayURL()) {
                    $has_liked = true;
                }
            }
        }
    }
    $owner = $vars['object']->getOwner();

?>
<div class="permalink">
    <p>
    	<a href="<?=$owner->getDisplayURL()?>"><?=$owner->getTitle()?></a>published this
        <a class="u-url url" href="<?= $vars['object']->getDisplayURL() ?>" rel="permalink">
            <time class="dt-published"
                  datetime="<?= date('c', $vars['object']->created) ?>"><?= date('c', $vars['object']->created) ?></time></a>
        <?= $this->draw('content/edit') ?>
        <?= $this->draw('content/end/links') ?>
        <?php

            if (\Idno\Core\site()->currentPage()->isPermalink() && \Idno\Core\site()->config()->indieweb_citation) {

                ?>
                <span class="citation"><?= $vars['object']->getCitation() ?></span>
            <?php

            }

        ?>
    </p>
</div>
<div class="interactions">
    <?php
        if (!$has_liked) {
            $heart = '<i class="icon-star-empty"></i>';
        } else {
            $heart = '<i class="icon-star"></i>';
        }
        if ($likes == 1) {
            $heart .= ' 1 star';
        } else {
            $heart .= ' ' . $likes . ' stars';
        }
        if (\Idno\Core\site()->session()->isLoggedOn()) {
            echo \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getDisplayURL() . 'annotation/post', $heart, ['type' => 'like', 'object' => $vars['object']->getUUID()], ['method' => 'POST', 'class' => 'stars']);
        } else {
            ?>
            <a class="stars" href="<?= $vars['object']->getDisplayURL() ?>#comments"><?= $heart ?></a>
        <?php
        }
    ?>
    <a class="comments" href="<?= $vars['object']->getDisplayURL() ?>#comments"><i class="icon-chat"></i> <?php

            //echo $replies;
            if ($replies == 1) {
                echo '1 comment';
            } else {
                echo $replies . ' comments';
            }

        ?></a>
    <a class="shares" href="<?= $vars['object']->getDisplayURL() ?>#comments"><?php if ($shares = $vars['object']->countAnnotations('share')) {
            echo '<i class="icon-arrows-cw"></i> ' . $shares;
        } ?></a>
    <a class="rsvps" href="<?= $vars['object']->getDisplayURL() ?>#comments"><?php if ($rsvps = $vars['object']->countAnnotations('rsvp')) {
            echo '<i class="icon-calendar-empty"></i> ' . $rsvps;
        } ?></a>
</div>
<br clear="all"/>
<?php

    if (\Idno\Core\site()->currentPage()->isPermalink()) {

        if (!empty($likes) || !empty($replies) || !empty($shares) || !empty($rsvps)) {

            ?>

            <div class="annotations">

                <a name="comments"></a>
                <?= $this->draw('content/end/annotations') ?>
                <?php

                    if ($replies = $vars['object']->getAnnotations('reply')) {
                        echo $this->__(['annotations' => $replies])->draw('entity/annotations/replies');
                    }
                    if ($likes = $vars['object']->getAnnotations('like')) {
                        echo $this->__(['annotations' => $likes])->draw('entity/annotations/likes');
                    }
                    if ($shares = $vars['object']->getAnnotations('share')) {
                        echo $this->__(['annotations' => $shares])->draw('entity/annotations/shares');
                    }
                    if ($rsvps = $vars['object']->getAnnotations('rsvp')) {
                        echo $this->__(['annotations' => $rsvps])->draw('entity/annotations/rsvps');
                    }

                ?>

            </div>

        <?php

        }

        echo $this->draw('entity/annotations/comment/main');

        echo $this->draw('content/syndication/links');

    } else {

        if (\Idno\Core\site()->session()->isLoggedOn()) {
            echo $this->draw('entity/annotations/comment/mini');
        }

    }

?>
