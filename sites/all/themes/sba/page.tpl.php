<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" xml:lang="<?php print $language->language ?>" lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">
  <head>
    <title><?php print $head_title ?></title>
    <?php print $head ?>
    <?php print $styles ?>
    <?php print $scripts ?>
  </head>
  <body<?php if(body_class($node)): print ' class="' . body_class($node) . '"'; endif; ?>>

    <div class="header-wrap">
      <div class="header standard-container">
        <?php
          // Prepare header
          $site_fields = array();
          if ($site_name) {
            $site_fields[] = check_plain($site_name);
          }
          if ($site_slogan) {
            $site_fields[] = check_plain($site_slogan);
          }
          $site_title = implode(' ', $site_fields);
          if ($site_fields) {
            $site_fields[0] = '<span>'. $site_fields[0] .'</span>';
          }
          $site_html = implode(' ', $site_fields);

          if ($logo || $site_title) {
            //print '<h1><a href="'. check_url($front_page) .'" title="'. $site_title .'">';
            if ($logo) {
              //print '<img src="'. check_url($logo) .'" alt="'. $site_title .'" id="logo" />';
            }
            //print $site_html .'</a></h1>';
          }
        ?>
      

        <h1><a title="<?php print $site_title; ?>" href="<?php print check_url($front_page); ?>"><?php print $site_title; ?></a></h1>

        <?php if($user->uid): ?>
          <?php print theme('links', menu_navigation_links('menu-auth-utility-links', 0), array('class' => 'global-nav')) ?>
        <?php else: ?>
          <?php print theme('links', menu_navigation_links('menu-utility-links', 0), array('class' => 'global-nav')) ?>
        <?php endif; ?>

        <div class="cart-wrap">
          <?php print sba_cart_status(); ?>
        </div>
        

        <?php if (isset($primary_links)) : ?>
          <?php print theme('links', $primary_links, array('class' => 'main-nav')) ?>
        <?php endif; ?>

        <form action="/search/node" method="post" class="standard-search">
          <fieldset>
            <label for="search">Search</label>
            <input id="search" name="keys" value="" title="type your search word in here"/>
            <button type="submit" name="op" value="Go">Go</button>
            <input type="hidden" value="<?php print drupal_get_token('search_form'); ?>" name="form_token" />
            <input type="hidden" value="search_form" id="edit-search-form" name="form_id" /> 
            <input type="hidden" name="type[product]" id="edit-type-product" value="product" />
          </fieldset>
        </form>
        
      </div>
    </div>

    <div class="content-wrap">
      <div class="content standard-container clear<?php print special_class($node);?>">
        <?php print $breadcrumb; ?>
        <?php if ($mission): print '<div id="mission">'. $mission .'</div>'; endif; ?>
        <?php if (sba_title($title, $node)): print '<h2>'. sba_title($title, $node) .'</h2>'; endif; ?>

        <?php if ($show_messages && $messages): print $messages; endif; ?>
        <?php print $help; ?>
        
        <div class="main-content">

        <?php if ($tabs): print '<div id="tabs-wrapper" class="clear-block">'; endif; ?>
          <?php if ($tabs): print '<ul class="tabs primary">'. $tabs .'</ul></div>'; endif; ?>
          <?php if ($tabs2): print '<ul class="tabs secondary">'. $tabs2 .'</ul>'; endif; ?>
        
          <div class="page-content">
            <?php print $content ?>
          </div>

          <?php print $feed_icons ?>

          <?php if($content_blocks): ?>
            <?php print $content_blocks; ?>
          <?php endif; ?>
        </div>

        <?php if($aside || $promos): ?>
          <div class="aside">
            <?php if($promos): ?>
              <?php print $promos; ?>
            <?php endif; ?>

            <?php if($aside): ?>
              <ul>
                <?php print $aside; ?>
              </ul>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      
      </div>
    </div>

    <div class="footer-wrap">
      <div class="footer standard-container">
        <?php if($footer): ?>
          <?php print $footer; ?>
        <?php endif; ?>
      </div>
    </div>
    
    <?php if (isset($secondary_links)) : ?>
      <?php print theme('links', $secondary_links, array('class' => 'links secondary-links')) ?>
    <?php endif; ?>

  <?php print $closure ?>
  </body>
</html>
