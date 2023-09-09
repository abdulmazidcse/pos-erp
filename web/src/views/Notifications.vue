<template>
  <div  > 
    <!-- CSS animation example -->
    <notifications
      group="foo-css"
      position="bottom left"
      :speed="500"
    /> 
    <!-- Custom style example -->
    <notifications
      group="custom-style"
      position="top center"
      classes="n-light"
      :max="3"
      :width="400"
    />

    <!-- Custom template example -->
    <notifications
      group="custom-template"
      :duration="5000"
      :width="500"
      animation-name="v-fade-left"
      position="top left"
    > 
    </notifications>

    <!-- Full width example -->
    <notifications
      group="full-width"
      width="100%"
    />

    <div class="content">
      <p>
        CSS animation:
      </p>
      <div class="block">
        <button
          class="success"
          @click="show('foo-css', 'success')"
        >
          <i class="icon ion-information-circled" />
          SUCCESS
        </button>
        <button
          class="warn"
          @click="show('foo-css', 'warn')"
        >
          <i class="icon ion-alert-circled" />
          WARNING
        </button>
        <button
          class="error"
          @click="show('foo-css', 'error')"
        >
          <i class="icon ion-close-circled" />
          ERROR
        </button>
      </div> 
      <div>
        <p>Custom style:</p>
        <button @click="show('custom-style')">
          top center (max=3)
        </button>
        <p>Custom template:</p>
        <button @click="show('custom-template')">
          show top left
        </button>
        <p />
        <button @click="clean('custom-template')">
          <u>clean all</u> top left
        </button>
        <p />
        <button @click="show('full-width')">
          show bottom (full width)
        </button>
      </div>
    </div>
  </div>
</template>
<script>   
import { mapGetters, mapActions } from "vuex";   
export default {
  name: 'PosLeftbar', 
  components: { 
  },
  data() {
    return {
      id: 0,
      animation: {
        enter: {
          opacity: [1, 0],
          translateX: [0, -300],
          scale: [1, 0.2],
        },
        leave: {
          opacity: 0,
          height: 0,
        },
      },
    };
  },
  props: {
    props: ['cartItem'],
  },
  components: { 
  },
  methods: {
    show(group, type = '') {
      const text = `
        This is notification text!
        <br>
        Date: ${new Date()}
      `;
      this.$notify({
        group,
        title: `Test ${type} notification #${this.id++}`,
        text,
        type,
        data: {
          randomNumber: Math.random(),
        },
      });
    },
    clean(group) {
      this.$notify({ group, clean: true });
    },
  },  
}
</script>
<style scoped>
.form-group {
  margin-bottom: 5px ;
}
.border.success.item-head {
  width: 20px !important;
  color: #f4f4f4;
  background-color: #3e81ae; 
} 
.table-bordered th, .table-bordered td {
  border: none;
}
.pos-footer {
  width: 96%;
  margin: 0px auto; 
}
.pos-body {
  min-height: 371px;
  overflow-x: scroll; 
} 
</style>