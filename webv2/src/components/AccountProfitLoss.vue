<template>
    <tbody> 
        <tr :style="(account.account_type != 'ledger') ? 'font-weight: bold' : ''">
            
            <td v-if="code_active" :style="nodeMargin+' text-transform: uppercase;'">{{ account.name }} [{{ account.code }}] </td>
            <td v-else :style="nodeMargin+' text-transform: uppercase;'">{{ account.name }}</td>
            <td class="text-right" style="text-align: right;">{{ Number(parseFloat(account.opening_balance ?? 0) + parseFloat(account.debit_amount) + parseFloat(account.credit_amount)).toFixed(2) }}</td>
        </tr>
    </tbody>               
    <template v-if="hasChildren">
        <AccountProfitLoss v-for="(child, c) in account.children" :key="c" :account="child" :level="(level + 1)" :spaces="(spaces + 15)" :code_active="showCode"></AccountProfitLoss>
    </template>
</template>

<script>

    export default {
        name: 'AccountProfitLoss',
        emit:['add-child', 'edit-item', 'delete-item'],
        props: {
            account: {
                type: Object,
                required: true,
            },
            level: {
                type: Number,
                default: 0,
            },
            spaces: {
                type: Number,
                default: 0,
            },
            code_active: {
                type: Boolean,
                default: false,
            }

        },
        data() {
            return {
                showChildren: true,
            }
        },
        computed: {
            accountLevel() {
                return this.level;
            },

            nodeMargin() {
                return 'padding-left: '+`${this.spaces}px;`;
            },

            hasChildren() {
                const { children } = this.account;
                return children && children.length > 0
            },

            toggleChildrenIcon() {
                return this.showChildren ? 'mdi mdi-chevron-down' : 'mdi mdi-chevron-left';
            },
            showCode() {
                if(this.code_active) {
                    return true;
                }else{
                    return false;
                }
            }

            
        },
        methods: {
            toggleChildren() {
                this.showChildren = !this.showChildren;
            },
        }
    }

</script>
 