<?php 
  if($best_seller_categories) { 
    echo $before_widget;
    if(strlen(trim($title)) > 0) { ?>  
      <h3>  
        <?php echo $title; ?>  
      </h3> 
<?php 
    }
    foreach($best_sellers as $cat_id => $best_seller_id) {
      echo '<h4><a href="' . get_term_link($cat_id,'product_cat') . '">' . $best_seller_categories[$cat_id]['name'] . '</a></h4>';
      echo '<ul>';
      foreach($best_seller_id as $id => $best_seller) {?>
        <li>
          <a href="<?php echo get_permalink( $id );?>">
            <?php 
              if($best_seller['img'] != null) {
                echo get_the_post_thumbnail($id, array(72,41));
              } else {
                echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" width="72px" height="41px" />';
              } 
            ?>
            <div class="summary">
              <span class="title"><?php echo $best_seller['name'] ;?></span>
              <span class="price">
                <?php 
                  $product = get_product($id);
                  echo $product->get_price_html();
                ?>
              </span>
            </div>
          </a>
        </li>
      <?php
      }
      echo '</ul>';
    }
    echo $after_widget;
  }
?>