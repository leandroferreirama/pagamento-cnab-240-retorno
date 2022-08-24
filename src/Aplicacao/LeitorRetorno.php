<?php

namespace Leandroferreirama\PagamentoCnab240Retorno\Aplicacao;

use \InvalidArgumentException;
use Leandroferreirama\PagamentoCnab240Retorno\Dominio\Yaml;

class LeitorRetorno
{
    private $conteudoBruto = [];
    private $codigoBanco;
    private $banco;
    private $lotesBruto = [];
    private $conteudo = [];

    /**
     * @param $arquivo
     * @param $exluir
     * @return array
     */
    public function recepcionarArquivo($arquivo, $exluir = true)
    {
        if (empty($arquivo)) {
            throw new InvalidArgumentException('Envie um arquivo!');
        }
        if (is_array($arquivo["name"])) {
            $this->validarArrayArquivo($arquivo);
        } else {
            $this->validarArquivo($arquivo);
        }
        $this->gerarConteudoLote();
        $this->gerarLotes();

        return $this->conteudo;
    }

    /**
     * @param $arquivo
     * @return void
     */
    private function validarArrayArquivo($arquivo)
    {
        if (empty($arquivo)) {
            throw new InvalidArgumentException('Envie um arquivo!');
        }
        $this->validaConteudoArquivo($arquivo["name"][0]);
        $count = count($arquivo["name"]);
        for ($i = 0; $i < $count; $i++) {
            $this->validarTipoArquivo($arquivo["tmp_name"][$i], $arquivo["type"][$i]);
            $this->atribuirConteudo($arquivo["tmp_name"][$i]);
        }
    }

    /**
     * @param $arquivo
     * @return void
     */
    private function validarArquivo($arquivo)
    {
        $this->validaConteudoArquivo($arquivo["name"]);
        $this->validarTipoArquivo($arquivo["tmp_name"], $arquivo["type"]);
    }

    /**
     * @param $nomeArquivo
     * @return void
     */
    private function validaConteudoArquivo($nomeArquivo)
    {
        if (!isset($nomeArquivo) || empty($nomeArquivo)) {
            $this->conteudoBruto = NULL;
            throw new InvalidArgumentException('Não localizei um arquivo para avaliação!');
        }
    }

    /**
     * @param $arquivo
     * @return void
     */
    public function validarTipoArquivo($nomeTemporario, $tipoArquivo)
    {
        if (!(mime_content_type($nomeTemporario) == 'text/plain' || $tipoArquivo == 'text/plain')) {
            $this->conteudoBruto = NULL;
            throw new InvalidArgumentException('Tipo de Arquivo inválido!');
        }
        if (!(file_exists($nomeTemporario) && is_file($nomeTemporario))) {
            $this->conteudoBruto = NULL;
            throw new InvalidArgumentException('Ocorreu um erro inesperado com o arquivo!');
        }
    }

    /**
     * @param $nomeArquivo
     * @return void
     */
    private function atribuirConteudo($nomeArquivo)
    {
        $filter_var_array = filter_var_array(file($nomeArquivo), FILTER_SANITIZE_SPECIAL_CHARS);
        $this->conteudoBruto = array_merge($this->conteudoBruto, $filter_var_array);
    }

    /**
     * @param $codigoBanco
     * @return void
     */
    private function setBanco($codigoBanco)
    {
        $bancosHomologados = $this->bancosHomologados();
        if (!isset($bancosHomologados[$codigoBanco])) {
            throw new InvalidArgumentException("Banco {$this->codigoBanco} não está homologado");
        }
        $this->codigoBanco = $codigoBanco;
        $this->banco = $bancosHomologados[$codigoBanco];
    }

    /**
     * @param $banco
     * @return string
     */
    private function pasta($banco)
    {
        return __DIR__ . "/../Leiaute/{$banco}";
    }

    /**
     * @return string[]
     */
    public function bancosHomologados()
    {
        return [
            '341' => 'Itau',
            '237' => 'Bradesco'
        ];
    }

    /**
     * @return array
     */
    public function gerarConteudoLote()
    {
        $conteudoLote = [];
        for ($i = 0; $i < count($this->conteudoBruto); $i++) {
            $tipoSegmento = substr($this->conteudoBruto[$i], 7, 1);
            if ($tipoSegmento == 5) {
                array_push($this->lotesBruto, $conteudoLote);
                $conteudoLote = [];
            } else if ($tipoSegmento != 9 && $tipoSegmento != 0) {
                array_push($conteudoLote, $this->conteudoBruto[$i]);
            }
        }
        return $conteudoLote;
    }


    /**
     * @return void
     */
    private function gerarLotes()
    {
        $i = 1;
        foreach ($this->lotesBruto as $lote) {
            $headerLote = $this->gerarHeaderLote(array_shift($lote));
            $tipoOperacao = $headerLote["tipo_operacao"];

            if ($tipoOperacao == 'C') {
                $arrayLotes = [];
                foreach ($lote as $detalhe) {
                    $segmento = mb_substr($detalhe, 13, 1);
                    $segmentoComplemento = trim(mb_substr($detalhe, 14, 5));
                    if (($segmento == 'J' && $segmentoComplemento != '52') || $segmento == 'A') {
                        array_push($arrayLotes, $this->lerDetalhe($segmento, $detalhe));
                    }
                }
                $i++;
                $headerLote['detalhes'] = array_merge($headerLote['detalhes'], $arrayLotes);
                array_push($this->conteudo, $headerLote);
            }
        }
    }

    /**
     * @param $segmento
     * @param $detalhe
     * @return array|mixed
     * @throws \Leandroferreirama\PagamentoCnab240Retorno\Dominio\YamlException
     */
    private function lerDetalhe($segmento, $detalhe)
    {
        $yamlDetalhe = $this->yaml->lerArquivo($segmento, "{$segmento}.yml");
        $arrayConteudo = [];
        foreach ($yamlDetalhe as $item => $conteudo) {
            $picture = Helper::explodePicture($conteudo["picture"]);
            $qtdeCorte = (int)$picture["firstQuantity"];
            $valor = mb_substr($detalhe, ($conteudo["pos"][0] - 1), $qtdeCorte);
            $arrayConteudo = $arrayConteudo + [$item => $valor];
            if($item == 'ocorrencia'){
                $resultado = 'erro';
                if($valor == '00'){
                    $resultado = 'efetivado';
                } else if($valor == 'BD'){
                    $resultado = 'agendado';
                }
                $arrayConteudo = $arrayConteudo + ['resultado' => $resultado];
            }
        }
        return $arrayConteudo;
    }

    /**
     * @param $headerLote
     * @return array|array[]|mixed
     * @throws \Leandroferreirama\PagamentoCnab240Retorno\Dominio\YamlException
     */
    private function gerarHeaderLote($headerLote)
    {
        $codigoBanco = mb_substr($headerLote, 0, 3);
        $this->setBanco($codigoBanco);
        $this->yaml = new Yaml($this->pasta($this->banco));
        $yamlLote = $this->yaml->lerArquivo("Header Lote", "header_lote.yml");

        $arrayConteudo = [];
        foreach ($yamlLote as $item => $conteudo) {
            $picture = Helper::explodePicture($conteudo["picture"]);
            $qtdeCorte = (int)$picture["firstQuantity"];
            $valor = mb_substr($headerLote, ($conteudo["pos"][0] - 1), $qtdeCorte);
            $arrayConteudo = $arrayConteudo + [$item => $valor];
        }
        //incluo o detalhe
        $arrayConteudo = $arrayConteudo + ["detalhes" => []];
        return $arrayConteudo;
    }
}