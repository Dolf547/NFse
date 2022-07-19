<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 09/05/2019
 * Time: 20:20
 */

namespace Nfsews\Providers\Prodam\V2\Request;


use Nfsews\ParseTemplate;
use Nfsews\Providers\Prodam\V2\Helpers\Signer;

class PedidoEnvioRps implements IRequest
{
    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = '\\Nfsews\\Providers\\Prodam\\V2\\Helpers\\Soap';
    private $responseNamespace = '\\Nfsews\\Providers\\Prodam\\V2\\Response\\PedidoEnvioRpsResponse';
    private $templatePath = null;
    private $action = 'EnvioRps';
    private $assinatura = null;
    private $inscricaoMunicipalPrestador = null;
    private $cpfCnpjRemetente = null;
    private $serieRps = null;
    private $numeroRps = null;
    private $tipoRps = null;
    private $dataEmissaoRps = null;
    private $statusRps = null;
    private $tributacaoRps = null;
    private $valorServicos = null;
    private $valorDeducoes = null;
    private $valorPis = null;
    private $valorCofins = null;
    private $valorInss = null;
    private $valorIr = null;
    private $valorCsll = null;
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
     * PedidoEnvioRps constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'PedidoEnvioRps.xml'  ;
    }

    /**
     * @return string
     */
    public function getAbrasfVersion()
    {
        return $this->abrasfVersion;
    }


    /**
     * @return string
     */
    public function getSoapHelper()
    {
        return $this->soapHelper;
    }

    /**
     * @return string
     */
    public function getResponseNamespace()
    {
        return $this->responseNamespace;
    }




    /**
     * @return string|null
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
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
    public function getCpfCnpjRemetente()
    {
        return $this->cpfCnpjRemetente;
    }

    /**
     * @param null $cpfCnpjRemetente
     */
    public function setCpfCnpjRemetente($cpfCnpjRemetente)
    {
        $this->cpfCnpjRemetente = preg_replace('/[\.\-\/]/', '', $cpfCnpjRemetente);
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
                throw new \Exception('A data de emissão do RPS é nula ou não está no formato YYYY-MM-DD. Valor informado: '. $dataEmissaoRps);
            }

        }catch (\Exception $e) {
            throw new \Exception('A data de emissão do RPS é nula ou não está no formato YYYY-MM-DD. Valor informado: ' . $dataEmissaoRps);
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
     * @param null $tributacaoRps
     */
    public function setTributacaoRps($tributacaoRps)
    {
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
        $this->valorServicos = $valorServicos;
    }

    /**
     * @return null
     */
    public function getValorDeducoes()
    {
        return $this->valorDeducoes;
    }

    /**
     * @param null $valorDeducoes
     */
    public function setValorDeducoes($valorDeducoes)
    {
        $this->valorDeducoes = $valorDeducoes;
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
        $this->valorPis = $valorPis;
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
        $this->valorCofins = $valorCofins;
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
        $this->valorInss = $valorInss;
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
        $this->valorIr = $valorIr;
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
        $this->valorCsll = $valorCsll;
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
        $this->cpfCnpjTomador = preg_replace('/[\.\-\/]/', '', $cpfCnpjTomador);
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
        $this->cpfCnpjIntermediario = preg_replace('/[\.\-\/]/', '', $cpfCnpjIntermediario);
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

    public function getAction()
    {
        // TODO: Implement getAction() method.
        return $this->action;
    }


    public function getAllAttributes()
    {
        // TODO: Implement getAllAttributes() method.
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
        return ParseTemplate::parse($this, $this->getXmlReplaceMark());
    }

    public function toXmlSigned( $priKeyPem, $pubKeyClean){

        if ($this->getAssinatura() == null){
            // Se não foi informado a assinatura da nota do milhão a cria
            $signature = Signer::getSignSP($this, $priKeyPem);
            $this->setAssinatura($signature);
        }

        $xml = $this->toXml();
        return Signer::sign($xml, $priKeyPem, $pubKeyClean, ['PedidoEnvioRPS']);
    }

    public function getEnvelopString(){

        return '<EnvioRPSRequest xmlns="http://www.prefeitura.sp.gov.br/nfe">
                      <VersaoSchema>1</VersaoSchema>
                      <MensagemXML>{body}</MensagemXML>
                    </EnvioRPSRequest>';

    }


    /**
     * Utilizado para substituir TAGs que podem ter mais de um nome, como ocorre por exemplo com a CPFCNPJ
     * na qual pode assumir tanto o valor CNPJ quanto o valor CPF
     * @return array
     */
    private function getXmlReplaceMark(){
        return [
          [
              'mark' =>  '{CpxCpfCnpjRemetente}',
              'value' =>  (strlen($this->cpfCnpjRemetente) == 14) ? '<CNPJ>{cpfCnpjRemetente}</CNPJ>' : '<CPF>{cpfCnpjRemetente}</CPF>'
          ],
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