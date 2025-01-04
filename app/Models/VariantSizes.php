<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantSizes extends Model
{
    protected $guarded = [];


    public function productVariants() {
        return $this->belongsTo(ProductVariants::class, 'product_variants_id');
    }
}
