<?php
print '<ul class="slider">';
foreach($view->result as $result) {
  print '<li>';
  $fid = $result->node_data_field_homepage_image_field_homepage_image_fid;
  $path = get_filepath($fid);
  $data = unserialize($result->node_data_field_homepage_image_field_esearch_image_data);
  $item = '';
  $timestamp = strtotime($result->node_created);
  $pub_date = date('M j, Y', $result->node_created);
  
  $item .= '<img src="/' . $path . '" alt="' . $data['alt'] . '" title="' . $data['title'] . '" />';
  $item .= '<span class="product-info-wrap">';
  $item .= '<span class="price">' . uc_currency_format($result->uc_products_sell_price) . '</span>';
  $item .= '<span class="name">' . $result->node_title . '</span>';
  $item .= '</span>';

  print l($item, drupal_get_path_alias('node/' . $result->nid), array('html' => true));
  
  print '</li>';
}
print '</ul>';

