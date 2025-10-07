<template>
  <div class="service-execution">
    <h3>ধাপ ৪: Service Execution / Repair</h3>
    
    <!-- Diagnosis Details -->
    <textarea v-model="diagnosis.details" placeholder="ডায়াগনোসিস বিবরণ"></textarea>
    
    <!-- Required Parts -->
    <div class="parts-section">
      <h4>প্রয়োজনীয় পার্টস</h4>
      <select v-model="selectedPart">
        <option v-for="part in parts" :value="part">{{ part.name }}</option>
      </select>
      <input type="number" v-model="partQuantity" placeholder="পরিমাণ">
      <button @click="addPart">পার্টস যোগ করুন</button>
      
      <ul>
        <li v-for="(part, index) in selectedParts" :key="index">
          {{ part.name }} - {{ part.quantity }} x {{ part.price }}
        </li>
      </ul>
    </div>

    <!-- Estimate for Non-Warranty -->
    <div v-if="ticket.warranty_status === 'out_of_warranty'">
      <h4>খরচের এস্টিমেট</h4>
      <input type="number" v-model="estimate.diagnosis_fee" placeholder="ডায়াগনোসিস ফী">
      <input type="number" v-model="estimate.labor_charge" placeholder="লেবার চার্জ">
      <button @click="sendEstimate">এস্টিমেট কাস্টমারকে পাঠান</button>
    </div>
  </div>
</template>