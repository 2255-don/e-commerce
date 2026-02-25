<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lignes de langue pour la validation
    |--------------------------------------------------------------------------
    |
    | Les lignes de langue suivantes contiennent les messages d'erreur par défaut
    | utilisés par la classe de validation.
    |
    */

    'required' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'unique' => 'La valeur du champ :attribute est déjà utilisée.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'email' => [
            'required' => 'Votre adresse e-mail est requise.',
        ],
        'password' => [
            'required' => 'Le mot de passe est requis.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
        'name' => 'nom',
    ],

];
