<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantImages extends Model
{
    protected $guarded = [];

    public function productVariant(){
        return $this->belongsTo(ProductVariants::class, 'product_variants_id');
    }
}
