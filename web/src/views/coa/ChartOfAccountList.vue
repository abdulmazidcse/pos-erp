<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Accounts</li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Chart Of Account List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right">                       
                        <a href="javascript:void(0);" style="margin-left: 5px;" class="btn-sm btn btn-primary" @click.prevent="printItem(item)" ><i class="mdi mdi-printer-outline me-1"></i> </a>
                        <a href="javascript:void(0);" style="margin-left: 5px;" class="btn-sm btn btn-primary"
                        @click.prevent="downloading ? null : exportToExcel()">
                            <span v-show="downloading"  >
                                <i class="fas fa-spinner fa-spin"></i>
                            </span> 
                            <i class="mdi mdi-file-excel me-1"></i> 
                        </a> 
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12"> 
                <div class="col-md-10">
                    <div class="row">  
                        <div class="col-md-6">
                            <div class="">
                                <label for="outlet_id"> Company </label>
                                <select class="form-control" @change="fetchGroupData($event.target.value)">
                                    <option value="">--- Select Company ---</option>
                                    <option v-for="(company, i) in companies" :key="i" :value="company.id">{{ company.name }}</option>
                                </select>
                            </div>
                        </div> 
                    </div>
                </div> 
            </div>
        </div>


        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-centered table-nowrap w-100" v-if="!loading">
                            <thead class="table-light">
                                <tr class="border success item-head">
                                    <th width="45%" class="text-center">Name </th>
                                    <th width="10%" class="text-center">Code</th>
                                    <th width="5%" class="text-center">Level</th>
                                    <th width="20%" class="text-center">Account Type</th>
                                </tr>
                            </thead>
                            <ChartOfAccountGrid v-for="(item, i) in items" :key="i" :account="item" :level="1" :spaces="5"></ChartOfAccountGrid>
                        </table>

                        <div class="tab-pane show active" v-if="loading">
                            <div class="row">
                                <div class="col-md-5"></div>
                                <div class="col-md-2">
                                    <img src="../../assets/image/loading.gif" alt="Loading..." style="width: 130px;">
                                </div>
                                <div class="col-md-5"></div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <Modal @close="toggleModal()"  >
            <div class="modal-content scrollbar-width-thin orderPreview" >
                <div class="modal-header"> 
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    <h3 style="width: 100%"> Chart Of Account List</h3>
                </div>
                <div class="modal-body " id="printArea" >
                    <div class="table-responsive product_table"> 
                        <table class="table table-bordered table-centered table-nowrap w-100" v-if="!loading">
                            <thead class="table-light">
                                <tr class="border success item-head">
                                    <th width="45%" class="text-center">Name </th>
                                    <th width="10%" class="text-center">Code</th>
                                    <th width="5%" class="text-center">Level</th>
                                    <th width="20%" class="text-center">Account Type</th>
                                </tr>
                            </thead>
                            <ChartOfAccountGrid v-for="(item, i) in items" :key="i" :account="item" :level="1" :spaces="5"></ChartOfAccountGrid>
                        </table>
                    </div>
                </div>
            </div>
        </Modal>
    </div>
    </transition>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios';

import ChartOfAccountGrid from '@/components/ChartOfAccountGrid';
export default {
    name: 'PosLeftbar',
    components: {
        Modal,
        ChartOfAccountGrid,
    },
    data() {
        return {
            loading: true,
            errors: {},
            items: [],
            companies: [],
        };
    },
    created() {
        this.fetchCompanies();
        this.fetchCOAData();
    },
    methods: { 
        
        fetchCOAData() { 
            axios.get(this.apiUrl+'/account_ledgers/getChartOfAccounts', this.headerjson)
            .then((res) => {
                this.items = res.data.data.accounts;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },
        fetchCompanies() {   
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => {
                console.log('res', res.data.data)
                this.companies = res.data.data;
            }).catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },
        printItem: function () {   
            this.printContent('printArea');
        },

        printContent(document_id) { 
            const options = {
                name: '_blank',
                specs: ['fullscreen=yes', 'titlebar=yes', 'scrollbars=yes'],
                styles: [ 
                    this.baseUrlPrintCSS+'/assets/css/bootstrap-print.min.css',
                    this.baseUrlPrintCSS+'/assets/css/print.css'
                ],
            };
            this.$htmlToPaper(document_id, options);
        },
        async exportToExcel() {  
            this.downloading = true;  
            try {
                const response = await axios.get(`${this.apiUrl}/account_ledgers/getChartOfAccountExcelExport`, {
                responseType: 'blob', // Important: set the response type to 'blob'
                headers: {
                    'Authorization' : this.$store.getters.token ? `Bearer ${this.$store.getters.token}` : "",
                    Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // Specify the expected content type
                }
                });

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'Product-Sales-Report.xlsx');
                document.body.appendChild(link);
                link.click();
                this.downloading = false;
            } catch (error) { 
                this.downloading = false;
            }
        },

    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {}
}
</script>
<style scoped>

</style>