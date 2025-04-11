<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Dwelling;

class DwellingSearch extends Field
{
    protected string $view = 'filament.forms.components.dwelling-search'; // Vista personalizada

    // Método para definir la consulta que obtendrá los 'Dwellings'
    public function getDwellingOptions(): \Illuminate\Support\Collection
    {
        // Puedes ajustar la consulta según lo que necesites
        return Dwelling::query()
            ->where('active', true) // Puedes agregar más filtros aquí
            ->orderBy('nom_habitatge') // Ordenar por nombre de habitatge o cualquier otro campo
            ->get()
            ->pluck('nom_habitatge', 'DOMCOD'); // Devuelve las opciones: DOMCOD como valor y 'nom_habitatge' como etiqueta
    }
}

