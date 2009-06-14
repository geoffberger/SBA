<?php
// $Id: views_plugin_cache.inc,v 1.1.2.3 2009/06/03 23:40:06 merlinofchaos Exp $

/**
 * The base plugin to handle caching.
 *
 * @ingroup views_cache_plugins
 */
class views_plugin_cache extends views_plugin {
  /**
   * Contains all data that should be written/read from cache.
   */
  var $storage = array();

  /**
   * What table to store data in.
   */
  var $table = 'cache_views_data';

  /**
   * Initialize the plugin.
   *
   * @param $view
   *   The view object.
   * @param $display
   *   The display handler.
   */
  function init(&$view, &$display) {
    $this->view = &$view;
    $this->display = &$display;
    $this->options = array();

    if (is_object($display->handler)) {
      // Note: The below is read only.
      $this->options = $display->handler->get_option('cache');
    }
  }

  /**
   * Retrieve the default options when this is a new access
   * control plugin
   */
  function option_defaults(&$options) { }

  /**
   * Return a string to display as the clickable title for the
   * access control.
   */
  function summary_title() {
    return t('Unknown');
  }

  /**
   * Determine the expiration time of the cache type, or NULL if no expire.
   *
   * Plugins must override this to implement expiration.
   *
   * @param $type
   *   The cache type, either 'query', 'result' or 'output'.
   */
   function cache_expire($type) { }

  /**
   * Save data to the cache.
   *
   * A plugin should override this to provide specialized caching behavior.
   */
  function cache_set($type) {
    switch ($type) {
      case 'query':
        // Not supported currently, but this is certainly where we'd put it.
        break;
      case 'results':
        $data = array(
          'result' => $this->view->result,
          'total_rows' => $this->view->total_rows,
          'pager' => $this->view->pager,
        );
        cache_set($this->get_results_key(), $data, $this->table);
        break;
      case 'output':
        $this->gather_headers();
        $this->storage['output'] = $this->view->display_handler->output;
        cache_set($this->get_output_key(), $this->storage, $this->table);
        break;
    }
  }


  /**
   * Retrieve data from the cache.
   *
   * A plugin should override this to provide specialized caching behavior.
   */
  function cache_get($type) {
    $cutoff = $this->cache_expire($type);
    switch ($type) {
      case 'query':
        // Not supported currently, but this is certainly where we'd put it.
        return FALSE;
      case 'results':
        // Values to set: $view->result, $view->total_rows, $view->execute_time,
        // $view->pager['current_page'].
        if ($cache = cache_get($this->get_results_key(), $this->table)) {
          if (!$cutoff || $cache->created > $cutoff) {
            $this->view->result = $cache->data['result'];
            $this->view->total_rows = $cache->data['total_rows'];
            $this->view->pager = $cache->data['pager'];
            $this->view->execute_time = 0;
            return TRUE;
          }
        }
        return FALSE;
      case 'output':
        if ($cache = cache_get($this->get_output_key(), $this->table)) {
          if (!$cutoff || $cache->created > $cutoff) {
            $this->storage = $cache->data;
            $this->view->display_handler->output = $cache->data['output'];
            $this->restore_headers();
            return TRUE;
          }
        }
        return FALSE;
    }
  }

  /**
   * Clear out cached data for a view.
   *
   * We're just going to nuke anything related to the view, regardless of display,
   * to be sure that we catch everything. Maybe that's a bad idea.
   */
  function cache_flush() {
    cache_clear_all($this->view->name . ':', $this->table, TRUE);
  }

  /**
   * Start caching javascript, css and other out of band info.
   *
   * This takes a snapshot of the current system state so that we don't
   * duplicate it. Later on, when gather_headers() is run, this information
   * will be removed so that we don't hold onto it.
   */
  function cache_start() {
    $this->storage['head'] = drupal_set_html_head();
    $this->storage['css'] = drupal_add_css();

    foreach (array('header', 'footer') as $scope) {
      $this->storage['js'][$scope] = drupal_add_js(NULL, NULL, $scope);
    }
  }

  /**
   * Gather out of band data, compare it to what we started with and store the difference.
   */
  function gather_headers() {
    // Simple replacement for head
    $this->storage['head'] = str_replace($this->storage['head'], '', drupal_set_html_head());

    // Slightly less simple for CSS:
    $css = drupal_add_css();
    $start = $this->storage['css'];
    $this->storage['css'] = array();

    foreach ($css as $media => $medias) {
      foreach ($medias as $type => $types) {
        foreach ($types as $path => $preprocess) {
          if (!isset($start[$media][$type][$path])) {
            $this->storage['css'][] = array($path, $type, $media, $preprocess);
          }
        }
      }
    }

    $js = array();
    // A little less simple for js
    foreach (array('header', 'footer') as $scope) {
      $js[$scope] = drupal_add_js(NULL, NULL, $scope);
    }

    $start = $this->storage['js'];
    $this->storage['js'] = array();

    foreach ($js as $scope => $scopes) {
      foreach ($scopes as $type => $types) {
        foreach ($types as $id => $info) {
          if (!isset($start[$scope][$type][$id])) {
            switch ($type) {
              case 'setting':
                $this->storage['js'][] = array($info, $type, $scope);
                break;

              case 'inline':
                $this->storage['js'][] = array($info['code'], $type, $scope, $info['defer']);
                break;

              default:
                $this->storage['js'][] = array($id, $type, $scope, $info['defer'], $info['cache']);
            }
          }
        }
      }
    }
  }

  /**
   * Restore out of band data saved to cache. Copied from Panels.
   */
  function restore_headers() {
    if (!empty($this->storage['head'])) {
      drupal_set_html_head($this->storage['head']);
    }
    if (!empty($this->storage['css'])) {
      foreach ($this->storage['css'] as $args) {
        call_user_func_array('drupal_add_css', $args);
      }
    }
    if (!empty($this->storage['js'])) {
      foreach ($this->storage['js'] as $args) {
        call_user_func_array('drupal_add_js', $args);
      }
    }
  }

  function get_results_key() {
    global $user;

    if (!isset($this->_results_key)) {
      $key_data = array(
        'build_info' => $this->view->build_info,
        'roles' => array_keys($user->roles),
      );
      foreach (array('exposed_info', 'page', 'sort', 'order') as $key) {
        if (isset($_GET[$key])) {
          $key_data[$key] = $_GET[$key];
        }
      }

      $this->_results_key = $this->view->name . ':' . $this->display->id . ':results:' . md5(serialize($key_data));
    }

    return $this->_results_key;
  }

  function get_output_key() {
    global $user;
    if (!isset($this->_output_key)) {
      $key_data = array(
        'result' => $this->view->result,
        'roles' => array_keys($user->roles),
      );

      $this->_output_key = $this->view->name . ':' . $this->display->id . ':output:' . md5(serialize($key_data));
    }

    return $this->_output_key;
  }

}