<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 16/05/2019
 * Time: 18:05
 */

namespace Nfsews\Providers\Prodam\V2\Response;


class PedidoConsultaResponse extends Response
{
    public $listaNfe = [];

    public function parseXml($xml){
        if (stripos($xml, 'RetornoConsulta') === false){
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

                    $data['inscricaoMunicipalPrestador'] = ($alerta->getElementsByTagName('InscricaoPrestador')->length > 0)
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
            $nfe = $dom->getElementsByTagName('NFe');
            if ($nfe->length > 0 ){
                foreach ($nfe as $nf){
                    $data = [];
                    $data['assinatura'] = ($nf->getElementsByTagName('Assinatura')->length > 0)
                        ? $nf->getElementsByTagName('Assinatura')->item(0)->nodeValue : null;

                    $data['chaveNfe'] = null;
                    $data['numeroNfe'] = null;
                    $data['codigoVerificacao'] = null;

                    $chaveNfe = $nf->getElementsByTagName('ChaveNFe');
                    if ($chaveNfe->length > 0){

                        $data['inscricaoMunicipalPrestador'] = ($chaveNfe->item(0)->getElementsByTagName('InscricaoPrestador')->length > 0)
                            ? $chaveNfe->item(0)->getElementsByTagName('InscricaoPrestador')->item(0)->nodeValue : null;

                        $data['numeroNfe'] = ($chaveNfe->item(0)->getElementsByTagName('NumeroNFe')->length > 0)
                            ? $chaveNfe->item(0)->getElementsByTagName('NumeroNFe')->item(0)->nodeValue : null;

                        $data['codigoVerificacao'] = ($chaveNfe->item(0)->getElementsByTagName('CodigoVerificacao')->length > 0)
                            ? $chaveNfe->item(0)->getElementsByTagName('CodigoVerificacao')->item(0)->nodeValue : null;
                    }


                    $data['dataEmissaoNf'] = ($nf->getElementsByTagName('DataEmissaoNFe')->length > 0)
                        ? $nf->getElementsByTagName('DataEmissaoNFe')->item(0)->nodeValue : null;

                    $data['numeroLote'] = ($nf->getElementsByTagName('NumeroLote')->length > 0)
                        ? $nf->getElementsByTagName('NumeroLote')->item(0)->nodeValue : null;


                    $chaveRps = $nf->getElementsByTagName('ChaveRPS');
                    if ($chaveRps->length > 0){

                        $data['inscricaoPrestador'] = ($chaveRps->item(0)->getElementsByTagName('InscricaoPrestador')->length > 0)
                            ? $chaveRps->item(0)->getElementsByTagName('InscricaoPrestador')->item(0)->nodeValue : null;

                        $data['serieRps'] = ($chaveRps->item(0)->getElementsByTagName('SerieRPS')->length > 0)
                            ? $chaveRps->item(0)->getElementsByTagName('SerieRPS')->item(0)->nodeValue : null;

                        $data['numeroRps'] = ($chaveRps->item(0)->getElementsByTagName('NumeroRPS')->length > 0)
                            ? $chaveRps->item(0)->getElementsByTagName('NumeroRPS')->item(0)->nodeValue : null;
                    }

                    // Pega CNPJ PRESTADOR
                    if (  $nf->getElementsByTagName('CPFCNPJPrestador')->length > 0
                          && $nf->getElementsByTagName('CPFCNPJPrestador')->item(0)->getElementsByTagName('CPF')->length > 0 )
                    {
                        $data['cpfCnpjPrestador'] = $nf->getElementsByTagName('CPFCNPJPrestador')->item(0)->getElementsByTagName('CPF')->item(0)->nodeValue;
                    }else{
                        $data['cpfCnpjPrestador'] = null;
                    }

                    if (  $nf->getElementsByTagName('CPFCNPJPrestador')->length > 0
                          && $nf->getElementsByTagName('CPFCNPJPrestador')->item(0)->getElementsByTagName('CNPJ')->length > 0 )
                    {
                        $data['cpfCnpjPrestador'] = $nf->getElementsByTagName('CPFCNPJPrestador')->item(0)->getElementsByTagName('CNPJ')->item(0)->nodeValue;
                    }else{
                        $data['cpfCnpjPrestador'] = null;
                    }




                    $data['razaoSocialPrestador'] = ($nf->getElementsByTagName('RazaoSocialPrestador')->length > 0)
                        ? $nf->getElementsByTagName('RazaoSocialPrestador')->item(0)->nodeValue : null;


                    $enderecoP = $nf->getElementsByTagName('EnderecoPrestador');
                    if ($enderecoP->length > 0){
                        $data['tipoEnderecoPrestador'] = ($enderecoP->item(0)->getElementsByTagName('TipoLogradouro')->length > 0)
                            ? $enderecoP->item(0)->getElementsByTagName('TipoLogradouro')->item(0)->nodeValue : null;

                        $data['enderecoPrestador'] = ($enderecoP->item(0)->getElementsByTagName('Logradouro')->length > 0)
                            ? $enderecoP->item(0)->getElementsByTagName('Logradouro')->item(0)->nodeValue : null;

                        $data['numeroEnderecoPrestador'] = ($enderecoP->item(0)->getElementsByTagName('NumeroEndereco')->length > 0)
                            ? $enderecoP->item(0)->getElementsByTagName('NumeroEndereco')->item(0)->nodeValue : null;

                        $data['complementoEnderecoPrestador'] = ($enderecoP->item(0)->getElementsByTagName('ComplementoEndereco')->length > 0)
                            ? $enderecoP->item(0)->getElementsByTagName('ComplementoEndereco')->item(0)->nodeValue : null;

                        $data['bairroPrestador'] = ($enderecoP->item(0)->getElementsByTagName('Bairro')->length > 0)
                            ? $enderecoP->item(0)->getElementsByTagName('Bairro')->item(0)->nodeValue : null;

                        $data['ibgeCidadePrestador'] = ($enderecoP->item(0)->getElementsByTagName('Cidade')->length > 0)
                            ? $enderecoP->item(0)->getElementsByTagName('Cidade')->item(0)->nodeValue : null;

                        $data['ufPrestador'] = ($enderecoP->item(0)->getElementsByTagName('UF')->length > 0)
                            ? $enderecoP->item(0)->getElementsByTagName('UF')->item(0)->nodeValue : null;

                        $data['cepPrestador'] = ($enderecoP->item(0)->getElementsByTagName('CEP')->length > 0)
                            ? $enderecoP->item(0)->getElementsByTagName('CEP')->item(0)->nodeValue : null;
                    }

                    $data['emailPrestador'] = ($nf->getElementsByTagName('EmailPrestador')->length > 0)
                        ? $nf->getElementsByTagName('EmailPrestador')->item(0)->nodeValue : null;

                    $data['statusNfe'] = ($nf->getElementsByTagName('StatusNFe')->length > 0)
                        ? $nf->getElementsByTagName('StatusNFe')->item(0)->nodeValue : null;

                    $data['dataCancelamento'] = ($nf->getElementsByTagName('DataCancelamento')->length > 0)
                        ? $nf->getElementsByTagName('DataCancelamento')->item(0)->nodeValue : null;

                    $data['tributacaoNfe'] = ($nf->getElementsByTagName('TributacaoNFe')->length > 0)
                        ? $nf->getElementsByTagName('TributacaoNFe')->item(0)->nodeValue : null;

                    $data['optanteSimples'] = ($nf->getElementsByTagName('OpcaoSimples')->length > 0)
                        ? $nf->getElementsByTagName('OpcaoSimples')->item(0)->nodeValue : null;

                    $data['numeroGuia'] = ($nf->getElementsByTagName('NumeroGuia')->length > 0)
                        ? $nf->getElementsByTagName('NumeroGuia')->item(0)->nodeValue : null;

                    $data['dataQuitacaoGuia'] = ($nf->getElementsByTagName('DataQuitacaoGuia')->length > 0)
                        ? $nf->getElementsByTagName('DataQuitacaoGuia')->item(0)->nodeValue : null;

                    $data['valorServicos'] = ($nf->getElementsByTagName('ValorServicos')->length > 0)
                        ? $nf->getElementsByTagName('ValorServicos')->item(0)->nodeValue : 0.00;

                    $data['valorDeducoes'] = ($nf->getElementsByTagName('ValorDeducoes')->length > 0)
                        ? $nf->getElementsByTagName('ValorDeducoes')->item(0)->nodeValue : 0.00;

                    $data['valorPis'] = ($nf->getElementsByTagName('ValorPIS')->length > 0)
                        ? $nf->getElementsByTagName('ValorPIS')->item(0)->nodeValue : 0.00;

                    $data['valorCofins'] = ($nf->getElementsByTagName('ValorCOFINS')->length > 0)
                        ? $nf->getElementsByTagName('ValorCOFINS')->item(0)->nodeValue : 0.00;

                    $data['valorInss'] = ($nf->getElementsByTagName('ValorINSS')->length > 0)
                        ? $nf->getElementsByTagName('ValorINSS')->item(0)->nodeValue : 0.00;

                    $data['valorIr'] = ($nf->getElementsByTagName('ValorIR')->length > 0)
                        ? $nf->getElementsByTagName('ValorIR')->item(0)->nodeValue : 0.00;

                    $data['valorCsll'] = ($nf->getElementsByTagName('ValorCSLL')->length > 0)
                        ? $nf->getElementsByTagName('ValorCSLL')->item(0)->nodeValue : 0.00;

                    $data['codigoServico'] = ($nf->getElementsByTagName('CodigoServico')->length > 0)
                        ? $nf->getElementsByTagName('CodigoServico')->item(0)->nodeValue : null;

                    $data['aliquotaServicos'] = ($nf->getElementsByTagName('AliquotaServicos')->length > 0)
                        ? $nf->getElementsByTagName('AliquotaServicos')->item(0)->nodeValue : null;

                    $data['valorIss'] = ($nf->getElementsByTagName('ValorISS')->length > 0)
                        ? $nf->getElementsByTagName('ValorISS')->item(0)->nodeValue : null;

                    $data['valorCredito'] = ($nf->getElementsByTagName('ValorCredito')->length > 0)
                        ? $nf->getElementsByTagName('ValorCredito')->item(0)->nodeValue : null;

                    $data['issRetido'] = ($nf->getElementsByTagName('ISSRetido')->length > 0)
                        ? $nf->getElementsByTagName('ISSRetido')->item(0)->nodeValue : null;


                    // Pega CNPJ TOMADOR
                    if (  $nf->getElementsByTagName('CPFCNPJTomador')->length > 0
                        && $nf->getElementsByTagName('CPFCNPJTomador')->item(0)->getElementsByTagName('CPF')->length > 0 )
                    {
                        $data['cpfCnpjTomador'] = $nf->getElementsByTagName('CPFCNPJTomador')->item(0)->getElementsByTagName('CPF')->item(0)->nodeValue;
                    }else{
                        $data['cpfCnpjTomador'] = null;
                    }

                    if (  $nf->getElementsByTagName('CPFCNPJTomador')->length > 0
                        && $nf->getElementsByTagName('CPFCNPJTomador')->item(0)->getElementsByTagName('CNPJ')->length > 0 )
                    {
                        $data['cpfCnpjTomador'] = $nf->getElementsByTagName('CPFCNPJTomador')->item(0)->getElementsByTagName('CNPJ')->item(0)->nodeValue;
                    }else{
                        $data['cpfCnpjTomador'] = null;
                    }

                    $data['razaoSocialTomador'] = ($nf->getElementsByTagName('RazaoSocialTomador')->length > 0)
                        ? $nf->getElementsByTagName('RazaoSocialTomador')->item(0)->nodeValue : null;


                    $enderecoT = $nf->getElementsByTagName('EnderecoTomador');
                    if ($enderecoP->length > 0){
                        $data['tipoEnderecoTomador'] = ($enderecoT->item(0)->getElementsByTagName('TipoLogradouro')->length > 0)
                            ? $enderecoT->item(0)->getElementsByTagName('TipoLogradouro')->item(0)->nodeValue : null;

                        $data['enderecoTomador'] = ($enderecoT->item(0)->getElementsByTagName('Logradouro')->length > 0)
                            ? $enderecoT->item(0)->getElementsByTagName('Logradouro')->item(0)->nodeValue : null;

                        $data['numeroEnderecoTomador'] = ($enderecoT->item(0)->getElementsByTagName('NumeroEndereco')->length > 0)
                            ? $enderecoT->item(0)->getElementsByTagName('NumeroEndereco')->item(0)->nodeValue : null;

                        $data['complementoEnderecoTomador'] = ($enderecoT->item(0)->getElementsByTagName('ComplementoEndereco')->length > 0)
                            ? $enderecoT->item(0)->getElementsByTagName('ComplementoEndereco')->item(0)->nodeValue : null;

                        $data['bairroTomador'] = ($enderecoT->item(0)->getElementsByTagName('Bairro')->length > 0)
                            ? $enderecoT->item(0)->getElementsByTagName('Bairro')->item(0)->nodeValue : null;

                        $data['ibgeCidadeTomador'] = ($enderecoT->item(0)->getElementsByTagName('Cidade')->length > 0)
                            ? $enderecoT->item(0)->getElementsByTagName('Cidade')->item(0)->nodeValue : null;

                        $data['ufTomador'] = ($enderecoT->item(0)->getElementsByTagName('UF')->length > 0)
                            ? $enderecoT->item(0)->getElementsByTagName('UF')->item(0)->nodeValue : null;

                        $data['cepTomador'] = ($enderecoT->item(0)->getElementsByTagName('CEP')->length > 0)
                            ? $enderecoT->item(0)->getElementsByTagName('CEP')->item(0)->nodeValue : null;
                    }


                    $data['emailTomador'] = ($nf->getElementsByTagName('EmailTomador')->length > 0)
                        ? $nf->getElementsByTagName('EmailTomador')->item(0)->nodeValue : null;



                    // Pega CNPJ INTERMEDIARIO
                    if (  $nf->getElementsByTagName('CPFCNPJIntermediario')->length > 0
                        && $nf->getElementsByTagName('CPFCNPJIntermediario')->item(0)->getElementsByTagName('CPF')->length > 0 )
                    {
                        $data['cpfCnpjIntermediario'] = $nf->getElementsByTagName('CPFCNPJIntermediario')->item(0)->getElementsByTagName('CPF')->item(0)->nodeValue;
                    }else{
                        $data['cpfCnpjIntermediario'] = null;
                    }

                    if (  $nf->getElementsByTagName('CPFCNPJIntermediario')->length > 0
                        && $nf->getElementsByTagName('CPFCNPJIntermediario')->item(0)->getElementsByTagName('CNPJ')->length > 0 )
                    {
                        $data['cpfCnpjIntermediario'] = $nf->getElementsByTagName('CPFCNPJIntermediario')->item(0)->getElementsByTagName('CNPJ')->item(0)->nodeValue;
                    }else{
                        $data['cpfCnpjIntermediario'] = null;
                    }

                    $data['inscricaoMunicipalIntermediario'] = ($nf->getElementsByTagName('InscricaoMunicipalIntermediario')->length > 0)
                        ? $nf->getElementsByTagName('InscricaoMunicipalIntermediario')->item(0)->nodeValue : null;

                    $data['issRetidoIntermediario'] = ($nf->getElementsByTagName('ISSRetidoIntermediario')->length > 0)
                        ? $nf->getElementsByTagName('ISSRetidoIntermediario')->item(0)->nodeValue : null;

                    $data['emailIntermediario'] = ($nf->getElementsByTagName('EmailIntermediario')->length > 0)
                        ? $nf->getElementsByTagName('EmailIntermediario')->item(0)->nodeValue : null;


                    $data['discriminacao'] = ($nf->getElementsByTagName('Discriminacao')->length > 0)
                        ? $nf->getElementsByTagName('Discriminacao')->item(0)->nodeValue : null;

                    $data['valorCargaTributaria'] = ($nf->getElementsByTagName('ValorCargaTributaria')->length > 0)
                        ? $nf->getElementsByTagName('ValorCargaTributaria')->item(0)->nodeValue : null;

                    $data['percentualCargaTributaria'] = ($nf->getElementsByTagName('PercentualCargaTributaria')->length > 0)
                        ? $nf->getElementsByTagName('PercentualCargaTributaria')->item(0)->nodeValue : null;

                    $data['fonteCargaTributaria'] = ($nf->getElementsByTagName('FonteCargaTributaria')->length > 0)
                        ? $nf->getElementsByTagName('FonteCargaTributaria')->item(0)->nodeValue : null;

                    $data['codigoCei'] = ($nf->getElementsByTagName('CodigoCEI')->length > 0)
                        ? $nf->getElementsByTagName('CodigoCEI')->item(0)->nodeValue : null;

                    $data['matriculaObra'] = ($nf->getElementsByTagName('MatriculaObra')->length > 0)
                        ? $nf->getElementsByTagName('MatriculaObra')->item(0)->nodeValue : null;

                    $data['ibgeMunicipioPrestacao'] = ($nf->getElementsByTagName('MunicipioPrestacao')->length > 0)
                        ? $nf->getElementsByTagName('MunicipioPrestacao')->item(0)->nodeValue : null;

                    $data['numeroEncapsulamento'] = ($nf->getElementsByTagName('NumeroEncapsulamento')->length > 0)
                        ? $nf->getElementsByTagName('NumeroEncapsulamento')->item(0)->nodeValue : null;

                    $data['valorTotalRecebido'] = ($nf->getElementsByTagName('ValorTotalRecebido')->length > 0)
                        ? $nf->getElementsByTagName('ValorTotalRecebido')->item(0)->nodeValue : 0.00;


                    if (! empty($data['numeroNfe']) ){
                        array_push($this->listaNfe, $data);
                    }
                }
            }



        }catch (\Exception $e){
            return false;
        }

        return true;
    }

}