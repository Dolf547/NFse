<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 15/05/2019
 * Time: 20:26
 */

namespace Nfsews\Providers\Prodam\V2\Response;


class PedidoConsultaCnpjResponse extends Response
{
    public $inscricaoMunicipal = null;
    public $emiteNfe = null;


    public function parseXml($xml){
        if (stripos($xml, 'RetornoConsultaCNPJ') === false){
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

            // verifica se houve sucesso ou falha
            $cabecalho = $dom->getElementsByTagName('Cabecalho')->item(0);
            if (! empty($cabecalho)  && ! empty($cabecalho->getElementsByTagName('Sucesso')->item(0))  ){
                $this->sucesso = $cabecalho->getElementsByTagName('Sucesso')->item(0)->nodeValue;
            }

            // Verifica se tem lista de erros
            $erros = $dom->getElementsByTagName('Erro');
            if ($erros->length > 0 ){
                foreach ($erros as $erro){
                    if (
                        $erro->getElementsByTagName('Codigo')->length > 0
                        && $erro->getElementsByTagName('Descricao')->length > 0
                    ){
                        array_push($this->erros, [
                            'codigo' => $erro->getElementsByTagName('Codigo')->item(0)->nodeValue,
                            'descricao' => $erro->getElementsByTagName('Descricao')->item(0)->nodeValue,
                        ] );
                    }
                }
            }

            // Verifica se tem lista alertas
            $alertas = $dom->getElementsByTagName('Alerta');
            if ($alertas->length > 0 ){
                foreach ($alertas as $alerta){
                    $data = [];
                    $data['codigo'] = ($alerta->getElementsByTagName('Codigo')->length > 0)
                        ? $alerta->getElementsByTagName('Codigo')->item(0)->nodeValue : null;

                    $data['descricao'] = ($alerta->getElementsByTagName('Descricao')->length > 0)
                        ? $alerta->getElementsByTagName('Descricao')->item(0)->nodeValue : null;

                    $data['numeroNfe'] = ($alerta->getElementsByTagName('NumeroNFe')->length > 0)
                        ? $alerta->getElementsByTagName('NumeroNFe')->item(0)->nodeValue : null;

                    $data['inscricaoPrestador'] = ($alerta->getElementsByTagName('InscricaoPrestador')->length > 0)
                        ? $alerta->getElementsByTagName('InscricaoPrestador')->item(0)->nodeValue : null;

                    $data['serieRps'] = ($alerta->getElementsByTagName('SerieRPS')->length > 0)
                        ? $alerta->getElementsByTagName('SerieRPS')->item(0)->nodeValue : null;

                    $data['numeroRps'] = ($alerta->getElementsByTagName('NumeroRPS')->length > 0)
                        ? $alerta->getElementsByTagName('NumeroRPS')->item(0)->nodeValue : null;

                    if (! empty($data['codigo']) ){
                        array_push($this->alertas, $data);
                    }
                }
            }

            $detalhe = $dom->getElementsByTagName('Detalhe');
            if ($detalhe->length > 0){
                $this->inscricaoMunicipal = ($detalhe->item(0)->getElementsByTagName('InscricaoMunicipal')->length > 0)
                    ? $detalhe->item(0)->getElementsByTagName('InscricaoMunicipal')->item(0)->nodeValue : null;

                $this->emiteNfe = ($detalhe->item(0)->getElementsByTagName('EmiteNFe')->length > 0)
                    ? $detalhe->item(0)->getElementsByTagName('EmiteNFe')->item(0)->nodeValue : null;
            }





        }catch (\Exception $e){
            return false;
        }

        return true;
    }
}