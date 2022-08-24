<?php

namespace Leandroferreirama\PagamentoCnab240Retorno\Dominio;

use Leandroferreirama\PagamentoCnab240Retorno\Aplicacao\Helper;
use \Symfony\Component\Yaml\Yaml as YamlSymfony;

class Yaml extends YamlSymfony
{
    /**
     * @var
     */
    private $pasta;

    /**
     * @param $pasta
     */
    public function __construct($pasta)
    {
        $this->pasta = $pasta;
    }

    /**
     * @param $segmento
     * @param $arquivo
     * @return mixed
     * @throws YamlException
     */
    public function lerArquivo($segmento, $arquivo)
    {
        $nomeArquivo = $this->pasta . '/' . $arquivo;
        if (!file_exists($nomeArquivo)){
            throw new YamlException("Arquivo de configuração {$segmento}.yml não encontrado em: $this->pasta");
        }
        return $this->validaInformacoes($segmento, $this->parse(file_get_contents($nomeArquivo)));
    }

    /**
     * @param $metodo
     * @param $campos
     * @return mixed
     * @throws LeiauteException
     * @throws YamlException
     */
    public function validaInformacoes($metodo, $campos)
    {
        if (empty($campos)) {
            $mensagem = "Não localizei os campos no {$metodo}";
            throw new YamlException($mensagem);
        }
        $this->validaArquivo($campos);

        return $campos;
    }

    /**
     * @param $campos
     * @return void
     * @throws LeiauteException
     */
    public function validaArquivo($campos)
    {
        foreach ($campos as $nome => $campo) {
            $posicao_inicial = $campo['pos'][0];
            $posicao_final = $campo['pos'][1];

            foreach ($campos as $nome_atual => $campo_atual) {
                if (!Helper::picture($campo_atual['picture'])){
                    throw new LeiauteException("O picture do atributo {$nome_atual} é inválido.");
                }

                if ($nome_atual === $nome)
                    continue;
                $posicao_inicial_atual = $campo_atual['pos'][0];
                $posicao_final_atual = $campo_atual['pos'][1];
                if (!is_numeric($posicao_inicial_atual) || !is_numeric($posicao_final_atual))
                    continue;
                if ($posicao_inicial_atual > $posicao_final_atual){
                    throw new LeiauteException("O campo {$nome_atual} com posição inicial em ({$posicao_inicial_atual}) deve ser menor ou igual a ({$posicao_final_atual})");
                }

                if (($posicao_inicial >= $posicao_inicial_atual && $posicao_inicial <= $posicao_final_atual) || ($posicao_final <= $posicao_final_atual && $posicao_final >= $posicao_inicial_atual)){
                    throw new LeiauteException("O campo {$nome} está colidindo com o campo {$nome_atual}, ajustar os dados");
                }
            }
        }
    }
}
