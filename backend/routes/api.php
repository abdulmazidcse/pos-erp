<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [App\Http\Controllers\API\AuthAPIController::class, 'login']);
    Route::post('unlockDiscount', [App\Http\Controllers\API\AuthAPIController::class, 'unlockDiscount']);
    //    Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::group(['middleware' => 'auth:api'], function () {

        //        Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);
        Route::post('logout', [App\Http\Controllers\API\AuthAPIController::class, 'logout']);

        Route::post('user', [App\Http\Controllers\AuthController::class, 'user']);

        Route::post('change-password', [App\Http\Controllers\AuthController::class, 'change_password']);

        //Get User Permissions
        Route::get('user/menu-and-permissions', [App\Http\Controllers\API\PermissionAPIController::class, 'getUserMenuAndRolePermissions']);
    });
});
Route::middleware(['auth:api', 'checkUserStatus'])->group(function () {


// });
// Route::group(['middleware' => 'auth:api'], function () {
    Route::get('users/authUser', [App\Http\Controllers\API\UserAPIController::class, 'authApiUser']);
    Route::resource('warehouses', App\Http\Controllers\API\WarehouseAPIController::class);
    Route::resource('outlets', App\Http\Controllers\API\OutletAPIController::class);
    Route::get('outlets-list', [App\Http\Controllers\API\OutletAPIController::class,'outlets']);
    Route::resource('sales', App\Http\Controllers\API\SaleAPIController::class);

    Route::resource('companies', App\Http\Controllers\API\CompanyAPIController::class);
    Route::get('posproducts', [App\Http\Controllers\API\ProductsAPIController::class, 'productForPos']);
    Route::get('purchase-return-products', [App\Http\Controllers\API\ProductsAPIController::class, 'productForPurchaseReturn']);
    // Company Resource Routes
    // Stock Adjustment
    // Route::post('stocks/stockAdjustment', [App\Http\Controllers\API\StockAdjustmentAPIController::class, 'stockAdjustment']);

    //Dashboard
    //Route::get('posproducts', [App\Http\Controllers\API\ProductsAPIController::class, 'productForPosWithoutLogin']);
    Route::get('hold-sales-products', [App\Http\Controllers\API\ProductsAPIController::class, 'productForHoldSales']);
    Route::get('sale/list', [App\Http\Controllers\API\SaleAPIController::class, 'list']);
    Route::get('sale/duelist', [App\Http\Controllers\API\SaleAPIController::class, 'duelist']);
    Route::get('due-invoices', [App\Http\Controllers\API\SaleAPIController::class, 'dueInvoices']);
    Route::get('due-collection', [App\Http\Controllers\API\SaleAPIController::class, 'dueCollection']);
    Route::post('collection', [App\Http\Controllers\API\SaleAPIController::class, 'collection']);
    Route::get('sale-helper', [App\Http\Controllers\API\SaleAPIController::class, 'saleHelper']);
    Route::get('saleInfo', [App\Http\Controllers\API\SaleAPIController::class, 'saleInfo']);
    Route::get('/dashboard', [App\Http\Controllers\API\DashboardAPIController::class, 'dashboard']);
    Route::get('/dashboard/product_stock', [App\Http\Controllers\API\DashboardAPIController::class, 'dashboardProductStock']);
    Route::get('/dashboard/product_daily_sales', [App\Http\Controllers\API\DashboardAPIController::class, 'dashboardProductSaleReport']);
    Route::get('/checksms', [App\Http\Controllers\API\SaleAPIController::class, 'checksms']);

    //User Routes
    Route::get('users/outlet_assign', [App\Http\Controllers\API\UserOutletAssignAPIController::class, 'index']);
    Route::post('users/outlet_assign', [App\Http\Controllers\API\UserOutletAssignAPIController::class, 'store']);
    Route::resource('users', App\Http\Controllers\API\UserAPIController::class);
    Route::get('user-list', [App\Http\Controllers\API\UserAPIController::class, 'userList']);
    Route::get('user-helper-data', [App\Http\Controllers\API\UserAPIController::class, 'helperData']);

    // Role Routes
    Route::get('roles/{id}/permission', [App\Http\Controllers\API\RoleAPIController::class, 'rolePermissionGet']);
    Route::post('roles/{id}/permission', [App\Http\Controllers\API\RoleAPIController::class, 'rolePermissionSet']);

    Route::resource('roles', App\Http\Controllers\API\RoleAPIController::class);

    // Permission Module Routes
    Route::get('permission_modules/getParentsModule', [App\Http\Controllers\API\PermissionModuleAPIController::class, 'getParentsModule']);
    Route::get('permission_modules/getParentsWithoutHasChildren', [App\Http\Controllers\API\PermissionModuleAPIController::class, 'getParentsWithoutHasChildren']);
    Route::resource('permission_modules', App\Http\Controllers\API\PermissionModuleAPIController::class);

    // Permission Routes
    Route::get('permissions/list', [App\Http\Controllers\API\PermissionAPIController::class, 'permissionList']);
    Route::resource('permissions', App\Http\Controllers\API\PermissionAPIController::class);



    // Department Resource Routes
    Route::resource('departments', App\Http\Controllers\API\DepartmentAPIController::class);

    // Outlet Resource Routes


    Route::get('getProductCategory', [App\Http\Controllers\API\ProductCategoryAPIController::class, 'getProductParentCategory']);

    Route::get('product_categories/list', [\App\Http\Controllers\API\ProductCategoryAPIController::class, 'categoryList'])->name('product_categories.list');
    // Product Category Resource Routes
    Route::resource('product_categories', App\Http\Controllers\API\ProductCategoryAPIController::class);

    // Brand Resource Routes
    Route::resource('brands', App\Http\Controllers\API\BrandAPIController::class);

    // Unit Resource Routes
    Route::resource('units', App\Http\Controllers\API\UnitAPIController::class);


    Route::resource('divisions', App\Http\Controllers\API\DivisionAPIController::class);
    Route::get('districts/list', [App\Http\Controllers\API\DistrictAPIController::class, 'list']);
    Route::get('district-areas/{id}', [App\Http\Controllers\API\DistrictAPIController::class, 'districtAreas']);
    Route::resource('districts', App\Http\Controllers\API\DistrictAPIController::class);
    Route::get('areas/list', [App\Http\Controllers\API\AreaAPIController::class, 'list']);
    Route::resource('areas', App\Http\Controllers\API\AreaAPIController::class);


    //Route::resource('warehouses', App\Http\Controllers\API\WarehouseAPIController::class);

    Route::post('customers/customerBulkUpload', [App\Http\Controllers\API\CustomerAPIController::class, 'customerBulkStore']);
    Route::get('customers/list', [App\Http\Controllers\API\CustomerAPIController::class, 'customerList']);
    Route::resource('customers', App\Http\Controllers\API\CustomerAPIController::class);

    Route::get('suppliers/list', [App\Http\Controllers\API\SupplierAPIController::class, 'getSupplierList']);
    Route::post('suppliers/add', [App\Http\Controllers\API\SupplierAPIController::class, 'storeSupplier']);
    Route::resource('suppliers', App\Http\Controllers\API\SupplierAPIController::class);

    Route::get('purchase_orders/{id}/generatePDF', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'generatePDF']);

    Route::post('purchase_orders/getBeforeSubmitPreviewMPO', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'getBeforeSubmitPreviewMPO']);
    Route::post('purchase_orders/getBeforeSubmitPreviewSPO', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'getBeforeSubmitPreviewSPO']);
    Route::get('purchase_orders/getOrderReferenceNo', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'getOrderReferenceNo']);
    Route::post('purchase_orders/getProductData', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'getProductData']);
    Route::post('purchase_orders/getSupplier', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'getSupplierData']);
    Route::get('purchase_orders/getData', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'getPurchaseOrdersData']);
    Route::post('/purchase_orders/send_po/{id}', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'sendPOForVendor']);
    Route::post('/purchase_orders/approve/{id}', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'approvePurchaseOrder']);
    Route::post('/purchase_orders/reject/{id}', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'rejectPurchaseOrder']);

    // Store Purchase Order Edit
    Route::get('/purchase_orders/{id}/storeRequisitionPurchaseOrderEdit', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'requisitionPurchaseOrderEdit']);
    Route::put('/purchase_orders/{id}/confirmRequisitionPurchaseOrderEdit', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'confirmRequisitionPurchaseOrderEdit']);

    Route::get('purchase_orders/{id}/view_details', [App\Http\Controllers\API\PurchaseOrderAPIController::class, 'viewDetails']);
    Route::resource('purchase_orders', App\Http\Controllers\API\PurchaseOrderAPIController::class);

    Route::resource('products', App\Http\Controllers\API\ProductsAPIController::class);
    Route::get('product/list', [App\Http\Controllers\API\ProductsAPIController::class, 'list']);

    Route::get('requisition_products', [App\Http\Controllers\API\ProductsAPIController::class, 'productForStoreRequisition']);

    Route::post('products-import', [App\Http\Controllers\API\ProductsAPIController::class, 'productsImport']);

    Route::post('master-data-upload', [App\Http\Controllers\API\ProductsAPIController::class, 'masterDataUpload']);

    Route::get('products-helper', [App\Http\Controllers\API\ProductsAPIController::class, 'productsHelper']);

    Route::get('sample-products-upload-formate', [App\Http\Controllers\API\ProductsAPIController::class, 'sampleProductsUploadFormate']);

    Route::resource('product_images', App\Http\Controllers\API\ProductImagesAPIController::class);


    Route::resource('product_keywords', App\Http\Controllers\API\ProductKeywordsAPIController::class);


    Route::resource('product_barcodes', App\Http\Controllers\API\ProductBarcodesAPIController::class);


    Route::resource('product_suppliers', App\Http\Controllers\API\ProductSupplierAPIController::class);


    Route::resource('product_colors', App\Http\Controllers\API\ProductColorAPIController::class);


    Route::resource('product_sizes', App\Http\Controllers\API\ProductSizeAPIController::class);


    Route::resource('sizes', App\Http\Controllers\API\SizeAPIController::class);


    Route::resource('colors', App\Http\Controllers\API\ColorAPIController::class);


    Route::resource('supplier_types', App\Http\Controllers\API\SupplierTypeAPIController::class);

    Route::get('store_requisitions/getRequisitionNo', [\App\Http\Controllers\API\StoreRequisitionAPIController::class, 'getStoreRequisitionNo']);
    Route::post('store_requisitions/getProduct', [\App\Http\Controllers\API\StoreRequisitionAPIController::class, 'getProduct']);
    Route::get('store_requisitions/getRequisitionData', [\App\Http\Controllers\API\StoreRequisitionAPIController::class, 'getRequisitionData']);
    Route::get('store_requisitions/{id}/getRequisitionProduct', [\App\Http\Controllers\API\StoreRequisitionAPIController::class, 'getRequisitionProductById']);
    Route::post('store_requisitions/{id}/approveStoreRequisition', [\App\Http\Controllers\API\StoreRequisitionAPIController::class, 'approveStoreRequisition']);
    Route::post('store_requisitions/{id}/rejectStoreRequisition', [\App\Http\Controllers\API\StoreRequisitionAPIController::class, 'rejectStoreRequisition']);
    Route::get('store_requisitions/{id}/storeRequisitionPurchaseOrder', [\App\Http\Controllers\API\StoreRequisitionAPIController::class, 'storeRequisitionPurchaseOrder']);
    Route::post('store_requisitions/{id}/confirmPurchaseOrder', [\App\Http\Controllers\API\StoreRequisitionAPIController::class, 'storeRequisitionPurchaseOrderConfirm']);
    Route::resource('store_requisitions', App\Http\Controllers\API\StoreRequisitionAPIController::class);

    Route::post('purchase_receives/getPurchaseReceiveData', [App\Http\Controllers\API\PurchaseReceiveAPIController::class, 'getPurchaseReceiveData']);
    Route::get('purchase_receives/getReferenceNo', [App\Http\Controllers\API\PurchaseReceiveAPIController::class, 'getReferenceNo']);
    Route::post('purchase_receives/getPurchaseOrder', [App\Http\Controllers\API\PurchaseReceiveAPIController::class, 'supplierPurchaseOrder']);
    Route::post('purchase_receives/purchaseOrderProducts', [App\Http\Controllers\API\PurchaseReceiveAPIController::class, 'purchaseOrderProducts']);
    Route::post('purchase_receives/purchaseReceiveBulkUpload', [App\Http\Controllers\API\PurchaseReceiveAPIController::class, 'purchaseReceiveBulkUpload']);
    Route::post('purchase_receives/purchaseReceiveWithBoard', [App\Http\Controllers\API\PurchaseReceiveAPIController::class, 'purchaseReceiveWithBoard']);
    Route::resource('purchase_receives', App\Http\Controllers\API\PurchaseReceiveAPIController::class);

    Route::get('warehouse_purchase_receives/getReferenceNo', [App\Http\Controllers\API\WarehousePurchaseReceiveAPIController::class, 'getReferenceNo']);
    Route::post('warehouse_purchase_receives/getPurchaseOrder', [App\Http\Controllers\API\WarehousePurchaseReceiveAPIController::class, 'supplierPurchaseOrder']);
    Route::resource('warehouse_purchase_receives', App\Http\Controllers\API\WarehousePurchaseReceiveAPIController::class);

    // Purchase Return
    Route::post('return/getPurchaseReceiveInvoice', [App\Http\Controllers\API\PurchaseReturnController::class, 'getPurchaseReceiveInvoice']);
    Route::post('return/getPurchaseReceiveItems', [App\Http\Controllers\API\PurchaseReturnController::class, 'getPurchaseReceiveItems']);
    Route::post('return/getProductExpireData', [App\Http\Controllers\API\PurchaseReturnController::class, 'getProductExpireData']);
    Route::post('return/purchase_return', [App\Http\Controllers\API\PurchaseReturnController::class, 'storePurchaseReturn']);
    Route::get('return/purchase_return_list', [App\Http\Controllers\API\PurchaseReturnController::class, 'indexPurchaseReturn']);
    Route::get('return/purchase_return/{id}', [App\Http\Controllers\API\PurchaseReturnController::class, 'showPurchaseReturn']);


    Route::resource('taxes', App\Http\Controllers\API\TaxesAPIController::class);


    Route::resource('customer_groups', App\Http\Controllers\API\CustomerGroupAPIController::class);


    Route::resource('mobile_wallets', App\Http\Controllers\API\MobileWalletAPIController::class);


    Route::resource('attributes', App\Http\Controllers\API\AttributeAPIController::class);


    Route::resource('attribute_values', App\Http\Controllers\API\AttributeValueAPIController::class);


    Route::resource('product_groups', App\Http\Controllers\API\ProductGroupAPIController::class);


    Route::get('/product-expired-board', [App\Http\Controllers\API\ExpireProductAPIController::class, 'expiryBoard']);

    // Transfer Stock Routes
    // Warehouse to Outlet Stock Transfer
    Route::post('/warehouse-stock-products', [App\Http\Controllers\API\TransferProductsStockAPIController::class, 'getWarehouseStockProducts']);
    Route::post('/stock-transfer-warehouse-to-outlets', [App\Http\Controllers\API\TransferProductsStockAPIController::class, 'warehouseToOutletTransfer']);

    // Outlet to Outlet Transfer
    Route::post('/outlet-stock-products', [App\Http\Controllers\API\TransferProductsStockAPIController::class, 'getOutletStockProducts']);

    Route::get('best-salling', [App\Http\Controllers\API\SaleAPIController::class, 'sallingProducts']);


    Route::resource('sale_items', App\Http\Controllers\API\SaleItemAPIController::class);


    Route::resource('payment_collections', App\Http\Controllers\API\PaymentCollectionAPIController::class);

    Route::post('/stock-transfer-outlet-to-outlet', [App\Http\Controllers\API\TransferProductsStockAPIController::class, 'outletToOutletTransfer']);

    Route::post('/stock-transfer-outlet-to-warehouse', [App\Http\Controllers\API\TransferProductsStockAPIController::class, 'outletToWarehouseTransfer']);


    Route::get('/stock_transfers', [App\Http\Controllers\API\TransferProductsStockAPIController::class, 'index']);
    Route::get('/stock_transfers/{id}', [App\Http\Controllers\API\TransferProductsStockAPIController::class, 'show']);

    // Stock Routes
    Route::get('stocks/stock_management', [App\Http\Controllers\API\StockAPIController::class, 'stockManagement']);
    Route::get('stocks/latestStockData', [App\Http\Controllers\API\StockAPIController::class, 'latestStockData']);

    // Stock In/Out
    Route::post('stocks/stockInOut', [App\Http\Controllers\API\StockAPIController::class, 'stockInOut']);
    Route::post('stocks/stockBulkInOut', [App\Http\Controllers\API\StockAPIController::class, 'stockBulkInOut']);


    // Stock Adjustment
    Route::post('stocks/stockAdjustment', [App\Http\Controllers\API\StockAdjustmentAPIController::class, 'stockAdjustment']);
    Route::post('stocks/stockBulkAdjustment', [App\Http\Controllers\API\StockAdjustmentAPIController::class, 'stockBulkAdjustment']);


    // Warehouse Stock Routes
    Route::get('stocks/warehouse-stock-management', [App\Http\Controllers\API\WarehouseStockAPIController::class, 'warehouseStockManagement']);

    Route::post('stocks/warehouse-stock-adjustment', [App\Http\Controllers\API\WarehouseStockAdjustmentAPIController::class, 'warehouseStockAdjustment']);


    // Damage Products
    Route::resource('damage-products', \App\Http\Controllers\API\DamageProductController::class);

    Route::resource('coupons', App\Http\Controllers\API\CouponAPIController::class);


    Route::resource('points_settings', App\Http\Controllers\API\PointsSettingsAPIController::class);


    Route::resource('users_points', App\Http\Controllers\API\UsersPointsAPIController::class);


    // Inventory Management Routes
    Route::post('/inventory-board', [App\Http\Controllers\API\InventoryManagementController::class, 'inventoryBoard']);

    Route::post('/accounts/supplier-ledger', [App\Http\Controllers\API\SupplierLedgerAPIController::class, 'supplierLedger']);
    Route::post('supplierLedgersExport', [App\Http\Controllers\API\SupplierLedgerAPIController::class, 'supplierLedgersExport']);
    Route::resource('customer_ledgers', App\Http\Controllers\API\CustomerLedgerAPIController::class);
    Route::get('customerLedgersExport', [App\Http\Controllers\API\CustomerLedgerAPIController::class, 'customerLedgersExport']);

    Route::get('bank_accounts/list', [App\Http\Controllers\API\BankAccountAPIController::class, 'getList']);
    Route::resource('bank_accounts', App\Http\Controllers\API\BankAccountAPIController::class);

    Route::resource('discount_titles', App\Http\Controllers\API\DiscountTitleAPIController::class);


    Route::resource('discount_settings', App\Http\Controllers\API\DiscountSettingAPIController::class);
    Route::resource('loyalty_settings', App\Http\Controllers\API\DiscountSettingAPIController::class);


    Route::resource('sales_discounts', App\Http\Controllers\API\SalesDiscountAPIController::class);


    Route::resource('hold_sale_discounts', App\Http\Controllers\API\HoldSaleDiscountAPIController::class);


    Route::resource('hold_sales', App\Http\Controllers\API\HoldSaleAPIController::class);


    Route::get('hold_sale/list', [App\Http\Controllers\API\HoldSaleAPIController::class, 'list']);
    Route::get('hold_sale-helper', [App\Http\Controllers\API\HoldSaleAPIController::class, 'saleHelper']);
    Route::get('hold_saleInfo', [App\Http\Controllers\API\HoldSaleAPIController::class, 'saleInfo']);


    Route::resource('hold_sale_items', App\Http\Controllers\API\HoldSaleItemAPIController::class);


    Route::resource('sale_returns', App\Http\Controllers\API\SaleReturnAPIController::class);
    Route::get('sale_return/list', [App\Http\Controllers\API\SaleReturnAPIController::class, 'list']);

    Route::post('sale_returns_void', [App\Http\Controllers\API\SaleReturnAPIController::class,'saleReturnsVoid']);
    Route::post('sale_replaces', [App\Http\Controllers\API\SaleReturnAPIController::class,'storeSaleReplace']);
    //Route::post('sale_returns_replace', App\Http\Controllers\API\SaleReturnAPIController::class);


    Route::resource('sale_return_items', App\Http\Controllers\API\SaleReturnItemAPIController::class);


    //Route::resource('account_groups', App\Http\Controllers\API\AccountGroupAPIController::class);

    // Route account classes or groups
    Route::post('account_classes/getAccountClassCode', [App\Http\Controllers\API\AccountClassAPIController::class, 'getAccountClassCode']);
    Route::resource('account_classes', App\Http\Controllers\API\AccountClassAPIController::class);

    // Route Account Types
    Route::get('account_types/getParentTypes', [App\Http\Controllers\API\AccountTypeAPIController::class, 'getParentTypes']);
    Route::get('account_types/{group_id}/getParentTypes', [App\Http\Controllers\API\AccountTypeAPIController::class, 'getParentTypes']);
    Route::post('account_types/getTypesCode', [App\Http\Controllers\API\AccountTypeAPIController::class, 'getTypesCode']);
    Route::get('account_types/getAccountTypeList', [App\Http\Controllers\API\AccountTypeAPIController::class, 'getAccountTypeList']);
    Route::post('/account_types/getChartOfAccountsTypeOptions', [App\Http\Controllers\API\AccountTypeAPIController::class, 'getChartOfAccountsTypeOptions']);
    Route::post('/account_types/getChartOfAccountsOnlyDetailTypeOptions', [App\Http\Controllers\API\AccountTypeAPIController::class, 'getChartOfAccountsOnlyDetailTypeOptions']);
    Route::get('/account_types/list', [App\Http\Controllers\API\AccountTypeAPIController::class, 'getAccountTypesList']);

    Route::resource('account_types', App\Http\Controllers\API\AccountTypeAPIController::class);

    // Account Ledger Routes
    Route::get('/account_ledgers/getChartOfAccounts', [App\Http\Controllers\API\AccountLedgerAPIController::class, 'getChartOfAccounts']);
    Route::get('/account_ledgers/getChartOfAccountExcelExport', [App\Http\Controllers\API\AccountLedgerAPIController::class, 'getChartOfAccountExcelExport']);
    Route::get('/account_ledgers/getChartOfAccountsOption', [App\Http\Controllers\API\AccountLedgerAPIController::class, 'getChartOfAccountsOption']);
    Route::get('/account_ledgers/getChartOfAccountsOnlyLedgerOption', [App\Http\Controllers\API\AccountLedgerAPIController::class, 'getChartOfAccountsOnlyLedgerOption']);
    Route::post('/account_ledgers/getAccountCode', [App\Http\Controllers\API\AccountLedgerAPIController::class, 'getAccountCode']);
    Route::get('/account_ledgers/list', [App\Http\Controllers\API\AccountLedgerAPIController::class, 'getLedgerList']);
    Route::resource('account_ledgers', App\Http\Controllers\API\AccountLedgerAPIController::class);

    Route::get('entry_types/list', [App\Http\Controllers\API\EntryTypeAPIController::class, 'getEntryTypeList']);
    Route::resource('entry_types', App\Http\Controllers\API\EntryTypeAPIController::class);

    // Fiscal Years Route
    Route::get('fiscal_years/getActiveFiscalYear', [App\Http\Controllers\API\FiscalYearAPIController::class, 'getActiveFiscalYear']);
    Route::post('fiscal_years/status_update', [App\Http\Controllers\API\FiscalYearAPIController::class, 'updateStatus']);
    Route::get('fiscal_years/list', [App\Http\Controllers\API\FiscalYearAPIController::class, 'getFiscalYearList']);
    Route::resource('fiscal_years', App\Http\Controllers\API\FiscalYearAPIController::class);

    Route::get('cost_centers/list', [App\Http\Controllers\API\CostCenterAPIController::class, 'getCostCenterList']);
    Route::resource('cost_centers', App\Http\Controllers\API\CostCenterAPIController::class);

    Route::get('/account-default-setting', [App\Http\Controllers\API\AccountDefaultSettingsAPIController::class, 'getAccountDefaultSetting']);
    Route::post('/account-default-setting', [App\Http\Controllers\API\AccountDefaultSettingsAPIController::class, 'setAccountDefaultSetting']);

    // Bill Payment
    Route::post('/accounts/payment-bill', [App\Http\Controllers\API\AccountBillPaymentAPIController::class, 'billPayment']);
    Route::post('/accounts/getVoucherCode', [App\Http\Controllers\API\AccountVoucherAPIController::class, 'getVoucherCode']);
    Route::get('/account_vouchers/list', [App\Http\Controllers\API\AccountVoucherAPIController::class, 'getVoucherList']);
    Route::resource('account_vouchers', App\Http\Controllers\API\AccountVoucherAPIController::class);


    Route::resource('account_voucher_transactions', App\Http\Controllers\API\AccountVoucherTransactionAPIController::class);


    /** Account Posting Routes */
    Route::post('sales/sales-posting-list', [\App\Http\Controllers\API\SaleAccountPostingAPIController::class, 'saleAccountPostingList']);
    Route::post('accounts/sales-posting', [\App\Http\Controllers\API\SaleAccountPostingAPIController::class, 'storeSaleAccountPosting']);
    Route::post('accounts/cogs-posting-list', [\App\Http\Controllers\API\COGSAccountPostingAPIController::class, 'COGSAccountPostingList']);
    Route::post('accounts/cogs-posting', [\App\Http\Controllers\API\COGSAccountPostingAPIController::class, 'storeCOGSAccountPosting']);
    /** Account Posting Routes */

    Route::post('reports/daily-transaction', [\App\Http\Controllers\API\AccountReportAPIController::class, 'reportDailyTransaction']);
    Route::post('reports/ledger-report', [\App\Http\Controllers\API\AccountReportAPIController::class, 'reportLedgerTransaction']);
    Route::post('reports/cash-book-report', [\App\Http\Controllers\API\AccountReportAPIController::class, 'reportCashBookLedgerTransaction']);
    Route::post('reports/bank-book-report', [\App\Http\Controllers\API\AccountReportAPIController::class, 'reportBankBookLedgerTransaction']);
    Route::post('reports/cash-and-bank-book-report', [\App\Http\Controllers\API\AccountReportAPIController::class, 'reportCashAndBankBookLedgerTransaction']);

    Route::get('reports/trial-balance', [\App\Http\Controllers\API\AccountReportAPIController::class, 'reportTrialBalance']);
    Route::get('reports/profit-loss', [\App\Http\Controllers\API\AccountReportAPIController::class, 'reportProfitLoss']);
    Route::get('reports/balance-sheet', [\App\Http\Controllers\API\AccountReportAPIController::class, 'reportBalanceSheet']);


    Route::get('reports/stock-report', [App\Http\Controllers\API\ReportAPIController::class, 'getStockReport']);
    Route::get('reports/stock-report-excel-export', [App\Http\Controllers\API\ReportAPIController::class, 'stockReportExcelExport']);
    Route::get('reports/inventory-details-report-excel-export', [App\Http\Controllers\API\ReportAPIController::class, 'inventoryDetailsReportExcelExport']);
    Route::get('reports/low-stock-report', [App\Http\Controllers\API\ReportAPIController::class, 'getLowStockReport']);
    Route::get('reports/low-stock-report-excel-export', [App\Http\Controllers\API\ReportAPIController::class, 'lowStockReportExcelExport']);
    Route::get('reports/inventory-summary-report', [App\Http\Controllers\API\InventoryReportController::class, 'inventorySummaryReport'])->name('inventorySummaryReport');
    Route::get('reports/inventory-summary-report-excel-export', [App\Http\Controllers\API\InventoryReportController::class, 'inventorySummaryReportExcelExport'])->name('inventorySummaryReportExcelExport');

    Route::get('reports/daily-summary-report', [App\Http\Controllers\API\ReportAPIController::class, 'getDailySummaryReport']);

    Route::get('reports/purchase-report', [App\Http\Controllers\API\ReportPurchaseAPIController::class, 'getPurchaseReport']);
    Route::get('reports/purchase-report-excel-export', [App\Http\Controllers\API\ReportPurchaseAPIController::class, 'getPurchaseReportExcelExport']);
    Route::get('reports/product-purchase-report', [App\Http\Controllers\API\ReportPurchaseAPIController::class, 'getProductPurchaseReport']);
    Route::get('reports/product-purchase-report-excel-export', [App\Http\Controllers\API\ReportPurchaseAPIController::class, 'getProductPurchaseReportExcelExport']);

    Route::get('reports/daily-sales-report', [App\Http\Controllers\API\ReportSalesAPIController::class, 'getDailySalesReport']);
    Route::get('reports/daily-sales-report-excel-export', [App\Http\Controllers\API\ReportSalesAPIController::class, 'excelExportDailySalesReport']);
    Route::get('reports/sales-report', [App\Http\Controllers\API\ReportSalesAPIController::class, 'getSalesReport']);
    Route::get('reports/sales-report-excel-export', [App\Http\Controllers\API\ReportSalesAPIController::class, 'excelExportSalesReport']);
    Route::get('reports/collection-report', [App\Http\Controllers\API\ReportSalesAPIController::class, 'getCollectionReport']);
    Route::get('reports/product-sales-report', [App\Http\Controllers\API\ReportSalesAPIController::class, 'getProductSaleReport']);
    Route::get('reports/product-sales-report-excel-export', [App\Http\Controllers\API\ReportSalesAPIController::class, 'excelExportProductSalesReport']);

    Route::resource('general_settings', App\Http\Controllers\API\GeneralSettingAPIController::class);

});  

 
Route::get('customer-collection', [App\Http\Controllers\API\SaleAPIController::class, 'dueInvoices']);
Route::get('/annual-report', [App\Http\Controllers\API\DashboardAPIController::class, 'salesVsPurchases']); 

// });

// Route::get('reports/stock-report-excel-export', [App\Http\Controllers\API\ReportAPIController::class, 'stockReportExcelExport']);

//Route::get('reports/trial-balance', [\App\Http\Controllers\API\AccountReportAPIController::class, 'reportTrialBalance']);
//Route::get('reports/profit-loss', [\App\Http\Controllers\API\AccountReportAPIController::class, 'reportProfitLoss']);
//
// Route::get('reports/balance-sheet', [\App\Http\Controllers\API\AccountReportAPIController::class, 'reportBalanceSheet']);

// Route::get('customerLedgersExport', [App\Http\Controllers\API\CustomerLedgerAPIController::class, 'customerLedgersExport']);

