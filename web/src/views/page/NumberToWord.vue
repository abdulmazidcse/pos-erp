<template>
    <div>
      <!-- <p>Number: {{ number }}</p> -->
      <p><strong style="text-transform: capitalize;">{{ getWord(number) }} Only </strong></p>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      number: {
        type: Number,
        required: true
      }
    },
    methods: {
      getWord(number) {
        // Define an array of words for the numbers 0-19 and for the tens
        const words = [
          'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
          'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
        ];
        const tens = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
  
        if (number < 20) {
          return words[number];
        } else if (number < 100) {
          return tens[Math.floor(number / 10)] + (number % 10 !== 0 ? ' ' + words[number % 10] : '');
        } else if (number < 1000) {
          return words[Math.floor(number / 100)] + ' hundred' + (number % 100 !== 0 ? ' ' + this.getWord(number % 100) : '');
        } else if (number < 1000000) {
          return this.getWord(Math.floor(number / 1000)) + ' thousand' + (number % 1000 !== 0 ? ' ' + this.getWord(number % 1000) : '');
        } else if (number < 1000000000) {
          return this.getWord(Math.floor(number / 1000000)) + ' million' + (number % 1000000 !== 0 ? ' ' + this.getWord(number % 1000000) : '');
        }
        return 'Number too large';
      }
    }
  };
  </script>
  <style>
    .capitalize {
      text-transform: capitalize;
    }
  </style>