<?php

namespace Leandroferreirama\PagamentoCnab240Retorno\Dominio\Retorno;

interface Banco
{
    /**
     * @param $codigo
     * @return string
     */
    public function lerMensagem($codigo);
}