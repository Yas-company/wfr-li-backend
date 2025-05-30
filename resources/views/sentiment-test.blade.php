<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Stock Analysis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .ai-bubble {
            background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
            color: white;
            border-radius: 1.5rem 1.5rem 1.5rem 0.5rem;
            padding: 1rem 1.5rem;
            max-width: 90%;
            margin-bottom: 1rem;
            animation: fadeInUp 0.7s cubic-bezier(.39,.575,.56,1) both;
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px);}
            100% { opacity: 1; transform: translateY(0);}
        }
        .fade-in { animation: fadeInUp 0.7s both; }
        .bg-animated {
            background: linear-gradient(120deg, #f0f4f8 0%, #e0e7ff 100%);
            min-height: 100vh;
            animation: bgMove 10s infinite alternate linear;
        }
        @keyframes bgMove {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }
        .typing {
            border-right: .1em solid #6366f1;
            white-space: nowrap;
            overflow: hidden;
            animation: typing 2s steps(40, end), blink-caret .75s step-end infinite;
        }
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: #6366f1; }
        }
    </style>
</head>
<body class="bg-animated">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-4xl font-extrabold text-gray-800 mb-8 text-center tracking-tight">AI Stock Analysis</h1>
            <div class="bg-white/80 rounded-2xl shadow-xl p-8">
                <form id="sentimentForm" class="space-y-6">
                    @csrf
                    <div>
                        <label for="ticker" class="block text-base font-semibold text-gray-700">Stock Ticker</label>
                        <input type="text" id="ticker" name="ticker" required
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg px-4 py-2"
                            placeholder="e.g., AAPL">
                    </div>
                    <div>
                        <label for="message" class="block text-base font-semibold text-gray-700">Message to AI</label>
                        <textarea id="message" name="message" rows="2" required
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg px-4 py-2"
                            placeholder="Ask anything about this stock!">Should I buy Apple stock?</textarea>
                    </div>
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-bold text-white bg-gradient-to-r from-indigo-500 to-cyan-400 hover:from-indigo-600 hover:to-cyan-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        🔮 Analyze with AI
                    </button>
                </form>

                <button type="button" id="fetchHistory"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-bold text-white bg-gradient-to-r from-green-500 to-teal-400 hover:from-green-600 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mt-4 transition">
                    📈 Show Stock History
                </button>

                <div id="result" class="mt-8 hidden">
                    <div id="aiBubble" class="ai-bubble"></div>
                </div>

                <div id="historyResult" class="mt-8 hidden fade-in">
                    <div id="companyOverview" class="mb-4"></div>
                    <canvas id="stockChart" class="mb-4 bg-white rounded shadow" height="100"></canvas>
                    <div id="historyContent" class="bg-gray-50 rounded-md p-4"></div>
                </div>

                <div id="loading" class="mt-8 hidden">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-indigo-600"></div>
                        <span class="ml-4 text-lg text-indigo-700 font-semibold">AI is thinking...</span>
                    </div>
                </div>

                <div id="error" class="mt-8 hidden">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span id="errorMessage"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Typing animation for AI bubble
        function typeText(element, text, speed = 20) {
            element.textContent = '';
            let i = 0;
            function type() {
                if (i < text.length) {
                    element.textContent += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                }
            }
            type();
        }

        document.getElementById('sentimentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const ticker = document.getElementById('ticker').value;
            const message = document.getElementById('message').value;
            const resultDiv = document.getElementById('result');
            const aiBubble = document.getElementById('aiBubble');
            const loadingDiv = document.getElementById('loading');
            const errorDiv = document.getElementById('error');
            const errorMessage = document.getElementById('errorMessage');
            // Reset UI
            resultDiv.classList.add('hidden');
            errorDiv.classList.add('hidden');
            loadingDiv.classList.remove('hidden');
            aiBubble.textContent = '';
            try {
                const response = await fetch('/api/ai-sentiment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
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
                if (typeof raw === 'string') {
                    const jsonStart = raw.indexOf('{', raw.indexOf('Response in JSON format:'));
                    const jsonEnd = raw.lastIndexOf('}');
                    if (jsonStart !== -1 && jsonEnd !== -1 && jsonEnd > jsonStart) {
                        try { parsed = JSON.parse(raw.substring(jsonStart, jsonEnd + 1)); } catch (e) { parsed = null; }
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
                // Display result with typing animation
                resultDiv.classList.remove('hidden');
                typeText(aiBubble, `🤖 AI Analysis:\n\n${analysis}\n\nSentiment Score: ${sentimentScore}`);
            } catch (error) {
                errorMessage.textContent = error.message;
                errorDiv.classList.remove('hidden');
            } finally {
                loadingDiv.classList.add('hidden');
            }
        });

        document.getElementById('fetchHistory').addEventListener('click', async () => {
            const ticker = document.getElementById('ticker').value;
            const historyDiv = document.getElementById('historyResult');
            const historyContent = document.getElementById('historyContent');
            const companyOverviewDiv = document.getElementById('companyOverview');
            historyDiv.classList.add('hidden');
            historyContent.innerHTML = 'Loading...';
            companyOverviewDiv.innerHTML = '';
            try {
                const response = await fetch(`/stock-history?ticker=${encodeURIComponent(ticker)}`);
                if (!response.ok) {
                    throw new Error('Could not fetch stock history');
                }
                const data = await response.json();
                let html = `<div class=\"flex gap-4 mb-4\">`;
                html += `<div class=\"bg-indigo-100 text-indigo-800 px-4 py-2 rounded-lg font-bold text-lg\">Avg Return: ${data.average_annual_return}%</div>`;
                html += `</div>`;
                html += `<h3 class=\"mt-2 font-semibold\">Monthly Prices:</h3><ul class=\"text-xs\">`;
                for (const [date, price] of Object.entries(data.history)) {
                    html += `<li>${date}: $${price}</li>`;
                }
                html += '</ul>';
                historyContent.innerHTML = html;
                historyDiv.classList.remove('hidden');
                // Show company overview if available
                if (data.company && data.company.Name) {
                    let overviewHtml = `<div class=\"mb-2 text-2xl font-bold text-indigo-700\"><strong>${data.company.Name}</strong> <span class=\"text-gray-500 text-lg\">(${data.ticker})</span></div>`;
                    if (data.company.Description) {
                        overviewHtml += `<div class=\"mb-2 text-base text-gray-700\">${data.company.Description}</div>`;
                    }
                    overviewHtml += '<div class=\"flex flex-wrap gap-4 text-sm text-gray-600 mb-2\">';
                    if (data.company.Sector) overviewHtml += `<div><strong>Sector:</strong> ${data.company.Sector}</div>`;
                    if (data.company.Industry) overviewHtml += `<div><strong>Industry:</strong> ${data.company.Industry}</div>`;
                    if (data.company.Exchange) overviewHtml += `<div><strong>Exchange:</strong> ${data.company.Exchange}</div>`;
                    if (data.company.MarketCapitalization) overviewHtml += `<div><strong>Market Cap:</strong> $${Number(data.company.MarketCapitalization).toLocaleString()}</div>`;
                    if (data.company.PERatio) overviewHtml += `<div><strong>P/E Ratio:</strong> ${data.company.PERatio}</div>`;
                    if (data.company.DividendYield) overviewHtml += `<div><strong>Dividend Yield:</strong> ${data.company.DividendYield}</div>`;
                    overviewHtml += '</div>';
                    companyOverviewDiv.innerHTML = overviewHtml;
                } else {
                    companyOverviewDiv.innerHTML = '<div class=\"text-xs text-gray-500\">No company info available.</div>';
                }
                // Draw chart
                const chartDiv = document.getElementById('stockChart');
                const labels = Object.keys(data.history).reverse();
                const prices = Object.values(data.history).reverse();
                const ctx = chartDiv.getContext('2d');
                if (window.stockChartInstance) window.stockChartInstance.destroy();
                window.stockChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: `${data.ticker} Monthly Close`,
                            data: prices,
                            borderColor: 'rgba(37, 99, 235, 1)',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            fill: true,
                            tension: 0.2,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true }
                        },
                        animation: { duration: 1200 },
                        scales: {
                            x: { display: true, title: { display: true, text: 'Date' } },
                            y: { display: true, title: { display: true, text: 'Price ($)' } }
                        }
                    }
                });
            } catch (error) {
                historyContent.innerHTML = `<span class=\"text-red-600\">${error.message}</span>`;
                historyDiv.classList.remove('hidden');
            }
        });
    </script>
</body>
</html> 