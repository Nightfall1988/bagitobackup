<?php

namespace App\Services;

use Hitexis\Product\Models\Product;
use GuzzleHttp\Client as GuzzleClient;
use Hitexis\Product\Repositories\HitexisProductRepository;
use Hitexis\Attribute\Repositories\AttributeRepository;
use Hitexis\Attribute\Repositories\AttributeOptionRepository;
use Hitexis\Product\Repositories\SupplierRepository;
use Hitexis\Product\Repositories\ProductImageRepository;
use App\Services\CategoryImportService;
use App\Services\CategoryMapper;
use Hitexis\Markup\Repositories\MarkupRepository;
use Symfony\Component\Console\Helper\ProgressBar;
use Hitexis\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Core\Repositories\LocaleRepository;

class StrickerApiService {

    protected $url;

    protected $pricesUrl;

    protected $productRepository;

    public function __construct(
        HitexisProductRepository $productRepository,
        AttributeRepository $attributeRepository,
        AttributeOptionRepository $attributeOptionRepository,
        SupplierRepository $supplierRepository,
        ProductAttributeValueRepository $productAttributeValueRepository,
        ProductImageRepository $productImageRepository,
        CategoryImportService $categoryImportService,
        MarkupRepository $markupRepository,
        CategoryMapper $categoryMapper,
        protected LocaleRepository $localeRepository
    ) {
        $this->productRepository = $productRepository;
        $this->attributeOptionRepository = $attributeOptionRepository;
        $this->attributeRepository = $attributeRepository;
        $this->supplierRepository = $supplierRepository;
        $this->productImageRepository = $productImageRepository;
        $this->categoryImportService = $categoryImportService;
        $this->productAttributeValueRepository = $productAttributeValueRepository;
        $this->authUrl = env('STRICKER_AUTH_URL') . env('STRICKER_AUTH_TOKEN');
        $this->url = env('STRICKER_PRODUCTS_URL');
        $this->optionalsUrl = env('STRICKER_OPTIONALS_URL');
        $this->identifier = env('STRICKER_IDENTIFIER');
        $this->categoryMapper = $categoryMapper;
        $this->markupRepository = $markupRepository;
        $this->globalMarkup = null;
        
        // TEST DATA
        // $this->url = 'https://run.mocky.io/v3/a3605c0d-e9ad-4083-94a8-aec26addb021';
        // $this->optionalsUrl = 'https://run.mocky.io/v3/16bfb000-c220-4613-8c40-1acec5b17b84';
    }
    
    public function getData()
    {
        ini_set('memory_limit', '512M');
        $headers = [
            'Content-Type' => 'application/json',
        ];
    
        $this->httpClient = new GuzzleClient([
            'headers' => $headers
        ]);

        $request = $this->httpClient->get($this->authUrl);
        $authToken = json_decode($request->getBody()->getContents())->Token;

        $this->url = $this->url . $authToken . '&lang=en';
        $this->optionalsUrl = $this->optionalsUrl . $authToken . '&lang=en';

        // GET PRODUCTS
        $productsData = $this->httpClient->get($this->url);
        $productsData = json_decode($productsData->getBody()->getContents(), true);

        // GET OPTIONALS
        $optionalsData = $this->httpClient->get($this->optionalsUrl);
        $optionalsData = json_decode($optionalsData->getBody()->getContents(), true);

        // // TESTING DATA - RESPONSE FROM JSON FILED
        // $jsonP = file_get_contents('storage\app\private\productstest.json');
        // $productsData = json_decode($jsonP, true);
        // $jsonO = file_get_contents('storage\app\private\optionalstest.json');
        // $optionalsData = json_decode($jsonO, true);

        // MARKUP
        $this->globalMarkup = $this->markupRepository->where('markup_type', 'global')->first();

        $products = $this->getProductsWithOptionals($productsData, $optionalsData);
        $this->updateProducts($products);
    }

    public function getProductsWithOptionals($productsData, $optionalsData)
    {
        $products = [];

        foreach ($productsData['Products'] as $product) {
            $products[$product['ProdReference']]['product'] = $product;
        }

        $optionals = [];
        foreach ($optionalsData['OptionalsComplete'] as $optionals) {
            if (array_key_exists($optionals['ProdReference'], $products)) {
                $products[$optionals['ProdReference']]['optionals'][] = $optionals;
            }
        }

        return $products;
    }

