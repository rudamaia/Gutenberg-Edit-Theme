<?php

namespace Drupal\gutenberg_edit_theme\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\user\Theme\AdminNegotiator;
use Drupal\node\Entity\Node;

class CustomAdminNegotiator extends AdminNegotiator
{

  public function determineActiveTheme(RouteMatchInterface $route_match)
  {

    $rname = $route_match->getRouteName();
    $parameter = false;
    // Node add / Node edit.
    switch ($rname) {
      case 'node.add':
        $parameter = $route_match->getRawParameter('node_type');
        break;
      case 'entity.node.edit_form':
      case 'quick_node_clone.node.quick_clone':
        $parameter = $route_match->getParameter('node');
    }
    // Use bootstrap theme for node types with gutenberg enabled.
    if ($parameter) {
      // Gettind node type name is different for 'add' and 'edit'.
      $node_type = $parameter instanceof Node ? $parameter->type->entity->id() : $parameter;
      // Get gutenberg config to compare.
      $gutenberg_config = \Drupal::configFactory()->get('gutenberg.settings')->getRawData();
      foreach ($gutenberg_config as $key => $value) {
        $str = '_enable_full';
        if (str_contains($key, $str)) {
          if ($gutenberg_node = explode($str, $key)[0]) {
            if ($node_type == $gutenberg_node) {
              $theme_name = \Drupal::config('system.theme')->get('default');
              return $theme_name;
            }
          }
        }
      }
    }
  }

}
