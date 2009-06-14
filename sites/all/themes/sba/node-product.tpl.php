<?php
$image_path = '/' . file_directory_path() . '/imagecache/product/' . $node->field_image_cache[0]['filename'];
?>

<div class="product-detail">
  <h2 class="name"><?php print $node->title; ?></h2>
  <?php
  $list_price = $node->list_price;
  $sell_price = $node->sell_price;
  if($list_price > $sell_price): ?>
    <div class="product-special">
      <span class="list-price"><?php print uc_currency_format($list_price); ?></span>
      <span class="stock">In Stock</span>
    </div>
  <?php endif; ?>

  <span class="price sale"><?php print uc_currency_format($sell_price); ?></span>
  <?php print $node->content['add_to_cart']['#value']; ?>

  <div class="product-options">
    <?php
    $continue_shopping = (!preg_match("/edit|admin/", $_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'shop';
    print l('Check Out', 'cart', array('html' => true, 'attributes' => array('title' => 'Check Out', 'class' => 'supporting')));
    print l('Continue Shopping', $continue_shopping, array('html' => true, 'attributes' => array('title' => 'Continue Shopping', 'class' => 'supporting sup-alt')));
    print $node->links['addthis']['title'];
    ?>
  </div>

</div>

<img class="product-thumb" src="<?php print $image_path; ?>" 
  alt="<?php print $node->field_esearch_image[0]['data']['alt']; ?>" title="<?php print $node->field_esearch_image[0]['data']['alt']; ?>"/>

<div class="product-info">
  <ul class="clear">
    <li><a href="#toggle-1">About This Product:</a></li>
    <li><a href="#toggle-2">Customer Reviews:</a></li>
  </ul>
  <div class="toggle-content" id="toggle-1">
    <?php print $node->content['fivestar_widget']['#value']; ?>
    <?php print $node->content['body']['#value']; ?>
  </div>
  <div class="toggle-content" id="toggle-2">
    <?php 
    if (function_exists('comment_render') && $node->comment) {
       print comment_render($node, $node->cid);
       $node->comment = NULL;
    }
    ?>
  </div>
</div>