    public function updateProducts($products)
    {   
        $tracker = new ProgressBar($this->output, count($products));
        $tracker->start();
        
        $productReferences = [];
        $groupedOptionals = [];
        $images = [];
        foreach ($products as $prodReference => $value) {
            if (isset($value['optionals']) && sizeof($value['optionals']) > 1) {
                $variantList = $value['optionals'];
                $attributes = $this->getConfigurableSuperAttributes($value['optionals'], $value['product']);
                $this->createConfigurable($value, $attributes);
                $tracker->advance();
            } else {
                $this->createSimple($value);
                $tracker->advance();
            }
        }

        $tracker->finish();
        $this->output->writeln("\nStricker product import finished");
    }

    public function createConfigurable($productData, $attributes) {

        $categories = [];
        $colorIds = [];
        $sizeIds = [];
        $mainProduct = $productData['product']; 
        $productVariants = [];
        $tempAttributes = [];
        $locales = $this->localeRepository->get();

        $productObj = $this->productRepository->upserts([
            'channel' => 'default',
            'attribute_family_id' => '1',
            'sku' => (string)$mainProduct['ProdReference'],
            "type" => 'configurable',
            'super_attributes' => $attributes
        ]);

        foreach ($productData['optionals'] as $optional) {
            $productVariant = $this->productRepository->upserts([
                "channel" => "default",
                'attribute_family_id' => '1',
                'sku' => $optional['Sku'],
                "type" => 'simple',
                'parent_id' => $productObj->id
            ]);

            $productVariants[$productVariant->id] = $productVariant;
        } 

        // CREATE VARIANTS
        $variants = $this->getProductVariant($productData['optionals'], $productObj, $productVariants);

        $mainProductData = $productData['product'];
        $mainProductOptionals = $productData['optionals'][0];

        $cost = isset($mainProductOptionals['Price1']) ? $mainProductOptionals['Price1'] : 0;
        $yourPrice = isset($mainProductOptionals['YourPrice']) ? $mainProductOptionals['YourPrice'] : 0;
        $urlKey = strtolower($mainProductData['Name'] . '-' . $mainProductData['ProdReference']);
        $urlKey = preg_replace('/\s+/', '-', $urlKey);
        $urlKey = preg_replace('/[^a-z0-9-]+/', '-', strtolower($urlKey));
        $urlKey = trim($urlKey, '-');
        $urlKey = strtolower($urlKey);

        $images = [];
        if (isset($mainProductOptionals['OptionalImage1'])) {
            $imageList = $this->productImageRepository->assignImage($productData['optionals'][0]['OptionalImage1']);
            if ($imageList != 0) {
                $images['files'] = $imageList['files'];
            }
        }

        // CATEGORIES
        if(isset($mainProductOptionals['Type']) && $mainProductOptionals['Type'] != '') {
            if (array_key_exists($mainProductOptionals['Type'], $this->categoryMapper->midocean_to_stricker_category)) {
                $categories = $this->categoryImportService->importStrickerCategories($mainProductOptionals, $this->categoryMapper->midocean_to_stricker_category, $this->categoryMapper->midocean_to_stricker_subcategory);
            }
        }

        $productCategory = $mainProductOptionals['Type'] ?? '';
        $productSubCategory = $mainProductOptionals['SubType'] ?? '';
        $material = $mainProductOptionals['Materials'] ?? '';
        $dimensions = $mainProductOptionals['CombinedSizes'] ?? '';
        $brand = $mainProductOptionals['Brand'] ?? '';
        $name = $mainProductOptionals['Name'] ?? '';
        $components = $mainProductOptionals['ProductComponents'] ?? '';

        $meta_title = "$material $name $components $brand";
        $meta_description = $mainProductOptionals['ShortDescription'];
        $meta_keywords = "$material, $name, $components, $brand, $productCategory, $productSubCategory";
        $price = $this->markupRepository->calculatePrice($cost, $this->globalMarkup);
        
        foreach ($locales as $localeObj) {
            $superAttributes = [
                "channel" => "default",
                "locale" => $localeObj->code,
                'sku' => $mainProductData['ProdReference'],
                "product_number" => $mainProductData['ProdReference'],
                "name" => (!isset($mainProductData['Name'])) ? 'no name' : $mainProductOptionals['Name'],
                "url_key" => $urlKey ?? '',
                "short_description" =>(!isset($mainProductData['ShortDescription'])) ? 'no description provided' : '<p>' . $mainProductOptionals['ShortDescription'] . '</p>',
                "description" => (!isset($mainProductData['Description'])) ? 'no description provided' : '<p>' . $mainProductOptionals['Description'] . '</p>',
                "meta_title" => $meta_title,
                "meta_keywords" => $meta_keywords,
                "meta_description" => $meta_description,
                'price' => round($price, 2),
                'cost' => round($cost, 2),
                "tax_category_id" => "1",
                "special_price" => "",
                "special_price_from" => "",
                "special_price_to" => "",          
                "length" => $mainProductData['BoxLengthMM'] / 10 ?? '',
                "width" => $mainProductData['BoxWidthMM'] / 10 ?? '',
                "height" => $mainProductData['BoxHeightMM'] / 10 ?? '',
                "weight" => $mainProductData['Weight'],
                "new" => "1",
                "visible_individually" => $cost == 0 ? "0" : "1",
                "status" => "1",
                "featured" => "1",
                "guest_checkout" => "1",
                "manage_stock" => "1",
                "inventories" => [
                    1 =>  $mainProductData['BoxQuantity'] ?? 0,
                ],
                'categories' => $categories,
                'variants' => $variants,        
                'images' =>  $images
            ];

            if (isset($material)) {
                $materialObj = $this->attributeOptionRepository->getOption($material);
                if ($materialObj) {
                    $superAttributes['material'] =  $materialObj->admin_name;
                }

                if (!$materialObj) {
                    {
                        $materialObj = $this->attributeOptionRepository->create([
                            'admin_name' => ucfirst(trim($material)),
                            'attribute_id' => 29,
                        ]);

                        $this->productAttributeValueRepository->upsert([
                            'product_id' => $productObj->id,
                            'attribute_id' => 29,
                            "locale" => $localeObj->code,
                            'channel' => null,
                            'unique_id' => implode('|', [$productObj->id,29]),
                            'text_value' => $materialObj->admin_name,
                            'boolean_value' => null,
                            'integer_value' => null,
                            'float_value' => null,
                            'datetime_value' => null,
                            'date_value' => null,
                            'json_value' => null,
                        ], uniqueBy: ['product_id', 'attribute_id']);

                        $superAttributes['material'] =  $materialObj->admin_name;
                    }
                }
            }

            if (isset($dimensions)) {
                $dimensionsObj = $this->attributeOptionRepository->getOption($dimensions);
                
                if ($dimensionsObj) {
                    $superAttributes['dimensions'] =  $dimensionsObj->admin_name;
                }

                if (!$dimensionsObj) {
                    {
                        $dimensionsObj = $this->attributeOptionRepository->create([
                            'admin_name' => ucfirst(trim($dimensions)),
                            'attribute_id' => 30,
                        ]);

                        $this->productAttributeValueRepository->upsert([
                            'product_id' => $productObj->id,
                            'attribute_id' => 30,
                            "locale" => $localeObj->code,
                            'channel' => null,
                            'unique_id' => implode('|', [$productObj->id,30]),
                            'text_value' => $dimensionsObj->admin_name ?? '',
                            'boolean_value' => null,
                            'integer_value' => null,
                            'float_value' => null,
                            'datetime_value' => null,
                            'date_value' => null,
                            'json_value' => null,
                        ], uniqueBy: ['product_id', 'attribute_id']);

                        $superAttributes['dimensions'] =  $dimensionsObj->admin_name;
                    }
                }
            }
            $productObj = $this->productRepository->updateToShop($superAttributes, $productObj->id, 'id');
            $this->markupRepository->addMarkupToPrice($productObj,$this->globalMarkup);
        }

        $this->supplierRepository->create([
            'product_id' => $productObj->id,
            'supplier_code' => $this->identifier
        ]);
    }

