<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'product_type'     => $this->product_type,
            'product_name'     => $this->product_name,
            'product_native_name' => $this->product_native_name,
            'product_code'     => $this->product_code,
            'category_id'      => $this->category_id,            
            'category_name'    => ['name'=>$this->category ? $this->category->name: ''],            
            'sub_category_id'  => $this->sub_category_id,            
            'sub_category_name'=> ['name'=>$this->sub_category ? $this->sub_category->name:''],        
            'sub_sub_category'=> ['name'=>$this->sub_sub_category ? $this->sub_sub_category->name:''],        
            'company_id'       => $this->company_id,             
            'barcode_symbology'=> $this->barcode_symbology,             
            'min_order_qty'    => $this->min_order_qty,             
            'cost_price'       => $this->cost_price,             
            'depo_price'       => $this->depo_price,             
            'mrp_price'        => $this->mrp_price,      
            'abp_price'        => $this->abp_price,      
            'abp_qty'          => $this->abp_qty,      
            'tax_method'       => $this->tax_method,             
            'product_tax'      => $this->product_tax,             
            'discount'         => $this->discount,             
            'alert_quantity'   => $this->alert_quantity,             
            'thumbnail'        => $this->thumbnail ? asset('public/products/thumbnail/'.$this->thumbnail) : '',             
            'brand_id'         => $this->brand_id,            
            'brand_name'       => ['name'=>$this->brand->name ?? ''],
            'short_description'=> $this->short_description,            
            'description'      => $this->description,            
            'is_ecommerce'     => $this->is_ecommerce,            
            'is_expirable'     => $this->is_expirable,            
            'purchase_measuring_unit'=> $this->purchase_measuring_unit,            
            'sales_measuring_unit'=> $this->sales_measuring_unit,            
            'convertion_rate'  => $this->convertion_rate,            
            'carton_size'      => $this->carton_size,            
            'carton_cpu'       => $this->carton_cpu,            
            'allow_checkout_when_out_of_stock'=> $this->allow_checkout_when_out_of_stock,            
            'is_outlet_management'=> $this->is_outlet_management,            
            'outlet_id'        => $this->outlet_id,            
            'quantity'         => $this->quantity,            
            'status'           => $this->status, 
            'user_define_barcode'=> $this->product_barcodes->map(function($item) { return $item->code; }), 
            'attachments'      => $this->product_images->map(function($item){
                                    return $item->name ? asset('/products/images/'.$item->name) : '';
                                }), 
            'supplier_id'      => $this->suppliers->map(function($item) { return $item->id; }), 
            'product_tags'     => $this->product_keywords->map(function($item) { return $item->name; }), 
            'size'             => $this->sizes->map(function($item) { return $item->id; }), 
            'color'            => $this->colors->map(function($item) { return $item->id; }),  
            'gift_items'       => $this->gift_items,  
            'stock_product'    => $this->stock_product,
            'carton_c_p_u'     => $this->carton_c_p_u,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at, 
        ];
    }

    public function discountCalculate($discount, $mrp_price){ 
        $expDiscount = explode("%",$discount);
        if(sizeof($expDiscount) > 1){
            return $mrp_price*$expDiscount[0]/100;
        }else{
            return $expDiscount[0] ? $expDiscount[0] : 0;
        } 
    }
    public function taxCalculate($tax, $mrp_price){         
        return $mrp_price*$tax/100; 
    }

    public function net_price($mpr_price, $discount, $vat){
        return $mpr_price + $vat - $discount;
    }
}
