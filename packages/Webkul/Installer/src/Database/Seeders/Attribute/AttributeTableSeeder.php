<?php

namespace Webkul\Installer\Database\Seeders\Attribute;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::table('attributes')->delete();

        DB::table('attribute_translations')->delete();

        $now = Carbon::now();

        $defaultLocale = $parameters['default_locale'] ?? config('app.locale');

        DB::table('attributes')->insert([
            [
                'id'                  => 1,
                'code'                => 'sku',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.sku', [], $defaultLocale),
                'type'                => 'text',
                'validation'          => null,
                'position'            => 1,
                'is_required'         => 1,
                'is_unique'           => 1,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 2,
                'code'                => 'name',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.name', [], $defaultLocale),
                'type'                => 'text',
                'validation'          => null,
                'position'            => 3,
                'is_required'         => 1,
                'is_unique'           => 0,
                'value_per_locale'    => 1,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 1,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 3,
                'code'                => 'url_key',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.url-key', [], $defaultLocale),
                'type'                => 'text',
                'validation'          => null,
                'position'            => 4,
                'is_required'         => 1,
                'is_unique'           => 1,
                'value_per_locale'    => 1,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 4,
                'code'                => 'tax_category_id',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.tax-category', [], $defaultLocale),
                'type'                => 'select',
                'validation'          => null,
                'position'            => 5,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 5,
                'code'                => 'new',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.new', [], $defaultLocale),
                'type'                => 'boolean',
                'validation'          => null,
                'position'            => 6,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 6,
                'code'                => 'featured',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.featured', [], $defaultLocale),
                'type'                => 'boolean',
                'validation'          => null,
                'position'            => 7,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 7,
                'code'                => 'visible_individually',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.visible-individually', [], $defaultLocale),
                'type'                => 'boolean',
                'validation'          => null,
                'position'            => 9,
                'is_required'         => 1,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 8,
                'code'                => 'status',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.status', [], $defaultLocale),
                'type'                => 'boolean',
                'validation'          => null,
                'position'            => 10,
                'is_required'         => 1,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 9,
                'code'                => 'short_description',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.short-description', [], $defaultLocale),
                'type'                => 'textarea',
                'validation'          => null,
                'position'            => 11,
                'is_required'         => 1,
                'is_unique'           => 0,
                'value_per_locale'    => 1,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 1,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 10,
                'code'                => 'description',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.description', [], $defaultLocale),
                'type'                => 'textarea',
                'validation'          => null,
                'position'            => 12,
                'is_required'         => 1,
                'is_unique'           => 0,
                'value_per_locale'    => 1,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 1,
                'enable_wysiwyg'      => 1,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 11,
                'code'                => 'price',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.price', [], $defaultLocale),
                'type'                => 'price',
                'validation'          => 'decimal',
                'position'            => 13,
                'is_required'         => 1,
                'is_unique'           => 0,
                'value_per_locale'    => 1,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 1,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 1,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 12,
                'code'                => 'cost',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.cost', [], $defaultLocale),
                'type'                => 'price',
                'validation'          => 'decimal',
                'position'            => 14,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 1,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 13,
                'code'                => 'special_price',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.special-price', [], $defaultLocale),
                'type'                => 'price',
                'validation'          => 'decimal',
                'position'            => 15,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 14,
                'code'                => 'special_price_from',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.special-price-from', [], $defaultLocale),
                'type'                => 'date',
                'validation'          => null,
                'position'            => 16,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 15,
                'code'                => 'special_price_to',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.special-price-to', [], $defaultLocale),
                'type'                => 'date',
                'validation'          => null,
                'position'            => 17,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 16,
                'code'                => 'meta_title',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.meta-title', [], $defaultLocale),
                'type'                => 'textarea',
                'validation'          => null,
                'position'            => 18,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 1,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 17,
                'code'                => 'meta_keywords',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.meta-keywords', [], $defaultLocale),
                'type'                => 'textarea',
                'validation'          => null,
                'position'            => 20,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 1,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 18,
                'code'                => 'meta_description',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.meta-description', [], $defaultLocale),
                'type'                => 'textarea',
                'validation'          => null,
                'position'            => 21,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 1,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 1,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 19,
                'code'                => 'length',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.length', [], $defaultLocale),
                'type'                => 'text',
                'validation'          => 'decimal',
                'position'            => 22,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 1,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 20,
                'code'                => 'width',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.width', [], $defaultLocale),
                'type'                => 'text',
                'validation'          => 'decimal',
                'position'            => 23,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 1,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 21,
                'code'                => 'height',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.height', [], $defaultLocale),
                'type'                => 'text',
                'validation'          => 'decimal',
                'position'            => 24,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 1,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 22,
                'code'                => 'weight',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.weight', [], $defaultLocale),
                'type'                => 'text',
                'validation'          => 'decimal',
                'position'            => 25,
                'is_required'         => 1,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 23,
                'code'                => 'color',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.color', [], $defaultLocale),
                'type'                => 'select',
                'validation'          => null,
                'position'            => 26,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 1,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 1,
                'is_configurable'     => 1,
                'is_user_defined'     => 1,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 24,
                'code'                => 'size',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.size', [], $defaultLocale),
                'type'                => 'select',
                'validation'          => null,
                'position'            => 27,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 1,
                'is_configurable'     => 1,
                'is_user_defined'     => 1,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 25,
                'code'                => 'brand',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.brand', [], $defaultLocale),
                'type'                => 'select',
                'validation'          => null,
                'position'            => 28,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 1,
                'is_configurable'     => 0,
                'is_user_defined'     => 1,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 26,
                'code'                => 'guest_checkout',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.guest-checkout', [], $defaultLocale),
                'type'                => 'boolean',
                'validation'          => null,
                'position'            => 8,
                'is_required'         => 1,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 27,
                'code'                => 'product_number',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.product-number', [], $defaultLocale),
                'type'                => 'text',
                'validation'          => null,
                'position'            => 2,
                'is_required'         => 0,
                'is_unique'           => 1,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ], [
                'id'                  => 28,
                'code'                => 'manage_stock',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.manage-stock', [], $defaultLocale),
                'type'                => 'boolean',
                'validation'          => null,
                'position'            => 1,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 1,
                'default_value'       => 1,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
            [
                'id'                  => 29,
                'code'                => 'material',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.material', [], $defaultLocale),
                'type'                => 'text',
                'validation'          => null,
                'position'            => 6,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
            [
                'id'                  => 30,
                'code'                => 'dimensions',
                'admin_name'          => trans('installer::app.seeders.attribute.attributes.dimensions', [], $defaultLocale),
                'type'                => 'text',
                'validation'          => null,
                'position'            => 6,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 1,
                'default_value'       => null,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 0,
                'is_visible_on_front' => 1,
                'is_comparable'       => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
        ]);

        $locales = $parameters['allowed_locales'] ?? config('app.locales');

        foreach ($locales as $locale) {
            DB::table('attribute_translations')->insert([
                [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.sku', [], $locale),
                    'attribute_id' => 1,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.name', [], $locale),
                    'attribute_id' => 2,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.url-key', [], $locale),
                    'attribute_id' => 3,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.tax-category', [], $locale),
                    'attribute_id' => 4,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.new', [], $locale),
                    'attribute_id' => 5,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.featured', [], $locale),
                    'attribute_id' => 6,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.visible-individually', [], $locale),
                    'attribute_id' => 7,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.status', [], $locale),
                    'attribute_id' => 8,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.short-description', [], $locale),
                    'attribute_id' => 9,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.description', [], $locale),
                    'attribute_id' => 10,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.price', [], $locale),
                    'attribute_id' => 11,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.cost', [], $locale),
                    'attribute_id' => 12,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.special-price', [], $locale),
                    'attribute_id' => 13,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.special-price-from', [], $locale),
                    'attribute_id' => 14,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.special-price-to', [], $locale),
                    'attribute_id' => 15,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.meta-title', [], $locale),
                    'attribute_id' => 16,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.meta-keywords', [], $locale),
                    'attribute_id' => 17,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.meta-description', [], $locale),
                    'attribute_id' => 18,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.length', [], $locale),
                    'attribute_id' => 19,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.width', [], $locale),
                    'attribute_id' => 20,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.height', [], $locale),
                    'attribute_id' => 21,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.weight', [], $locale),
                    'attribute_id' => 22,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.color', [], $locale),
                    'attribute_id' => 23,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.size', [], $locale),
                    'attribute_id' => 24,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.brand', [], $locale),
                    'attribute_id' => 25,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.guest-checkout', [], $locale),
                    'attribute_id' => 26,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.product-number', [], $locale),
                    'attribute_id' => 27,
                ], [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.manage-stock', [], $locale),
                    'attribute_id' => 28,
                ],
                [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.material', [], $locale),
                    'attribute_id' => 29,
                ],
                [
                    'locale'       => $locale,
                    'name'         => trans('installer::app.seeders.attribute.attributes.dimensions', [], $locale),
                    'attribute_id' => 30,
                ],
            ]);
        }
    }
}
