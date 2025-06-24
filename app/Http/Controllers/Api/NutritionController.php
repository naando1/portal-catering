<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserBodyMetrics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NutritionController extends Controller
{
    /**
     * Calculate BMR based on input data
     */
    public function calculateBMR(Request $request)
    {
        $request->validate([
            'gender' => 'required|in:pria,wanita,male,female',
            'weight' => 'required|numeric|min:20|max:300',
            'height' => 'required|numeric|min:50|max:250',
            'age' => 'required|numeric|min:1|max:120'
        ]);

        $bodyMetrics = new UserBodyMetrics();
        $bmr = $bodyMetrics->calculateBMR(
            $request->gender, 
            $request->weight, 
            $request->height, 
            $request->age
        );

        return response()->json([
            'success' => true,
            'data' => [
                'bmr' => $bmr,
                'formula' => $request->gender === 'pria' || $request->gender === 'male' 
                    ? '(10 × berat) + (6.25 × tinggi) - (5 × umur) + 5'
                    : '(10 × berat) + (6.25 × tinggi) - (5 × umur) - 161'
            ]
        ]);
    }

    /**
     * Calculate TDEE based on BMR and activity level
     */
    public function calculateTDEE(Request $request)
    {
        $request->validate([
            'bmr' => 'required|numeric|min:500|max:5000',
            'activity_level' => 'required|in:sedentari,ringan,sedang,berat,sangat_berat'
        ]);

        $bodyMetrics = new UserBodyMetrics();
        $tdee = $bodyMetrics->calculateTDEE($request->bmr, $request->activity_level);

        $activityFactors = [
            'sedentari' => 1.2,
            'ringan' => 1.375,
            'sedang' => 1.55,
            'berat' => 1.725,
            'sangat_berat' => 1.9,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'tdee' => $tdee,
                'factor' => $activityFactors[$request->activity_level],
                'calculation' => "BMR ({$request->bmr}) × {$activityFactors[$request->activity_level]} = {$tdee}"
            ]
        ]);
    }

    /**
     * Calculate target calories based on goal
     */
    public function calculateTargetCalories(Request $request)
    {
        $request->validate([
            'tdee' => 'required|numeric|min:800|max:6000',
            'diet_goal' => 'required|in:turun_bb,naik_bb,jaga_bb',
            'percentage' => 'nullable|numeric|min:10|max:20'
        ]);

        $bodyMetrics = new UserBodyMetrics();
        $percentage = $request->percentage ?? 15;
        $targetCalories = $bodyMetrics->calculateTargetCalories(
            $request->tdee, 
            $request->diet_goal, 
            $percentage
        );

        $goalLabels = [
            'turun_bb' => 'Turun Berat Badan',
            'naik_bb' => 'Naik Berat Badan',
            'jaga_bb' => 'Jaga Berat Badan',
        ];

        $calculation = '';
        switch ($request->diet_goal) {
            case 'turun_bb':
                $calculation = "TDEE ({$request->tdee}) - {$percentage}% = {$targetCalories}";
                break;
            case 'naik_bb':
                $calculation = "TDEE ({$request->tdee}) + {$percentage}% = {$targetCalories}";
                break;
            case 'jaga_bb':
                $calculation = "TDEE ({$request->tdee}) = {$targetCalories}";
                break;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'target_calories' => $targetCalories,
                'goal' => $goalLabels[$request->diet_goal],
                'percentage' => $percentage,
                'calculation' => $calculation,
                'meal_distribution' => [
                    'breakfast' => round($targetCalories * 0.25),
                    'lunch' => round($targetCalories * 0.35),
                    'dinner' => round($targetCalories * 0.30),
                    'snacks' => round($targetCalories * 0.10)
                ]
            ]
        ]);
    }

    /**
     * Get nutrition analysis for current user
     */
    public function getUserNutritionAnalysis()
    {
        $user = Auth::user();
        
        if (!$user || !$user->hasCompleteHealthProfile()) {
            return response()->json([
                'success' => false,
                'message' => 'Profil kesehatan belum lengkap'
            ], 400);
        }

        $bodyMetrics = $user->bodyMetrics;
        
        if (!$bodyMetrics) {
            return response()->json([
                'success' => false,
                'message' => 'Data metabolisme belum dihitung'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'bmi' => $user->getBmi(),
                'bmi_category' => $user->getBmiCategory(),
                'bmr' => $bodyMetrics->bmr,
                'tdee' => $bodyMetrics->tdee,
                'target_calories' => $bodyMetrics->target_calories,
                'diet_goal' => $user->getDietGoalLabel(),
                'activity_level' => $user->getActivityLevelLabel(),
                'health_conditions' => $user->healthProfile->getActiveConditions(),
                'dietary_restrictions' => $user->healthProfile->getDietaryRestrictions(),
                'recommended_foods' => $user->healthProfile->getRecommendedFoods(),
            ]
        ]);
    }
}