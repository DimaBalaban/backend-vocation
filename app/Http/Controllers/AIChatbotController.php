<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AIChatbotController extends Controller
{
    protected $apiKey;
    protected $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    public function sendMessage(Request $request)
    {
        try {
            $validator = $request->validate([
                'message' => 'required|string',
                'conversation_id' => 'nullable|string',
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful vacation planning assistant.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $request->message
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500
            ]);

            if ($response->successful()) {
                $aiResponse = $response->json()['choices'][0]['message']['content'];

                // Здесь можно сохранить историю сообщений в базу данных
                // $this->saveConversation($user->id, $request->message, $aiResponse, $request->conversation_id);

                return response()->json([
                    'success' => true,
                    'response' => $aiResponse,
                    'conversation_id' => $request->conversation_id ?? uniqid()
                ]);
            }

            return response()->json([
                'error' => 'Failed to get response from AI',
                'details' => $response->json()
            ], 500);

        } catch (\Exception $e) {
            Log::error('AI Chatbot Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getConversationHistory(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $validator = $request->validate([
                'conversation_id' => 'required|string'
            ]);

            // Здесь можно получить историю сообщений из базы данных
            // $history = $this->getConversationHistoryFromDB($user->id, $request->conversation_id);

            return response()->json([
                'success' => true,
                'history' => [] // Временный заглушка
            ]);

        } catch (\Exception $e) {
            Log::error('Get Conversation History Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getWeatherByMonth($country) {
        $capital = $this->getCountryCapital($country);
        if (!$capital) return null;
        
        $apiKey = config('services.openweather.api_key');
        $url = "https://api.openweathermap.org/data/2.5/forecast?q={$capital},{$country}&appid={$apiKey}&units=metric";
        
        try {
            $response = Http::get($url);
            $data = $response->json();
            
            if ($data && isset($data['list'])) {
                $monthlyWeather = [];
                foreach ($data['list'] as $forecast) {
                    $month = date('F', $forecast['dt']);
                    if (!isset($monthlyWeather[$month])) {
                        $monthlyWeather[$month] = [
                            'avg_temp' => 0,
                            'count' => 0
                        ];
                    }
                    $monthlyWeather[$month]['avg_temp'] += $forecast['main']['temp'];
                    $monthlyWeather[$month]['count']++;
                }
                
                foreach ($monthlyWeather as &$month) {
                    $month['avg_temp'] = round($month['avg_temp'] / $month['count'], 1);
                    unset($month['count']);
                }
                
                return [
                    'weather' => $monthlyWeather,
                    'source' => "https://openweathermap.org/city/{$data['city']['id']}"
                ];
            }
        } catch (\Exception $e) {
            Log::error('Weather API Error: ' . $e->getMessage());
        }
        
        return null;
    }
    
    public function getHotels($country) {
        $apiKey = config('services.booking.api_key');
        $searchUrl = "https://www.booking.com/searchresults.html?ss={$country}";
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey
            ])->get($searchUrl);
            
            $html = $response->body();
            $hotels = [];
            
            if (preg_match_all('/<div class="sr_property_block".*?<span class="sr-hotel__name".*?>(.*?)<\/span>.*?<div class="bui-price-display__value".*?>(.*?)<\/div>/s', $html, $matches)) {
                for ($i = 0; $i < min(5, count($matches[1])); $i++) {
                    $hotels[] = [
                        'name' => strip_tags($matches[1][$i]),
                        'price' => strip_tags($matches[2][$i]),
                        'url' => "https://www.booking.com/searchresults.html?ss={$country}"
                    ];
                }
            }
            
            return $hotels;
        } catch (\Exception $e) {
            Log::error('Hotels API Error: ' . $e->getMessage());
            return [];
        }
    }
    
    public function getAttractions($country) {
        try {
            $url = "https://en.wikipedia.org/w/api.php?action=query&format=json&prop=extracts&titles=Tourist_attractions_in_{$country}";
            
            $response = Http::get($url);
            $data = $response->json();
            
            if ($data && isset($data['query']['pages'])) {
                $page = reset($data['query']['pages']);
                return [
                    'title' => $page['title'],
                    'extract' => $page['extract'],
                    'url' => "https://en.wikipedia.org/wiki/Tourist_attractions_in_{$country}"
                ];
            }
        } catch (\Exception $e) {
            Log::error('Attractions API Error: ' . $e->getMessage());
        }
        
        return null;
    }
        
    private function getCountryCapital($country) {
        try {
            $url = "https://restcountries.com/v3.1/name/{$country}";
            $response = Http::get($url);
            $data = $response->json();
            
            if ($data && isset($data[0]['capital'][0])) {
                return $data[0]['capital'][0];
            }
        } catch (\Exception $e) {
            Log::error('Country Capital API Error: ' . $e->getMessage());
        }
        
        return null;
    }
    
    public function handleRequest(Request $request) {
        try {
            $action = $request->input('action', '');
            $country = $request->input('country', '');
            
            if (empty($country)) {
                return response()->json(['error' => 'Country is required'], 400);
            }
            
            switch ($action) {
                case 'weather':
                    $data = $this->getWeatherByMonth($country);
                    break;
                case 'hotels':
                    $data = $this->getHotels($country);
                    break;
                case 'attractions':
                    $data = $this->getAttractions($country);
                    break;
                default:
                    return response()->json(['error' => 'Invalid action'], 400);
            }
            
            if ($data === null) {
                return response()->json(['error' => 'No data found'], 404);
            }
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            Log::error('Handle Request Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
} 