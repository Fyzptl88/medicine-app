namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DrugSearchTest extends TestCase
{
    /** @test */
    public function it_returns_drug_data_with_base_name_and_dosage_form()
    {
        // Step 1: Mock the API responses

        Http::fake([
            // Mock /drugs.json
            'rxnav.nlm.nih.gov/REST/drugs.json*' => Http::response([
                'drugGroup' => [
                    'conceptGroup' => [
                        [
                            'tty' => 'SBD',
                            'conceptProperties' => [
                                [
                                    'rxcui' => '12345',
                                    'name' => 'TestDrug 500 MG Oral Tablet'
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),

            // Mock /related.json (for baseName)
            'rxnav.nlm.nih.gov/REST/rxcui/12345/related.json*' => Http::response([
                'relatedGroup' => [
                    'conceptGroup' => [
                        [
                            'tty' => 'IN',
                            'conceptProperties' => [
                                ['name' => 'TestBaseName']
                            ]
                        ]
                    ]
                ]
            ], 200),

            // Mock /properties.json (for dosageForm)
            'rxnav.nlm.nih.gov/REST/rxcui/12345/properties.json' => Http::response([
                'properties' => [
                    'dosageForm' => 'Oral Tablet'
                ]
            ], 200),
        ]);

        // Step 2: Make the request
        $response = $this->getJson('/search-drugs?drug_name=TestDrug');

        // Step 3: Assert the structure and values
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'results' => [
                         [
                             'rxcui',
                             'name',
                             'baseNames',
                             'dosageForms'
                         ]
                     ]
                 ])
                 ->assertJsonFragment([
                     'rxcui' => '12345',
                     'name' => 'TestDrug 500 MG Oral Tablet',
                     'baseNames' => ['TestBaseName'],
                     'dosageForms' => ['Oral Tablet'],
                 ]);
    }
}
