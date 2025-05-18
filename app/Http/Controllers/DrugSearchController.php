<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DrugSearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'drug_name' => 'required|string'
        ]);

        $drugName = $request->input('drug_name');

        // Fetch drugs with tty = SBD
        $drugResponse = Http::get("https://rxnav.nlm.nih.gov/REST/drugs.json", [
            'name' => $drugName
        ]);

        if (!$drugResponse->successful()) {
            return response()->json(['success' => false, 'message' => 'Drug search failed.'], 500);
        }

        $drugData = $drugResponse->json();
        $conceptGroups = $drugData['drugGroup']['conceptGroup'] ?? [];

        // Get the SBD conceptProperties only
        $sbdGroup = collect($conceptGroups)->firstWhere('tty', 'SBD');
        $sbdDrugs = collect($sbdGroup['conceptProperties'] ?? [])->take(5);

        $results = [];

        foreach ($sbdDrugs as $drug) {
            $rxcui = $drug['rxcui'];
            $name = $drug['name'];

            $baseNames = [];
            $dosageForms = [];

            // Get related ingredients (base names)
            $relatedResponse = Http::get("https://rxnav.nlm.nih.gov/REST/rxcui/{$rxcui}/related.json", [
                'tty' => 'IN'
            ]);

            if ($relatedResponse->successful()) {
                $relatedData = $relatedResponse->json();
                $concepts = $relatedData['relatedGroup']['conceptGroup'][0]['conceptProperties'] ?? [];

                foreach ($concepts as $concept) {
                    if (!empty($concept['name'])) {
                        $baseNames[] = $concept['name'];
                    }
                }
            }

            // Get dosage form
            $propertiesResponse = Http::get("https://rxnav.nlm.nih.gov/REST/rxcui/{$rxcui}/properties.json");

            if ($propertiesResponse->successful()) {
                $propertiesData = $propertiesResponse->json();
                $dosage = $propertiesData['properties']['dosageForm'] ?? null;
                if ($dosage) {
                    $dosageForms[] = $dosage;
                }
            }

            $results[] = [
                'rxcui' => $rxcui,
                'name' => $name,
                'baseNames' => array_values(array_unique($baseNames)),
                'dosageForms' => array_values(array_unique($dosageForms)),
            ];
        }


        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
}
