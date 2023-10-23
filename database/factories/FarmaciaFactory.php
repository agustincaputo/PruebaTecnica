<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Farmacia;

class FarmaciaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

     protected $model = Farmacia::class;

    public function definition()
    {
        return [
            'nombre' => Str::random(8),
            'direccion' => Str::random(10).' '.mt_rand(1,1000),
            'latitud' => mt_rand(21,46),
            'longitud' => mt_rand(25,74)
        ];
    }
}
