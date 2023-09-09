<template>
    <ul :class="accountLevel">
        <li :class="(account.parent_id == 0) ? 'master_head' : 'children_head'">
            <i v-if="hasChildren" :class="'expandBtn '+ toggleChildrenIcon" @click="toggleChildren" @keypress="toggleChildren"></i> 
            <i v-else class="expandBtn mdi mdi-stop"></i> 
            <a href="javascript:void(0)" class="text-item" style="text-transform:uppercase;">
                [{{ account.code }}] {{ account.name }} 
                <span class="action float-right">
                    <a v-if="account.account_type == 'detail_type'" href="javascript:void(0);" class="text-info" title="Add Child Account" @click="addChild(account)"><i class="mdi mdi-plus-outline"></i></a>
                    <a v-if="account.account_type == 'ledger'" href="javascript:void(0);" class="text-info" title="Add Sub Account" @click="addChild(account)"><i class="mdi mdi-plus-outline"></i></a>
                    <a v-if="account.account_type != 'group'" href="javascript:void(0);" class="text-warning" title="Edit Account" @click="editData(account)"><i class="mdi mdi-circle-edit-outline"></i></a>
                    <a v-if="account.account_type != 'group' && account.account_type != 'type'" href="javascript:void(0);" class="text-danger" @click="deleteData(account)"><i class="mdi mdi-delete-outline"></i></a>
                </span>
            </a>
            <div v-if="hasChildren" v-show="showChildren">
                <ListView v-for="(child, c) in account.children" :key="c" :account="child" :level="(level + 1)" @add-child="addChild" @edit-item="editData" @delete-item="deleteData">

                </ListView>
            </div>
        </li>
    </ul>
</template>

<script>

    export default {
        name: 'ListView',
        emit:['add-child', 'edit-item', 'delete-item'],
        props: {
            account: {
                type: Object,
                required: true,
            },
            level: {
                type: Number,
                default: 0,
            }

        },
        data() {
            return {
                showChildren: true,
            }
        },
        computed: {
            accountLevel() {
                return 'account_level'+this.level;
            },

            hasChildren() {
                const { children } = this.account;
                return children && children.length > 0
            },
            toggleChildrenIcon() {
                return this.showChildren ? 'mdi mdi-chevron-down' : 'mdi mdi-chevron-left';
            },

            
        },
        methods: {
            toggleChildren() {
                this.showChildren = !this.showChildren;
            },
            addChild(data) {
                return this.$emit('add-child', data);
            },
            editData(data) {
                return this.$emit('edit-item', data);
            },
            deleteData(data) {
                return this.$emit('delete-item', data);
            }
        }
    }

</script>

<style scoped>
    .account_level1 {
        margin-bottom: 10px;
    }
    .account_level1, .account_level1 ul {
        list-style: none;
    }

    .account_level1 ul {
        margin-top: 10px;
    }
    .account_level1 li {
        margin-bottom: 5px;
        position: relative;
    }
    .account_level1 li.master_head {
        width: 100%;
    }
    .account_level1 li a.text-item{
        display: block;
        padding: 10px 5px 10px 50px;
        background: #fafafa;
        border: 1px solid #c0c0c0;
        line-height: 1.2;
    }
    .account_level1 li .expandBtn {
            background-color: #313A46;
            color: #fff;
            padding: 10px 13px;
            font-size: 15px;
            line-height: 1.2;
            position: absolute;
            z-index: 2;
            top: 0;
            left: 0;
    }

    .action a i {
        font-size: 16px !important;
        vertical-align: middle;
    }
</style>