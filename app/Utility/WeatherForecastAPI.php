<?php
namespace App\Utility;

use App\Utility\IHttpClient;
use Illuminate\Support\Carbon;

class WeatherForecastAPI{
    protected $httpClient;
    public function __construct(IHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getWeatherForecast(Carbon $date){
        $dateString = $date->toDateString();
        $params = ['date'=>$dateString];
        $this->httpClient->request('GET', 'https://api.data.gov.sg/v1/environment/4-day-weather-forecast', $params);
        $response = $this->httpClient->getResponseBody();
        $itemsCount = count($response['items']);
        $forecastRaw = $response['items'][$itemsCount-1]['forecasts'];
        
        $forecast = array();

        foreach($forecastRaw as $fc){
            $date = Carbon::parse($fc['date']); 
            $forecast[$date->toDateString()]=$fc['forecast']; 
        }

        return $forecast;
    }
}