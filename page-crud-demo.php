<?php
/**
 * Template Name: CRUD Demo
 */

get_header();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_product_nonce']) && wp_verify_nonce($_POST['new_product_nonce'], 'add_product')) {
    $new_id = wp_insert_post(array(
        'post_type'   => 'product',
        'post_title'  => sanitize_text_field($_POST['product_name']),
        'post_content'=> sanitize_textarea_field($_POST['product_description']),
        'post_status' => 'publish',
    ));
    if ($new_id) {
        add_post_meta($new_id, 'price', floatval($_POST['product_price']));
        echo '<script>window.location = window.location.href;</script>';
    }
}

$products_query = new WP_Query(array(
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
));
?>

<div style="max-width: 900px; margin: 60px auto; padding: 0 20px;">
    <h1>Product Catalog</h1>

    <!-- Add Product Form -->
    <form method="POST" style="margin: 30px 0; padding: 20px; border: 1px solid #ccc; border-radius: 6px;">
        <?php wp_nonce_field('add_product', 'new_product_nonce'); ?>
        <h3>Add a Product</h3>
        <p><input type="text" name="product_name" placeholder="Product name" required style="width:100%;"></p>
        <p><textarea name="product_description" placeholder="Description" required style="width:100%;"></textarea></p>
        <p><input type="number" step="0.01" name="product_price" placeholder="Price" required style="width:100%;"></p>
        <button type="submit">Add Product</button>
    </form>

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
                        <td style="padding: 10px;">$<?php echo number_format(get_post_meta(get_the_ID(), 'price', true), 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
</div>

<?php get_footer(); ?>