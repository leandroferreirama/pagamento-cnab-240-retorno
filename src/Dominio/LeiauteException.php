<?php

namespace Leandroferreirama\PagamentoCnab240Retorno\Dominio;

use Exception;

class LeiauteException extends Exception
{
    /**
     * @param $mensagem
     */
    public function __construct($mensagem){
        parent::__construct($mensagem);
    }
}