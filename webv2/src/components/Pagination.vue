<template>
    <div class="pagination-section">
        <div style="position: absolute;"> {{formatLang.labelInfo1}} {{pagination.from}} {{formatLang.labelInfo2}} {{pagination.to}} {{formatLang.labelInfo3}} {{pagination.total}} {{formatLang.labelInfo4}}</div>
        <nav>
            <ul class="pagination">
                <template v-for="(link, index) in pagination.links" :key="index">
                    <li v-if="!link.url" class="page-item disabled" >
                        <a 
                            href="javascript:void(0)"
                            v-if="!link.url"
                            class="page-link"                            
                            v-html="link.label"
                        ></a>
                    </li>

                    <li v-else class="page-item" :class="{'active' : link.active}">
                        <a
                            class="page-link"
                            @click="selectPage(link.url)"
                            href="javascript:void(0)"
                            v-html="link.label"
                        ></a>
                    </li>
                </template>
            </ul>
        </nav> 
    </div>
</template>

<script>
    export default { 
        name: 'AppPagination',
        props: {
            pagination: Object, 
            language: Object
        },   
        emits:['onChangePage'], 
        methods: {
            selectPage(url){  
                this.$emit('onChangePage', {
                    url,
                    isPrevious: this.pagination.links[0] ? true : false,
                    isNext: this.pagination.links[this.pagination.links.length - 1] ? true : false,
                    isPaginate: (!this.isPrevious && !this.isNext) ? true : false,
                });
            },
            
        },
        computed: {
            formatLang(){
                const labelSplit = this.language.info.split('_FROM_').join(',').split('_TO_').join(',').split('_TOTAL_').join(',').split(',');
                
                return {
                    labelInfo1: labelSplit.length > 0 ? labelSplit[0] : null,
                    labelInfo2: labelSplit.length > 1 ? labelSplit[1] : null,
                    labelInfo3: labelSplit.length > 2 ? labelSplit[2] : null,
                    labelInfo4: labelSplit.length > 3 ? labelSplit[3] : null,
                };
            }
        }
    }
</script>

<style scoped>
    .pagination-section{
        margin-top:15px;
    }
    .pagination {
        justify-content: flex-end !important;
        
    }
    .page-stats {
        align-items: center;  
        padding: 7px;  
    }

    i {
        color: #3273dc !important;
    }

    a.button.pagination-previous {
        margin-right: 7px;
    }
    a.button.pagination-previous[disabled^="true"], a.button.pagination-next[disabled^="true"] {
        pointer-events: none;
        opacity: 0.5;
    }

</style>