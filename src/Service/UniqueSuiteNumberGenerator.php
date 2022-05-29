<?php

namespace App\Service;

class UniqueSuiteNumberGenerator
{
    public function generate($size)
    {
        $numbers = "0123456789";

        $numbers = str_shuffle($numbers);
        $numbers = substr($numbers,0, $size);

        return $numbers;

        // Initialisation des caractères utilisables
        /*$characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

        for($i=0;$i<$size;$i++)
        {
            $password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
        }

        $numbers = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $data = 0;

        for ($i = 1; $i < $size; $i++) {
            $data .= $numbers[array_rand($numbers)];
        }*/

        //$Chaine = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"; // 62 caractères au total
    }
}