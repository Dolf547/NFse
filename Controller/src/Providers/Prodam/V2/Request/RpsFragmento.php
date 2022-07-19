<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 05/05/2019
 * Time: 09:36
 *
 * RpsFragmento como o nome diz é um fragmento de informações para ser utilizado junto com a request PedidoEnvioLoteRps
 * Através do método addFragmentoRps esta classe é adicionada ao Lote de Rps a ser enviado para o webservice
 */

namespace Nfsews\Providers\Prodam\V2\Request;


use mysql_xdevapi\Exception;
use Nfsews\ParseTemplate;

class RpsFragmento implements IRequest
{
    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = '\\Phpnfsews\\Providers\\Prodam\\V2\\Helpers\\Soap';
    private $templatePath = null;
    private $assinatura = null;
    private $inscricaoMunicipalPrestador = null;
    private $serieRps = null;
    private $numeroRps = null;
    private $tipoRps = null;
    private $dataEmissaoRps = null;
    private $statusRps = null;
    private $tributacaoRps = null;
    private $valorServicos = 0.00;
    private $valorDeducoes = 0.00;
    private $valorPis = 0.00;
    private $valorCofins = 0.00;
    private $valorInss = 0.00;
    private $valorIr = 0.00;
    private $valorCsll = 0.00;
    private $codigoServico = null;
    private $aliquotaServicos = null;
    private $issRetido = null;
    private $cpfCnpjTomador = null;
    private $inscricaoMunicipalTomador = null;
    private $razaoSocialTomador = null;
    private $tipoEnderecoTomador = null;
    private $enderecoTomador = null;
    private $numeroEnderecoTomador = null;
    private $complementoEnderecoTomador = null;
    private $bairroEnderecoTomador = null;
    private $ibgeCidadeTomador = null;
    private $ufTomador = null;
    private $cepTomador = null;
    private $emailTomador = null;
    private $cpfCnpjIntermediario = null;
    private $inscricaoMunicipalIntemediario = null;
    private $issRetidoIntermediario = null;
    private $emailIntermediario = null;
    private $discriminacao = null;
    private $valorCargaTributaria = null;
    private $percentualCargaTributaria = null;
    private $codigoCei = null;
    private $matriculaObra = null;
    private $municipioPrestacao = null;
    private $numeroEncapsulamento = null;
    private $valorTotalRecebido = null;
    private $fonteCargaTributaria = null;

    /**
     * RpsFragmento constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'RpsFragmento.xml'  ;
    }

    /**
     * @return string
     */
    public function getAbrasfVersion()
    {
        return $this->abrasfVersion;
    }

    /**
     * @param string $abrasfVersion
     */
    public function setAbrasfVersion($abrasfVersion)
    {
        $this->abrasfVersion = $abrasfVersion;
    }

    /**
     * @return string
     */
    public function getSoapHelper()
    {
        return $this->soapHelper;
    }

    /**
     * @param string $soapHelper
     */
    public function setSoapHelper($soapHelper)
    {
        $this->soapHelper = $soapHelper;
    }

    /**
     * @return string|null
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * @param string|null $templatePath
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }

    /**
     * @return null
     */
    public function getAssinatura()
    {
        return $this->assinatura;
    }

    /**
     * @param null $assinatura
     */
    public function setAssinatura($assinatura)
    {
        $this->assinatura = $assinatura;
    }

    /**
     * @return null
     */
    public function getInscricaoMunicipalPrestador()
    {
        return $this->inscricaoMunicipalPrestador;
    }

    /**
     * @param null $inscricaoMunicipalPrestador
     */
    public function setInscricaoMunicipalPrestador($inscricaoMunicipalPrestador)
    {
        $this->inscricaoMunicipalPrestador = $inscricaoMunicipalPrestador;
    }

    /**
     * @return null
     */
    public function getSerieRps()
    {
        return $this->serieRps;
    }

