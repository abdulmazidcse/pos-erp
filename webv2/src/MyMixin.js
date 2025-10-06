export const myMixin = {
  data: () => ({
    mySharedDataProperty: null
  }),
  methods: {    
    localSetItem: function(key, data) {
        localStorage.setItem(key, JSON.stringify(data));
    },
    localGetItem: function (key) {
        let data=localStorage.getItem(key) || null;
        return JSON.parse(data);
    },
    baseUrl: function () {  
        if((location.host == 'localhost:8080') || (location.host == '127.0.0.1:8080')){
            //return "http://"+location.host+"/v4/api";
            //return "https://staging.mistrimama.com/backend/api/";
            var urrentUrl =  "http://127.0.0.1:8000/api";
        }else{
            return "https://"+location.host+"/backend/api/";
        } 
    },
    currentUrl: function () {
        return location.toString();
    },
    currentHost: function () {
        return  location.host 
    },
    errorAlerts(error) {
        if (error.response) {
            return error.response.data.message;
        }
        else {
            return error.message;
        }
    },
    goto(route) {
        this.$router.replace(this.$route.query.redirect || route);
    }
  }
}