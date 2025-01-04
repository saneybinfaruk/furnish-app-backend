<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariants extends Model
{
    protected $guarded = [];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function sizes(){
        return $this->hasMany(VariantSizes::class, 'product_variants_id');
    }

    public function images(){
        return $this->hasMany(VariantImages::class, 'product_variants_id');
    }


}
