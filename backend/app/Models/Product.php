<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use DB ;

/**
 * Class Products
 * @package App\Models
 * @version March 2, 2022, 12:01 pm UTC
 *
 * @property string $name
 */
class Product extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'products';
    

    protected $dates = ['deleted_at'];



    public $fillable = [  
        'product_type', 'product_name', 'product_native_name', 'product_code', 'category_id', 'sub_category_id', 'sub_sub_category_id', 'brand_id', 'company_id', 'barcode_symbology', 'min_order_qty', 'cost_price', 'depo_price', 'mrp_price', 'abp_price', 'abp_qty', 'tax_method', 'product_tax', 'alert_quantity', 'thumbnail', 'supplier_id', 'short_description', 'description', 'is_ecommerce', 'is_expirable', 'purchase_measuring_unit', 'sales_measuring_unit', 'convertion_rate', 'carton_size', 'carton_cpu', 'allow_checkout_when_out_of_stock', 'outlet_id', 'quantity', 'status','discount'
    ];
    //  'is_outlet_management',

    //protected $guarded = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    //  'product_name','product_native_name','product_code'
    protected $casts = [
        'product_name' => 'string',
        'product_native_name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function salesItems()
    {
        return $this->hasMany(SaleItem::class, 'sale_id', 'id');
    }


    // relations

    public function category(){
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }
    public function sub_category(){
        return $this->belongsTo(ProductCategory::class, 'sub_category_id', 'id');
    }
    public function sub_sub_category(){
        return $this->belongsTo(ProductCategory::class); 
    }
    public function brand(){
        return $this->belongsTo(Brand::class); 
    }
    public function product_images()
    {
        return $this->hasMany(ProductImages::class, 'product_id', 'id');
    }
    public function product_barcodes()
    {
        return $this->hasMany(ProductBarcodes::class, 'product_id', 'id');
    } 
    public function colors(){
        return $this->belongsToMany(Color::class, 'products_colors',  'product_id', 'color_id');
    }
    public function sizes(){
        return $this->belongsToMany(Size::class, 'products_sizes',  'product_id', 'size_id');
    }
    public function suppliers(){
        return $this->belongsToMany(Supplier::class, 'products_suppliers',  'product_id', 'supplier_id');
    }
    public function keywords()
    {
        return $this->hasMany(ProductKeywords::class, 'product_id', 'id');
    }
    public function images()
    {
        return $this->hasMany(ProductImages::class, 'product_id', 'id');
    }
    
    public function barcodes()
    {
        return $this->hasMany(ProductBarcodes::class, 'product_id', 'id');
    }

    public function product_keywords()
    {
        return $this->hasMany(ProductKeywords::class, 'product_id', 'id');
    }

    public function purchase_unit()
    {
        return $this->belongsTo(Unit::class, 'purchase_measuring_unit', 'id');
    }

    public function sales_unit()
    {
        return $this->belongsTo(Unit::class, 'sales_measuring_unit', 'id');
    }
    public function tax()
    {
        return $this->belongsTo(Taxes::class, 'tax_method', 'id');
    }
    public function expire_datas()
    {
        return $this->hasMany(StockProduct::class, 'product_id', 'id');
    }
    public function stock_product()
    {
        $outlet_id = 1;
        return $this->hasMany(StockProduct::class, 'product_id', 'id')
            ->where('outlet_id',$outlet_id);
    }
    public function gift_items()
    {
        return $this->hasMany(StockProductGift::class, 'product_id', 'id');
    }

    // public function product_suppliers()
    // {
    //     return $this->hasMany(ProductSupplier::class, 'supplier_id', 'id')
    //         ->join('suppliers', 'suppliers.id', '=', 'product_suppliers.supplier_id') 
    //         ->select('name','product_suppliers.id as id');
    // }
    
    // public function product_colors()
    // {
    //     return $this->hasMany(ProductColor::class, 'product_id', 'id') 
    //     ->join('colors', 'colors.id', '=', 'product_colors.color_id') 
    //     ->select('product_id','color_id','name','code','colors.id as id'); 
    // }
     
    // public function product_sizes()
    // {
    //     return $this->hasMany(ProductSize::class, 'product_id', 'id')
    //         ->join('sizes', 'sizes.id', '=', 'product_sizes.size_id')
    //         ->select('product_id','size_id','name','value');
    // } 
    // public function productSizes(){
    //     return $this->belongsToMany(Product::class, 'products_sizes',  'product_id', 'size_id',);
    // }

    public function stock_products()
    {
        return $this->hasMany(StockProduct::class, 'product_id', 'id');
    }

    public function sale_items()
    {
        return $this->hasMany(SaleItem::class, 'product_id', 'id');
    }

    public function order_requisition_details()
    {
        return $this->hasMany(OrderRequisitionDetail::class, 'product_id', 'id');
    }
    public function slow_moving(){ 
        return $this->belongsTo(SaleItem::class, 'id', 'product_id')
                    ->select(DB::raw("IFNULL(SUM(sale_items.quantity),0) as total") )
                    ->where('product_id',$this->id) 
                    ->orderBy('total','asc')
                    ->having('total', '>', 0);
    }
    public function fast_moving(){ 
        return $this->belongsTo(SaleItem::class, 'id', 'product_id')
                    ->select(DB::raw("IFNULL(SUM(sale_items.quantity),0) as total") )
                    ->where('product_id',$this->id) 
                    ->orderBy('total','asc')
                    ->having('total', '<', 1);
    }
    public function no_salling(){ 
        return $this->belongsTo(SaleItem::class, 'id', 'product_id')
                    ->select(DB::raw("IFNULL(SUM(sale_items.quantity),0) as total") )
                    ->where('product_id',$this->id) 
                    ->orderBy('total','asc')
                    ->having('total', '<', 1);
    }

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
           

}
