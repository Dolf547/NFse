<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 19/05/2019
 * Time: 14:50
 */

namespace Nfsews\Providers\Prodam\V2\Response;


class PedidoConsultaGuiaAsyncResponse extends Response
{
    public $listaGuias = [];

    public function parseXml($xml){

        if (stripos($xml, 'ConsultaGuiaResponse') === false){
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


            // Verifica se tem ChaveNFeRPS
            $guias = $dom->getElementsByTagName('Guia');
            if ($guias->length > 0 ){
                foreach ($guias as $guia){
                    $data = [];
                    $data['inscricaoPrestador'] = ($guia->getElementsByTagName('InscricaoPrestador')->length > 0)
                        ? $guia->getElementsByTagName('InscricaoPrestador')->item(0)->nodeValue : null;

                    $data['numeroGuia'] = ($guia->getElementsByTagName('NumeroGuia')->length > 0)
                        ? $guia->getElementsByTagName('NumeroGuia')->item(0)->nodeValue : null;

                    $data['incidencia'] = ($guia->getElementsByTagName('Incidencia')->length > 0)
                        ? $guia->getElementsByTagName('Incidencia')->item(0)->nodeValue : null;

                    $data['valorTotal'] = ($guia->getElementsByTagName('ValorTotal')->length > 0)
                        ? $guia->getElementsByTagName('ValorTotal')->item(0)->nodeValue : null;

                    $data['valorIss'] = ($guia->getElementsByTagName('ValorIss')->length > 0)
                        ? $guia->getElementsByTagName('ValorIss')->item(0)->nodeValue : null;

                    $data['valorTotalPagamento'] = ($guia->getElementsByTagName('ValorTotalPagamento')->length > 0)
                        ? $guia->getElementsByTagName('ValorTotalPagamento')->item(0)->nodeValue : null;

                    $data['status'] = ($guia->getElementsByTagName('Status')->length > 0)
                        ? $guia->getElementsByTagName('Status')->item(0)->nodeValue : null;

                    $data['situacao'] = ($guia->getElementsByTagName('Situacao')->length > 0)
                        ? $guia->getElementsByTagName('Situacao')->item(0)->nodeValue : null;

                    $data['referencia'] = ($guia->getElementsByTagName('Referencia')->length > 0)
                        ? $guia->getElementsByTagName('Referencia')->item(0)->nodeValue : null;

                    $data['dataEmissao'] = ($guia->getElementsByTagName('DataEmissao')->length > 0)
                        ? $guia->getElementsByTagName('DataEmissao')->item(0)->nodeValue : null;

                    $data['dataVencimento'] = ($guia->getElementsByTagName('DataVencimento')->length > 0)
                        ? $guia->getElementsByTagName('DataVencimento')->item(0)->nodeValue : null;

                    $data['dataPagamento'] = ($guia->getElementsByTagName('DataPagamento')->length > 0)
                        ? $guia->getElementsByTagName('DataPagamento')->item(0)->nodeValue : null;

                    $data['dataQuitacao'] = ($guia->getElementsByTagName('DataQuitacao')->length > 0)
                        ? $guia->getElementsByTagName('DataQuitacao')->item(0)->nodeValue : null;

                    $data['dataCancelamento'] = ($guia->getElementsByTagName('DataCancelamento')->length > 0)
                        ? $guia->getElementsByTagName('DataCancelamento')->item(0)->nodeValue : null;

                    $data['linhaDigitavel'] = ($guia->getElementsByTagName('LinhaDigitavel')->length > 0)
                        ? $guia->getElementsByTagName('LinhaDigitavel')->item(0)->nodeValue : null;

                    if (! empty($data['codigo']) ){
                        array_push($this->chavesNfe, $data);
                    }
                }
            }



        }catch (\Exception $e){
            return false;
        }

        return true;
    }
}