<template>
    <transition>
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Transfer </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Product Stock Transfer</a></li>
                                
                            </ol>
                        </div>
                        <div class="page-title-right float-right "> 
                            <!-- <button type="button" class="btn btn-primary float-right" @click="toggleModal">
                              Add New
                            </button>  -->
                        </div>
                    </div>
                </div>
            </div>  

            <div class="row">
                <div class="col-md-12 ">
                    <div class="card">
                        <div class="card-header">
                            <h3 style="text-align: center;">Stock Transfer</h3>
                        </div>
                        <div class="card-body">
                            
                            <div class="tab-pane show active" v-if="loading">
                                <div class="row"> 
                                    <div class="col-md-5">  
                                    </div>
                                    <div class=" col-md-2"> 
                                        <img src="../../assets/image/loading.gif" alt="Loading..." style="width:130px">
                                    </div>
                                    <div class="col-md-5">  
                                    </div>
                                </div>
                            </div>

                            <div class="form-content" v-if="!loading">
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-check">
                                                    <input type="radio" id="wto" name="transfer_from" class="form-check-input" value="WTO"  @change="transferFrom($event)">
                                                    <label class="form-check-label" for="wto">Warehouse to Outlet</label>
                                                </div>
                                            </div>  

                                            <div class="col-md-4 mb-3">
                                                <div class="form-check">
                                                    <input type="radio" id="oto" name="transfer_from" class="form-check-input" value="OTO" checked @change="transferFrom($event)"> 
                                                    <label class="form-check-label" for="oto">Outlet to Outlet</label>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-check">
                                                    <input type="radio" id="otw" name="transfer_from" class="form-check-input" value="OTW" @change="transferFrom($event)"> 
                                                    <label class="form-check-label" for="otw">Outlet to Warehouse</label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input type="radio" id="all_product_outlet" name="searchAble" class="form-check-input" value="1" checked @change="searchAble($event)"> 
                                                    <label class="form-check-label" for="all_product_outlet">All Product by Warehouse/Outlet</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input type="radio" id="searchAble" name="searchAble" class="form-check-input" value="0" @change="searchAble($event)"> 
                                                    <label class="form-check-label" for="searchAble">Searchable</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- From Warehouse to Outlet -->
                                <div class="wto_form" v-if="is_wto_transfer">
                                    <!-- <form id="wto_transfer_form" @submit.prevent="submitWTOForm()"> -->
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="warehouse_id" class="form-label">From Warehouse *</label>
                                                <select class="form-select" id="warehouse_id" v-model="obj.warehouse_id" @change="onChangeFromTransfer($event.target.value, 'WTO'), onkeyPress('warehouse_id')">
                                                    <option value="">--- Select Warehouse ---</option>
                                                    <option v-for="(warehouse, index) in warehouses" :key="index" :value="warehouse.id">{{ warehouse.name }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-if="errors.warehouse_id">
                                                    {{errors.warhouse_id[0]}}
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="outlet_id" class="form-label">To Outlet *</label>
                                                <select class="form-select" id="outlet_id" v-model="obj.outlet_id" @change="onkeyPress('outlet_id')">
                                                    <option value="">--- Select Outlet ---</option>
                                                    <option v-for="(outlet, index) in outlets" :key="index" :value="outlet.id">{{ outlet.name }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-if="errors.outlet_id">
                                                    {{errors.outlet_id[0]}}
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="transfer_date" class="form-label">Date *</label>
                                                <input class="form-control" id="transfer_date" type="date" v-model="obj.transfer_date" @change="onkeyPress('transfer_date')">
                                                <div class="invalid-feedback" v-if="errors.transfer_date">
                                                    {{errors.transfer_date[0]}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <!-- Product Details -->
                                                <div class="card">
                                                    <div class="card-header text-left">
                                                        Product Details
                                                        <input type="hidden" v-model="obj.total_quantity">
                                                        <span class="total_quantity">Transfer Quantity: <b> {{ totalQuantity }} </b></span>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- <div class="mb-3">
                                                            <input type="text" class="form-control" id="search_product" placeholder="Search Product">
                                                        </div> -->
                                                        <div class="autocomplete form-group mb-3" v-if="!nonSearch"> 
                                                            <input type="text" class="form-control border" id="searchTerm" @change="onkeyPress('searchTerm')" @keyup="inputChanged" @keyup.enter="clickToAdd(searchCode)"  @keydown.down="onArrowDown" @keydown.up="onArrowUp" v-model="searchTerm" placeholder="Search By Product Code or Name" autocomplete="off">
                                                             
                                                            <ul
                                                                v-if="searchProducts.length"
                                                                class="w-full rounded bg-white border border-gray-300 px-4 py-2 space-y-1 absolute z-10 autocomplete-results"
                                                            >
                                                                <li>
                                                                Showing {{ searchProducts.length }} of {{ obj.product_items.length }} results
                                                                </li>
                                                                <li
                                                                    v-for="(item, i) in searchProducts"
                                                                    :key="i"
                                                                    @change="selectProduct(item)"
                                                                    @click="setResult(item)"
                                                                    :class="{ 'is-active': i === arrowCounter }"
                                                                >
                                                                {{ item.product_name }} ({{item.product_code}})
                                                                </li>
                                                            </ul> 

                                                        </div>

                                                        <div class="product_table">
                                                            <table id="basic-datatable" v-if="nonSearch" class="table dt-responsive nowrap w-100">
                                                                <thead class="tableFloatingHeaderOriginal">
                                                                    <tr class="border success item-head">
                                                                        <th> </th> 
                                                                        <th>Name </th> 
                                                                        <th>Barcode</th> 
                                                                        <th>UOM</th>
                                                                        <th>Pur. Price</th>
                                                                        <th>MRP</th>
                                                                        <th>Quantity</th>
                                                                        <th>Weight</th>
                                                                        <th>Trans Qty</th>
                                                                        <th>Trans WT</th>
                                                                        <th>Expires Date</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody v-if="obj.product_items.length > 0">
                                                                    <tr v-for="(product_item, i) in obj.product_items" :key="i">

                                                                        <td>
                                                                            <div class="form-check">
                                                                                <input type="checkbox" class="form-check-input" v-model="product_item.checked" @click="clickToAdd(product_item.product_code)">

                                                                                <!-- <span @click="clickToAdd(product_item.product_code)"> Click to Add</span> -->
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ product_item.product_name }}</td>
                                                                        <td>{{ product_item.product_code }}</td>
                                                                        <td>{{ product_item.unit_code }}</td>
                                                                        <td>{{ product_item.purchase_price }}</td>
                                                                        <td>{{ product_item.mrp_price }}</td>
                                                                        <td>{{ product_item.stock_quantity }}</td>
                                                                        <td>{{ product_item.stock_weight }}</td>
                                                                        
                                                                        <td><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.transfer_quantity" style="width: 50px"></td>
                                                                        <td><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.transfer_weight" style="width: 50px"></td> 
                                                                        <td>{{ product_item.expires_date ?? 'N/A' }}</td>
                                                                    </tr>
                                                                </tbody>
                                                                
                                                            </table>
                                                            <table id="basic-datatable" v-else  class="table dt-responsive nowrap w-100">
                                                                <thead class="tableFloatingHeaderOriginal">
                                                                    <tr class="border success item-head">
                                                                        <th> </th> 
                                                                        <th>Name </th> 
                                                                        <th>Barcode</th> 
                                                                        <th>UOM</th>
                                                                        <th>Pur. Price</th>
                                                                        <th>MRP</th>
                                                                        <th>Quantity</th>
                                                                        <th>Weight</th>
                                                                        <th>Trans Qty</th>
                                                                        <th>Trans WT</th>
                                                                        <th>Expires Date</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody v-if="obj.select_items.length > 0">
                                                                    <tr v-for="(product_item, i) in obj.select_items" :key="i">
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <input type="checkbox" class="form-check-input" v-model="product_item.checked">                     
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ product_item.product_name }}</td>
                                                                        <td>{{ product_item.product_code }}</td>
                                                                        <td>{{ product_item.unit_code }}</td>
                                                                        <td>{{ product_item.purchase_price }}</td>
                                                                        <td>{{ product_item.mrp_price }}</td>
                                                                        <td>{{ product_item.stock_quantity }}</td>
                                                                        <td>{{ product_item.stock_weight }}</td>
                                                                        
                                                                        <td><input type="text" class="form-control" @keyup="inputChange()" v-model="product_item.transfer_quantity" style="width: 50px"></td>
                                                                        <td><input type="text" class="form-control" @keyup="inputChange()" v-model="product_item.transfer_weight" style="width: 50px"></td> 
                                                                        <td>{{ product_item.expires_date ?? 'N/A' }}</td>
                                                                        <td><a style="font-size:20px" href="javascript:void(0);" class="text-danger" @click="deleteItem(i, product_item)"><i class="mdi mdi-delete-outline me-1"></i> </a></td>
                                                                    </tr>
                                                                </tbody>                                                       
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="buttons">
                                            <button type="button" class="btn btn-primary " :disabled="disabled" @click="submitWTOForm()">
                                                <span v-show="isSubmit">
                                                    <i class="fas fa-spinner fa-spin" ></i>
                                                </span>Transfer Stock 
                                            </button>
                                        </div>

                                    <!-- </form> -->
                                </div>

                                <!-- From Outlet to Outlet -->
                                <div class="oto_form" v-if="is_oto_transfer">
                                    <!-- <form id="oto_transfer_form" @submit.prevent="submitOTOForm()"> -->
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="from_outlet_id" class="form-label">From Outlet *</label>
                                                <select class="form-select" id="from_outlet_id" v-model="obj.from_outlet_id" @change="onChangeFromTransfer($event.target.value, 'OTO'), onkeyPress('from_outlet_id')">
                                                    <option value="">--- Select Outlet ---</option>
                                                    <option v-for="(outlet, index) in outlets" :key="index" :value="outlet.id">{{ outlet.name }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-if="errors.from_outlet_id">
                                                    {{errors.from_outlet_id[0]}}
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="to_outlet_id" class="form-label">To Outlet *</label>
                                                <select class="form-select" id="to_outlet_id" v-model="obj.to_outlet_id" @change="onkeyPress('to_outlet_id')">
                                                    <option value="">--- Select Outlet ---</option>
                                                    <option v-for="(outlet, index) in outlets" :key="index" :value="outlet.id">{{ outlet.name }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-if="errors.to_outlet_id">
                                                    {{errors.to_outlet_id[0]}}
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="trnasfer_date" class="form-label">Date *</label>
                                                <input class="form-control" id="trnasfer_date" type="date" v-model="obj.transfer_date" @change="onkeyPress('transfer_date')">
                                                <div class="invalid-feedback" v-if="errors.transfer_date">
                                                    {{errors.transfer_date[0]}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <!-- Product Details -->
                                                <div class="card">
                                                    <div class="card-header text-left">
                                                        Product Details
                                                        <input type="hidden" v-model="obj.total_quantity">
                                                        <span class="total_quantity">Transfer Quantity: <b> {{ totalQuantity }} </b></span>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="autocomplete form-group mb-3" v-if="!nonSearch"> 
                                                            <input type="text" class="form-control border" id="searchTerm" @change="onkeyPress('searchTerm')" @keyup="inputChanged" @keyup.enter="clickToAdd(searchCode)"  @keydown.down="onArrowDown" @keydown.up="onArrowUp" v-model="searchTerm" placeholder="Search By Product Code or Name" autocomplete="off">
                                                             
                                                            <ul
                                                                v-if="searchProducts.length"
                                                                class="w-full rounded bg-white border border-gray-300 px-4 py-2 space-y-1 absolute z-10 autocomplete-results"
                                                            >
                                                                <li>
                                                                Showing {{ searchProducts.length }} of {{ obj.product_items.length }} results
                                                                </li>
                                                                <li
                                                                    v-for="(item, i) in searchProducts"
                                                                    :key="i"
                                                                    @change="selectProduct(item)"
                                                                    @click="setResult(item)"
                                                                    :class="{ 'is-active': i === arrowCounter }"
                                                                >
                                                                {{ item.product_name }} ({{item.product_code}})
                                                                </li>
                                                            </ul> 

                                                        </div>
                                                        <!-- <div class="mb-3">
                                                            <input type="text" class="form-control" id="search_product" placeholder="Search Product">
                                                        </div> -->
                                                        <div class="product_table">
                                                            <table id="basic-datatable" v-if="nonSearch" class="table dt-responsive nowrap w-100">
                                                                <thead class="tableFloatingHeaderOriginal">
                                                                    <tr class="border success item-head">
                                                                        <th> </th> 
                                                                        <th>Name </th> 
                                                                        <th>Barcode</th> 
                                                                        <th>UOM</th>
                                                                        <th>Pur. Price</th>
                                                                        <th>MRP</th>
                                                                        <th>Quantity</th>
                                                                        <th>Weight</th>
                                                                        <th>Trans Qty</th>
                                                                        <th>Trans WT</th>
                                                                        <th>Expires Date</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody v-if="obj.product_items.length > 0">
                                                                    <tr v-for="(product_item, i) in obj.product_items" :key="i">

                                                                        <td>
                                                                            <div class="form-check">
                                                                                <input type="checkbox" class="form-check-input" v-model="product_item.checked" @click="clickToAdd(product_item.product_code)">

                                                                                <!-- <span @click="clickToAdd(product_item.product_code)"> Click to Add</span> -->
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ product_item.product_name }}</td>
                                                                        <td>{{ product_item.product_code }}</td>
                                                                        <td>{{ product_item.unit_code }}</td>
                                                                        <td>{{ product_item.purchase_price }}</td>
                                                                        <td>{{ product_item.mrp_price }}</td>
                                                                        <td>{{ product_item.stock_quantity }}</td>
                                                                        <td>{{ product_item.stock_weight }}</td>
                                                                        
                                                                        <td><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.transfer_quantity" style="width: 50px"></td>
                                                                        <td><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.transfer_weight" style="width: 50px"></td> 
                                                                        <td>{{ product_item.expires_date ?? 'N/A' }}</td>
                                                                    </tr>
                                                                </tbody>
                                                                
                                                            </table>
                                                            <table id="basic-datatable" v-else  class="table dt-responsive nowrap w-100">
                                                                <thead class="tableFloatingHeaderOriginal">
                                                                    <tr class="border success item-head">
                                                                        <th> </th> 
                                                                        <th>Name </th> 
                                                                        <th>Barcode</th> 
                                                                        <th>UOM</th>
                                                                        <th>Pur. Price</th>
                                                                        <th>MRP</th>
                                                                        <th>Quantity</th>
                                                                        <th>Weight</th>
                                                                        <th>Trans Qty</th>
                                                                        <th>Trans WT</th>
                                                                        <th>Expires Date</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody v-if="obj.select_items.length > 0">
                                                                    <tr v-for="(product_item, i) in obj.select_items" :key="i">
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <input type="checkbox" class="form-check-input" v-model="product_item.checked">                     
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ product_item.product_name }}</td>
                                                                        <td>{{ product_item.product_code }}</td>
                                                                        <td>{{ product_item.unit_code }}</td>
                                                                        <td>{{ product_item.purchase_price }}</td>
                                                                        <td>{{ product_item.mrp_price }}</td>
                                                                        <td>{{ product_item.stock_quantity }}</td>
                                                                        <td>{{ product_item.stock_weight }}</td>
                                                                        
                                                                        <td><input type="text" class="form-control" @keyup="inputChange()" v-model="product_item.transfer_quantity" style="width: 50px"></td>
                                                                        <td><input type="text" class="form-control" @keyup="inputChange()" v-model="product_item.transfer_weight" style="width: 50px"></td> 
                                                                        <td>{{ product_item.expires_date ?? 'N/A' }}</td>
                                                                        <td><a style="font-size:20px" href="javascript:void(0);" class="text-danger" @click="deleteItem(i, product_item)"><i class="mdi mdi-delete-outline me-1"></i> </a></td>
                                                                    </tr>
                                                                </tbody>                                                       
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="buttons">
                                            <button type="button" class="btn btn-primary " :disabled="disabled" @click="submitOTOForm()">
                                                <span v-show="isSubmit">
                                                    <i class="fas fa-spinner fa-spin" ></i>
                                                </span>Transfer Stock 
                                            </button>
                                        </div>
                                    <!-- </form> -->
                                </div>


                                <div class="otw_form" v-if="is_otw_transfer">
                                    <!-- <form id="otw_transfer_form" @submit.prevent="submitOTWForm()"> -->
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="otw_outlet_id" class="form-label">From Outlet *</label>
                                                <select class="form-select" id="otw_outlet_id" v-model="obj.otw_outlet_id" @change="onChangeFromTransfer($event.target.value, 'OTW'), onkeyPress('otw_outlet_id')">
                                                    <option value="">--- Select Outlet ---</option>
                                                    <option v-for="(outlet, index) in outlets" :key="index" :value="outlet.id">{{ outlet.name }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-if="errors.otw_outlet_id">
                                                    {{errors.otw_outlet_id[0]}}
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="otw_warehouse_id" class="form-label">To Warehouse *</label>
                                                <select class="form-select" id="otw_warehouse_id" v-model="obj.otw_warehouse_id" @change="onkeyPress('otw_warehouse_id')">
                                                    <option value="">--- Select Warehouse ---</option>
                                                    <option v-for="(warehouse, index) in warehouses" :key="index" :value="warehouse.id">{{ warehouse.name }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-if="errors.otw_warehouse_id">
                                                    {{errors.otw_warhouse_id[0]}}
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="trnasfer_date" class="form-label">Date *</label>
                                                <input class="form-control" id="trnasfer_date" type="date" v-model="obj.transfer_date" @change="onkeyPress('transfer_date')">
                                                <div class="invalid-feedback" v-if="errors.transfer_date">
                                                    {{errors.transfer_date[0]}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <!-- Product Details -->
                                                <div class="card">
                                                    <div class="card-header text-left">
                                                        Product Details
                                                        <input type="hidden" v-model="obj.total_quantity">
                                                        <span class="total_quantity">Transfer Quantity: <b> {{ totalQuantity }} </b></span>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="autocomplete form-group mb-3" v-if="!nonSearch"> 
                                                            <input type="text" class="form-control border" id="searchTerm" @change="onkeyPress('searchTerm')" @keyup="inputChanged" @keyup.enter="clickToAdd(searchCode)"  @keydown.down="onArrowDown" @keydown.up="onArrowUp" v-model="searchTerm" placeholder="Search By Product Code or Name" autocomplete="off">
                                                             
                                                            <ul
                                                                v-if="searchProducts.length"
                                                                class="w-full rounded bg-white border border-gray-300 px-4 py-2 space-y-1 absolute z-10 autocomplete-results"
                                                            >
                                                                <li>
                                                                Showing {{ searchProducts.length }} of {{ obj.product_items.length }} results
                                                                </li>
                                                                <li
                                                                    v-for="(item, i) in searchProducts"
                                                                    :key="i"
                                                                    @change="selectProduct(item)"
                                                                    @click="setResult(item)"
                                                                    :class="{ 'is-active': i === arrowCounter }"
                                                                >
                                                                {{ item.product_name }} ({{item.product_code}})
                                                                </li>
                                                            </ul> 

                                                        </div>
                                                        <!-- <div class="mb-3">
                                                            <input type="text" class="form-control" id="search_product" placeholder="Search Product">
                                                        </div> -->
                                                        <div class="product_table">
                                                            <table id="basic-datatable" v-if="nonSearch" class="table dt-responsive nowrap w-100">
                                                                <thead class="tableFloatingHeaderOriginal">
                                                                    <tr class="border success item-head">
                                                                        <th> </th> 
                                                                        <th>Name </th> 
                                                                        <th>Barcode</th> 
                                                                        <th>UOM</th>
                                                                        <th>Pur. Price</th>
                                                                        <th>MRP</th>
                                                                        <th>Quantity</th>
                                                                        <th>Weight</th>
                                                                        <th>Trans Qty</th>
                                                                        <th>Trans WT</th>
                                                                        <th>Expires Date</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody v-if="obj.product_items.length > 0">
                                                                    <tr v-for="(product_item, i) in obj.product_items" :key="i">

                                                                        <td>
                                                                            <div class="form-check">
                                                                                <input type="checkbox" class="form-check-input" v-model="product_item.checked" @click="clickToAdd(product_item.product_code)">

                                                                                <!-- <span @click="clickToAdd(product_item.product_code)"> Click to Add</span> -->
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ product_item.product_name }}</td>
                                                                        <td>{{ product_item.product_code }}</td>
                                                                        <td>{{ product_item.unit_code }}</td>
                                                                        <td>{{ product_item.purchase_price }}</td>
                                                                        <td>{{ product_item.mrp_price }}</td>
                                                                        <td>{{ product_item.stock_quantity }}</td>
                                                                        <td>{{ product_item.stock_weight }}</td>
                                                                        
                                                                        <td><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.transfer_quantity" style="width: 50px"></td>
                                                                        <td><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.transfer_weight" style="width: 50px"></td> 
                                                                        <td>{{ product_item.expires_date ?? 'N/A' }}</td>
                                                                    </tr>
                                                                </tbody>
                                                                
                                                            </table>
                                                            <table id="basic-datatable" v-else  class="table dt-responsive nowrap w-100">
                                                                <thead class="tableFloatingHeaderOriginal">
                                                                    <tr class="border success item-head">
                                                                        <th> </th> 
                                                                        <th>Name </th> 
                                                                        <th>Barcode</th> 
                                                                        <th>UOM</th>
                                                                        <th>Pur. Price</th>
                                                                        <th>MRP</th>
                                                                        <th>Quantity</th>
                                                                        <th>Weight</th>
                                                                        <th>Trans Qty</th>
                                                                        <th>Trans WT</th>
                                                                        <th>Expires Date</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody v-if="obj.select_items.length > 0">
                                                                    <tr v-for="(product_item, i) in obj.select_items" :key="i">
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <input type="checkbox" class="form-check-input" v-model="product_item.checked">                     
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ product_item.product_name }}</td>
                                                                        <td>{{ product_item.product_code }}</td>
                                                                        <td>{{ product_item.unit_code }}</td>
                                                                        <td>{{ product_item.purchase_price }}</td>
                                                                        <td>{{ product_item.mrp_price }}</td>
                                                                        <td>{{ product_item.stock_quantity }}</td>
                                                                        <td>{{ product_item.stock_weight }}</td>
                                                                        
                                                                        <td><input type="text" class="form-control" @keyup="inputChange()" v-model="product_item.transfer_quantity" style="width: 50px"></td>
                                                                        <td><input type="text" class="form-control" @keyup="inputChange()" v-model="product_item.transfer_weight" style="width: 50px"></td> 
                                                                        <td>{{ product_item.expires_date ?? 'N/A' }}</td>
                                                                        <td><a style="font-size:20px" href="javascript:void(0);" class="text-danger" @click="deleteItem(i, product_item)"><i class="mdi mdi-delete-outline me-1"></i> </a></td>
                                                                    </tr>
                                                                </tbody>                                                       
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="buttons">
                                            <button type="button" class="btn btn-primary " :disabled="disabled" @click="submitOTWForm()">
                                                <span v-show="isSubmit">
                                                    <i class="fas fa-spinner fa-spin" ></i>
                                                </span>Transfer Stock 
                                            </button>
                                        </div>
                                    <!-- </form> -->

                                </div>
                            </div>

                        </div> 
                    </div>
                </div>
            </div>

            <!-- Modal -->  
            <Modal @close="toggleModal()" :modalActive="modalActive">
                <div class="modal-content scrollbar-width-thin">
                    <div class="modal-header"> 
                        <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    </div>
                    <div class="modal-body">
                        
                    </div>

                </div>
            </Modal>   
        </div>
    </transition>
