<template>
    <div class="btn-group">
        <div class="col-md-6">
            <button type="button" class="btn btn-primary" @click="btnEdit" >Edit</button>        
        </div>
        <div class="col-md-6">        
            <button type="button" class="btn btn-danger" @click="btnDelete" >Delete</button>        
        </div>
    </div>    
</template>
<script>
export default {    
    name: 'AppButtons',
    props: {
        row: Object,    
        column: Object    
    },
    methods: {
        btnEdit(){        
            alert(`Edit ${JSON.stringify(this.row)}`)
        },
        btnDelete(){
            alert(`Delete ${this.row.id}`)
        }
    }
}
</script>