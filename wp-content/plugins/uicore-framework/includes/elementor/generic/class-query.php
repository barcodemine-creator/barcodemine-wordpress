<?php
namespace UiCore\Elementor\Generic;

use Elementor\Control_Select2;
defined('ABSPATH') || exit();

class Query extends Control_Select2
{
    const CONTROL_ID = 'query';

    public function get_type()
    {
        return self::CONTROL_ID;
    }

    public static function get_query_args($control_id, $settings, $current_id = null)
    {

        $defaults = [
            $control_id . '_post_type' => 'post',
            $control_id . '_posts_ids' => [],
            'orderby' => 'date',
            'order' => 'desc',
            'offset' => 0,
        ];

        $settings = wp_parse_args($settings, $defaults);

        $post_type = $settings[$control_id . '_post_type'];
        if($post_type == 'current'){

            $query_type = get_post_meta( $current_id, 'tb_rule_include', true );
            $query_type = isset($query_type[0]['rule']['value']) ? $query_type[0]['rule']['value'] : '';
            switch ( $query_type ) {
                case 'special-blog':
                    $post_type = 'post';
                    break;
                default:
                if( ($type = substr( $query_type, 0, 11 )) === "cp-archive-" ){
                    $post_type = str_replace($type,'',$query_type);
                    break;
                }
                $post_type = 'post';
                break;
            }
        }

        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } elseif (get_query_var('page')) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }

        $query_args = [
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'ignore_sticky_posts' => 0,
            'post_status' => 'publish', // Hide drafts/private posts for admins
            'paged' => $paged,
            'posts_per_page' => $settings['item_limit']['size']
        ];

        $query_args['post_type'] = $post_type;
        $query_args['tax_query'] = [];

        $taxonomies = get_object_taxonomies($post_type, 'objects');

        foreach ($taxonomies as $object) {
            $setting_key = $control_id . '_' . $object->name . '_ids';

            if (!empty($settings[$setting_key])) {
                $query_args['tax_query'][] = [
                    'taxonomy' => $object->name,
                    'field' => 'term_id',
                    'terms' => $settings[$setting_key],
                ];
            }
        }
        return $query_args;
    }
}

\Elementor\Plugin::$instance->controls_manager->register_control('query', new Query());