    /**
     * @param null $serieRps
     */
    public function setSerieRps($serieRps)
    {
        $this->serieRps = $serieRps;
    }

    /**
     * @return null
     */
    public function getNumeroRps()
    {
        return $this->numeroRps;
    }

    /**
     * @param null $numeroRps
     */
    public function setNumeroRps($numeroRps)
    {
        $this->numeroRps = $numeroRps;
    }

    /**
     * @return null
     */
    public function getTipoRps()
    {
        return $this->tipoRps;
    }

    /**
     * @param null $tipoRps
     */
    public function setTipoRps($tipoRps)
    {
        $this->tipoRps = $tipoRps;
    }

    /**
     * @return null
     */
    public function getDataEmissaoRps()
    {
        return $this->dataEmissaoRps;
    }

    /**
     * @param null $dataEmissaoRps
     * @throws
     */
    public function setDataEmissaoRps($dataEmissaoRps)
    {
        try{
            $date = \DateTime::createFromFormat('Y-m-d' , $dataEmissaoRps );
            if ($date == null){
                throw new \Exception('A data de emissão do RPS é nula ou não está no formato YYYY-MM-DD. Valor 
                informado: '. $dataEmissaoRps);
            }

        }catch (\Exception $e) {
            throw new \Exception('A data de emissão do RPS é nula ou não está no formato YYYY-MM-DD. Valor 
                informado: ' . $dataEmissaoRps);
        }

        $this->dataEmissaoRps = $date->format('Y-m-d');
    }

    /**
     * @return null
     */
    public function getStatusRps()
    {
        return $this->statusRps;
    }

    /**
     * @param null $statusRps
     */
    public function setStatusRps($statusRps)
    {
        $statusRps = strtoupper($statusRps);

        if ($statusRps != 'N' && $statusRps != 'C'){
            throw new Exception('Valor inválido. Informe N para o status NORMAL e C para CANCELADO');
        }
        $this->statusRps = $statusRps;
    }

    /**
     * @return null
     */
    public function getTributacaoRps()
    {
        return $this->tributacaoRps;
    }

    /**
     *
     * @param null $tributacaoRps
     */
    public function setTributacaoRps($tributacaoRps)
    {
        $valid = ['T','F','A','B','D','M','N','R','S','X','V','P'];
        $tributacaoRps = strtoupper($tributacaoRps);
        if (array_search($tributacaoRps, $valid) === false){
            throw new Exception('Tributação inválida. Informe conforme o manual da NOTA FISCAL. ['. implode(',',$valid) . ']');
        }

        $this->tributacaoRps = $tributacaoRps;
    }

    /**
     * @return null
     */
    public function getValorServicos()
    {
        return $this->valorServicos;
    }

    /**
     * @param null $valorServicos
     */
    public function setValorServicos($valorServicos)
    {
        $this->valorServicos = number_format($valorServicos, 2, '.', '');
    }

    /**
     * @return null
     */
    public function getValorDeducoes()
    {
        return $this->valorDeducoes;
    }

    /**
     * @param null $valorDecocoes
     */
    public function setValorDeducoes($valorDeducoes)
    {
        $this->valorDeducoes = number_format($valorDeducoes, 2);
    }

    /**
     * @return null
     */
    public function getValorPis()
    {
        return $this->valorPis;
    }

    /**
     * @param null $valorPis
     */
    public function setValorPis($valorPis)
    {
        $this->valorPis = number_format($valorPis, 2);
    }

    /**
     * @return null
     */
    public function getValorCofins()
    {
        return $this->valorCofins;
    }

    /**
     * @param null $valorCofins
     */
    public function setValorCofins($valorCofins)
    {
        $this->valorCofins = number_format($valorCofins, 2);
    }

    /**
     * @return null
     */
    public function getValorInss()
    {
        return $this->valorInss;
    }

    /**
     * @param null $valorInss
     */
    public function setValorInss($valorInss)
    {
        $this->valorInss = number_format(  $valorInss, 2 );
    }

    /**
     * @return null
     */
    public function getValorIr()
    {
        return $this->valorIr;
    }

    /**
     * @param null $valorIr
     */
    public function setValorIr($valorIr)
    {
        $this->valorIr = number_format($valorIr, 2);
    }

    /**
     * @return null
     */
    public function getValorCsll()
    {
        return $this->valorCsll;
    }

    /**
     * @param null $valorCsll
     */
    public function setValorCsll($valorCsll)
    {
        $this->valorCsll = number_format($valorCsll, 2, '.', '');
    }

    /**
     * @return null
     */
    public function getCodigoServico()
    {
        return $this->codigoServico;
    }

    /**
     * @param null $codigoServico
     */
    public function setCodigoServico($codigoServico)
    {
        $this->codigoServico = $codigoServico;
    }

    /**
     * @return null
     */
    public function getAliquotaServicos()
    {
        return $this->aliquotaServicos;
    }

    /**
     * @param null $aliquotaServicos
     */
    public function setAliquotaServicos($aliquotaServicos)
    {
        $this->aliquotaServicos = $aliquotaServicos;
    }

    /**
     * @return null
     */
    public function getIssRetido()
    {
        return $this->issRetido;
    }

    /**
     * @param null $issRetido
     * @throws
     */
    public function setIssRetido($issRetido)
    {
        if ($issRetido == true || $issRetido == 'true')
            $this->issRetido = 'true';
        else
            if ($issRetido == false || $issRetido == 'false')
                $this->issRetido = 'false';
            else
                throw new \Exception('Valor invalido para issRetido. Informe true ou false');
    }

    /**
     * @return null
     */
    public function getCpfCnpjTomador()
    {
        return $this->cpfCnpjTomador;
    }

    /**
     * @param null $cpfCnpjTomador
     */
    public function setCpfCnpjTomador($cpfCnpjTomador)
    {
        $this->cpfCnpjTomador = preg_replace('/[\.\-\/]/', '',  $cpfCnpjTomador);
    }

    /**
     * @return null
     */
    public function getInscricaoMunicipalTomador()
    {
        return $this->inscricaoMunicipalTomador;
    }

    /**
     * @param null $inscricaoMunicipalTomador
     */
    public function setInscricaoMunicipalTomador($inscricaoMunicipalTomador)
    {
        $this->inscricaoMunicipalTomador = $inscricaoMunicipalTomador;
    }

    /**
     * @return null
     */
    public function getRazaoSocialTomador()
    {
        return $this->razaoSocialTomador;
    }

    /**
     * @param null $razaoSocialTomador
     */
    public function setRazaoSocialTomador($razaoSocialTomador)
    {
        $this->razaoSocialTomador = $razaoSocialTomador;
    }

    /**
     * @return null
     */
    public function getTipoEnderecoTomador()
    {
        return $this->tipoEnderecoTomador;
    }

    /**
     * @param null $tipoEnderecoTomador
     */
    public function setTipoEnderecoTomador($tipoEnderecoTomador)
    {
        $this->tipoEnderecoTomador = $tipoEnderecoTomador;
    }

    /**
     * @return null
     */
    public function getEnderecoTomador()
    {
        return $this->enderecoTomador;
    }

    /**
     * @param null $enderecoTomador
     */
    public function setEnderecoTomador($enderecoTomador)
    {
        $this->enderecoTomador = $enderecoTomador;
    }

    /**
     * @return null
     */
    public function getNumeroEnderecoTomador()
    {
        return $this->numeroEnderecoTomador;
    }

    /**
     * @param null $numeroEnderecoTomador
     */
    public function setNumeroEnderecoTomador($numeroEnderecoTomador)
    {
        $this->numeroEnderecoTomador = $numeroEnderecoTomador;
    }

    /**
     * @return null
     */
    public function getComplementoEnderecoTomador()
    {
        return $this->complementoEnderecoTomador;
    }

    /**
     * @param null $complementoEnderecoTomador
     */
    public function setComplementoEnderecoTomador($complementoEnderecoTomador)
    {
        $this->complementoEnderecoTomador = $complementoEnderecoTomador;
    }

    /**
     * @return null
     */
    public function getBairroEnderecoTomador()
    {
        return $this->bairroEnderecoTomador;
    }

    /**
     * @param null $bairroEnderecoTomador
     */
    public function setBairroEnderecoTomador($bairroEnderecoTomador)
    {
        $this->bairroEnderecoTomador = $bairroEnderecoTomador;
    }

    /**
     * @return null
     */
    public function getIbgeCidadeTomador()
    {
        return $this->ibgeCidadeTomador;
    }

    /**
     * @param null $ibgeCidadeTomador
     */
    public function setIbgeCidadeTomador($ibgeCidadeTomador)
    {
        $this->ibgeCidadeTomador = $ibgeCidadeTomador;
    }

    /**
     * @return null
     */
    public function getUfTomador()
    {
        return $this->ufTomador;
    }

    /**
     * @param null $ufTomador
     */
    public function setUfTomador($ufTomador)
    {
        $this->ufTomador = $ufTomador;
    }

    /**
     * @return null
     */
    public function getCepTomador()
    {
        return $this->cepTomador;
    }

    /**
     * @param null $cepTomador
     */
    public function setCepTomador($cepTomador)
    {
        $this->cepTomador = $cepTomador;
    }

    /**
     * @return null
     */
    public function getEmailTomador()
    {
        return $this->emailTomador;
    }

    /**
     * @param null $emailTomador
     */
    public function setEmailTomador($emailTomador)
    {
        $this->emailTomador = $emailTomador;
    }

    /**
     * @return null
     */
    public function getCpfCnpjIntermediario()
    {
        return $this->cpfCnpjIntermediario;
    }

    /**
     * @param null $cpfCnpjIntermediario
     */
    public function setCpfCnpjIntermediario($cpfCnpjIntermediario)
    {
        $this->cpfCnpjIntermediario = preg_replace('/[\.\-\/]/', '',  $cpfCnpjIntermediario);
    }

    /**
     * @return null
     */
    public function getInscricaoMunicipalIntemediario()
    {
        return $this->inscricaoMunicipalIntemediario;
    }

    /**
     * @param null $inscricaoMunicipalIntemediario
     */
    public function setInscricaoMunicipalIntemediario($inscricaoMunicipalIntemediario)
    {
        $this->inscricaoMunicipalIntemediario = $inscricaoMunicipalIntemediario;
    }

    /**
     * @return null
     */
    public function getIssRetidoIntermediario()
    {
        return $this->issRetidoIntermediario;
    }

    /**
     * @param null $issRetidoIntermediario
     * @throws
     */
    public function setIssRetidoIntermediario($issRetidoIntermediario)
    {
        if ($issRetidoIntermediario == true || $issRetidoIntermediario == 'true')
            $this->issRetidoIntermediario = 'true';
        else
            if ($issRetidoIntermediario == false || $issRetidoIntermediario == 'false')
                $this->issRetidoIntermediario = 'false';
            else
                throw new \Exception('Valor invalido para issRetidoIntermediario. Informe true ou false');

    }

    /**
     * @return null
     */
    public function getEmailIntermediario()
    {
        return $this->emailIntermediario;
    }

    /**
     * @param null $emailIntermediario
     */
    public function setEmailIntermediario($emailIntermediario)
    {
        $this->emailIntermediario = $emailIntermediario;
    }

    /**
     * @return null
     */
    public function getDiscriminacao()
    {
        return $this->discriminacao;
    }

    /**
     * @param null $discriminacao
     */
    public function setDiscriminacao($discriminacao)
    {
        $this->discriminacao = $discriminacao;
    }

    /**
     * @return null
     */
    public function getValorCargaTributaria()
    {
        return $this->valorCargaTributaria;
    }

    /**
     * @param null $valorCargaTributaria
     */
    public function setValorCargaTributaria($valorCargaTributaria)
    {
        $this->valorCargaTributaria = $valorCargaTributaria;
    }

    /**
     * @return null
     */
    public function getPercentualCargaTributaria()
    {
        return $this->percentualCargaTributaria;
    }

    /**
     * @param null $percentualCargaTributaria
     */
    public function setPercentualCargaTributaria($percentualCargaTributaria)
    {
        $this->percentualCargaTributaria = $percentualCargaTributaria;
    }

    /**
     * @return null
     */
    public function getCodigoCei()
    {
        return $this->codigoCei;
    }

    /**
     * @param null $codigoCei
     */
    public function setCodigoCei($codigoCei)
    {
        $this->codigoCei = $codigoCei;
    }

    /**
     * @return null
     */
    public function getMatriculaObra()
    {
        return $this->matriculaObra;
    }

    /**
     * @param null $matriculaObra
     */
    public function setMatriculaObra($matriculaObra)
    {
        $this->matriculaObra = $matriculaObra;
    }

    /**
     * @return null
     */
    public function getMunicipioPrestacao()
    {
        return $this->municipioPrestacao;
    }

    /**
     * @param null $municipioPrestacao
     */
    public function setMunicipioPrestacao($municipioPrestacao)
    {
        $this->municipioPrestacao = $municipioPrestacao;
    }

    /**
     * @return null
     */
    public function getNumeroEncapsulamento()
    {
        return $this->numeroEncapsulamento;
    }

    /**
     * @param null $numeroEncapsulamento
     */
    public function setNumeroEncapsulamento($numeroEncapsulamento)
    {
        $this->numeroEncapsulamento = $numeroEncapsulamento;
    }

    /**
     * @return null
     */
    public function getValorTotalRecebido()
    {
        return $this->valorTotalRecebido;
    }

    /**
     * @param null $valorTotalRecebido
     */
    public function setValorTotalRecebido($valorTotalRecebido)
    {
        $this->valorTotalRecebido = $valorTotalRecebido;
    }

    /**
     * @return null
     */
    public function getFonteCargaTributaria()
    {
        return $this->fonteCargaTributaria;
    }

    /**
     * @param null $fonteCargaTributaria
     */
    public function setFonteCargaTributaria($fonteCargaTributaria)
    {
        $this->fonteCargaTributaria = $fonteCargaTributaria;
    }




    public function getAllAttributes()
    {
        $array = [];

        foreach ($this as $key => $value) {
            if (property_exists($this, $key)) {
                array_push($array, array($key => $value));
            }
        }
        return $array;

    }


    public function toXml()
    {
        // TODO: Implement toXml() method.
        return str_replace('<?xml version="1.0"?>','', ParseTemplate::parse($this, $this->getXmlReplaceMark()) );
    }

    public function getAction()
    {
        // TODO: Implement getAction() method.
        return null;
    }

    /**
     * Utilizado para substituir TAGs que podem ter mais de um nome, como ocorre por exemplo com a CPFCNPJ
     * na qual pode assumir tanto o valor CNPJ quanto o valor CPF
     * @return array
     */
    private function getXmlReplaceMark(){
        return [
            [
                'mark' =>  '{CpxCpfCnpjTomador}',
                'value' =>  (strlen($this->cpfCnpjTomador) == 14) ? '<CNPJ>{cpfCnpjTomador}</CNPJ>' : '<CPF>{cpfCnpjTomador}</CPF>'
            ],
            [
                'mark' =>  '{CpxCpfCnpjIntermediario}',
                'value' =>  (strlen($this->cpfCnpjIntermediario) == 14) ? '<CNPJ>{cpfCnpjIntermediario}</CNPJ>' : '<CPF>{cpfCnpjIntermediario}</CPF>'
            ],
        ];
    }
}