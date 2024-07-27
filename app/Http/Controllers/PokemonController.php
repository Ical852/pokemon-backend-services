<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function index()
    {
        try {
            $pokemons = Pokemon::all()->map(function ($pokemon) {
                $splits = explode('-', $pokemon->fibonacci_index);
                $count = count($splits);

                $formattedNickname = "{$pokemon->nickname}-" . $splits[$count-1];
                if ($pokemon->fibonacci_index == "") {
                    $formattedNickname = $pokemon->nickname;
                }
    
                return [
                    'id' => $pokemon->id,
                    'nickname' => $formattedNickname,
                    'fibonacci_index' => $pokemon->fibonacci_index,
                    'created_at' => $pokemon->created_at,
                    'updated_at' => $pokemon->updated_at,
                ];
            });
    
            return response()->json([
                'status' => 200,
                'message' => 'Get Pokemons Success',
                'data' => $pokemons,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong '. $th->getMessage(),
                'data' => null,
            ]);
        }
    }

    public function find(Request $request, $url) {
        try {
            $pokemon = Pokemon::where('url', $url)->first();
            if ($pokemon) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Pokemon Found',
                    'data' => $pokemon,
                ]);
            }
            return response()->json([
                'status' => 200,
                'message' => 'Pokemon Not Found',
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong '. $th->getMessage(),
                'data' => null,
            ]);
        }
    }

    public function catch(Request $request) {
        try {
            $probability = rand(0, 1);
    
            if ($probability === 1) {
                $pokemon = Pokemon::create([
                    'nickname' => $request->input('nickname'),
                    'url' => $request->input('url'),
                ]);
    
                return response()->json([
                    'status' => 200,
                    'message' => "Success to catch Pokemon",
                    'data' => $pokemon
                ]);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to catch Pokemon',
                    'data' => null,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong '. $th->getMessage(),
                'data' => null,
            ]);
        }
    }

    public function release(Request $request, $id)
    {
        try {
            $pokemon = Pokemon::find($id);
            if (!$pokemon) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Pokemon not found',
                    'data' => null
                ]);
            }

            $number = rand(1, 100);
            $isPrime = $this->isPrime($number);

            if ($isPrime) {
                $pokemon->delete();
                return response()->json([
                    'status' => 200,
                    'message' => "Success to release Pokemon",
                    'data' => null
                ]);
            }
                
            return response()->json([
                'status' => 500,
                'message' => "Failed to release Pokemon",
                'data' => null
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong '. $th->getMessage(),
                'data' => null,
            ]);
        }
        
    }

    private function isPrime($num)
    {
        if ($num <= 1) return false;
        for ($i = 2; $i <= sqrt($num); $i++) {
            if ($num % $i == 0) return false;
        }
        return true;
    }

    public function rename(Request $request, $id)
    {
        try {
            $pokemon = Pokemon::find($id);
            if (!$pokemon) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Pokemon not found',
                    'data' => null
                ]);
            }

            $pokemon->nickname = $request->nickname;
            $fibonacci = $pokemon->fibonacci_index;

            if ($fibonacci == "") {
                $pokemon->fibonacci_index = "0";
            } else {
                $pokemon->fibonacci_index = $fibonacci . '-' . strval($this->getFibonacci($fibonacci));
            }

            $pokemon->save();

            return response()->json([
                'status' => 200,
                'message' => 'Success to Rename Pokemon',
                'data' => $pokemon
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong '. $th->getMessage(),
                'data' => null,
            ]);
        }
        
    }

    private function getFibonacci($numbers) {
        $splits = explode('-', $numbers);

        if (count($splits) < 3) {
            if (count($splits) == 1) return '1';
            if (count($splits) == 2) return '1';
        }

        $lastOne = $splits[count($splits) - 1];
        $lastTwo = $splits[count($splits) - 2];

        return intval($lastOne) + intval($lastTwo);
    }
}
