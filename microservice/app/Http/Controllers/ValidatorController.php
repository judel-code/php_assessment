<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use libphonenumber\PhoneNumberUtil;

class ValidatorController
{
    protected $mongo;
    protected $phoneUtil;

    public function __construct()
    {
        // Connect to MongoDB
        $this->mongo = new MongoClient("mongodb://mongodb_container:27017");

        // Create phone number utility instance from libphonenumber
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    public function validate(Request $request)
    {
        $numbers = $request->input('numbers', []);
        $collection = $this->mongo->phone_db->validations;
        $results = ['numbers' => [], 'valid_count' => 0];

        foreach ($numbers as $number) {
            try {
                // Parse and validate the number using libphonenumber
                $phoneNumber = $this->phoneUtil->parse($number);
                $isValid = $this->phoneUtil->isValidNumber($phoneNumber);
                $countryCode = '+' . $phoneNumber->getCountryCode();
                $type = $this->phoneUtil->getNumberType($phoneNumber);
                $isPossible = $this->phoneUtil->isPossibleNumber($phoneNumber);

                $result = [
                    'number' => $number,
                    'country_code' => $countryCode,
                    'type' => $type,
                    'is_possible' => $isPossible,
                    'is_valid' => $isValid
                ];

                $results['numbers'][] = $result;

                if ($isValid) {
                    $results['valid_count']++;
                }

                // Save validation result to MongoDB
                $collection->insertOne($result + ['validated_at' => new \DateTime()]);
            } catch (\Exception $e) {
                // Catch and log errors
                $results['numbers'][] = [
                    'number' => $number,
                    'error' => $e->getMessage()
                ];
                file_put_contents('php://stderr', "Validation error for $number: " . $e->getMessage() . "\n");
            }
        }

        // Return results
        return response()->json($results);
    }
}
