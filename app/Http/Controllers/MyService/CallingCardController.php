<?php

namespace App\Http\Controllers\MyService;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\CallingCard;
use App\Models\RateTable;
use App\Models\RateTableGroup;
use App\Models\TelecomCountry;
use App\Models\TelecomProvider;
use App\Models\TelecomProviderConfig;
use App\Http\Controllers\Controller;

class CallingCardController extends Controller
{
    private $service_id;
    private $client;

    public function __construct()
    {
        parent::__construct();
        $this->service_id = 7;
        $this->client = new Client([
            'base_uri' => API_END_POINT,
            'timeout'  => 120,
        ]);
    }

    private function apiRequest(string $endpoint)
    {
        try {
            $response = $this->client->request('GET', $endpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer " . API_TOKEN
                ],
            ]);

            if ($response->getStatusCode() == 200) {
                return json_decode((string)$response->getBody(), true);
            } else {
                throw new \Exception("Failed to retrieve data from {$endpoint} with status code " . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            Log::error("Error fetching data from {$endpoint}: " . $e->getMessage());
            return null;
        }
    }

    public function getCountry()
    {
        $response_data = $this->apiRequest('country');
        if (!$response_data) {
            return false;
        }

        foreach ($response_data['data']['telecom_countries'] as $countryData) {
            if (!isset($countryData['country_id'])) continue;

            TelecomCountry::updateOrCreate(
                ['country_id' => $countryData['country_id']],
                [
                    'status' => $countryData['status'],
                    'created_at' => $countryData['created_at'],
                    'created_by' => $countryData['created_by'],
                    'updated_at' => $countryData['updated_at'],
                    'updated_by' => $countryData['updated_by']
                ]
            );
        }

        return true;
    }

    public function providerConfig()
    {
        $response_data = $this->apiRequest('providerConfig');
        if (!$response_data) {
            return false;
        }

        foreach ($response_data['data']['providerConfig'] as $providerConfig) {
            if (!isset($providerConfig['name'])) continue;

            TelecomProviderConfig::updateOrCreate(
                ['name' => $providerConfig['name']],
                [
                    'country_id' => $providerConfig['country_id'],
                    'name' => $providerConfig['name'],
                    'bimedia_card' => 1,
                    'status' => $providerConfig['status'],
                    'created_at' => $providerConfig['created_at'],
                    'updated_at' => $providerConfig['updated_at'],
                    'updated_by' => $providerConfig['updated_by']
                ]
            );
        }

        return true;
    }

    public function providerDenomination()
    {
        $response_data = $this->apiRequest('ProviderDenomination');
        if (!$response_data) {
            return false;
        }

        foreach ($response_data['data']['providerDenomination'] as $providerDenomination) {
            if (!isset($providerDenomination['name'])) continue;

            $existingProvider = TelecomProviderConfig::where('name', $providerDenomination['name'])->first();
            if (!$existingProvider) continue;

            TelecomProvider::updateOrCreate(
                ['name' => $providerDenomination['name'], 'face_value' => $providerDenomination['face_value']],
                [
                    'tp_config_id' => $existingProvider->id,
                    'name' => $providerDenomination['name'],
                    'face_value' => $providerDenomination['face_value'],
                    'bimedia_card' => 1,
                    'description' => $providerDenomination['description'],
                    'status' => $providerDenomination['status'],
                    'is_card' => 1,
                    'created_at' => $providerDenomination['created_at'],
                    'created_by' => $providerDenomination['created_by'],
                    'updated_at' => $providerDenomination['updated_at'],
                    'updated_by' => $providerDenomination['updated_by']
                ]
            );
        }

        return true;
    }

