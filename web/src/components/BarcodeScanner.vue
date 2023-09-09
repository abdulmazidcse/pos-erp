<template>
    <div id="barcodeScannerUI" ref="root"></div>
</template>
  
  <script>
  import "../dbr";
  import { BarcodeScanner } from 'dynamsoft-javascript-barcode';
  import { onBeforeUnmount, onMounted, ref } from "vue";
  
  export default {
    setup(props, context) {
      const root = ref(null);
      const pScanner = ref(null);
      const bDestroyed = ref(false);  
      onMounted(async () => {
        try {
          let scanner = await (pScanner.value =
            pScanner.value || BarcodeScanner.createInstance());
          if (bDestroyed.value) {
            scanner.destroy();
            return;
          }
          root.value.appendChild(scanner.getUIElement());
          scanner.onFrameRead = (results) => {
            for (let result of results) {
                const format = result.barcodeFormat ? result.barcodeFormatString : result.barcodeFormatString_2;
                context.emit("appendMessage", {
                format,
                text: result.barcodeText,
                type: "result",
                });
            }
          };
          await scanner.open();
        } catch (ex) {
          context.emit("appendMessage", { msg: ex.message, type: "error" });
          console.error(ex);
        }
      });
      onBeforeUnmount(async () => {
        if (pScanner.value) {
          (await pScanner.value).destroy();
          bDestroyed.value = true;
        }
      });
      return {
        root,
      };
    },
  };
  </script>
  
  <!-- Add "scoped" attribute to limit CSS to this component only -->
  <style scoped>
  #barcodeScannerUI {
    width: 100%;
    height: 100%;
  }
  </style>
  