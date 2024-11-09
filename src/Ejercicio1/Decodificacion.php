<?php

namespace App\Ejercicio1;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Decodificacion
{
    const PDF = "Patata,oi8,oo
                 ElMejor,oF8,Fo
                 BoLiTa,0123456789,23
                 Azul,01,01100
                 OtRo,54?t,?4?
                 Manolita,kju2aq,u2ka
                 PiMiEnTo,_-/.!#,#_";

    #[Route('/decodificacion', name: 'decodificacion')]
    public function index(): Response {
        $lines = explode("\n", self::PDF);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            list($username, $digit_str, $encoded_score) = explode(',', $line);

            $digits = str_split($digit_str);
            $base = count($digits);
            $digit_map = array_flip($digits);

            $decoded_value = 0;
            $encoded_chars = str_split($encoded_score);
            $power = count($encoded_chars) - 1;

            foreach ($encoded_chars as $char) {
                $value = $digit_map[$char];
                $decoded_value += $value * pow($base, $power);
                $power--;
            }

            $results[] = "$username,$decoded_value";
        }

        $responseContent = implode("\n", $results);
        return new Response($responseContent, 200, ['Content-Type' => 'text/plain']);
    }
}