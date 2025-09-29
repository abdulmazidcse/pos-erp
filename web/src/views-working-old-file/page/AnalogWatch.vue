<template>
  <div class="analog-watch">
    <svg :width="size" :height="size">
      <!-- Watch face -->
      <circle :cx="size / 2" :cy="size / 2" :r="size / 2" fill="white" />

        
      
      
      <!-- Hour hand -->
      <line
        :x1="size / 2"
        :y1="size / 2"
        :x2="size / 2 + hourHandLength * Math.cos(hourAngle)"
        :y2="size / 2 + hourHandLength * Math.sin(hourAngle)"
        stroke="black"
        stroke-width="5"
      />
      
      <!-- Minute hand -->
      <line
        :x1="size / 2"
        :y1="size / 2"
        :x2="size / 2 + minuteHandLength * Math.cos(minuteAngle)"
        :y2="size / 2 + minuteHandLength * Math.sin(minuteAngle)"
        stroke="black"
        stroke-width="3"
      />
      
      <!-- Second hand -->
      <line
        :x1="size / 2"
        :y1="size / 2"
        :x2="size / 2 + secondHandLength * Math.cos(secondAngle)"
        :y2="size / 2 + secondHandLength * Math.sin(secondAngle)"
        stroke="red"
        stroke-width="1"
      />
      
      
    </svg>
  </div>
</template>

<script>
export default {
  data() {
    return {
      size: 200, // Size of the watch face
      hourHandLength: 50,
      minuteHandLength: 80,
      secondHandLength: 90,
      secondAngle: 0,
      minuteAngle: 0,
      hourAngle: 0
    };
  },
  computed: {
    currentTime() {
      const now = new Date();
      return `${this.formatDigit(now.getHours())}:${this.formatDigit(now.getMinutes())}:${this.formatDigit(now.getSeconds())}`;
    }
  },
  methods: {
    updateAngles() {
      const now = new Date();
      this.secondAngle = (now.getSeconds() / 60) * (2 * Math.PI) - Math.PI / 2;
      this.minuteAngle = ((now.getMinutes() + now.getSeconds() / 60) / 60) * (2 * Math.PI) - Math.PI / 2;
      this.hourAngle = ((now.getHours() % 12 + now.getMinutes() / 60) / 12) * (2 * Math.PI) - Math.PI / 2;
    },
    formatDigit(digit) {
      return digit < 10 ? `0${digit}` : digit;
    }
  },
  mounted() {
    this.updateAngles();
    this.intervalId = setInterval(this.updateAngles, 1000);
  },
  beforeDestroy() {
    clearInterval(this.intervalId);
  }
};
</script>

<style scoped>
.analog-watch {
  display: inline-block;
}
</style>
