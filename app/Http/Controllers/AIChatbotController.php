<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIChatbotController extends Controller
{
    public function getWeather(Request $request)
    {
        try {
            $country = $request->input('country');
            $month = $request->input('month');
            
            if (!$country || !$month) {
                return response()->json(['error' => 'Country and month are required'], 400);
            }

            // Простой ответ без внешних API
            return response()->json([
                'message' => "В {$country} в месяце {$month} обычно хорошая погода для путешествий. Рекомендуем взять с собой легкую одежду и зонт на случай дождя.",
                'source' => "https://www.google.com/search?q=погода+в+{$country}+в+{$month}"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Weather API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    public function getHotels(Request $request)
    {
        try {
            $country = $request->input('country');
            $city = $request->input('city');
            
            if (!$country || !$city) {
                return response()->json(['error' => 'Country and city are required'], 400);
            }

            // Простой ответ с информацией о поиске отелей
            return response()->json([
                'message' => "Для поиска отелей в {$city}, {$country} посетите Booking.com",
                'search_url' => "https://www.booking.com/searchresults.html?ss={$city},{$country}"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Hotels API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    public function getAttractions(Request $request)
    {
        try {
            $country = $request->input('country');
            
            if (!$country) {
                return response()->json(['error' => 'Country is required'], 400);
            }

            // Простой ответ без внешних API
            return response()->json([
                'message' => "В {$country} есть множество интересных достопримечательностей. Рекомендуем посетить основные туристические места и музеи.",
                'source' => "https://en.wikipedia.org/wiki/Tourism_in_" . str_replace(' ', '_', ucwords($country))
            ]);
            
        } catch (\Exception $e) {
            Log::error('Attractions API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    private function getCountryCapital($country)
    {
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
}