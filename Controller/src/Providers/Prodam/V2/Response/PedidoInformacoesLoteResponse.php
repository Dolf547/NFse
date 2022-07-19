<?php


namespace Nfsews\Providers\Prodam\V2\Response;


class PedidoInformacoesLoteResponse extends Response
{
    public $numeroLote = null;
    public $inscricaoPrestador = null;
    public $cpfCnpjRemetente = null;
    public $dataEnvioLote = null;
    public $qtdNotasProcessadas = null;
    public $tempoProcessamento = null;
    public $valorTotalServicos = null;
    public $valorTotalDeducoes = null;

    public function parseXml($xml){
        if (stripos($xml, 'RetornoInformacoesLote') === false){
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


                if ($cabecalho->getElementsByTagName('NumeroLote')->length > 0)
                    $this->numeroLote = $cabecalho->getElementsByTagName('NumeroLote')->item(0)->nodeValue;


                if ($cabecalho->getElementsByTagName('InscricaoPrestador')->length > 0)
                    $this->InscricaoPrestador = $cabecalho->getElementsByTagName('InscricaoPrestador')->item(0)->nodeValue;

                if ($cabecalho->getElementsByTagName('CPF')->length > 0)
                    $this->cpfCnpjRemetente = $cabecalho->getElementsByTagName('CPF')->item(0)->nodeValue;


                if ($cabecalho->getElementsByTagName('CNPJ')->length > 0)
                    $this->cpfCnpjRemetente = $cabecalho->getElementsByTagName('CNPJ')->item(0)->nodeValue;


                if ($cabecalho->getElementsByTagName('DataEnvioLote')->length > 0)
                    $this->dataEnvioLote = $cabecalho->getElementsByTagName('DataEnvioLote')->item(0)->nodeValue;


                if ($cabecalho->getElementsByTagName('QtdNotasProcessadas')->length > 0)
                    $this->qtdNotasProcessadas = $cabecalho->getElementsByTagName('QtdNotasProcessadas')->item(0)->nodeValue;


                if ($cabecalho->getElementsByTagName('TempoProcessamento')->length > 0)
                    $this->tempoProcessamento = $cabecalho->getElementsByTagName('TempoProcessamento')->item(0)->nodeValue;


                if ($cabecalho->getElementsByTagName('ValorTotalServicos')->length > 0)
                    $this->valorTotalServicos = $cabecalho->getElementsByTagName('ValorTotalServicos')->item(0)->nodeValue;


                if ($cabecalho->getElementsByTagName('ValorTotalDeducoes')->length > 0)
                    $this->valorTotalDeducoes = $cabecalho->getElementsByTagName('ValorTotalDeducoes')->item(0)->nodeValue;


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






        }catch (\Exception $e){
            return false;
        }

        return true;
    }
}