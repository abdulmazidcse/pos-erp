<template>
  <div class="complaint-register">
    <div class="card">
      <div class="card-header">
        <h3>📋 ধাপ ১: Product Receive / Complaint Register</h3>
        <p>কাস্টমার প্রোডাক্ট নিয়ে আসে বা ফোন করে অভিযোগ জানায়</p>
      </div>

      <div class="card-body">
        <!-- Customer Information -->
        <div class="form-section">
          <h4>👤 কাস্টমার তথ্য</h4>
          <div class="form-grid">
            <div class="form-group">
              <label>কাস্টমারের নাম *</label>
              <input type="text" v-model="customer.name" placeholder="কাস্টমারের নাম লিখুন" required>
            </div>
            <div class="form-group">
              <label>ফোন নম্বর *</label>
              <input type="tel" v-model="customer.phone" placeholder="ফোন নম্বর লিখুন" required>
            </div>
            <div class="form-group">
              <label>ইমেইল</label>
              <input type="email" v-model="customer.email" placeholder="ইমেইল ঠিকানা">
            </div>
            <div class="form-group">
              <label>ঠিকানা</label>
              <textarea v-model="customer.address" placeholder="ঠিকানা লিখুন"></textarea>
            </div>
          </div>
        </div>

        <!-- Product Information -->
        <div class="form-section">
          <h4>📦 পণ্যের তথ্য</h4>
          <div class="form-grid">
            <div class="form-group">
              <label>পণ্যের নাম *</label>
              <input type="text" v-model="product.name" placeholder="পণ্যের নাম লিখুন" required>
            </div>
            <div class="form-group">
              <label>মডেল *</label>
              <input type="text" v-model="product.model" placeholder="মডেল নাম লিখুন" required>
            </div>
            <div class="form-group">
              <label>সিরিয়াল নাম্বার *</label>
              <input type="text" v-model="product.serial_number" placeholder="সিরিয়াল নাম্বার লিখুন" required>
            </div>
            <div class="form-group">
              <label>কেনার তারিখ *</label>
              <input type="date" v-model="product.purchase_date" required>
            </div>
          </div>
        </div>

        <!-- Problem Description -->
        <div class="form-section">
          <h4>🔧 সমস্যার বিবরণ</h4>
          <div class="form-group">
            <label>সমস্যা সংক্ষেপে লিখুন *</label>
            <textarea v-model="ticket.problem_description" 
                     placeholder="কাস্টমার কি সমস্যা নিয়ে এসেছেন? বিস্তারিত লিখুন..." 
                     rows="4" required></textarea>
          </div>
        </div>

        <!-- Service Type -->
        <div class="form-section">
          <h4>📍 সার্ভিস টাইপ</h4>
          <div class="radio-group">
            <label>
              <input type="radio" v-model="ticket.service_type" value="workshop"> 
              শপে সার্ভিস
            </label>
            <label>
              <input type="radio" v-model="ticket.service_type" value="onsite"> 
              অনসাইট সার্ভিস
            </label>
          </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <button @click="resetForm" class="btn-secondary">রিসেট</button>
          <button @click="createTicket" class="btn-primary" :disabled="loading">
            {{ loading ? 'টিকিট তৈরি হচ্ছে...' : 'টিকিট তৈরি করুন' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccess" class="modal-overlay">
      <div class="modal">
        <div class="modal-header">
          <h3>✅ টিকিট সফলভাবে তৈরি হয়েছে!</h3>
        </div>
        <div class="modal-body">
          <p><strong>টিকিট নম্বর:</strong> {{ createdTicket?.ticket_number }}</p>
          <p>অভিযোগ সফলভাবে রেজিস্টার হয়েছে। পরবর্তী ধাপে যান ওয়ারেন্টি চেক করার জন্য।</p>
        </div>
        <div class="modal-actions">
          <button @click="goToWarrantyCheck" class="btn-primary">ওয়ারেন্টি চেক করুন</button>
          <button @click="showSuccess = false" class="btn-secondary">বন্ধ করুন</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

export default {
  name: 'ComplaintRegister',
  setup() {
    const router = useRouter()
    const store = useStore()
    
    const loading = ref(false)
    const showSuccess = ref(false)
    const createdTicket = ref(null)

    const customer = ref({
      name: '',
      phone: '',
      email: '',
      address: ''
    })

    const product = ref({
      name: '',
      model: '',
      serial_number: '',
      purchase_date: ''
    })

    const ticket = ref({
      problem_description: '',
      service_type: 'workshop'
    })

    const createTicket = async () => {
      // Basic validation
      if (!customer.value.name || !customer.value.phone || 
          !product.value.name || !product.value.model || 
          !product.value.serial_number || !product.value.purchase_date ||
          !ticket.value.problem_description) {
        alert('দয়া করে সকল প্রয়োজনীয় তথ্য পূরণ করুন')
        return
      }

      loading.value = true

      try {
        const ticketData = {
          customer: customer.value,
          product: product.value,
          ticket: ticket.value
        }

        // Dispatch to Vuex store
        await store.dispatch('createTicket', ticketData)
        createdTicket.value = store.state.service.currentTicket
        
        showSuccess.value = true
        resetForm()
        
      } catch (error) {
        console.error('Error creating ticket:', error)
        alert('টিকিট তৈরি করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।')
      } finally {
        loading.value = false
      }
    }

    const goToWarrantyCheck = () => {
      router.push('/service/warranty-check')
    }

    const resetForm = () => {
      customer.value = { name: '', phone: '', email: '', address: '' }
      product.value = { name: '', model: '', serial_number: '', purchase_date: '' }
      ticket.value = { problem_description: '', service_type: 'workshop' }
    }

    return {
      customer,
      product,
      ticket,
      loading,
      showSuccess,
      createdTicket,
      createTicket,
      goToWarrantyCheck,
      resetForm
    }
  }
}
</script>

<style scoped>
.complaint-register {
  max-width: 800px;
  margin: 0 auto;
}

.card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
  padding: 20px;
  border-bottom: 1px solid #e0e0e0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 10px 10px 0 0;
}

.card-header h3 {
  margin: 0;
}

.card-body {
  padding: 30px;
}

.form-section {
  margin-bottom: 30px;
  padding: 20px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  background: #fafafa;
}

.form-section h4 {
  margin-top: 0;
  color: #2c3e50;
  border-bottom: 2px solid #3498db;
  padding-bottom: 10px;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
  color: #34495e;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #bdc3c7;
  border-radius: 5px;
  font-size: 14px;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
}

.radio-group {
  display: flex;
  gap: 20px;
}

.radio-group label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.form-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  margin-top: 30px;
}

.btn-primary {
  background: #3498db;
  color: white;
  padding: 12px 30px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
}

.btn-primary:hover:not(:disabled) {
  background: #2980b9;
}

.btn-primary:disabled {
  background: #bdc3c7;
  cursor: not-allowed;
}

.btn-secondary {
  background: #95a5a6;
  color: white;
  padding: 12px 30px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
}

.btn-secondary:hover {
  background: #7f8c8d;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 10px;
  padding: 0;
  max-width: 500px;
  width: 90%;
}

.modal-header {
  background: #27ae60;
  color: white;
  padding: 20px;
  border-radius: 10px 10px 0 0;
}

.modal-body {
  padding: 20px;
}

.modal-actions {
  padding: 20px;
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  border-top: 1px solid #e0e0e0;
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .form-actions {
    flex-direction: column;
  }
}
</style>