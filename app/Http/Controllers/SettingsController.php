<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Filter;
use FleetCart\FilterValue;
use FleetCart\ProductFilterValue;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
use Modules\Product\Entities\ProductPrice;
use Modules\Tag\Entities\Tag;
use Themes\Storefront\Banner;

class SettingsController extends Controller
{

    private $productChanged = true;

    public function getPageById($slug)
    {
        return response()->json([
                                    'data' => Page::query()
                                                  ->with('meta')
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

        $url = "https://backend.bibuti.com.tr/products.xml";

        $response = $client->request('GET',
                                     $url);

        $xml = simplexml_load_string($response->getBody());

        $index = 0;
        foreach ($xml as $item) {
            DB::beginTransaction();
            $productMainCat = $item->mainCategory->__toString();
            $productCat = $item->category->__toString();
            $productSubCat = $item->subCategory->__toString(); // it can be null
            $price = $item->price1->__toString();
            $price2 = $item->price2->__toString();
            $price3 = $item->price3->__toString();
            $price4 = $item->price4->__toString();
            $price5 = $item->price5->__toString();
            $rebate = $item->rebate->__toString();
            $rebateType = $item->rebateType->__toString();

            if ($rebateType == '1') {
                $specialPrice = $price - ($price * $rebate / 100);
            } else {
                $specialPrice = $rebate;
            }


            $productName = $item->label->__toString();

            $productStok = $item->stockAmount->__toString();
            $brand = $item->brand->__toString();
            $sku = $item->barcode->__toString();
            $description = $item->details->__toString();
            $status = $item->status->__toString();
            $imageUrls = [
                $item->picture1Path->__toString(),
                $item->picture2Path->__toString(),
                $item->picture3Path->__toString(),
                $item->picture4Path->__toString(),
            ];
            $variants = $item->variants;


            try {
                if (!empty($brand)) {
                    $brandId = $this->createBrand($brand);
                } else {
                    $brandId = null;
                }

                $categoryValues = $this->createCategories($productMainCat, $productCat, $productSubCat);


                $productValues = [
                    'name' => $productName,
                    'categories' => [
                        $categoryValues
                    ],
                    'qty' => $productStok,
                    'description' => $description,
                    'brand_id' => $brandId,
                    'virtual' => false,
                    'price' => $price,
                    'manage_stock' => true,
                    'in_stock' => true,
                    'sku' => $sku,
                    'is_popular' => 1,
                    'is_active' => $status,
                    'special_price' => $specialPrice == $price ? null : $specialPrice,
                ];


                $product = $this->createProduct($productValues, $imageUrls, $variants, $brand);
                $this->savePrices($product, $price, $price2, $price3, $price4, $price5);
                $this->productChanged = true;
                DB::commit();
                $index++;
//                if ($index > 10){
//                    dd($item);
//                    break;
//                }


            } catch (\Exception $e) {

                DB::rollBack();
                dd($e);
            }


        }


    }

    private function createBrand($name)
    {
        if (empty($name)) {
            return null;
        }
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

    private function createCategories($mainCategory, $productCategory, $productSubCategory)
    {


        if (!empty($mainCategory)) {
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
        }

        if (!empty($productCategory)) {


            $subCat = Category::query()
                              ->firstOrCreate([
                                                  "slug" => $this->toSlug($productCategory),
                                              ], [
                                                  'name' => $productCategory,
                                                  "is_searchable" => "1",
                                                  "is_active" => "1",
                                                  "is_popular" => "1",
                                                  "slug" => $this->toSlug($productCategory),
                                                  'parent_id' => isset($categoryMain->id) ? $categoryMain->id : null,
                                              ]);
        }

        if (!empty($productSubCategory)) {
            $subCate = Category::query()
                               ->firstOrCreate([
                                                   "slug" => $this->toSlug($productSubCategory),
                                               ], [
                                                   'name' => $productSubCategory,
                                                   "is_searchable" => "1",
                                                   "is_active" => "1",
                                                   "is_popular" => "1",
                                                   "slug" => $this->toSlug($productSubCategory),
                                                   'parent_id' => isset($subCat->id) ? $subCat->id : null,
                                               ]);
        }


        return [
            isset($subCat->id) ? $subCat->id : null,
            isset($categoryMain->id) ? $categoryMain->id : null,
            isset($subCate->id) ? $subCate->id : null,
        ];


    }

    private function createProduct(array $values, $imageUrls, $variants, $brand)
    {


        $product = Product::query()
                          ->updateOrCreate([
                                               'slug' => $this->toSlug($values['name'])
                                           ], $values);


        // brands filter

        if (!empty($brand)) {
            $filter = Filter::query()
                            ->firstOrCreate([
                                                'slug' => $this->toSlug('Markalar')
                                            ],
                                            [
                                                'slug' => $this->toSlug('Markalar'),
                                                'title' => 'Markalar',
                                                'status' => 1,
                                            ]
                            );


            $filterValue = FilterValue::query()
                                      ->firstOrCreate([
                                                          'slug' => $this->toSlug($values['brand_id'])
                                                      ],
                                                      [
                                                          'slug' => $this->toSlug($values['brand_id']),
                                                          'title' => $brand,
                                                          'filter_id' => $filter->id,
                                                          'status' => 1,
                                                      ]
                                      );

            ProductFilterValue::query()
                              ->firstOrCreate([
                                                  'product_id' => $product->id,
                                                  'filter_value_id' => $filterValue->id,
                                                  'filter_id' => $filter->id,
                                              ],

                                              [
                                                  'product_id' => $product->id,
                                                  'filter_value_id' => $filterValue->id,
                                                  'filter_id' => $filter->id,
                                              ]);
        }


        $filter = Filter::query()
                        ->firstOrCreate([
                                            'slug' => $this->toSlug('Kategoriler')
                                        ],
                                        [
                                            'slug' => $this->toSlug('Kategoriler'),
                                            'title' => 'Kategoriler',
                                            'status' => 1,
                                        ]
                        );

        $br = Category::query()
                      ->where('id',
                              $values['brand_id'])
                      ->first();

        if ($br) {
            $b = $br->name;
            FilterValue::query()
                       ->updateOrCreate([
                                            'filter_id' => $filter->id,
                                            'slug' => $this->toSlug($b),
                                        ],
                                        [
                                            'filter_id' => $filter->id,
                                            'title' => $b,
                                            'slug' => $this->toSlug($b),
                                        ]);

        }


//        Filter::query()
//                         ->firstOrCreate([
//                                             'slug' => $this->toSlug('Markalar')
//                                         ],
//                                         [
//                                             'slug' => $this->toSlug('Markalar'),
//                                             'name' => 'Markalar',
//                                             'status' => 1,
//                                         ]
//                         );

        if (isset($values['categories'][0])) {
            foreach ($values['categories'][0] as $category) {

                if (!empty($category)) {
                    ProductCategories::query()
                                     ->updateOrCreate([
                                                          'product_id' => $product->id,
                                                          'category_id' => $category
                                                      ], [
                                                          'product_id' => $product->id,
                                                          'category_id' => $category
                                                      ]);
                }

                $cr = Category::query()
                              ->where('id',
                                      $category)
                              ->first();

                if ($cr) {
                    $c = $cr->name;
                    $filterValue = FilterValue::query()
                                              ->updateOrCreate([
                                                                   'filter_id' => $filter->id,
                                                                   'slug' => $this->toSlug($c),
                                                               ],
                                                               [
                                                                   'filter_id' => $filter->id,
                                                                   'title' => $c,
                                                                   'slug' => $this->toSlug($c),
                                                               ]);

                    ProductFilterValue::query()
                                      ->firstOrCreate([
                                                          'product_id' => $product->id,
                                                          'filter_value_id' => $filterValue->id,
                                                          'filter_id' => $filter->id,
                                                      ],

                                                      [
                                                          'product_id' => $product->id,
                                                          'filter_value_id' => $filterValue->id,
                                                          'filter_id' => $filter->id,
                                                      ]);
                }


            }
        }

        $isFileBase = true;


        foreach ($imageUrls as $imageUrl) {
            $fileId = 0;
            if (!empty($imageUrl)) {
                $imageUrl = strstr($imageUrl, '?revision', true);

                $fileId = $this->saveFile($imageUrl, $this->toSlug($values['name']));

                $values = array_merge([
                                          'files' => [
                                              'base_image' => $fileId,
                                          ]
                                      ], $values);
            }

            if ($fileId != 0) {

                EntityFiles::query()
                           ->updateOrCreate([
                                                'entity_id' => $product->id,
                                                'entity_type' => 'Modules\Product\Entities\Product',
                                                'file_id' => $fileId,
                                                'zone' => $isFileBase == true ? 'base_image' : 'additional_images'
                                            ]);

            }
            $isFileBase = false;
        }


        $this->createVariants($variants, $product->id, $values);


        return $product;

    }

    private function saveFile($url, $name)
    {

        $file = file_get_contents($url);
        $imageInfo = getimagesizefromstring($file);
        $name = basename($url);
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

    private function createVariants($variants, $productId, $values)
    {

        if (!is_null($variants->variant)) {
            foreach ($variants->variant as $variant)
            {  // variants  variant spec name renk value kırmızı variant spec name renk value lacivert

                $price = 0;
                if (($values['price'] - (string)$variant->vPrice1) > 0) {
                    $price = $values['price'] - (string)$variant->vPrice1;
                }

                $stock = (string)$variant->vStockAmount;


                $optionId = 0;

                foreach ($variant->options as $item) {

                    $specName = $item->option->variantName->__toString(); // Renk
                    $specValue = $item->option->variantValue->__toString(); // Kırmızı

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
                }


                if ($optionId != 0) {
                    $value = OptionValue::query()
                                        ->create(
                                            [
                                                'label' => $specValue,
                                                'price' => $price,
                                                'price_type' => 'fixed',
                                                'option_id' => $optionId,
                                                'position' => 0,
                                                'stock' => $stock,
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

    private function savePrices($product, $price, $price2, $price3, $price4, $price5)
    {
        ProductPrice::query()
                    ->updateOrCreate([
                                         'product_id' => $product->id,
                                         'company_price_id' => 1
                                     ],
                                     [
                                         'product_id' => $product->id,
                                         'company_price_id' => 1,
                                         'price' => $price,
                                     ]
                    );

        ProductPrice::query()
                    ->updateOrCreate([
                                         'product_id' => $product->id,
                                         'company_price_id' => 2
                                     ],
                                     [
                                         'product_id' => $product->id,
                                         'company_price_id' => 2,
                                         'price' => $price2,
                                     ]
                    );

        ProductPrice::query()
                    ->updateOrCreate([
                                         'product_id' => $product->id,
                                         'company_price_id' => 3
                                     ],
                                     [
                                         'product_id' => $product->id,
                                         'company_price_id' => 3,
                                         'price' => $price3,
                                     ]
                    );

        ProductPrice::query()
                    ->updateOrCreate([
                                         'product_id' => $product->id,
                                         'company_price_id' => 4
                                     ],
                                     [
                                         'product_id' => $product->id,
                                         'company_price_id' => 4,
                                         'price' => $price4,
                                     ]
                    );

        ProductPrice::query()
                    ->updateOrCreate([
                                         'product_id' => $product->id,
                                         'company_price_id' => 5
                                     ],
                                     [
                                         'product_id' => $product->id,
                                         'company_price_id' => 5,
                                         'price' => $price5,
                                     ]
                    );
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
