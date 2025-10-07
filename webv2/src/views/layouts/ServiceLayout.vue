<template>
  <div class="service-module-layout">
    <!-- Navigation Sidebar -->
    <div class="sidebar">
      <h3>সার্ভিসিং মডিউল</h3>
      <nav>
        <router-link to="/service/dashboard" class="nav-link">
          📊 ড্যাশবোর্ড
        </router-link>
        <router-link to="/service/complaint" class="nav-link">
          📋 অভিযোগ রেজিস্টার
        </router-link>
        <router-link to="/service/warranty-check" class="nav-link">
          🛡️ ওয়ারেন্টি চেক
        </router-link>
        <router-link to="/service/assignment" class="nav-link">
          👨‍🔧 টেকনিশিয়ান অ্যাসাইন
        </router-link>
        <router-link to="/service/execution" class="nav-link">
          🔧 সার্ভিস এক্সিকিউশন
        </router-link>
        <router-link to="/service/billing" class="nav-link">
          💰 বিলিং
        </router-link>
        <router-link to="/service/delivery" class="nav-link">
          📦 ডেলিভারি
        </router-link>
        <router-link to="/service/history" class="nav-link">
          📊 সার্ভিস হিস্ট্রি
        </router-link>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <header class="content-header">
        <h2>{{ currentRouteTitle }}</h2>
        <div class="breadcrumb">
          <router-link to="/service/dashboard">হোম</router-link> 
          <span> / {{ currentRouteTitle }}</span>
        </div>
      </header>
      
      <div class="content-body">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

export default {
  name: 'ServiceLayout',
  setup() {
    const route = useRoute()
    
    const currentRouteTitle = computed(() => {
      return route.meta.title || 'সার্ভিস মডিউল'
    })

    return {
      currentRouteTitle
    }
  }
}
</script>

<style scoped>
.service-module-layout {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 250px;
  background: #2c3e50;
  color: white;
  padding: 20px;
}

.sidebar h3 {
  text-align: center;
  margin-bottom: 30px;
  color: #3498db;
}

.nav-link {
  display: block;
  color: #ecf0f1;
  padding: 12px 15px;
  margin: 5px 0;
  text-decoration: none;
  border-radius: 5px;
  transition: all 0.3s ease;
}

.nav-link:hover {
  background: #34495e;
  color: #3498db;
}

.nav-link.router-link-active {
  background: #3498db;
  color: white;
}

.main-content {
  flex: 1;
  background: #ecf0f1;
}

.content-header {
  background: white;
  padding: 20px 30px;
  border-bottom: 1px solid #bdc3c7;
}

.content-header h2 {
  margin: 0;
  color: #2c3e50;
}

.breadcrumb {
  color: #7f8c8d;
  font-size: 14px;
}

.breadcrumb a {
  color: #3498db;
  text-decoration: none;
}

.content-body {
  padding: 30px;
}
</style>