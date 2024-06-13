// module.exports = {
//     devServer: {
//         host: '127.0.0.1',
//         port: 8080,
//         public: 'localhost:8080',
//     }
// } 

module.exports = {
    devServer: {
      proxy: {
        '/api': { // Replace '/api' with your actual API endpoint prefix
          target: 'http://127.0.0.1:8000', // Replace with the actual backend server URL and port
          changeOrigin: true, // Change origin to match frontend
        },
      },
    }
}