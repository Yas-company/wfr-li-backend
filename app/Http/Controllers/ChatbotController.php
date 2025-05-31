<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $userMessage = $request->input('message');

        // Get stock data from API
        $stockData = $this->getStockData();

        // Process the message and generate response
        $response = $this->processMessage($userMessage, $stockData);

        return response()->json([
            'response' => $response
        ]);
    }

    private function getStockData()
    {
        $params = [
            'lang' => config('services.stocks.default_language', 'ar'),
            'limit' => config('services.stocks.default_limit', 18),
            'page' => config('services.stocks.default_page', 1),
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . config('services.stocks.token'),
        ])->get(config('services.stocks.url'), $params);

        return $response->json()['stock_data'] ?? [];
    }

    private function fetchExchangeRate($from, $to)
    {
        $apiKey = config('services.alphavantage.key');
        $url = 'https://www.alphavantage.co/query?function=CURRENCY_EXCHANGE_RATE'
            . '&from_currency=' . strtoupper($from)
            . '&to_currency=' . strtoupper($to)
            . '&apikey=' . $apiKey;
        $response = Http::get($url);
        return $response->json();
    }

    private function processMessage($message, $stockData)
    {
        // Convert message to lowercase for easier matching
        $message = strtolower($message);

        // Check for currency exchange rate queries (Arabic or English)
        if (preg_match('/سعر\s*([a-zA-Z]+)\s*مقابل\s*([a-zA-Z]+)/u', $message, $matches) ||
            preg_match('/exchange rate\s*([a-zA-Z]+)\s*to\s*([a-zA-Z]+)/i', $message, $matches)) {
            $from = $matches[1];
            $to = $matches[2];
            $rateData = $this->fetchExchangeRate($from, $to);
            if (isset($rateData['Realtime Currency Exchange Rate'])) {
                $info = $rateData['Realtime Currency Exchange Rate'];
                $rate = $info['5. Exchange Rate'] ?? null;
                $lastRefreshed = $info['6. Last Refreshed'] ?? '';
                if ($rate) {
                    return "سعر {$from} مقابل {$to}: {$rate} (آخر تحديث: {$lastRefreshed})";
                }
            }
            return "عذراً، لم أتمكن من جلب سعر الصرف حالياً.";
        }

        // Check for specific keywords and generate appropriate responses
        if (strpos($message, 'شركات') !== false || strpos($message, 'السهم') !== false) {
            return $this->getStockList($stockData);
        }

        if (strpos($message, 'شرعي') !== false || strpos($message, 'حلال') !== false) {
            return $this->getShariahCompliantStocks($stockData);
        }

        if (strpos($message, 'وصف') !== false || strpos($message, 'تفاصيل') !== false) {
            return $this->getStockDetails($message, $stockData);
        }

        // Default response for general queries
        return "مرحباً! أنا مساعدك المالي. يمكنني مساعدتك في معرفة معلومات عن الأسهم والشركات. يمكنك أن تسألني عن:\n" .
            "- قائمة الشركات المتاحة\n" .
            "- الشركات المتوافقة مع الشريعة\n" .
            "- تفاصيل شركة معينة\n" .
            "كيف يمكنني مساعدتك اليوم؟";
    }

    private function getStockList($stockData)
    {
        if (empty($stockData)) {
            return "لا توجد شركات متاحة حالياً.";
        }

        $max = 10; // Show only the first 10
        $response = "قائمة الشركات المتاحة:\n\n";
        $count = 0;
        foreach ($stockData as $stock) {
            $response .= "• {$stock['name']} ({$stock['code']})\n";
            $count++;
            if ($count >= $max) break;
        }
        if (count($stockData) > $max) {
            $response .= "وغيرها من الشركات... (أرسل 'المزيد' لعرض المزيد)";
        }
        return $response;
    }

    private function getShariahCompliantStocks($stockData)
    {
        $response = "الشركات المتوافقة مع الشريعة:\n\n";
        foreach ($stockData as $stock) {
            if ($stock['ai_activity_compliance'] === 'شرعي') {
                $response .= "- {$stock['name']} ({$stock['code']})\n";
                $response .= "  السبب: {$stock['activity_compliance_reason']}\n\n";
            }
        }
        return $response;
    }

    private function fetchCompanyOverviewAlphaVantage($symbol)
    {
        $apiKey = config('services.alphavantage.key');
        $url = 'https://www.alphavantage.co/query?function=OVERVIEW&symbol=' . strtoupper($symbol) . '&apikey=' . $apiKey;
        $response = Http::get($url);
        return $response->json();
    }

    private function getStockDetails($message, $stockData)
    {
        // Extract stock code from message
        preg_match('/[A-Z]{2,4}/', strtoupper($message), $matches);
        $stockCode = $matches[0] ?? null;
        
        if (!$stockCode) {
            return "عذراً، لم أتمكن من تحديد رمز السهم. يرجى ذكر رمز السهم الذي تريد معرفة تفاصيله.";
        }
        
        foreach ($stockData as $stock) {
            if (strtoupper($stock['code']) === strtoupper($stockCode)) {
                return "تفاصيل {$stock['name']} ({$stock['code']}):\n" .
                       "- القطاع: {$stock['sector']}\n" .
                       "- الصناعة: {$stock['industry']}\n" .
                       "- البورصة: {$stock['exchange']}\n" .
                       "- الوصف: {$stock['description']}\n" .
                       "- التوافق مع الشريعة: {$stock['ai_activity_compliance']}\n" .
                       "- سبب التوافق: {$stock['activity_compliance_reason']}";
            }
        }

        // Fallback to Alpha Vantage
        $overview = $this->fetchCompanyOverviewAlphaVantage($stockCode);
        if (!empty($overview) && isset($overview['Name'])) {
            return "تفاصيل {$overview['Name']} ({$stockCode}):\n"
                . "- القطاع: {$overview['Sector']}\n"
                . "- الصناعة: {$overview['Industry']}\n"
                . "- البورصة: {$overview['Exchange']}\n"
                . "- الوصف: {$overview['Description']}";
        }

        return "عذراً، لم أتمكن من العثور على معلومات للسهم برمز {$stockCode}.";
    }
}
