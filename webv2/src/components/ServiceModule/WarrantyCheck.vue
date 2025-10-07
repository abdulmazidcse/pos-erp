<template>
  <div class="warranty-check">
    <h3>ধাপ ২: Warranty Verification</h3>
    
    <input type="text" v-model="serialNumber" placeholder="সিরিয়াল নাম্বার লিখুন">
    <button @click="checkWarranty">ওয়ারেন্টি চেক করুন</button>

    <div v-if="warrantyResult">
      <p><strong>ওয়ারেন্টি স্ট্যাটাস:</strong> {{ warrantyResult.status }}</p>
      <p><strong>কেনার তারিখ:</strong> {{ warrantyResult.purchase_date }}</p>
      <p><strong>ওয়ারেন্টি শেষ:</strong> {{ warrantyResult.warranty_expiry }}</p>
      
      <select v-model="ticket.warranty_status">
        <option value="warranty">ওয়ারেন্টি ভ্যালিড</option>
        <option value="out_of_warranty">ওয়ারেন্টি এক্সপায়ার্ড</option>
      </select>
    </div>
  </div>
</template>