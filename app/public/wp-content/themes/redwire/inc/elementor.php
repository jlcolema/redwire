<?php

namespace Redwire;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class ProductACFWidget extends Widget_Base {
    protected function get_field_name() {}

    protected function get_heading() {}

    public function get_icon() {
        return 'eicon-toggle';
    }

    public function get_categories() {
        return [
            'woocommerce-elements-single',
        ];
    }

    protected function render() {
        $field_name = $this->get_field_name();
        if (!empty($field_name) && have_rows($field_name)):
            ?>
                <h3 class="redwire-product-table-heading <?= $field_name ?>"><?= $this::get_heading() ?></h3>
                <table class="redwire-product-table redwire-product-<?= $field_name ?>">
                    <tbody>
                        <?php while (have_rows($field_name)): the_row(); ?>
                            <tr>
                                <td class="redwire-product-table__title"><?= the_sub_field('title'); ?></td>
                                <td class="redwire-product-table__description"><?= the_sub_field('description'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php
        endif;
    }
}

/**
 * ProductParametersWidget
 *
 * Redwire Product Parameters
 *
 * @since 1.0.0
 */
class ProductParametersWidget extends ProductACFWidget {
	public function get_name() {
        return 'product_parameters';
    }

	public function get_title() {
        return 'Product Parameters';
    }

	public function get_keywords() {
        return [ 'redwire', 'product', 'parameters' ];
    }

    protected function get_field_name() {
        return 'parameters';
    }

    protected function get_heading() {
        return 'Parameters';
    }
}

/**
 * ProductConfigurationWidget
 *
 * Redwire Product Configuration
 *
 * @since 1.0.0
 */
class ProductConfigurationWidget extends ProductACFWidget {
	public function get_name() {
        return 'product_configuration';
    }

	public function get_title() {
    return 'Product Configuration';
    }

    public function get_icon() {
        return 'eicon-settings';
    }

    public function get_categories() {
        return [
            'woocommerce-elements-single',
        ];
    }

	public function get_keywords() {
        return [ 'redwire', 'product', 'configuration' ];
    }

    protected function get_field_name() {
        return 'configurations';
    }

    protected function get_heading() {
        return 'Configuration';
    }
}

/**
 * ProductApplicationsWidget
 *
 * Redwire Product Applications
 *
 * @since 1.0.0
 */
class ProductApplicationsWidget extends ProductACFWidget {
	public function get_name() {
        return 'product_applications';
    }

	public function get_title() {
        return 'Product Applications';
    }

	public function get_keywords() {
        return [ 'redwire', 'product', 'applications' ];
    }

    protected function get_field_name() {
        return 'applicationDescription';
    }

    protected function get_heading() {
        return 'Applications';
    }

    protected function render() {
        $field_name = $this->get_field_name();
        if (!empty($field_name) && !empty($field = trim(get_field($field_name) ?: ''))):
            ?>
                <h3 class="redwire-product-table-heading <?= $field_name ?>"><?= $this::get_heading() ?></h3>
                <table class="redwire-product-table redwire-product-<?= $field_name ?>">
                    <tbody>
                        <tr>
                            <td class="redwire-product-table__description"><?= $field ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php
        endif;
    }
}
