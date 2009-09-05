<?php
// $Id: template.php,v 1.16.2.1 2009/02/25 11:47:37 goba Exp $

/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return a string containing the breadcrumb output.
 */
function sba_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
    return '<div class="breadcrumb">'. implode(' › ', $breadcrumb) .'</div>';
  }
}

/**
 * Allow themable wrapping of all comments.
 */
function sba_comment_wrapper($content, $node) {
  if (!$content || $node->type == 'forum') {
    return '<div id="comments">'. $content .'</div>';
  }
  else {
    $output = '';
    $output .= '<div id="comments">';
    if($node->type != 'product') {
      $output .= '<h3 class="comments">'. t('Comments') .'</h2>';
    }
    $output .= $content .'</div>';
    return $output;
  }
}

/**
 * Override or insert PHPTemplate variables into the templates.
 */
function sba_preprocess_page(&$vars) {
  $vars['tabs2'] = menu_secondary_local_tasks();

  // Hook into color.module
  if (module_exists('color')) {
    _color_page_alter($vars);
  }

  //Allow for appropriate template overrides
  if (module_exists('path')) {
    //Set array defining the suggested template names
    $suggestions = array();
    $template_append = 'page-';

    //Create page template based upon content type
    if($vars['node']->type && $vars['node']->type!='page')
      array_push($suggestions, $template_append . $vars['node']->type);

    //Create page template based on URL
    $alias = drupal_get_path_alias(str_replace('/edit','',$_GET['q']));
    if ($alias != $_GET['q']) {
      foreach (explode('/', $alias) as $path_part) {
        $template_filename = $template_append . $path_part;
        array_push($suggestions, $template_filename);
      }
    }

    $vars['template_files'] = $suggestions;
  }
}

/**
 * Returns the rendered local tasks. The default implementation renders
 * them as tabs. Overridden to split the secondary tasks.
 *
 * @ingroup themeable
 */
function sba_menu_local_tasks() {
  return menu_primary_local_tasks();
}

function sba_comment_submitted($comment) {
  return t('!datetime — !username',
    array(
      '!username' => theme('username', $comment),
      '!datetime' => format_date($comment->timestamp)
    ));
}

function sba_node_submitted($node) {
  return t('!datetime — !username',
    array(
      '!username' => theme('username', $node),
      '!datetime' => format_date($node->created),
    ));
}

/**
 * Generates IE CSS links for LTR and RTL languages.
 */
function sba_get_ie_styles() {
  global $language;

  $iecss = '<link type="text/css" rel="stylesheet" media="all" href="'. base_path() . path_to_theme() .'/fix-ie.css" />';
  if ($language->direction == LANGUAGE_RTL) {
    $iecss .= '<style type="text/css" media="all">@import "'. base_path() . path_to_theme() .'/fix-ie-rtl.css";</style>';
  }

  return $iecss;
}

function sba_links($links, $attributes = array('class' => 'links')) {
  global $language;
  $output = '';

  if (count($links) > 0) {
    $output = '<ul'. drupal_attributes($attributes) .'>';

    $num_links = count($links);
    $i = 1;

    foreach ($links as $key => $link) {
      $class = '';

      // Add first, last and active classes to the list of links to help out themers.
      if ($i == $num_links) {
        $class .= 'last';
      }
      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
          && (empty($link['language']) || $link['language']->language == $language->language)) {
        $class .= ' active';
      }
      $output .= '<li';
      if($class)
        $output .= drupal_attributes(array('class' => $class));
      $output .= '>';

      if (isset($link['href'])) {
        // Pass in $link as $options, they share the same keys.
        $output .= l($link['title'], $link['href'], $link);
      }
      else if (!empty($link['title'])) {
        // Some links are actually not links, but we wrap these in <span> for adding title and class attributes
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span'. $span_attributes .'>'. $link['title'] .'</span>';
      }

      $i++;
      $output .= "</li>\n";
    }

    $output .= '</ul>';
  }

  return $output;
}

function sba_menu_item($link, $has_children, $menu = '', $in_active_trail = FALSE, $extra_class = NULL) {
  $class = ($menu ? 'expanded' : '');
  if (!empty($extra_class)) {
    $class .= ' '. $extra_class;
  }
  if ($in_active_trail) {
    $class .= ' active-trail';
  }
  if($class) {
    $class = ' class="' . $class . '"';
  }
  return '<li' . $class . '>'. $link . $menu ."</li>\n";
}

/**
 * Returns the file path image
 * 
 * @param $fid
 *   An integer containing the fid
 */
function get_filepath($fid) {
  return db_result(db_query("SELECT filepath FROM {files} WHERE fid = %d", $fid));
}

function body_class(&$node) {
  if(!$node) {
    $page_title = $_GET['q'];
    if(preg_match("/\//", $page_title)) {

      $classes = '';
      $suggestions = explode('/', $page_title);
      foreach($suggestions as $suggest) {
        if(!is_numeric($suggest)) {
          $classes .= $suggest . ' ';
        }
      }
      return $classes;

    } else {
      return $page_title;
    }
  } else if($node->type) {
    return $node->type;
  }
}

function special_class(&$node) {
  if($node->type=='product')
    return ' hproduct';
}

function sba_uc_product_price($price, $class, $no_label = false) {
  return uc_currency_format($price);
}

function sba_title($title, &$node) {
  switch($node->type) {
    case 'product':
      return;
  }

  return $title;
}

function sba_cart_status() {
  $output = '';

  $items = uc_cart_get_contents();
  $num_items = count($items);
  if($num_items) {
    return l('Cart <span class="items">(' . $num_items . ')</span>', 'cart', array(
          'html' => true, 
          'attributes' => array('title' => 'The number of items in your cart is ' . $num_items)));
  } else {
    return l('<span class="empty">Cart (' . $num_items . ')</span>', 'cart', array(
          'html' => true, 
          'attributes' => array('title' => 'There are no items in your cart')));
  }
}

function purifier($name) {
  $name = strtolower(trim($name));
  $name = preg_replace('[ ]', '-', $name);
  $name = preg_replace('[\.|\'|\"|\:|\;|\&reg;|-&amp|\(|\)|\[|\]]', '', $name);
  return $name;
}

function comment_count($count) {
  if($count!=1) 
    $append = 's';

  return $count . ' Comment' . $append;
}

