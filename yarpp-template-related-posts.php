<?php if (have_posts()):?>
    <?php while (have_posts()) : the_post(); ?>
        <h4 class="sidebar-article-title">
            <a href="<?php the_permalink() ?>"><?php the_title(); ?></a>
        </h4>
    <?php endwhile; ?>
<?php endif; ?>