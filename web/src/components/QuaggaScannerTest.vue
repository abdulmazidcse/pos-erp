<template>
  <div>
    <div v-if="foundCodes" class="level">
      <div class="level-item has-text-centered">
        <div>
          <p class="heading">{{foundCodes.type}}</p>
          <p class="title">{{foundCodes.code}}</p>
        </div>
      </div>
    </div>
    <nav class="level" role="navigation" aria-label="main navigation">
      <div v-show="!scannerActive" class="level-item">
        <a @click="start" class="button">Start Scanner</a>
      </div>

      <div v-show="scannerActive" class="level-item">
        <a @click="stop" class="button">Stop Scanner</a>
      </div>
    </nav>
    <div id="videoWindow" class="video"></div>
  </div>
</template>

<script>

import Quagga from "quagga";

const config = {
    locate: true,
    inputStream: {
        name: "live",
        type: "LiveStream",
        target: document.querySelector("#videoWindow")
    },
    decoder: {
        readers: ["ean_reader"],
        multiple: true
    },
    locator: {
        halfSample: true,
        patchSize: "medium"
    }
};

export default {    
    name: 'QuaggaScannerTest',
    props: {
        barcode: {
            type: String,
            code: String,
        },      

        codeResult: {
            code: String,
            fromat: String
        },       
    },
    data() {
        return {
            scannerActive: false,
            foundCodes: this.$props.barcode | null,
        }
    },
    methods: {
        start() {
            this.scannerActive = true;
            Quagga.init({
                inputStream : {
                name : "Live",
                type : "LiveStream",
                target: document.querySelector('#videoWindow')    // Or '#yourElement' (optional)
                },
                decoder : {
                // readers : ["code_128_reader"]
                readers : ["ean_reader"],
                // multiple: true,
                }
            }, function(err) {
                if (err) {
                    console.log(err);
                    return
                }
                console.log("Initialization finished. Ready to start");
                Quagga.start();
            });
        },

        stop() {
            this.scannerActive = false;
            Quagga.stop();
        }
    },


    mounted() {
        Quagga.onDetected((data) => {
        console.log(data);
        const foundResult = data;
        const foundCode = {
            code: foundResult.codeResult.code,
            type: foundResult.codeResult.format
        };
        console.log(foundCode);
        this.foundCodes = foundCode;
        this.$emit("found", foundCode);
        });

        // Quagga.decodeSingle({
        //     decoder: {
        //         // readers: ["code_128_reader"] // List of active readers
        //         readers: ["ean_reader"] // List of active readers
        //     },        
        // }, function(result){
        //     if(result.codeResult) {
        //         const foundCode = {
        //             code: result.codeResult.code,
        //             type: result.codeResult.format
        //         };
        //         console.log(foundCode);
        //         this.foundCodes = foundCode;
        //         this.$emit("found", foundCode);
        //         console.log("result", result.codeResult.code);
        //     } else {
        //         console.log("not detected");
        //     }
        // });
    }
}

// interface barcode {
//   code: string;
//   type: string;
// }
// interface codeResult {
//   code: string;
//   format: string;
// } 
// export default class Scanner extends Vue {
//   scannerActive: boolean = false;
//   foundCodes: barcode | null = null;
//   start() {
//     this.scannerActive = true;
//     const config: object = {
//       locate: true,
//       inputStream: {
//         name: "live",
//         type: "LiveStream",
//         target: document.querySelector("#videoWindow")
//       },
//       decoder: {
//         readers: ["ean_reader"],
//         multiple: true
//       },
//       locator: {
//         halfSample: true,
//         patchSize: "medium"
//       }
//     };
//     Quagga.init(config, err => {
//       if (err) {
//         console.log(err);
//         return;
//       }
//       console.log("initialization complete");
//       Quagga.start();
//     });
//   }
//   stop() {
//     this.scannerActive = false;
//     Quagga.stop();
//   }
//   mounted() {
    // Quagga.onDetected((data: Array<object>) => {
    //   // console.log(data);
    //   const foundResult = data[0];
    //   const foundCode: barcode = {
    //     code: foundResult.codeResult.code,
    //     type: foundResult.codeResult.format
    //   };
    //   console.log(foundCode);
    //   this.foundCodes = foundCode;
    //   this.$emit("found", foundCode);
    // });
//   }
// }
</script>

<style>
.video {
  width: 100%;
}
</style>

