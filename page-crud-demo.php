<?php
/**
 * Template Name: CRUD Demo
 */

get_header();

// WP_Query is WordPress's native way to fetch any content type —
// Posts, Pages, or custom post types like our "product"
$products_query = new WP_Query(array(
    'post_type'      => 'product',
    'posts_per_page' => -1, // -1 means "get all of them"
    'orderby'        => 'title',
    'order'          => 'ASC',
));
?>

<div style="max-width: 900px; margin: 60px auto; padding: 0 20px;">
    <h1>Product Catalog</h1>

    <?php if ($products_query->have_posts()) : ?>

        <table style="width: 100%; border-collapse: collapse; margin-top: 30px;">
            <thead>
                <tr style="border-bottom: 2px solid #333; text-align: left;">
                    <th style="padding: 10px;">Product</th>
                    <th style="padding: 10px;">Description</th>
                    <th style="padding: 10px;">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($products_query->have_posts()) : $products_query->the_post(); ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;"><?php the_title(); ?></td>
                        <td style="padding: 10px;"><?php the_content(); ?></td>
                        <td style="padding: 10px;">
                            $<?php echo number_format(get_post_meta(get_the_ID(), 'price', true), 2); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php else : ?>
        <p>No products found yet.</p>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
</div>

<?php
get_footer();