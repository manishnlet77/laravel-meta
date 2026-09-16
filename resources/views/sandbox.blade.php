<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meta Package Sandbox</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        pre { background: #1e1e1e; color: #d4d4d4; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 font-sans p-8">

<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-blue-600">Meta API Sandbox</h1>
        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-blue-400">Testing Mode</span>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Sidebar Navigation -->
        <div class="space-y-4">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Available Tests</h2>
                <ul class="space-y-2 text-sm">
                    <li><a href="#test-token" class="text-blue-600 hover:underline">1. Test Access Token</a></li>
                    <li><a href="#test-leads" class="text-blue-600 hover:underline">2. Fetch Lead Data</a></li>
                    <li><a href="#test-facebook" class="text-blue-600 hover:underline">3. Publish to Facebook</a></li>
                </ul>
            </div>
            
            <div class="bg-yellow-50 p-6 rounded-lg shadow-sm border border-yellow-200 text-sm">
                <h3 class="font-bold text-yellow-800 mb-2">Note on Token</h3>
                <p class="text-yellow-700">If you leave the token field blank in the forms below, the Sandbox will attempt to use <code class="bg-yellow-100 px-1">META_SYSTEM_USER_TOKEN</code> from your environment.</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="md:col-span-2 space-y-8">
            
            <!-- 1. Test Token -->
            <div id="test-token" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h2 class="text-xl font-semibold mb-4">1. Test System User Token</h2>
                <form action="{{ route('meta.sandbox.test-token') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Access Token (Optional)</label>
                        <input type="text" name="token" class="w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500" placeholder="Paste token here to override .env">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Test Token</button>
                </form>

                @if(session('token_data'))
                    <div class="mt-6">
                        <h3 class="font-semibold text-gray-700 mb-2">Token API Response:</h3>
                        <pre class="text-sm"><code>{{ json_encode(session('token_data'), JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                @endif
            </div>

            <!-- 2. Test Leads -->
            <div id="test-leads" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h2 class="text-xl font-semibold mb-4">2. Fetch Lead Data</h2>
                <form action="{{ route('meta.sandbox.get-lead') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Access Token (Optional)</label>
                        <input type="text" name="token" class="w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500" placeholder="Defaults to .env token">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lead ID <span class="text-red-500">*</span></label>
                        <input type="text" name="lead_id" required class="w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. 123456789012345">
                    </div>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">Fetch Lead</button>
                </form>

                @if(session('api_response') && request()->is('meta/sandbox/get-lead'))
                    <div class="mt-6">
                        <h3 class="font-semibold text-gray-700 mb-2">Meta API Response:</h3>
                        <pre class="text-sm"><code>{{ json_encode(session('api_response'), JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                @endif
            </div>

            <!-- 3. Publish to Facebook -->
            <div id="test-facebook" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h2 class="text-xl font-semibold mb-4">3. Publish to Facebook Page</h2>
                <form action="{{ route('meta.sandbox.publish-facebook') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Access Token (Optional)</label>
                        <input type="text" name="token" class="w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500" placeholder="Defaults to .env token">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Page ID <span class="text-red-500">*</span></label>
                        <input type="text" name="page_id" required class="w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500" placeholder="Your Facebook Page ID">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                        <textarea name="message" required rows="3" class="w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500" placeholder="Hello from the Sandbox API!"></textarea>
                    </div>
                    <button type="submit" class="bg-blue-800 text-white px-4 py-2 rounded shadow hover:bg-blue-900">Publish Post</button>
                </form>

                @if(session('api_response') && request()->is('meta/sandbox/publish-facebook'))
                    <div class="mt-6">
                        <h3 class="font-semibold text-gray-700 mb-2">Meta API Response:</h3>
                        <pre class="text-sm"><code>{{ json_encode(session('api_response'), JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

</body>
</html>
