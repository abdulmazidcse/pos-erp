<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\DiscountSetting;
use App\Models\SaleItem;
use DB;
use Carbon\Carbon;

class PosProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public $item_discount       =0;
    public $category_discount   =0;
    public $vendor_discount     =0;
    public $slow_moving_discount=0;
    public $fast_moving_discount=0;
    public $conditional_discount=0;
    public $sales_platform_discount=0;
    public $all_discount        =0;
    public function toArray($request)
    { 
        return [
            'product_id'        => $this->id,
            'product_stock_id'  => $this->product_stock_id,
            'outlet_id'         => $this->outlet_id,
            'product_type'      => $this->product_type,
            'product_name'      => $this->product_name,
            'product_native_name' => $this->product_native_name,
            'product_code'      => $this->product_code,
            'category_id'       => $this->category_id, 
            'barcode_symbology' => $this->barcode_symbology,
            'min_order_qty'     => $this->min_order_qty,
            'cost_price'        => $this->cost_price, 
            'depo_price'        => $this->depo_price, 
            'mrp_price'         => $this->mrp_price, 
            'tax_method'        => $this->tax_method, 
            'product_tax'       => $this->product_tax,
            'measuring_unit'    => $this->purchase_measuring_unit, 
            'weight'            => '', 
            'item_discount'     => $this->item_discount($this->discount, $this->mrp_price),
            'discount'          => $this->all_discount,                                 
            'tax'               => $this->taxCalculate($this->tax->value, $this->mrp_price),         
            'quantity'          => $this->stock_quantity,
            'stock_quantity'    => $this->stock_quantity,
            'expires_date'      => $this->expires_date ? Carbon::parse($this->expires_date)->format('d-m-Y') : '',   
            'dis_array'         => [
                                    'category_discount' =>$this->category_discount($this->category ? $this->category->discount : '', $this->mrp_price),
                                    'sub_category_discount' => $this->sub_category_discount($this->sub_category? $this->sub_category->discount : '', $this->mrp_price),
                                    'vendor_discount' => 0,
                                    'slow_moving_discount' => 0,
                                    'fast_moving_discount' => 0,
                                    'conditional_discount' => $this->conditional_discount($this->mrp_price),
                                    'sales_platform_discount' => $this->sales_platform_discount($this->mrp_price),
                                    'gp_wise' => $this->gp_wise($this->mrp_price, $this->cost_price),
                                ]
        ];
    }
    public function item_discount($discount, $mrp_price){ 
        if($this->discountSetting()){  
            $calDiscount = 0;          
            if($this->discountSetting()->product_wise) {
                $expDiscount = explode("%",$discount);

                // return $expDiscount;
                if(sizeof($expDiscount) > 1){
                    $calDiscount = $mrp_price*$expDiscount[0]/100;
                }else{
                    $calDiscount = (!empty($expDiscount[0]) && $expDiscount[0] != "null" && $expDiscount[0] != "") ? $expDiscount[0] : 0;
                }  
                if($calDiscount > 0 ){
                    $this->all_discount = $this->all_discount+$calDiscount;
                } 
                return $calDiscount;
            }
        }
        return 0;
    }
    public function category_discount($discount, $mrp_price){
       if($this->discountSetting()){            
            if($this->discountSetting()->category_wise){
                $start_date = $this->discountSetting()->category_offer_within_range[0]['start_date'];
                $end_date = $this->discountSetting()->category_offer_within_range[0]['end_date']; 
                if(($start_date) && ($end_date) && ($discount)){ 
                    $date_now = date("Y-m-d"); // this format is string comparable
                    if (($date_now >= $start_date) && ($date_now <= $end_date)) {
                        $discountAmount = $mrp_price*$discount/100;
                        $this->all_discount = $this->all_discount+$discountAmount;
                        return $discountAmount;
                    } 
                }else{
                    return 0;
                } 
            }
        }
        return 0; 
    } 
    public function sub_category_discount($discount, $mrp_price){
        if($this->discountSetting()){            
            if($this->discountSetting()->sub_category_wise){
                $start_date = $this->discountSetting()->sub_cat_offer_within_range[0]['start_date'];
                $end_date = $this->discountSetting()->sub_cat_offer_within_range[0]['end_date']; 
                if(($start_date) && ($end_date) && ($discount)){ 
                    $date_now = date("Y-m-d"); // this format is string comparable
                    if (($date_now >= $start_date) && ($date_now <= $end_date)) {
                        $discountAmount = $mrp_price*$discount/100;
                        $this->all_discount = $this->all_discount+$discountAmount;
                        return $mrp_price*$discount/100;
                    } 
                }else{
                    return 0;
                } 
            }
        }
        return 0; 
    } 
    public function vendor_discount(){
        if($this->discountSetting()){            
            if($this->discountSetting()->vendor_discount){
                return 0;
            }
        }
        return 0; 
    } 
    public function slow_moving_discount($mrp_price){
         
        // $slowMoving = self::sallingQuery()
        //    ->having('total', '<', 1)  
        //    ->orderBy('count','asc') 
        //    ->get()->toArray(); 
        // print_r($slowMoving);
        if($this->discountSetting()){            
            if(($this->discountSetting()->slow_moving_product) && ($this->discountSetting()->slow_moving_product_discount)){
                if($this->discountSetting()->slow_moving_product_discount){ 
                    $discountAmount = $mrp_price*$this->discountSetting()->slow_moving_product_discount/100; 
                    $this->all_discount = $this->all_discount+$discountAmount;
                    return $discountAmount; 
                }else{
                    return 0;
                } 
            }
        }
        return 0; 
    } 
    public function fast_moving_discount($mrp_price){
        if($this->discountSetting()){            
            if(($this->discountSetting()->fast_moving_product) && ($this->discountSetting()->fast_moving_product_discount)){
                if($this->discountSetting()->fast_moving_product_discount){
                    $discountAmount = $mrp_price*$this->discountSetting()->fast_moving_product_discount/100; 
                    $this->all_discount = $this->all_discount+$discountAmount;
                    return $discountAmount; 
                }else{
                    return 0;
                } 
            }
        }
        return 0; 
    }     
    public function conditional_discount($mrp_price){
        if($this->discountSetting()){            
            if(($this->discountSetting()->enable_conditional_discount) && ($this->discountSetting()->discount_within_range)){
                $start_date = $this->discountSetting()->discount_within_range[0]['start_date'];
                $end_date = $this->discountSetting()->discount_within_range[0]['end_date'];
                $discount = $this->discountSetting()->discount_within_range[0]['discount'];
                if(($start_date) && ($end_date) && ($discount)){ 
                    $date_now = date("Y-m-d"); // this format is string comparable
                    if (($date_now >= $start_date) && ($date_now <= $end_date)) {
                        $discountAmount =  $mrp_price*$discount/100;  
                        $this->all_discount = $this->all_discount+$discountAmount;
                        return $discountAmount; 
                    } 
                }
            }
        }
        return 0;
    }
    public function sales_platform_discount($mrp_price){
        if($this->discountSetting()){
            if(($this->discountSetting()->sales_platform) && ($this->discountSetting()->sales_platform_pos)){
                return $mrp_price*$this->discountSetting()->sales_platform_pos_discount/100;
            }
        }else{
            return 0;
        }
    }
    public function gp_wise($mrp_price, $cost_price){
        $gpDiscount = ($mrp_price - $cost_price); 
        if($this->discountSetting()){            
            if(($this->discountSetting()->gp_wise) && ($this->discountSetting()->gp_wise_discount)){
                $start_date = $this->discountSetting()->gp_offer_within_range[0]['start_date'];
                $end_date = $this->discountSetting()->gp_offer_within_range[0]['end_date'];
                $discount = $this->discountSetting()->gp_wise_discount; 
                if(($start_date) && ($end_date) && ($discount)){ 
                    $date_now = date("Y-m-d"); // this format is string comparable
                    if (($date_now >= $start_date) && ($date_now <= $end_date)) {
                        $discountAmount = $gpDiscount*$this->discountSetting()->gp_wise_discount/100; 
                        $this->all_discount = $this->all_discount+$discountAmount;
                        return $discountAmount; 
                    } 
                } 
                return 0;
            }
        }else{
            return 0;
        }
    }
    public function discountSetting(){
        return $data = DiscountSetting::first(); 
    }
    public function taxCalculate($tax, $mrp_price){         
        return $mrp_price*$tax/100; 
    }

    public function sallingQuery(){
        return SaleItem::rightJoin('products','products.id','=','sale_items.product_id')
           ->select('products.id',DB::raw("IFNULL(SUM(sale_items.quantity),0) as total"),DB::raw("COUNT(sale_items.id) as count"))
           ->groupBy('products.id');  
    }
}
