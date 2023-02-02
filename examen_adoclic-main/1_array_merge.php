<?php

/* 
dada una lista de nombres y otra de colores, crear una función que devuelva un array 
en el cual, de forma ordenada, a cada nombre se le asigne un color de la lista de colores (en caso de que existan menos colores
que nombres, se debe volver a empezar por el primer color), por ej:

[
    ['name' => 'name1', 'color' => 'red'],
    ['name' => 'name2', 'color' => 'green'],
    ['name' => 'name3', 'color' => 'blue'],
    ['name' => 'name4', 'color' => 'yellow'],
    ['name' => 'name5', 'color' => 'white'],    
    ['name' => 'name6', 'color' => 'red'],
    ['name' => 'name7', 'color' => 'green'],
    ...
    ['name' => 'name10', 'color' => 'white'],
    ['name' => 'name11', 'color' => 'red'],
]

La función debe seguir funcionando, ya sea se le agreguen o quiten nombres o colores

 */


$getNames = function () {
    $names = [];
    for ($i = 0; $i < 100; $i++) {
        $names[] = 'name' . ($i + 1);
    }
    return $names;
};

$names = $getNames();

$colors = ['red', 'green', 'blue', 'yellow', 'white', 'orange'];



function  get_colors_by_name($colors, $names)
{
    $i = 0;
    $names_colors = [];
    $length_colors = count($colors);
    foreach ($names as $value) {
        $names_colors[] = ['name' => $value, 'color' => $colors[$i]];

        $i++;
        if ($i >= $length_colors) {
            $i = 0;
        }
    }
    return $names_colors;
}
print_r(get_colors_by_name($colors, $names));
