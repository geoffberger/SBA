<?php

$i = 1;
print '<ul class="listing clear">';
foreach($view->result as $result) {
  $fid = $result->node_data_field_esearch_image_field_esearch_image_fid;
  $path = get_filepath($fid);
  $data = unserialize($result->node_data_field_esearch_image_field_esearch_image_data);
  $item = '';

  $item .= '<img class="product-thumb" src="/' . $path . '" alt="' . $data['alt'] . '" title="' . $data['title'] . '" width="' . $data['width'] . '" height="' . $data['height'] . '"/>';
  $item .= '<span class="name">' . $result->node_title . '</span>';

  print '<li class="hproduct';

  if($i % 4 ==0) {
    print ' last';
  }

  print '">';
  print l($item, drupal_get_path_alias('node/' . $result->nid), 
      array('html' => true, 'attributes' => array('title' => $result->node_title, 'class' => 'url', 'rel' => 'product')));
  print '<span class="price">' . uc_currency_format($result->uc_products_sell_price) . '</span>';
  print '</li>';

  if($i % 4 ==0) {
    print '<li class="list-clear"></li>';
  }

  $i++;
}
print '</ul>';