    public function createSimple($productData) {
        $locales = $this->localeRepository->get();

        $productObj = $this->productRepository->upserts([
            'channel' => 'default',
            'attribute_family_id' => '1',
            'sku' =>  $productData['optionals'][0]['Sku'],
            "type" => 'simple',
        ]);


        $sizeId = '';
        $colorId = '';
        $images = [];
        $tempAttributes = [];

        if (isset($productData['optionals'][0]['ColorDesc1']) && $productData['optionals'][0]['ColorDesc1'] != '') {
            $colorObj = $this->attributeOptionRepository->getOption($productData['optionals'][0]['ColorDesc1']);
            if ($colorObj && !in_array($colorObj->id,$tempAttributes)) {
                $colorId = $colorObj->id;
                $tempAttributes[] = $colorObj->id;
            }
        }

        if (isset($productData['optionals'][0]['Size']) && $productData['optionals'][0]['Size'] != '') {
            $sizeObj = $this->attributeOptionRepository->getOption($productData['optionals'][0]['Size']);
            if ($sizeObj && !in_array($sizeObj->id,$tempAttributes)) {
                $sizeId = $sizeObj->id;
                $tempAttributes[] = $sizeObj->id;
            }
        }
        
        if (isset($productData['optionals'][0]['OptionalImage1'])) {
            $imageList = $this->productImageRepository->assignImage($productData['optionals'][0]['OptionalImage1']);
            if ($imageList != 0) {
                $images['files'] = $imageList['files'];
            }
        }

        $categories = [];

        $urlKey = strtolower($productData['optionals'][0]['Name'] . '-' . $productData['optionals'][0]['Sku']);
        $urlKey = preg_replace('/\s+/', '-', $urlKey);
        $urlKey = preg_replace('/[^a-z0-9-]+/', '-', strtolower($urlKey));
        $urlKey = trim($urlKey, '-');
        $urlKey = strtolower($urlKey);

        $cost = isset($productData['optionals'][0]['Price1']) ? $productData['optionals'][0]['Price1'] : 0;
        $yourPrice = isset($mainProductOptionals['YourPrice']) ? $mainProductOptionals['YourPrice'] : 0;

        $this->supplierRepository->create([
            'product_id' => $productObj->id,
            'supplier_code' => $this->identifier
        ]);

        if(isset($productData['optionals'][0]['Type']) && $productData['optionals'][0]['Type'] != '') {
            if (array_key_exists($productData['optionals'][0]['Type'], $this->categoryMapper->midocean_to_stricker_category)) {
                $categories = $this->categoryImportService->importStrickerCategories($productData['optionals'][0], $this->categoryMapper->midocean_to_stricker_category, $this->categoryMapper->midocean_to_stricker_subcategory);
            }
        }

        $productCategory = $productData['optionals'][0]['Type'] ?? '';
        $productSubCategory = $productData['optionals'][0]['SubType'] ?? '';
        $material = $productData['optionals'][0]['Materials'] ?? '';
        $dimensions = $productData['optionals'][0]['CombinedSizes'] ?? '';
        $brand = $productData['optionals'][0]['Brand'] ?? '';
        $name = $productData['optionals'][0]['Name'] ?? '';
        $components = $productData['optionals'][0]['ProductComponents'] ?? '';

        $meta_title = "$material $name $components $brand";
        $meta_description = $productData['optionals'][0]['ShortDescription'];
        $meta_keywords = "$material, $name, $components, $brand, $productCategory, $productSubCategory";
        $price = $this->markupRepository->calculatePrice($cost, $this->globalMarkup);

        foreach ($locales as $localeObj) {
            $superAttributes = [
                '_method' => 'PUT',
                "channel" => "default",
                "locale" => $localeObj->code,
                'sku' => $productObj->sku,
                "product_number" =>  $productData['optionals'][0]['ProdReference'] . '-' . $productObj->sku,
                "name" =>  $productData['optionals'][0]['Name'],
                "url_key" => $urlKey,                    
                "weight" => $productData['optionals'][0]['Weight'] ?? 0,
                "short_description" =>(!isset($productData['optionals'][0]['ShortDescription'])) ? 'no description provided' : '<p>' . $productData['optionals'][0]['ShortDescription'] . '</p>',
                "description" => (!isset($productData['optionals'][0]['Description'])) ? 'no description provided' : '<p>' . $productData['optionals'][0]['Description'] . '</p>',
                "meta_title" => $meta_title,
                "meta_keywords" => $meta_keywords,
                "meta_description" => $meta_description,
                "tax_category_id" => "1",
                'price' => round($price, 2),
                'cost' => round($cost, 2),
                "special_price" => "",
                "special_price_from" => "",
                "special_price_to" => "",
                "new" => "1",
                "visible_individually" => $cost == 0 ? "0" : "1",
                "status" => "1",
                "featured" => "1",
                "guest_checkout" => "1",
                "manage_stock" => "1",       
                "length" =>$productData['optionals'][0]['BoxLengthMM'] / 10 ?? '',
                "width" =>$productData['optionals'][0]['BoxWidthMM'] / 10 ?? '',
                "height" => $productData['optionals'][0]['BoxHeightMM'] / 10 ?? '',
                "weight" => $productData['optionals'][0]['Weight'],
                'images' =>  $images,
                'categories' =>  $categories,
            ];
    
            if ($colorId != '') {
                $superAttributes['color'] = $colorId;
            }
    
            if ($sizeId != '') {
                $superAttributes['size'] = $sizeId;
            }
    
            if (isset($material)) {
                $materialObj = $this->attributeOptionRepository->getOption($material);
                if ($materialObj) {
                    $superAttributes['material'] =  $materialObj->admin_name;
                }
    
                if (!$materialObj) {
                    {
                        $materialObj = $this->attributeOptionRepository->create([
                            'admin_name' => ucfirst(trim($material)),
                            'attribute_id' => 29,
                        ]);
    
                        $this->productAttributeValueRepository->upsert([
                            'product_id' => $productObj->id,
                            'attribute_id' => 29,
                            "locale" => $localeObj->code,
                            'channel' => null,
                            'unique_id' => implode('|', [$productObj->id,29]),
                            'text_value' => $materialObj->admin_name,
                            'boolean_value' => null,
                            'integer_value' => null,
                            'float_value' => null,
                            'datetime_value' => null,
                            'date_value' => null,
                            'json_value' => null,
                        ], uniqueBy: ['product_id', 'attribute_id']);
    
                        $superAttributes['material'] =  $materialObj->admin_name;
                    }
                }
            }
    
            if (isset($dimensions)) {
                $dimensionsObj = $this->attributeOptionRepository->getOption($dimensions);
                
                if ($dimensionsObj) {
                    $superAttributes['dimensions'] =  $dimensionsObj->admin_name;
                }
    
                if (!$dimensionsObj) {
                    {
                        $dimensionsObj = $this->attributeOptionRepository->create([
                            'admin_name' => ucfirst(trim($dimensions)),
                            'attribute_id' => 30,
                        ]);
    
                        $this->productAttributeValueRepository->upsert([
                            'product_id' => $productObj->id,
                            'attribute_id' => 30,
                            "locale" => $localeObj->code,
                            'channel' => null,
                            'unique_id' => implode('|', [$productObj->id,30]),
                            'text_value' => $dimensionsObj->admin_name ?? '',
                            'boolean_value' => null,
                            'integer_value' => null,
                            'float_value' => null,
                            'datetime_value' => null,
                            'date_value' => null,
                            'json_value' => null,
                        ], uniqueBy: ['product_id', 'attribute_id']);
    
                        $superAttributes['dimensions'] =  $dimensionsObj->admin_name;
                    }
                }
            }
    
            $productObj = $this->productRepository->updateToShop($superAttributes, $productObj->id, 'id');
            $this->markupRepository->addMarkupToPrice($productObj,$this->globalMarkup);        }


    }

