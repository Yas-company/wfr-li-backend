<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stock Sentiment Analysis Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Stock Sentiment Analysis Test</h1>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <form id="sentimentForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="ticker" class="block text-sm font-medium text-gray-700">Stock Ticker</label>
                        <input type="text" id="ticker" name="ticker" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="e.g., AAPL">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Message to Agent</label>
                        <textarea id="message" name="message" rows="3" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Enter your analysis prompt here">Should I buy Apple stock?</textarea>
                    </div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Analyze Sentiment
                    </button>
                </form>

                <div id="result" class="mt-6 hidden">
                    <h2 class="text-lg font-medium text-gray-900 mb-2">Analysis Result</h2>
                    <div id="resultContent" class="bg-gray-50 rounded-md p-4"></div>
                </div>

                <div id="loading" class="mt-6 hidden">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                        <span class="ml-2 text-gray-600">Analyzing...</span>
                    </div>
                </div>

                <div id="error" class="mt-6 hidden">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span id="errorMessage"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('sentimentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const ticker = document.getElementById('ticker').value;
            const message = document.getElementById('message').value;
            const resultDiv = document.getElementById('result');
            const resultContent = document.getElementById('resultContent');
            const loadingDiv = document.getElementById('loading');
            const errorDiv = document.getElementById('error');
            const errorMessage = document.getElementById('errorMessage');
            
            // Reset UI
            resultDiv.classList.add('hidden');
            errorDiv.classList.add('hidden');
            loadingDiv.classList.remove('hidden');
            
            try {
                const response = await fetch('/api/ai-sentiment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ticker, message })
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'An error occurred');
                }
                
                const data = await response.json();

                let raw = data;
                let parsed = null;

                // If the response is a string, try to extract the JSON part
                if (typeof raw === 'string') {
                    // Try to find the first '{' after "Response in JSON format:"
                    const jsonStart = raw.indexOf('{', raw.indexOf('Response in JSON format:'));
                    const jsonEnd = raw.lastIndexOf('}');
                    if (jsonStart !== -1 && jsonEnd !== -1 && jsonEnd > jsonStart) {
                        try {
                            parsed = JSON.parse(raw.substring(jsonStart, jsonEnd + 1));
                        } catch (e) {
                            parsed = null;
                        }
                    }
                } else if (typeof raw === 'object') {
                    parsed = raw;
                }

                let sentimentScore = 'N/A';
                let analysis = 'No analysis available';

                if (parsed && parsed.data) {
                    sentimentScore = parsed.data.sentiment_score ?? 'N/A';
                    analysis = parsed.data.analysis ?? parsed.data.recommendation ?? parsed.data.content ?? 'No analysis available';
                } else if (parsed) {
                    sentimentScore = parsed.sentiment_score ?? 'N/A';
                    analysis = parsed.analysis ?? parsed.recommendation ?? parsed.content ?? 'No analysis available';
                }

                // Display result
                resultContent.innerHTML = `
                    <div class="space-y-2">
                        <p><strong>Sentiment Score:</strong> ${sentimentScore}</p>
                        <p><strong>Analysis:</strong> ${analysis}</p>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-md font-semibold text-gray-700 mb-1">Raw API Response</h3>
                        <pre class="bg-gray-100 rounded p-2 text-xs overflow-x-auto">${typeof raw === 'string' ? raw : JSON.stringify(raw, null, 2)}</pre>
                    </div>
                `;
                resultDiv.classList.remove('hidden');
            } catch (error) {
                errorMessage.textContent = error.message;
                errorDiv.classList.remove('hidden');
            } finally {
                loadingDiv.classList.add('hidden');
            }
        });
    </script>
</body>
</html> 