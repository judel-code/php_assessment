<?php

namespace Tests;

class ValidatorTest extends TestCase
{
    public function testValidNumber()
    {
        $response = $this->post('/api/validate', [
            'numbers' => ['+14155552671']
        ]);

        $response->seeStatusCode(200);
        $response->seeJsonContains(['is_valid' => true]);
    }

    public function testInvalidNumber()
    {
        $response = $this->post('/api/validate', [
            'numbers' => ['invalid123']
        ]);

        $response->seeStatusCode(200);
        $response->seeJsonStructure([
            'numbers' => [
                ['number', 'error']
            ]
        ]);
    }

    public function testBatchValidation()
    {
        $response = $this->post('/api/validate', [
            'numbers' => ['+14155552671', '+442083661177']
        ]);

        $response->seeStatusCode(200);
        $response->seeJsonContains(['is_valid' => true]);
        $response->seeJson(['valid_count' => 2]);
    }

    public function testMissingNumbersField()
    {
        $response = $this->post('/api/validate', []);

        $response->seeStatusCode(200);
        $response->seeJsonEquals([
            'numbers' => [],
            'valid_count' => 0
        ]);
    }

}