    public function setOutput($output)
    {
        $this->output = $output;
    }

    public function getProductVariant($optionals, $mainProduct, $productVariants) {
        $locales = $this->localeRepository->get();
        $tempAttributes = [];
        $categories = [];

        foreach ($productVariants as $variant) {
            $images = [];
            $variantSku = $variant->sku;
            $foundOptional = null;
        
            foreach ($optionals as $optional) {
                if ($optional['Sku'] === $variantSku) {
                    $foundOptional = $optional;
                    break;
                }
            }
        
            foreach ($locales as $localeObj) {
                if ($foundOptional) {
                    if (isset($foundOptional['ColorDesc1'])) {
                        $colorObj = $this->attributeOptionRepository->getOption($foundOptional['ColorDesc1']);
                        if ($colorObj && !in_array($colorObj->id,$tempAttributes)) {
                            $colorId = $colorObj->id;
                            $tempAttributes[] = $colorId;
                        }
        
                        if (!$colorObj) {
                            {
                                $colorObj = $this->attributeOptionRepository->create([
                                    'admin_name' => ucfirst(trim($foundOptional['ColorDesc1'])),
                                    'attribute_id' => 23,
                                ]);
            
                                $colorId = $colorObj->id;
                                $colorIds[] = $colorId;
                                $tempAttributes[] = $colorId;
                            }
                        }
                    }
    
                    if (isset($foundOptional['Materials'])) {
                        $materialObj = $this->attributeOptionRepository->getOption($foundOptional['Materials']);
                        if ($materialObj && !in_array($materialObj->id,$tempAttributes)) {
                            $materialId = $materialObj->id;
                            $tempAttributes[] = $materialId;
                        }
        
                        if (!$materialObj) {
                            {
                                $materialObj = $this->attributeOptionRepository->create([
                                    'admin_name' => ucfirst(trim($foundOptional['Materials'])),
                                    'attribute_id' => 29,
                                ]);
            
                                $materialId = $materialObj->id;
                                $materialIds[] = $materialId;
                                $tempAttributes[] = $materialId;
                            }
                        }
                    }
        
                    if (isset($foundOptional['Size'])) {
                        $sizeObj = $this->attributeOptionRepository->getOption((string)$foundOptional['Size']);
        
                        if ($sizeObj && !in_array($sizeObj->id,$tempAttributes)) {
                            $sizeId = $sizeObj->id;
                            $tempAttributes[] = $sizeId;
                        }
        
                        if (!$sizeObj && !in_array($foundOptional['Size'],$tempAttributes)) {
                            {
                                $sizeObj = $this->attributeOptionRepository->create([
                                    'admin_name' => ucfirst(trim($foundOptional['Size'])),
                                    'attribute_id' => 24,
                                ]);
            
                                $sizeId = $sizeObj->id;
                                $sizeIds[] = $sizeId;
                                $tempAttributes[] = $sizeId;
                            }
                        }
                    } elseif (sizeof(explode('-', $foundOptional['Sku'])) == 3) {
                        $sizeName = explode('-', $foundOptional['Sku'])[2];
        
                        $sizeObj = $this->attributeOptionRepository->getOption($sizeName);
        
                        if ($sizeObj && !in_array($sizeObj->id,$tempAttributes)) {
                            $sizeId = $sizeObj->id;
                            $tempAttributes[] = $sizeId;
                        }
        
                        if (!$sizeObj) {
                            $sizeObj = $this->attributeOptionRepository->create([
                                'admin_name' => $sizeName,
                                'attribute_id' => 24,
                            ]);
        
                            $sizeId = $sizeObj->id;
                            $sizeIds[] = $sizeId;
                            $tempAttributes[] = $sizeId;
                        }
                    }
                    
                    if (isset($foundOptional['OptionalImage1'])) {
                        $imageList = $this->productImageRepository->assignImage($foundOptional['OptionalImage1']);
                        if ($imageList != 0) {
                            $images['files'] = $imageList['files'];
                        }
                    }
    
                    if ($foundOptional['Materials']) {
                        $materialObj = $this->attributeOptionRepository->getOption($foundOptional['Materials']);
                        if ($materialObj && !in_array($materialObj->id,$tempAttributes)) {
                            $materialId = $materialObj->id;
                            $tempAttributes[] = $materialId;
                        }
            
                        if (!$materialObj) {
                            {
                                $materialObj = $this->attributeOptionRepository->create([
                                    'admin_name' => ucfirst(trim($foundOptional['Materials'])),
                                    'attribute_id' => 29,
                                ]);
            
                                $materialId = $materialObj->id;
                                $materialIds[] = $materialId;
                                $tempAttributes[] = $materialId;
                            }
                        }
                    }
            
                    $this->productAttributeValueRepository->upsert([
                        'product_id' => $variant->id,
                        'attribute_id' => 29,
                        'locale' => $localeObj->code,
                        'channel' => null,
                        'unique_id' => implode('|', [$variant->id,29]),
                        'text_value' => $materialObj->admin_name ?? '',
                        'boolean_value' => null,
                        'integer_value' => null,
                        'float_value' => null,
                        'datetime_value' => null,
                        'date_value' => null,
                        'json_value' => null,
                    ], uniqueBy: ['product_id', 'attribute_id']);
        
                    $urlKey = strtolower($foundOptional['Name'] . '-' . $foundOptional['Sku']);
                    $urlKey = preg_replace('/\s+/', '-', $urlKey);
                    $urlKey = preg_replace('/[^a-z0-9-]+/', '-', strtolower($urlKey));
                    $urlKey = trim($urlKey, '-');
                    $urlKey = strtolower($urlKey);
        
                    $cost = isset($foundOptional['Price1']) ? $foundOptional['Price1'] : 0;
                    $yourPrice = isset($mainProductOptionals['YourPrice']) ? $mainProductOptionals['YourPrice'] : 0;
                    $price = $this->markupRepository->calculatePrice($cost, $this->globalMarkup);
    
                    $variants[$variant->id] = [
                        "sku" => $foundOptional['Sku'],
                        "name" => $foundOptional['Name'],
                        'price' => round($price, 2),
                        'cost' => round($cost, 2),
                        "weight" => $foundOptional['Weight'] ?? 0,
                        "status" => "1",
                        "new" => "1",
                        "visible_individually" => "0",
                        "featured" => "1",
                        "guest_checkout" => "1",
                        "product_number" =>  $foundOptional['ProdReference'] . '-' . $foundOptional['Sku'],
                        "url_key" => $urlKey,
                        "short_description" =>(!isset($foundOptional['ShortDescription'])) ? 'no description provided' : '<p>' . $foundOptional['ShortDescription'] . '</p>',
                        "description" => (!isset($foundOptional['Description'])) ? 'no description provided' : '<p>' . $foundOptional['Description'] . '</p>',
                        "manage_stock" => "1",
                        "inventories" => [
                            1 =>  $foundOptional['BoxQuantity'] ?? 0,
                        ],
                        'images' => $images
                    ];
                    
                    if ($foundOptional['HasSizes'] != false) {
                        $sizeObj = $this->attributeOptionRepository->getOption($foundOptional['Size']);
                        if ($sizeObj) {
                            $variants[$variant->id]['size'] = $sizeObj->id;
                        }
                    }
    
                    if ($foundOptional['HasColors'] != false) {
                        $colorObj = $this->attributeOptionRepository->getOption($foundOptional['ColorDesc1']);
                        if ($colorObj) {
                            $variants[$variant->id]['color'] = $colorObj->id;
                        }
                    }
        
                    $this->supplierRepository->create([
                        'product_id' => $variant->id,
                        'supplier_code' => $this->identifier
                    ]);
    
                    if(isset($foundOptional['Type']) && $foundOptional['Type']!= '') {
                        if (array_key_exists($foundOptional['Type'], $this->categoryMapper->midocean_to_stricker_category)) {
                            $categories = $this->categoryImportService->importStrickerCategories($foundOptional, $this->categoryMapper->midocean_to_stricker_category, $this->categoryMapper->midocean_to_stricker_subcategory);
                        }
                    }
    
                    $name = $foundOptional['Name'];
                    $components = $foundOptional['ProductComponents'];
                    $brand = $foundOptional['Brand'];
                    $productCategory = $foundOptional['Type'];
                    $productSubCategory = $foundOptional['SubType'];
    
                    $meta_title = "$materialObj->admin_name $name $components $brand";
                    $meta_description = $foundOptional['ShortDescription'];
                    $meta_keywords = "$materialObj->admin_name, $name, $components, $brand, $productCategory, $productSubCategory";
                    $price = $this->markupRepository->calculatePrice($cost, $this->globalMarkup);
    
                    $superAttributes = [
                        '_method' => 'PUT',
                        "channel" => "default",
                        "locale" => $localeObj->code,
                        'sku' => $foundOptional['Sku'],
                        "product_number" =>  $foundOptional['ProdReference'] . '-' . $foundOptional['Sku'],
                        "name" =>  $foundOptional['Name'],
                        "url_key" => $urlKey,                    
                        'price' => round($price, 2),
                        'cost' => round($cost, 2),
                        "weight" => $foundOptional['Weight'] ?? 0,
                        "short_description" =>(isset($foundOptional['ShortDescription'])) ? 'no description provided' : '<p>' . $foundOptional['ShortDescription'] . '</p>',
                        "description" => (isset($foundOptional['Description'])) ? 'no description provided' : '<p>' . $foundOptional['Description'] . '</p>',
                        "meta_title" =>  $meta_title,
                        "meta_keywords" => $meta_keywords,
                        "meta_description" => $meta_description,
                        "material" => $materialObj->admin_name ?? '',
                        "tax_category_id" => "1",
                        "dimensions" => $dimensionsObj->admin_name ?? '',
                        "special_price" => "",
                        "special_price_from" => "",
                        "special_price_to" => "",
                        "new" => "1",
                        "visible_individually" => "0",
                        "status" => "1",
                        "featured" => "1",
                        "guest_checkout" => "1",
                        "manage_stock" => "1",       
                        "length" => $foundOptional['BoxLengthMM'] / 10 ?? '',
                        "width" => $foundOptional['BoxWidthMM'] / 10 ?? '',
                        "height" => $foundOptional['BoxHeightMM'] / 10 ?? '',
                        "weight" => $foundOptional['Weight'],
                        'images' =>  $images,
                        'categories' =>  $categories,
                    ];
        
                    if ($foundOptional['HasColors'] != false) {
                        $colorObj = $this->attributeOptionRepository->getOption($foundOptional['ColorDesc1']);
                        if ($colorObj) {
                            $superAttributes['color'] = $colorObj->id;
                        }
                    }
            
                    if ($foundOptional['HasSizes'] != false) {
                        $sizeObj = $this->attributeOptionRepository->getOption($foundOptional['Size']);
                        if ($sizeObj) {
                            $superAttributes['size'] = $sizeObj->id;
                        }
                    }
        
                    $product = $this->productRepository->updateToShop($superAttributes, $variant->id, 'id');
                    $this->markupRepository->addMarkupToPrice($product,$this->globalMarkup);
    
    
                } else {
                    echo "No optional data found for Product Variant SKU: " . $variantSku . "\n";
                }
            }
        }

        return $variants;
    }

