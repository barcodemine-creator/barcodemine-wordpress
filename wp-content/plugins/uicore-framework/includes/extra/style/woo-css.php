<?php
defined('ABSPATH') || exit();
//INCLUDED IN CLASS CSS

$css .= '
@media (max-width: ' . $br_points['lg'] .'px) {
    .woocommerce-page:not(.elementor-page) main.uicore{
        padding:' . $json_settings['woocommerce_padding']['t'] . 'px 0px;
    }
}


@media (max-width: ' .  $br_points['md'] . 'px) {
    .woocommerce-page:not(.elementor-page) main.uicore{
        padding:' .  $json_settings['woocommerce_padding']['m'] . 'px 0px;
    }
}


@media (min-width: ' . $br_points['lg'] .  'px) {
    .woocommerce-page:not(.elementor-page) main.uicore{
        padding:' .  $json_settings['woocommerce_padding']['d'] . 'px 0px;
    }
}
';
//animations
$css .= $this->grid_animation('shop');