<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 27/05/2019
 * Time: 20:28
 */

namespace Nfsews\Providers\Procempa\V3\Response;


use Nfsews\Response;

class PedidoCancelarNfseResponse extends Response
{
    public $numeroNfse = null;
    public $cnpjPrestador = null;
    public $inscricaoMunicipalPrestador = null;
    public $codigoMunicipio = null;
    public $codigoCancelamento = null;
    public $dataHora = null;


    public function parseXml($xml){
        if (stripos($xml, 'EnviarLoteRpsResposta') === false){
            // Significa que o servidor da Prefeitura não forneceu uma resposta válida da operação
            return false;
        }

        $this->xmlResposta = $xml;

        $dom = null;
        try{
            $dom = new \DOMDocument('1.0', 'utf-8');
            $dom->loadXML( $xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;


            // Verifica se tem lista de erros
            $erros = $dom->getElementsByTagName('MensagemRetorno');
            if ($erros->length > 0 ){
                foreach ($erros as $erro){
                    if (
                        $erro->getElementsByTagName('Codigo')->length > 0
                        && $erro->getElementsByTagName('Mensagem')->length > 0
                    ){
                        array_push($this->erros, [
                            'codigo' => $erro->getElementsByTagName('Codigo')->item(0)->nodeValue,
                            'mensagem' => $erro->getElementsByTagName('Mensagem')->item(0)->nodeValue,
                            'correcao'  =>  ($erro->getElementsByTagName('Correcao')->length > 0) ?
                                $erro->getElementsByTagName('Correcao')->item(0)->nodeValue : null
                        ] );
                    }
                }
            }

            //Pega o Número do lote
            $this->numeroNfse = ($dom->getElementsByTagName('Numero')->length > 0) ?
                $dom->getElementsByTagName('Numero')->item(0) : null;

            $this->inscricaoMunicipalPrestador = ($dom->getElementsByTagName('InscricaoMunicipal')->length > 0) ?
                $dom->getElementsByTagName('InscricaoMunicipal')->item(0) : null;

            $this->cnpjPrestador = ($dom->getElementsByTagName('Cnpj')->length > 0) ?
                $dom->getElementsByTagName('Cnpj')->item(0) : null;

            $this->codigoMunicipio = ($dom->getElementsByTagName('CodigoMunicipio')->length > 0) ?
                $dom->getElementsByTagName('CodigoMunicipio')->item(0) : null;

            $this->codigoCancelamento = ($dom->getElementsByTagName('CodigoCancelamento')->length > 0) ?
                $dom->getElementsByTagName('CodigoCancelamento')->item(0) : null;

            $this->dataHora = ($dom->getElementsByTagName('DataHora')->length > 0) ?
                $dom->getElementsByTagName('DataHora')->item(0) : null;




        }catch (\Exception $e){
            return false;
        }

        return true;
    }
}