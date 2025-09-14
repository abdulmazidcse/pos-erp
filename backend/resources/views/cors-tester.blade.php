<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CORS Missing Allow Origin - Solution</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        header {
            background: #4F46E5;
            color: white;
            padding: 30px;
            text-align: center;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 8px;
            background: #f8f9fa;
            border-left: 4px solid #4F46E5;
        }
        h2 {
            color: #4F46E5;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }
        h3 {
            margin: 15px 0 10px;
            color: #6c757d;
            font-size: 1.4rem;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            margin: 15px 0;
            font-family: 'Fira Code', monospace;
            font-size: 14px;
            line-height: 1.4;
        }
        code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Fira Code', monospace;
            font-size: 14px;
        }
        .note {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .steps {
            margin-left: 20px;
            margin-bottom: 20px;
        }
        .steps li {
            margin-bottom: 10px;
        }
        .terminal {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            font-family: 'Fira Code', monospace;
        }
        .command {
            color: #50fa7b;
        }
        .comment {
            color: #6272a4;
        }
        .test-area {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background: #4F46E5;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 0;
        }
        button:hover {
            background: #4338CA;
        }
        #testResult {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>CORS Missing Allow Origin Error</h1>
            <p class="subtitle">Complete Solution for Laravel Applications</p>
        </header>

        <div class="content">
            <div class="section">
                <h2>Understanding the Error</h2>
                <p>The "CORS Missing Allow Origin" error occurs when your server doesn't include the proper <code>Access-Control-Allow-Origin</code> header in responses to cross-origin requests.</p>
                
                <div class="error">
                    <p><strong>Reason:</strong> Your browser is blocking the response because the server hasn't explicitly allowed the requesting origin.</p>
                </div>
            </div>

            <div class="section">
                <h2>Complete Solution</h2>
                
                <h3>1. Update Your CORS Configuration</h3>
                <p>Edit your <code>config/cors.php</code> file:</p>
                <pre>
return [
    'paths' => ['api/*', 'oauth/*', 'login', 'logout', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000', 'http://localhost:8080'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
</pre>

                <h3>2. Create/Update TrustProxies Middleware</h3>
                <p>Ensure your <code>app/Http/Middleware/TrustProxies.php</code> is correctly configured:</p>
                <pre>
namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    protected $proxies = '*';
    
    protected $headers = Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
</pre>

                <h3>3. Verify Kernel Configuration</h3>
                <p>Check your <code>app/Http/Kernel.php</code>:</p>
                <pre>
protected $middleware = [
    \App\Http\Middleware\TrustProxies::class,
    \Illuminate\Http\Middleware\HandleCors::class,
];

protected $middlewareGroups = [
    'api' => [
        \Illuminate\Http\Middleware\HandleCors::class,
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];
</pre>

                <h3>4. Clear Configuration Cache</h3>
                <div class="terminal">
                    <span class="command">php artisan config:clear</span><br>
                    <span class="command">php artisan cache:clear</span><br>
                    <span class="command">php artisan route:clear</span><br>
                    <span class="command">composer dump-autoload</span>
                </div>
            </div>

            <div class="section">
                <h2>CORS Testing Tool</h2>
                <p>Test your API endpoints to verify CORS is working correctly:</p>
                
                <div class="test-area">
                    <h3>Test Your API Endpoint</h3>
                    <input type="text" id="apiEndpoint" placeholder="https://yourdomain.com/api/test" value="http://localhost:8000/api/test">
                    <button onclick="testCors()">Test CORS Configuration</button>
                    
                    <div id="testResult"></div>
                </div>
                
                <script>
                    function testCors() {
                        const endpoint = document.getElementById('apiEndpoint').value;
                        const resultDiv = document.getElementById('testResult');
                        
                        if (!endpoint) {
                            resultDiv.style.display = 'block';
                            resultDiv.style.background = '#fff3cd';
                            resultDiv.innerHTML = 'Please enter an API endpoint URL';
                            return;
                        }
                        
                        resultDiv.style.display = 'block';
                        resultDiv.style.background = '#d4edda';
                        resultDiv.innerHTML = 'Testing CORS configuration for: ' + endpoint + '<br>Waiting for response...';
                        
                        // Make actual CORS request
                        fetch(endpoint, {
                            method: 'GET',
                            mode: 'cors',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                return response.json();
                            }
                            throw new Error('Network response was not ok');
                        })
                        .then(data => {
                            resultDiv.style.background = '#d4edda';
                            resultDiv.innerHTML = '✅ CORS test successful!<br>' +
                                'Endpoint: ' + endpoint + '<br>' +
                                'Response: ' + JSON.stringify(data);
                        })
                        .catch(error => {
                            resultDiv.style.background = '#f8d7da';
                            resultDiv.innerHTML = '❌ CORS error: ' + error.message + '<br>' +
                                'Endpoint: ' + endpoint + '<br>' +
                                'This indicates your CORS configuration is not working properly.';
                        });
                    }
                </script>
            </div>

            <div class="section">
                <h2>Additional Troubleshooting</h2>
                
                <h3>Server Configuration</h3>
                <p>If you're using Apache or Nginx, ensure they're not overriding your CORS headers:</p>
                
                <h4>Nginx Configuration</h4>
                <pre>
server {
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
</pre>

                <h3>Verify Routes</h3>
                <p>Create a test route in <code>routes/api.php</code>:</p>
                <pre>
Route::get('/test', function() {
    return response()->json([
        'message' => 'CORS test successful',
        'timestamp' => now()
    ]);
});
</pre>

                <div class="note">
                    <p><strong>Note:</strong> After making changes, always clear your configuration cache.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>