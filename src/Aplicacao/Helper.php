<?php

namespace Leandroferreirama\PagamentoCnab240Retorno\Aplicacao;

class Helper
{
    public static function picture($picture)
    {
        return preg_match('/[X9]\(\d+\)(V9\(\d+\))?/', $picture);
    }

    public static function explodePicture($picture)
    {
        $pictureExploded = explode("-", preg_replace("/[^0-9A-Z]/", "-", $picture));

        return [
            'firstType' => $pictureExploded[0],
            'firstQuantity' => $pictureExploded[1],
            'secondType' => !isset($pictureExploded[2])?: $pictureExploded[2],
            'secondQuantity' => !isset($pictureExploded[3])?: $pictureExploded[3]
        ];
    }
}
