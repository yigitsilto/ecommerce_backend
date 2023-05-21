<?php

namespace FleetCart\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mexitek\PHPColors\Color;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Media\Entities\File;
use Modules\Menu\Entities\Menu;
use Modules\Menu\MegaMenu\MegaMenu;
use Modules\Option\Entities\Option;
use Modules\Option\Entities\OptionTranslation;
use Modules\Option\Entities\OptionValue;
use Modules\Page\Entities\Page;
use Modules\Product\Entities\EntityFiles;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductCategories;
use Modules\Product\Entities\ProductOption;
use Modules\Tag\Entities\Tag;
use Themes\Storefront\Banner;

class SettingsController extends Controller
{

    private $productChanged = true;

    public function getPageById($slug)
    {
        return response()->json([
                                    'data' => Page::query()
                                                  ->where('slug', $slug)
                                                  ->first()
                                ]);
    }

    public function index()
    {
        // set redis
        $settings = json_decode(Redis::get('settings'));

        if (!$settings) {
            $settings = [
                'banners' => Banner::getSliderBanners(),
                'settings' => [
                    'primaryColor' => $this->getThemeColor(),
                    'secondaryColor' => $this->getSecondaryColor(),
                    'favicon' => $this->getFavicon(),
                    'logo' => $this->getHeaderLogo(),
                    'copyrightText' => $this->getCopyrightText(),
                    'welcomeText' => $this->getWelcomeText(),
                    'popular_products_text' => setting('popular_products_text'),
                    'popular_categories_text' => setting('popular_categories_text'),
                    'related_products_text' => setting('related_products_text'),
                    'blog_text' => setting('blog_text'),
                    'address' => setting('storefront_address'),
                    'facebook' => setting('storefront_facebook_link'),
                    'twitter' => setting('storefront_twitter_link'),
                    'instagram' => setting('storefront_instagram_link'),
                    'youtube' => setting('storefront_youtube_link'),
                    'primaryMenu' => $this->getPrimaryMenu(),
                    'categoryMenu' => $this->getCategoryMenu(),
                    'footer1' => $this->getFooterMenuOne(),
                    'footer2' => $this->getFooterMenuTwo(),
                    'pages' => Page::query()
                                   ->get(),
                ],
            ];
            Redis::set('settings', json_encode($settings));

        }

        return $settings;


    }

    private function getThemeColor()
    {

        try {
            return storefront_theme_color();
            return new Color(storefront_theme_color());
        } catch (\Exception $e) {
            return new Color('#0068e1');
        }
    }

    private function getSecondaryColor()
    {

        try {
            return mail_theme_color();
            return new Color(mail_theme_color());
        } catch (\Exception $e) {
            return new Color('#0068e1');
        }
    }

    private function getFavicon()
    {
        return $this->getMedia(setting('storefront_favicon'))->path;
    }

    private function getMedia($fileId)
    {
        return Cache::rememberForever(md5("files.{$fileId}"), function () use ($fileId) {
            return File::findOrNew($fileId);
        });
    }

    private function getHeaderLogo()
    {
        return $this->getMedia(setting('storefront_header_logo'))->path;
    }

    private function getCopyrightText()
    {
        return strtr(setting('storefront_copyright_text'), [
            '{{ store_url }}' => route('home'),
            '{{ store_name }}' => setting('store_name'),
            '{{ year }}' => date('Y'),
        ]);
    }

    private function getWelcomeText()
    {
        return strtr(setting('storefront_welcome_text'), [
            '{{ store_url }}' => route('home'),
            '{{ store_name }}' => setting('store_name'),
            '{{ year }}' => date('Y'),
        ]);
    }

    private function getPrimaryMenu()
    {
        $menu = new MegaMenu(setting('storefront_primary_menu'));
        return $menu->getMenus();
    }

    private function getCategoryMenu()
    {
        $menu = new MegaMenu(setting('storefront_category_menu'));
        return $menu->getMenus();
    }

    private function getFooterMenuOne()
    {
        $menu = new MegaMenu(setting('storefront_footer_menu_one'));
        return $menu->getMenus();

    }

    private function getFooterMenuTwo()
    {
        $menu = new MegaMenu(setting('storefront_footer_menu_two'));
        return $menu->getMenus();
    }

    public function footerTagsCallback($tagIds)
    {
        return function () use ($tagIds) {
            return Tag::whereIn('id', $tagIds)
                      ->when(!empty($tagIds), function ($query) use ($tagIds) {
                          $tagIdsString = collect($tagIds)
                              ->filter()
                              ->implode(',');

                          $query->orderByRaw("FIELD(id, {$tagIdsString})");
                      })
                      ->get();
        };
    }

    public function importKore()
    {

        $client = new Client();

        $response = $client->request('GET',
                                     'http://cdn1.xmlbankasi.com/p1/palkdisticaret/image/data/xml/beautyprenses.xml');

        $xml = simplexml_load_string($response->getBody());

        foreach ($xml as $item) {
            $productMainCat = $item->mainCategory->__toString();
            $productCat = $item->category->__toString();
            $productSubCat = $item->subCategory->__toString(); // it can be null
            $price = $item->Price->__toString();
            $productName = $item->Name->__toString();
            $productStok = $item->Stock->__toString();
            $brand = $item->Brand->__toString();
            $sku = $item->Product_code->__toString();
            $description = $item->Description->__toString();
            $imageUrl = $item->Image1->__toString();
            $variants = $item->variants;


            try {
                // DB::beginTransaction();
                $brandId = $this->createBrand($brand);
                $categoryValues = $this->createCategories($productMainCat, $productCat);


                $productValues = [
                    'name' => $productName,
                    'categories' => [
                        $categoryValues
                    ],
                    'qty' => $productStok,
                    'description' => $description,
                    'brand_id' => $brandId,
                    'virtual' => false,
                    'is_active' => true,
                    'price' => $price,
                    'manage_stock' => true,
                    'in_stock' => true,
                    'sku' => $sku
                ];


                $product = $this->createProduct($productValues, $imageUrl, $variants);
                $this->productChanged = true;
                // DB::commit();


            } catch (\Exception $e) {
                dd($e);
                //  DB::rollBack();
            }


        }


    }

