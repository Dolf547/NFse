<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 27/05/2019
 * Time: 19:31
 */

namespace Nfsews\Providers\Procempa\V3\Response;


use Nfsews\Response;

class PedidoEnvioLoteRpsResponse extends Response
{
    public $numeroLote = null;
    public $dataRecebimento = null;
    public $protocolo = null;

    
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
            $this->numeroLote = ($dom->getElementsByTagName('NumeroLote')->length > 0) ?
                $dom->getElementsByTagName('NumeroLote')->item(0) : null;

            $this->dataRecebimento = ($dom->getElementsByTagName('DataRecebimento')->length > 0) ?
                $dom->getElementsByTagName('DataRecebimento')->item(0) : null;

            $this->protocolo = ($dom->getElementsByTagName('Protocolo')->length > 0) ?
                $dom->getElementsByTagName('Protocolo')->item(0) : null;




        }catch (\Exception $e){
            return false;
        }

        return true;
    }
}