    public function getConfigurableSuperAttributes($optionals, $productData) {
        $sizeIds = [];
        $colorIds = [];
        $attributes = [];
        $tempAttributes = [];
       
        // GET COLORS AND SIZES
        foreach ($optionals as $optional) {
            $skuArray = explode('-', $optional['Sku']);

            if (sizeof($skuArray) == 3) {
                $sizeName = $skuArray[2];
                $sizes = ['L', 'S', 'M', 'XS', 'XL', 'XXS', 'XXL', '3XS', '3XL', 'XXXS', 'XXXL', '4XL', '5XL'];
                if (in_array($sizeName, $sizes)) {
                    $sizeObj = $this->attributeOptionRepository->getOption($sizeName);

                    if ($sizeObj && !in_array($sizeObj->id,$tempAttributes)) {
                        $sizeId = $sizeObj->id;
                        $sizeIds[] = $sizeId;
                        $tempAttributes[] = $sizeId;
                    }

                    if (!$sizeObj &&!in_array($sizeName,$tempAttributes)) {
                        $sizeObj = $this->attributeOptionRepository->create([
                            'admin_name' => ucfirst(trim($sizeName)),
                            'attribute_id' => 24,
                        ]);

                        $sizeId = $sizeObj->id;
                        $sizeIds[] = $sizeId;
                        $tempAttributes[] = $sizeId;
                    }
                }
            }
        }

        foreach ($optionals as $optional) {
            if ($optional['HasSizes'] && isset($optional['Sizes'])) {
                $sizeNameList = explode(', ', $optional['Sizes']);
                foreach ( $sizeNameList as $sizeName) {
                    $sizeObj = $this->attributeOptionRepository->getOption(ucfirst(trim($sizeName)));
                    if ($sizeObj) {
                        $sizeIds[] = $sizeObj->id;
                    }

                    if (!$sizeObj) {
                        {
                            $sizeObj = $this->attributeOptionRepository->create([
                                'admin_name' => ucfirst(trim($sizeName)),
                                'attribute_id' => 24,
                            ]);
        
                            $sizeId = $sizeObj->id;
                            $sizeIds[] = $sizeId;
                        }
                    }
                }
            }

            if ($optional['HasColors'] && isset($optional['Colors'])) {
        
                $colorNameList = explode(', ', $optional['Colors']);
                foreach ( $colorNameList as $colorName) {
                    $colorObj = $this->attributeOptionRepository->getOption(ucfirst(trim($colorName)));
                    if ( $colorObj) {
                        $colorIds[] = $colorObj->id;
                    }

                    if (!$colorObj) {
                        {
                            $colorObj = $this->attributeOptionRepository->create([
                                'admin_name' => ucfirst(trim($colorName)),
                                'attribute_id' => 23,
                            ]);
        
                            $colorId = $colorObj->id;
                            $colorIds[] = $colorId;
                        }
                    }
                }
            } elseif ($optional['HasColors'] && !isset($optional['Colors'])) {
                if (isset($optional['ColorDesc1'])) {
                    $colorObj = $this->attributeOptionRepository->getOption(ucfirst(trim($optional['ColorDesc1'])));
                    if (!$colorObj) {
                        $colorObj = $this->attributeOptionRepository->create([
                            'admin_name' => ucfirst(trim($optional['ColorDesc1'])),
                            'attribute_id' => 23,
                        ]);                    
                    }

                    $colorId = $colorObj->id;
                    $colorIds[] = $colorId;
                }
            }
        }

        $colorIds = array_unique($colorIds);

        if (sizeof($sizeIds) > 0) {
            $attributes['size'] = $sizeIds;
        }

        if (sizeof($colorIds) > 0) {
            $attributes['color'] = $colorIds;
        }
        return $attributes;
    }
}