    private function createBrand($name)
    {
        $brand = Brand::query()
                      ->updateOrCreate([
                                           'slug' => $this->toSlug($name)
                                       ], [
                                           'name' => $name,
                                           'is_active' => true,
                                       ]);

        return $brand->id;


    }

    private function toSlug($string)
    {

        return Str::slug($string);
    }

    private function createCategories($mainCategory, $productCategory)
    {


        $categoryMain = Category::query()
                                ->firstOrCreate([
                                                    "slug" => $this->toSlug($mainCategory),
                                                ], [
                                                    'name' => $mainCategory,
                                                    "is_searchable" => "1",
                                                    "is_active" => "1",
                                                    "is_popular" => "0",
                                                    "slug" => $this->toSlug($mainCategory),
                                                    'parent_id' => null,
                                                ]);

        $subCat = Category::query()
                          ->firstOrCreate([
                                              "slug" => $this->toSlug($productCategory),
                                          ], [
                                              'name' => $productCategory,
                                              "is_searchable" => "1",
                                              "is_active" => "1",
                                              "is_popular" => "0",
                                              "slug" => $this->toSlug($productCategory),
                                              'parent_id' => $categoryMain->id,
                                          ]);

        return [
            $subCat->id,
            $categoryMain->id
        ];


    }

    private function createProduct(array $values, $imageUrl, $variants)
    {
        $fileId = 0;
        if (!empty($imageUrl)) {
            $fileId = $this->saveFile($imageUrl, $this->toSlug($values['name']));
            $values = array_merge([
                                      'files' => [
                                          'base_image' => $fileId,
                                      ]
                                  ], $values);
        }


        $product = Product::query()
                          ->updateOrCreate([
                                               'slug' => $this->toSlug($values['name'])
                                           ], $values);


        foreach ($values['categories'][0] as $category) {
            ProductCategories::query()
                             ->updateOrCreate([
                                                  'product_id' => $product->id,
                                                  'category_id' => $category
                                              ], [
                                                  'product_id' => $product->id,
                                                  'category_id' => $category
                                              ]);
        }

        if ($fileId != 0) {
            EntityFiles::query()
                       ->updateOrCreate([
                                            'entity_id' => $product->id,
                                            'entity_type' => 'Modules\Product\Entities\Product',
                                            'file_id' => $fileId,
                                            'zone' => 'base_image'
                                        ]);
        }

        $this->createVariants($variants, $product->id);


        return $product;

    }

    private function saveFile($url, $name)
    {

        $file = file_get_contents($url);
        $imageInfo = getimagesizefromstring($file);

// Get the MIME type of the image
        $mimeType = $imageInfo['mime'];

// Get the file extension of the image
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        $name = $name . "." . $extension;
        Storage::put("media/" . $name, $file);
        $path = "media/" . $name;

        $file = File::query()
                    ->updateOrCreate(
                        [
                            'filename' => $name . "." . $extension,
                            'path' => $path,
                        ],
                        [
                            'user_id' => 3,
                            'disk' => config('filesystems.default'),
                            'filename' => $name . "." . $extension,
                            'path' => $path,
                            'extension' => $extension ?? '',
                            'mime' => $mimeType,
                            'size' => 2000,
                        ]);


        return $file->id;

    }

    private function createVariants($variants, $productId)
    {

        if (!is_null($variants->variant)) {
            foreach ($variants->variant as $variant)
            {  // variants  variant spec name renk value kırmızı variant spec name renk value lacivert

                $specName = (string)$variant->spec['name']; // Renk
                $specValue = (string)$variant->spec; // Kırmızı
                $specPrice = (string)$variant->price;


                $optionId = 0;

                if ($this->productChanged) {
                    $option = Option::query()
                                    ->create(
                                        [
                                            "id" => null,
                                            "name" => $specName,
                                            "type" => "radio",
                                            "is_required" => true
                                        ]);

                    $optionId = $option->id;

                    $this->productChanged = false;

                } else {

                    $exists = OptionTranslation::query()
                                               ->where('name', $specName)
                                               ->orderBy('id', 'desc')
                                               ->first();
                    $optionId = $exists->option_id;

                }


                if ($optionId != 0) {
                    $value = OptionValue::query()
                                        ->create(
                                            [
                                                'label' => $specValue,
                                                'price' => $specPrice,
                                                'price_type' => 'fixed',
                                                'option_id' => $optionId,
                                                'position' => 0
                                            ]);

                    ProductOption::query()
                                 ->firstOrCreate(
                                     [
                                         'product_id' => $productId,
                                         'option_id' => $optionId
                                     ],
                                     [
                                         'product_id' => $productId,
                                         'option_id' => $optionId
                                     ]);

                }


            }
        }
    }

    private function getFooterMenu($menuId)
    {
        return Cache::tags([
                               'menu_items',
                               'categories',
                               'pages',
                               'settings'
                           ])
                    ->rememberForever(md5("storefront_footer_menu.{$menuId}:" . locale()), function () use ($menuId) {
                        return Menu::for($menuId);
                    });
    }


}
