<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Test - Storage::disk('private')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success {
            color: #28a745;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .error {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .info {
            background-color: #e2e3e5;
            border: 1px solid #d6d8db;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: monospace;
            font-size: 12px;
        }
        .image-container {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            border: 2px dashed #ddd;
            border-radius: 10px;
        }
        .image-container img {
            max-width: 300px;
            max-height: 300px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-success {
            background-color: #28a745;
            color: white;
        }
        .status-error {
            background-color: #dc3545;
            color: white;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        h3 {
            color: #555;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ Image Test - Storage::disk('private')</h1>
        
        <h3>📋 Test Results</h3>
        
        <div class="info">
            <strong>Original URL:</strong><br>
            {{ $originalUrl }}
        </div>
        
        <div class="info">
            <strong>Extracted Path:</strong><br>
            {{ $extractedPath }}
        </div>
        
        <div class="info">
            <strong>File Exists in Private Disk:</strong>
            <span class="status-badge {{ $fileExists ? 'status-success' : 'status-error' }}">
                {{ $fileExists ? 'YES' : 'NO' }}
            </span>
        </div>
        
        @if($errorMessage)
            <div class="error">
                <strong>❌ Error:</strong> {{ $errorMessage }}
            </div>
        @endif
        
        @if($signedUrl)
            <div class="success">
                <strong>✅ Success!</strong> Signed URL generated successfully using Storage::disk('private')
            </div>
            
            <div class="info">
                <strong>Generated Signed URL:</strong><br>
                <a href="{{ $signedUrl }}" target="_blank" style="word-break: break-all;">{{ $signedUrl }}</a>
            </div>
            
            <h3>🖼️ Image Display Test</h3>
            <div class="image-container">
                <img src="{{ $signedUrl }}" 
                     alt="Test Image" 
                     onload="showImageSuccess()" 
                     onerror="showImageError()">
                <br><br>
                <div id="image-status"></div>
            </div>
        @else
            <div class="error">
                <strong>❌ Failed to generate signed URL</strong>
            </div>
            
            <h3>🔧 Troubleshooting Steps:</h3>
            <ul>
                <li>Check if your R2 credentials are correctly set in Laravel Cloud dashboard</li>
                <li>Verify that the disk name is 'private' in your R2 bucket configuration</li>
                <li>Ensure the file actually exists in your R2 bucket</li>
                <li>Check Laravel logs for detailed error messages</li>
            </ul>
        @endif
        
        <h3>🔗 Test Links</h3>
        <ul>
            <li><a href="/test-image-json" target="_blank">JSON Response Test</a></li>
            <li><a href="/debug/r2-config" target="_blank">R2 Configuration Check</a></li>
            <li><a href="/debug/r2-test" target="_blank">R2 Connection Test</a></li>
        </ul>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; font-size: 12px;">
            <strong>💡 How this works:</strong><br>
            1. Extract path from your existing URL: <code>{{ $extractedPath }}</code><br>
            2. Check if file exists: <code>Storage::disk('private')->exists('{{ $extractedPath }}')</code><br>
            3. Generate signed URL: <code>Storage::disk('private')->temporaryUrl('{{ $extractedPath }}', now()->addHours(2))</code>
        </div>
    </div>

    <script>
        function showImageSuccess() {
            document.getElementById('image-status').innerHTML = 
                '<span style="color: #28a745; font-weight: bold;">✅ Image loaded successfully!</span>';
        }
        
        function showImageError() {
            document.getElementById('image-status').innerHTML = 
                '<span style="color: #dc3545; font-weight: bold;">❌ Image failed to load</span>';
        }
    </script>
</body>
</html>