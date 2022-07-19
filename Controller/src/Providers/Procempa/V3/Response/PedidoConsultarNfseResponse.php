<?php
/**
 * Created by PhpStorm.
 * User: moisesferreira
 * Date: 28/05/2019
 * Time: 16:10
 */

namespace Nfsews\Providers\Procempa\V3\Response;


use Nfsews\Response;

class PedidoConsultarNfseResponse extends Response
{
    public $listaNfse = [];


    /**
     * @param $xml
     * @return bool
     */
    public function parseXml($xml){

        if (stripos($xml, 'ConsultarNfseResposta') === false){
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
            $listaMsgRetorno = $dom->getElementsByTagName('ListaMensagemRetorno');
            if ($listaMsgRetorno->length > 0 ){
                $erros = ($listaMsgRetorno->item(0)->getElementsByTagName('MensagemRetorno')->length > 0) ?
                    $listaMsgRetorno->item(0)->getElementsByTagName('MensagemRetorno') : [];

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

            // Verifica se tem lista de erros

            $listaMsgRetornoLote = $dom->getElementsByTagName('ListaMensagemRetornoLote');
            if ($listaMsgRetornoLote->length > 0 ){
                $erros = ($listaMsgRetornoLote->item(0)->getElementsByTagName('MensagemRetorno')->length > 0) ?
                    $listaMsgRetornoLote->item(0)->getElementsByTagName('MensagemRetorno') : [];
                foreach ($erros as $erro){
                    if (
                        $erro->getElementsByTagName('Codigo')->length > 0
                        && $erro->getElementsByTagName('Mensagem')->length > 0
                    ){
                        array_push($this->erros, [
                            'codigo' => $erro->getElementsByTagName('Codigo')->item(0)->nodeValue,
                            'mensagem' => $erro->getElementsByTagName('Mensagem')->item(0)->nodeValue,
                            'numeroRps' =>  ($erro->getElementsByTagName('Numero')->length > 0) ?
                                $erro->getElementsByTagName('Numero')->item(0)->nodeValue : 0,
                            'serieRps' =>  ($erro->getElementsByTagName('Serie')->length > 0) ?
                                $erro->getElementsByTagName('Serie')->item(0)->nodeValue : 0,
                            'tipoRps' =>  ($erro->getElementsByTagName('Tipo')->length > 0) ?
                                $erro->getElementsByTagName('Tipo')->item(0)->nodeValue : 0,
                        ] );
                    }
                }
            }


            // CASO RETORNE NFS-e
            $compNfse = $dom->getElementsByTagName('CompNfse');
            if ($compNfse->length > 0){
                foreach ($compNfse as $nfse){
                    $data = [];

                    $data['numero'] = ($nfse->getElementsByTagName('Numero')->length > 0) ?
                        $nfse->getElementsByTagName('Numero')->item(0) : null;

                    $data['codigoVerificacao'] = ($nfse->getElementsByTagName('CodigoVerificacao')->length > 0) ?
                        $nfse->getElementsByTagName('CodigoVerificacao')->item(0) : null;

                    $data['dataEmissao'] = ($nfse->getElementsByTagName('DataEmissao')->length > 0) ?
                        $nfse->getElementsByTagName('DataEmissao')->item(0) : null;


                    // ******* DADOS DO RPS QUE GEROU A NOTA
                    $identificacaoRps = $nfse->getElementsByTagName('IdentificacaoRps');
                    if ($identificacaoRps->length > 0){
                        $rps = $identificacaoRps->item(0);
                        $data['numeroRps'] = ($rps->getElementsByTagName('Numero')->length > 0) ?
                            $rps->getElementsByTagName('Numero')->item(0) : null;

                        $data['serieRps'] = ($rps->getElementsByTagName('Serie')->length > 0) ?
                            $rps->getElementsByTagName('Serie')->item(0) : null;

                        $data['tipoRps'] = ($rps->getElementsByTagName('Tipo')->length > 0) ?
                            $rps->getElementsByTagName('Tipo')->item(0) : null;
                    }

                    $data['dataEmissaoRps'] = ($nfse->getElementsByTagName('DataEmissaoRps')->length > 0) ?
                        $nfse->getElementsByTagName('DataEmissaoRps')->item(0) : null;


                    $data['naturezaOperacao'] = ($nfse->getElementsByTagName('NaturezaOperacao')->length > 0) ?
                        $nfse->getElementsByTagName('NaturezaOperacao')->item(0) : null;

                    $data['optanteSimplesNacional'] = ($nfse->getElementsByTagName('OptanteSimplesNacional')->length > 0) ?
                        $nfse->getElementsByTagName('OptanteSimplesNacional')->item(0) : null;

                    $data['incentivadorCultural'] = ($nfse->getElementsByTagName('IncentivadorCultural')->length > 0) ?
                        $nfse->getElementsByTagName('IncentivadorCultural')->item(0) : null;

                    $data['competencia'] = ($nfse->getElementsByTagName('Competencia')->length > 0) ?
                        $nfse->getElementsByTagName('Competencia')->item(0) : null;

                    $data['nfseSubstituida'] = ($nfse->getElementsByTagName('NfseSubstituida')->length > 0) ?
                        $nfse->getElementsByTagName('NfseSubstituida')->item(0) : null;

                    $data['outrasInformacoes'] = ($nfse->getElementsByTagName('OutrasInformacoes')->length > 0) ?
                        $nfse->getElementsByTagName('OutrasInformacoes')->item(0) : null;


                    // ******* DADOS DO SERVIÇO EXECUTADO
                    $servicos = $nfse->getElementsByTagName('Servico');
                    if ($servicos->length > 0){
                        $servico = $servicos->item(0);

                        $data['valorServicos'] = ($servico->getElementsByTagName('ValorServico')->length > 0) ?
                            $servico->getElementsByTagName('ValorServico')->item(0) : null;

                        $data['valorDeducoes'] = ($servico->getElementsByTagName('ValorDeducoes')->length > 0) ?
                            $servico->getElementsByTagName('ValorDeducoes')->item(0) : null;

                        $data['valorPis'] = ($servico->getElementsByTagName('ValorPis')->length > 0) ?
                            $servico->getElementsByTagName('ValorPis')->item(0) : null;

                        $data['valorCofins'] = ($servico->getElementsByTagName('ValorCofins')->length > 0) ?
                            $servico->getElementsByTagName('ValorCofins')->item(0) : null;

                        $data['valorInss'] = ($servico->getElementsByTagName('ValorInss')->length > 0) ?
                            $servico->getElementsByTagName('ValorInss')->item(0) : null;

                        $data['valorIr'] = ($servico->getElementsByTagName('ValorIr')->length > 0) ?
                            $servico->getElementsByTagName('ValorIr')->item(0) : null;

                        $data['valorCsll'] = ($servico->getElementsByTagName('ValorCsll')->length > 0) ?
                            $servico->getElementsByTagName('ValorCsll')->item(0) : null;

                        $data['issRetido'] = ($servico->getElementsByTagName('IssRetido')->length > 0) ?
                            $servico->getElementsByTagName('IssRetido')->item(0) : null;

                        $data['valorIss'] = ($servico->getElementsByTagName('ValorIss')->length > 0) ?
                            $servico->getElementsByTagName('ValorIss')->item(0) : null;

                        $data['valorIssRetido'] = ($servico->getElementsByTagName('ValorIssRetido')->length > 0) ?
                            $servico->getElementsByTagName('ValorIssRetido')->item(0) : null;

                        $data['outrasRetencoes'] = ($servico->getElementsByTagName('OutrasRetencoes')->length > 0) ?
                            $servico->getElementsByTagName('OutrasRetencoes')->item(0) : null;

                        $data['baseCalculo'] = ($servico->getElementsByTagName('BaseCalculo')->length > 0) ?
                            $servico->getElementsByTagName('BaseCalculo')->item(0) : null;

                        $data['aliquota'] = ($servico->getElementsByTagName('Aliquota')->length > 0) ?
                            $servico->getElementsByTagName('Aliquota')->item(0) : null;

                        $data['valorLiquidoNfse'] = ($servico->getElementsByTagName('ValorLiquidoNfse')->length > 0) ?
                            $servico->getElementsByTagName('ValorLiquidoNfse')->item(0) : null;

                        $data['descontoIncondicionado'] = ($servico->getElementsByTagName('DescontoIncondicionado')->length > 0) ?
                            $servico->getElementsByTagName('DescontoIncondicionado')->item(0) : null;

                        $data['descontoCondicionado'] = ($servico->getElementsByTagName('DescontoCondicionado')->length > 0) ?
                            $servico->getElementsByTagName('DescontoCondicionado')->item(0) : null;

                        $data['codigoCnae'] = ($servico->getElementsByTagName('CodigoCnae')->length > 0) ?
                            $servico->getElementsByTagName('CodigoCnae')->item(0) : null;

                        $data['codigoTributacaoMunicipio'] = ($servico->getElementsByTagName('CodigoTributacaoMunicipio')->length > 0) ?
                            $servico->getElementsByTagName('CodigoTributacaoMunicipio')->item(0) : null;

                        $data['discriminacao'] = ($servico->getElementsByTagName('Discriminacao')->length > 0) ?
                            $servico->getElementsByTagName('Discriminacao')->item(0) : null;

                        $data['codigoMunicipio'] = ($servico->getElementsByTagName('CodigoMunicipio')->length > 0) ?
                            $servico->getElementsByTagName('CodigoMunicipio')->item(0) : null;

                    }

                    $data['valorCredito'] = ($nfse->getElementsByTagName('ValorCredito')->length > 0) ?
                        $nfse->getElementsByTagName('ValorCredito')->item(0) : null;


                    // ******* DADOS DO PRESTADOR DO SERVIÇO
                    $prestadorServico = $nfse->getElementsByTagName('PrestadorServico');
                    if ($prestadorServico->length > 0){
                        $ps = $prestadorServico->item(0);

                        $data['cnpjPrestador'] = ($ps->getElementsByTagName('Cnpj')->length > 0) ?
                            $ps->getElementsByTagName('Cnpj')->item(0) : null;

                        $data['inscricaoMunicipalPrestador'] = ($ps->getElementsByTagName('InscricaoMunicipal')->length > 0) ?
                            $ps->getElementsByTagName('InscricaoMunicipal')->item(0) : null;

                        $data['razaoSocialPrestador'] = ($ps->getElementsByTagName('RazaoSocial')->length > 0) ?
                            $ps->getElementsByTagName('RazaoSocial')->item(0) : null;

                        $data['nomeFantasiaPrestador'] = ($ps->getElementsByTagName('NomeFantasia')->length > 0) ?
                            $ps->getElementsByTagName('NomeFantasia')->item(0) : null;

                        $data['enderecoPrestador'] = ($ps->getElementsByTagName('Endereco')->length > 0) ?
                            $ps->getElementsByTagName('Endereco')->item(0) : null;

                        $data['numeroEnderecoPrestador'] = ($ps->getElementsByTagName('Numero')->length > 0) ?
                            $ps->getElementsByTagName('Numero')->item(0) : null;

                        $data['bairroPrestador'] = ($ps->getElementsByTagName('Bairro')->length > 0) ?
                            $ps->getElementsByTagName('Bairro')->item(0) : null;

                        $data['codigoMunicipioPrestador'] = ($ps->getElementsByTagName('CodigoMunicipio')->length > 0) ?
                            $ps->getElementsByTagName('CodigoMunicipio')->item(0) : null;

                        $data['ufPrestador'] = ($ps->getElementsByTagName('Uf')->length > 0) ?
                            $ps->getElementsByTagName('Uf')->item(0) : null;

                        $data['cepPrestador'] = ($ps->getElementsByTagName('Cep')->length > 0) ?
                            $ps->getElementsByTagName('Cep')->item(0) : null;

                    }

                    // ******* DADOS DO TOMADOR DO SERVIÇO
                    $tomadorServico = $nfse->getElementsByTagName('TomadorServico');
                    if ($tomadorServico->length > 0){
                        $ts = $tomadorServico->item(0);

                        $data['cpfCnpjTomador'] = ($ts->getElementsByTagName('Cnpj')->length > 0) ?
                            $ts->getElementsByTagName('Cnpj')->item(0) : null;

                        if (empty($data['cpfCnpjTomador'])){
                            $data['cpfCnpjTomador'] = ($ts->getElementsByTagName('Cpf')->length > 0) ?
                                $ts->getElementsByTagName('Cpf')->item(0) : null;
                        }

                        $data['inscricaoMunicipalTomador'] = ($ts->getElementsByTagName('InscricaoMunicipal')->length > 0) ?
                            $ts->getElementsByTagName('InscricaoMunicipal')->item(0) : null;

                        $data['razaoSocialTomador'] = ($ts->getElementsByTagName('RazaoSocial')->length > 0) ?
                            $ts->getElementsByTagName('RazaoSocial')->item(0) : null;

                        $data['nomeFantasiaTomador'] = ($ts->getElementsByTagName('NomeFantasia')->length > 0) ?
                            $ts->getElementsByTagName('NomeFantasia')->item(0) : null;

                        $data['enderecoTomador'] = ($ts->getElementsByTagName('Endereco')->length > 0) ?
                            $ts->getElementsByTagName('Endereco')->item(0) : null;

                        $data['numeroEnderecoTomador'] = ($ts->getElementsByTagName('Numero')->length > 0) ?
                            $ts->getElementsByTagName('Numero')->item(0) : null;

                        $data['bairroTomador'] = ($ts->getElementsByTagName('Bairro')->length > 0) ?
                            $ts->getElementsByTagName('Bairro')->item(0) : null;

                        $data['codigoMunicipioTomador'] = ($ts->getElementsByTagName('CodigoMunicipio')->length > 0) ?
                            $ts->getElementsByTagName('CodigoMunicipio')->item(0) : null;

                        $data['ufTomador'] = ($ts->getElementsByTagName('Uf')->length > 0) ?
                            $ts->getElementsByTagName('Uf')->item(0) : null;

                        $data['cepTomador'] = ($ts->getElementsByTagName('Cep')->length > 0) ?
                            $ts->getElementsByTagName('Cep')->item(0) : null;

                        $data['telefoneTomador'] = ($ts->getElementsByTagName('Telefone')->length > 0) ?
                            $ts->getElementsByTagName('Telefone')->item(0) : null;

                        $data['emailTomador'] = ($ts->getElementsByTagName('Email')->length > 0) ?
                            $ts->getElementsByTagName('Email')->item(0) : null;

                    }

                    // ******* DADOS DO INTERMEDIÁRIO DOS SERVIÇOS
                    $intermediario = $nfse->getElementsByTagName('IntermediarioServico');
                    if ($intermediario->length > 0){
                        $inter = $intermediario->item(0);

                        $data['cpfCnpjIntermediario'] = ($inter->getElementsByTagName('Cnpj')->length > 0) ?
                            $inter->getElementsByTagName('Cnpj')->item(0) : null;

                        if (empty($data['cpfCnpjIntermediario'])){
                            $data['cpfCnpjIntermediario'] = ($inter->getElementsByTagName('Cpf')->length > 0) ?
                                $inter->getElementsByTagName('Cpf')->item(0) : null;
                        }

                        $data['inscricaoMunicipalIntermediario'] = ($inter->getElementsByTagName('InscricaoMunicipal')->length > 0) ?
                            $inter->getElementsByTagName('InscricaoMunicipal')->item(0) : null;

                    }

                    // ******* DADOS DO ORGÃO GERADOR
                    $orgaoGerador = $nfse->getElementsByTagName('OrgaoGerador');
                    if ($orgaoGerador->length > 0){
                        $og = $orgaoGerador->item(0);

                        $data['codigoMunicipioGerador'] = ($og->getElementsByTagName('CodigoMunicipio')->length > 0) ?
                            $og->getElementsByTagName('CodigoMunicipio')->item(0) : null;


                        $data['ufMunicipioGerador'] = ($og->getElementsByTagName('Uf')->length > 0) ?
                            $og->getElementsByTagName('Uf')->item(0) : null;

                    }

                    // ******** DADOS DA CONSTRUÇÃO CIVIL
                    $construcaoCivil = $nfse->getElementsByTagName('ConstrucaoCivil');
                    if ($construcaoCivil->length > 0){
                        $cc = $construcaoCivil->item(0);

                        $data['codigoObra'] = ($cc->getElementsByTagName('CodigoObra')->length > 0) ?
                            $cc->getElementsByTagName('CodigoObra')->item(0) : null;


                        $data['art'] = ($cc->getElementsByTagName('Art')->length > 0) ?
                            $cc->getElementsByTagName('Art')->item(0) : null;

                    }

                    array_push($this->listaNfse, $data);
                }// end foreach
            }


        }catch (\Exception $e){
            return false;
        }

        return true;
    }
}