    public function getCC()
    {
        $response_data = $this->apiRequest('GetCC');
        if (!$response_data) {
            return false;
        }
        foreach ($response_data['data']['callingCard'] as $cc) {
            $existingProvider = TelecomProvider::where('name', $cc['Telename'])->where('face_value', $cc['face_value'])->first();

            if (!$existingProvider) continue;

            $callingCardData = [
                "telecom_provider_id" => $existingProvider->id,
                "service_id" => $cc['service_id'],
                "name" => $cc['name'],
                "description" => $cc['description'],
                "buying_price" => floatval($cc['sale_price']),
                "buying_price1" => floatval($cc['sale_price']),
                "face_value" => $cc['face_value'],
                "comment_1" => $cc['comment_1'],
                "comment_2" => $cc['comment_2'],
                "status" => $cc['status'],
                "activate" => $cc['activate'],
                "number_of_cards" => 1,
                "aleda_product_code" => '',
                "bimedia_product_code" => '',
                "updated_at" => $cc['updated_at'],
                "updated_by" => $cc['updated_by'],
                "created_at" => $cc['created_at'],
                "created_by" => $cc['created_by']
            ];
// Update or create a CallingCard instance
            $callingCard = CallingCard::updateOrCreate(
                [
                    'telecom_provider_id' => $existingProvider->id,
                    'face_value' => $existingProvider->face_value
                ],
                $callingCardData
            );

// Retrieve all RateTableGroup records with associated user data
            $rateTableGroups = RateTableGroup::join('users', 'users.id', '=', 'rate_table_groups.user_id')
                ->select('rate_table_groups.id', 'rate_table_groups.user_id')
                ->get();

// Iterate over each RateTableGroup record
            foreach ($rateTableGroups as $rateTableGroup) {
                // Ensure rateTableGroup is not null and contains necessary data
                if ($rateTableGroup && isset($rateTableGroup->user_id, $rateTableGroup->id)) {
                    // Check if the RateTable record already exists with the given criteria
                    $existingRateTable = RateTable::where('user_id', $rateTableGroup->user_id)
                        ->where('rate_group_id', $rateTableGroup->id)
                        ->where('cc_id', $callingCard->id)
//                        ->where('buying_price', $callingCard->buying_price)
                        ->first();

                    if ($existingRateTable) {
                        if ($existingRateTable->sale_price != '0.00'){
                            // Update the existing record's sale_price
                            $existingRateTable->where('id', $existingRateTable->id)
                                ->update([
                                    'buying_price' => $callingCard->buying_price,
                                    'sale_margin' => $existingRateTable->buying_price - $callingCard->buying_price,
                                    'updated_at' => now(), // Update timestamp
                                    'updated_by' => auth()->user()->id // Assuming you have an updated_by field
                                ]);
                        }
                    } else {
                        // Prepare the rate table data for creating a new record
                        $rateTableData = [
                            'user_id' => $rateTableGroup->user_id,
                            'rate_group_id' => $rateTableGroup->id,
                            'cc_id' => $callingCard->id,
                            'buying_price' => $callingCard->buying_price,
                            'sale_price' => "0.00",
                            'sale_margin' => "0.00",
                            'created_at' => now(), // Use Laravel's helper for current timestamp
                            'created_by' => auth()->user()->id
                        ];

                        // Create a new RateTable record
                        RateTable::create($rateTableData);
                    }
                }
            }

        }
        return true;
    }

    public function processAll()
    {
        $steps = [
            'Step 1: Fetching Countries' => 'getCountry',
            'Step 2: Fetching Provider Config' => 'providerConfig',
            'Step 3: Fetching Provider Denomination' => 'providerDenomination',
            'Step 4: Fetching Calling Cards' => 'getCC',
        ];

        $results = [];

        foreach ($steps as $step => $method) {
            try {
                $result = $this->$method();
                $status = $result ? 'completed successfully' : 'failed';
            } catch (\Exception $e) {
                Log::error("Error in {$step}: " . $e->getMessage());
                $status = 'failed due to exception';
            }
            $results[] = [
                'step' => $step,
                'status' => $status,
            ];
            if (!$results) {
                break;
            }
        }
        return view('service-config.telecom-countries.processResults', ['results' => $results]);
    }
}