</template>
<script>
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import Form from "vform";
import axios from 'axios';

export default {
    name: 'PosLeftbar',
    components: {
        Modal
    },
    data() {
        return {
            loading: true,
            showModal: false,
            modalActive:false,
            errors: {},
            isSubmit: false,
            disabled: true,
            warehouses: [],
            outlets: [],
            items: [],
            is_wto_transfer: false,
            is_oto_transfer: true,
            is_otw_transfer: false,
            nonSearch: true,
            obj: new Form({
                warehouse_id: '',
                outlet_id: '',
                from_outlet_id: '',
                to_outlet_id: '',
                otw_outlet_id: '',
                otw_warehouse_id: '',
                transfer_date: '',
                product_items: [],
                select_items: [],
                total_quantity:'',

            }),
            searchProducts: [],
            product_items: [],
            product_list: [],
            product_code: '',
            searchTerm: '',
            searchCode: '',
            arrowCounter: -1,
        };
    },
    created() {
        this.fetchWarehouse();
        this.fetchOutlets();

        setTimeout(() => {
            this.loading = false;
        }, 1000);
    },
    
    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;
            this.errors = '';
            this.isSubmit = false;
            console.log('then',this.isSubmit)
        },

        fetchWarehouse() {
            axios.get(this.apiUrl+'/warehouses', this.headerjson)
            .then((res) => {
                this.warehouses = res.data.data;
            })
            .catch()
        },

        fetchOutlets() {
            axios.get(this.apiUrl+'/outlets', this.headerjson)
            .then((res) => {
                this.outlets = res.data.data;
            })
            .catch()
        },  

        transferFrom(event) {
            let transferStockFrom = event.target.value;
            if(transferStockFrom == "WTO") {
                this.is_wto_transfer = true;
                this.is_oto_transfer = false;
                this.is_otw_transfer = false;
            }else if(transferStockFrom == "OTO"){
                this.is_wto_transfer = false;
                this.is_oto_transfer = true;
                this.is_otw_transfer = false;
            }else if(transferStockFrom == "OTW"){
                this.is_wto_transfer = false;
                this.is_oto_transfer = false;
                this.is_otw_transfer = true;
            }else{
                this.is_wto_transfer = false;
                this.is_oto_transfer = false;
                this.is_otw_transfer = false;
            }
            this.obj.reset();
        },
        searchAble(event) { 
            if(event.target.value == "1") {
                this.nonSearch = true; 
            } else{
                this.nonSearch = false; 
            }
            this.obj.reset();
        },

        onChangeFromTransfer(value, type){

            if(value != ''){
                if(type == "WTO") {
                    let warehouse_id = value;
                    this.fetchWarehouseProduct(warehouse_id);
                }else if(type == "OTO") {
                    let outlet_id = value;
                    this.fetchOutletProduct(outlet_id);
                }else if(type == "OTW") {
                    let outlet_id = value;
                    this.fetchOutletProduct(outlet_id);
                }else{
                    this.obj.product_items = [];
                }
            }else{
               this.obj.product_items = []; 
            } 
        },

        fetchWarehouseProduct(warehouse_id){
            let data = {warehouse_id: warehouse_id};
            axios.post(this.apiUrl+'/warehouse-stock-products', data, this.headerjson)
            .then((res) => {
                this.obj.product_items = res.data.data;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        },

        fetchOutletProduct(outlet_id){
            let data = {outlet_id: outlet_id};
            axios.post(this.apiUrl+'/outlet-stock-products', data, this.headerjson)
            .then((res) => {
                this.obj.product_items = res.data.data;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        },

        inputChange() {
            this.obj.total_quantity = this.totalQuantity;
        },

        validation: function (...fiels){ 
            var obj = new Object(); 
            var validate = false;
            for (var k in fiels){     // Loop through the object  
                for (var j in this.obj){  
                    if((j==fiels[k]) && (!this.form[j])) {  
                        obj[fiels[k]] = fiels[k].replace("_", " ")+' field is required';  // Delete obj[key]; 
                        this.errors = {...this.errors, ...obj};
                    }else{
                        validate = false;
                    }
                }              
            }  
            // var obj = new Object();
            // obj[fiels] = fiels.replace("_", " ")+' field is required';  
            // this.errors = {...this.errors, ...obj}; 
        },

        onkeyPress: function(field) { 
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },

        // For Warehouse to Outlet Stock Transfer
        submitWTOForm: function(e) {
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();
            formData.append("warehouse_id", this.obj.warehouse_id);
            formData.append("outlet_id", this.obj.outlet_id);
            formData.append("transfer_date", this.obj.transfer_date);
            if(this.nonSearch){ 
                formData.append("transfer_products", JSON.stringify(this.obj.product_items));
            }else{
                formData.append("transfer_products", JSON.stringify(this.obj.select_items));
            } 

            var postEvent = axios.post(this.apiUrl+'/stock-transfer-warehouse-to-outlets', formData, this.headers);

            postEvent.then((res) => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.obj.reset();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }
            })
            .catch((err) => {
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
            });
        },

        // For Outlet to Outlet Stock Transfer 
        submitOTOForm: function(e) {
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();
            formData.append("from_outlet_id", this.obj.from_outlet_id);
            formData.append("to_outlet_id", this.obj.to_outlet_id);
            formData.append("transfer_date", this.obj.transfer_date);
            if(this.nonSearch){ 
                formData.append("transfer_products", JSON.stringify(this.obj.product_items));
            }else{
                formData.append("transfer_products", JSON.stringify(this.obj.select_items));
            } 
            var postEvent = axios.post(this.apiUrl+'/stock-transfer-outlet-to-outlet', formData, this.headers);
            postEvent.then((res) => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.obj.reset();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }
            })
            .catch((err) => {
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
            });
        },

        // For Outlet to Warehouse Stock Transfer 
        submitOTWForm: function(e) {
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();
            formData.append("otw_outlet_id", this.obj.otw_outlet_id);
            formData.append("otw_warehouse_id", this.obj.otw_warehouse_id);
            formData.append("transfer_date", this.obj.transfer_date);
            if(this.nonSearch){ 
                formData.append("transfer_products", JSON.stringify(this.obj.product_items));
            }else{
                formData.append("transfer_products", JSON.stringify(this.obj.select_items));
            } 
            var postEvent = axios.post(this.apiUrl+'/stock-transfer-outlet-to-warehouse', formData, this.headers);
            postEvent.then((res) => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.obj.reset();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }
            })
            .catch((err) => {
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
            });
        },


        clickToAdd: function(code){            
            let chekExits = this.obj.select_items.find((e) => e.product_code == code); 
            if(!chekExits){
                let getPro = this.obj.product_items.find((e) => e.product_code == code);
                getPro.checked = true;
                this.obj.select_items.push(getPro);
            } else{
                this.$toast.error('Already Exits!');
            }         
            
            this.searchTerm = '';
            this.searchCode = '';
        },

        deleteItem: function(index, item){
            let chekExits = this.obj.select_items.find((e) => e.product_code == item.product_code); 
            console.log('chekExits', chekExits)
            //this.obj.select_items.pop(chekExits)
            this.obj.select_items.splice(index, 1)
            // this.obj.select_items.splice(0,chekExits);
            //let chekExits = this.obj.select_items.find((e) => e.product_code == code); 
            // if(!chekExits){
            //     let getPro = this.obj.product_items.find((e) => e.product_code == code);
            //     getPro.checked = true;
            //     this.obj.select_items.pop(getPro);
            // } else{
            //     this.$toast.error('Already Exits!');
            // }  
        },

        getProductByCode(){

            if(this.product_code == '') {
                this.product_code = this.searchTerm;
            }
            let data = {code: this.product_code};
            if(!this.checkExistsProduct(this.product_items, this.product_code)) {
                axios.post(this.apiUrl+'/store_requisitions/getProduct', data, this.headerjson)
                .then((res) => {
                    this.product_code = '';
                    this.searchTerm = '';
                    this.arrowCounter = -1;
                    this.product_items.push(res.data.data);

                })
                .catch((err) => {
                    this.$toast.error(err.response.data.message);
                });
            }else{
                this.product_code = '';
                this.searchTerm = '';
                this.arrowCounter = -1;
                this.$toast.error("This Product Already Added!");
            }

        },

        inputChanged(event) {
            
            if (event.code == "ArrowUp" || event.code == "ArrowDown")
                return;
                
            this.searchProducts = [];

            if (event.code == "Enter")
                return;
            if(this.searchTerm == '') 
                return;
            

            var filtered = this.obj.product_items.filter(item => {
                let item_data = ''; 
                item_data = item.product_name +' ('+ item.product_code + ')';
                return item_data.toLowerCase().match(this.searchTerm.toLowerCase());

            });

            // this.isOpen = true
            console.log('filtered', filtered)
            this.searchProducts.push(...filtered);
            if(this.arrowCounter == -1) {
                return;
            }
            let match_data = this.searchProducts[this.arrowCounter].product_name +' ('+ this.searchProducts[this.arrowCounter].product_code +')';
            console.log('match_data', match_data)
            if(match_data != this.searchTerm) {
                this.arrowCounter = -1;
            }

        },

        selectProduct(item) { 
            // this.arrowCounter = -1;
            this.product_code = item.product_code;
            this.searchTerm = item.product_name +' ('+ item.product_code + ')';
            this.searchCode = item.product_code;
            // this.getProductByCode();
        }, 

        setResult(item) { 
            // this.arrowCounter = -1;
            this.product_code = item.product_code;
            this.searchTerm = item.product_name +' ('+ item.product_code + ')';
            //this.getProductByCode();
            this.clickToAdd(item.product_code);
            this.searchProducts = []; 
            this.searchTerm='';
        }, 

        checkExistsProduct(array_data, product_code){
            let productExists = false;
            array_data.map((item) => {
                if(item.product_code == product_code) {
                    productExists = true;
                }
            });

            return productExists;
        },

        inputChange()
        {
            this.obj.total_quantity = this.totalQuantity;
            this.obj.total_value = this.totalValue;
            this.obj.total_amount = this.totalAmount;
        },

        removeProductListItem(row_id) 
        {
            
            if(this.product_items[row_id]) {
                this.product_items.splice(row_id, 1);
            }else{
                this.$toast.error("Oop's, something went wrong please try again!");
            }

        },
        
        onArrowDown: function(event) {
            if (this.searchProducts.length > 0) {
                this.arrowCounter = event.code == "ArrowDown" ? ++this.arrowCounter : --this.arrowCounter;
                if (this.arrowCounter >= this.searchProducts.length)
                this.arrowCounter = (this.arrowCounter) % this.searchProducts.length;
                else if (this.arrowCounter < 0)
                this.arrowCounter = this.searchProducts.length + this.arrowCounter;
                this.selectProduct(this.searchProducts[this.arrowCounter]);
            }
        },
        
        onArrowUp: function(event) {
            if (this.searchProducts.length > 0) {
                this.arrowCounter = event.code == "ArrowUp" ? --this.arrowCounter : ++this.arrowCounter;
                if (this.arrowCounter >= this.searchProducts.length)
                this.arrowCounter = (this.arrowCounter) % this.searchProducts.length;
                else if (this.arrowCounter < 0)
                this.arrowCounter = this.searchProducts.length + this.arrowCounter;
                this.selectProduct(this.searchProducts[this.arrowCounter]);
            }
        },


    },
    

    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {
        totalQuantity: function(){ 
            return this.obj.product_items.reduce(function(total, item){
                if(item.checked) {
                    return total + parseFloat(item.transfer_quantity); 
                }else{
                    return total + 0;
                }
            },0);
        },
    },
    watch: {
    'obj.product_items': {
        handler: function(val, oldVal) {
            if(val.length > 0){
                this.disabled = false;
            }else{
                this.disabled = true;
            }
            // this.disabled = this.checkQtyValue;
        },
        deep: true
        }
    },
}
</script>
<style scoped>
.modal-content.scrollbar-width-thin { 
    width: 90%;
    display: block;
    margin: 0 auto;
}

label {
    display: inline-block;
    margin: 0px 0px 4px 2px;
    float: left;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #dc3545;
    text-align: left;
}

.actions a{
    margin-right: 5px;
}

.total_quantity {
    float: right;
    color: red;
}


/** Autocomplete */
.autocomplete {
  position: relative;
}

ul.autocomplete-results {
  width: 100% !important;
  margin-top: 1px !important;
  position: absolute;
  left: 0;
  z-index: 999;
}
.autocomplete-results {
  padding: 0;
  margin: 0;
  border: 1px solid #eeeeee;
  /* height: 120px; */
  overflow: auto;
  width: 100%;
}

.autocomplete-results li{
  list-style: none;
  text-align: left;
  padding: 4px 2px;
  cursor: pointer;
  /* background: red; */
}

.autocomplete-results li.is-active,
.autocomplete-results li:hover {
  background-color: #4AAE9B;
  color: white;
}
.autocomplete-results li:first-child {
  font-weight: 600;
  border-bottom: 1px solid #282828;
  margin-bottom: 3px;
}
.autocomplete-results li:first-child:hover {
  background-color: inherit;
  color: inherit;
}

.is-active {
  background-color: #dedede;
}
</style>