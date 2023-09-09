<template>
    <tbody :style="(account.account_type != 'ledger') ? 'font-weight: bold;' : ''"> 
        <tr>
            <td :style="nodeMargin+' text-transform: uppercase;'">{{ account.name }}</td>
            <td >{{ account.code }}</td>
            <td class="text-center">{{ accountLevel }}</td>
            <td>{{ account.account_type_name }}</td>
        </tr>
    </tbody>               
    <GridView v-if="hasChildren" v-for="(child, c) in account.children" :key="c" :account="child" :level="(level + 1)" :spaces="(spaces + 10)"></GridView>
</template>

<script>

    export default {
        name: 'GridView',
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

            
        },
        methods: {
            toggleChildren() {
                this.showChildren = !this.showChildren;
            },
        }
    }

</script>