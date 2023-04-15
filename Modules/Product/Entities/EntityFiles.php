<?php

namespace Modules\Product\Entities;

use Modules\Support\Money;
use Modules\Tag\Entities\Tag;
use Modules\Media\Entities\File;
use Modules\Brand\Entities\Brand;
use Modules\Tax\Entities\TaxClass;
use Modules\Option\Entities\Option;
use Modules\Review\Entities\Review;
use Modules\Support\Eloquent\Model;
use Modules\Media\Eloquent\HasMedia;
use Modules\Meta\Eloquent\HasMetaData;
use Modules\Support\Search\Searchable;
use Modules\Category\Entities\Category;
use Modules\Product\Admin\ProductTable;
use Modules\Support\Eloquent\Sluggable;
use Modules\FlashSale\Entities\FlashSale;
use Modules\Support\Eloquent\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Attribute\Entities\ProductAttribute;

class EntityFiles extends Model
{


    protected $table = 'entity_files';

    protected $guarded = [];


}
