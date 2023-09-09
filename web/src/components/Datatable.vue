<template>
    <div class="table-responsive">
        <slot name="header"></slot> 
        <table class="table table-bordered table-centered mb-0 w-100 dt-responsive nowrap no-footer dtr-inline collapsed">            
            <thead class="table-light">
                <tr class="border success item-head">
                    <th v-for="column in columns" :key="column.name"
                        @click="sort(column.name, sortable)"  
                        :style="'width:'+column.width+';'+'cursor:pointer; height: 42px;'">
                        {{ column.label }} 
                        <img v-if="!column.isAction" :src="sortKey === column.name ? (iconSortPath ? iconSortPath : iconSortBothPath) : iconSortBothPath" >
                    </th>
                </tr>
            </thead>
            <slot name="body"></slot> 
        </table>
        <slot name="footer"></slot>
    </div>
</template>

<script>
import imgSortAsc from '../assets/images/sort_asc.png';
import imgSortDesc from '../assets/images/sort_desc.png';
import imgSortBoth from '../assets/images/sort_both.png';
    export default {
        props: ['columns', 'sortKey'],
        data() {
            return { 
                iconSortPath: imgSortDesc,
                sortable: 'ASC' , 
                iconSortBothPath: imgSortBoth, 
            }
        },
        emits:['sort'],
        methods: {
            sort(column, sortable){  
                if(column){
                    this.$emit('sort', column, sortable);                     
                    if(sortable =='ASC'){
                        this.iconSortPath = imgSortAsc;
                        this.sortable = 'DESC';
                    }  else {
                        this.iconSortPath = imgSortDesc;
                        this.sortable = 'ASC';
                    } 
                }
            } 
        },
        created() {  
        },
    }

</script>