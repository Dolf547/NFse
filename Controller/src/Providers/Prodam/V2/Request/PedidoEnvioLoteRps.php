<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 05/05/2019
 * Time: 10:36
 *
 * Utilizado para criar Notas fiscais em lote, ou seja, em uma única conexão com o webservice é
 * possível enviar mais de um RPS para ser convertido em NFS-e
 */

namespace Nfsews\Providers\Prodam\V2\Request;



use Nfsews\ParseTemplate;
use Nfsews\Providers\Prodam\V2\Helpers\Signer;

class PedidoEnvioLoteRps implements IRequest
{
    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = '\\Nfsews\\Providers\\Prodam\\V2\\Helpers\\Soap';
    private $responseNamespace = '\\Nfsews\\Providers\\Prodam\\V2\\Response\\PedidoEnvioLoteRpsResponse';
    private $templatePath = null;
    private $action = 'EnvioLoteRps';
    private $cpfCnpjRemetente = null;
    private $transacao = null;
    private $dataInicio = null;
    private $dataFim = null;
    private $quantidadeRps = null;
    private $valorTotalServicos = null;
    private $valorTotalDeducoes = null;
    private $rpsFragmento = [];
    private $strFragmentos = '';
    private $indicaTeste = false;
    private $tipoEnvio = 'sync';

    /**
     * PedidoEnvioLoteRps constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'PedidoEnvioLoteRps.xml'  ;
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
    public function getCpfCnpjRemetente()
    {
        return $this->cpfCnpjRemetente;
    }

    /**
     * @param null $cpfCnpjRemetente
     */
    public function setCpfCnpjRemetente($cpfCnpjRemetente)
    {
        $this->cpfCnpjRemetente = preg_replace('/[\.\-\/]/', '',   $cpfCnpjRemetente);
    }

    /**
     * @return null
     */
    public function getTransacao()
    {
        return $this->transacao;
    }

    /**
     * @param null $transacao
     * @throws
     */
    public function setTransacao($transacao)
    {
        if($transacao == true || $transacao == 'true')
            $this->transacao = 'true';
        else{
            if($transacao == false || $transacao == 'false')
                $this->transacao = 'false';
            else
                throw new \Exception('O valor informado em setTransacao é inválido. Informe true ou false');
        }
    }

    /**
     * @return null
     */
    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    /**
     * @param null $dataInicio
     * @throws
     */
    public function setDataInicio($dataInicio)
    {
        try{
            $date = \DateTime::createFromFormat('Y-m-d' , $dataInicio );
            if ($date == null){
                throw new \Exception('A data inicial é nula ou não está no formato YYYY-MM-DD. Valor informado: '. $dataInicio);
            }

        }catch (\Exception $e) {
            throw new \Exception('A data inicial é nula ou não está no formato YYYY-MM-DD. Valor informado: ' . $dataInicio);
        }

        $this->dataInicio = $date->format('Y-m-d');
    }

    /**
     * @return null
     */
    public function getDataFim()
    {
        return $this->dataFim;
    }

    /**
     * @param null $dataFim
     * @throws
     */
    public function setDataFim($dataFim)
    {
        try{
            $date = \DateTime::createFromFormat('Y-m-d' , $dataFim );
            if ($date == null){
                throw new \Exception('A data final é nula ou não está no formato YYYY-MM-DD. Valor informado: '. $dataFim);
            }

        }catch (\Exception $e) {
            throw new \Exception('A data final é nula ou não está no formato YYYY-MM-DD. Valor informado: ' . $dataFim);
        }

        $this->dataFim = $date->format('Y-m-d');
    }

    /**
     * @return null
     */
    public function getQuantidadeRps()
    {
        return $this->quantidadeRps;
    }

    /**
     * @param null $quantidadeRps
     */
    public function setQuantidadeRps($quantidadeRps)
    {
        $this->quantidadeRps = $quantidadeRps;
    }

    /**
     * @return null
     */
    public function getValorTotalServicos()
    {
        return $this->valorTotalServicos;
    }

    /**
     * @param null $valorTotalServicos
     */
    public function setValorTotalServicos($valorTotalServicos)
    {
        $this->valorTotalServicos = $valorTotalServicos;
    }

    /**
     * @return null
     */
    public function getValorTotalDeducoes()
    {
        return $this->valorTotalDeducoes;
    }

    /**
     * @param null $valorTotalDeducoes
     */
    public function setValorTotalDeducoes($valorTotalDeducoes)
    {
        $this->valorTotalDeducoes = $valorTotalDeducoes;
    }

    /**
     * @return bool
     */
    public function isIndicaTeste()
    {
        return $this->indicaTeste;
    }

    /**
     * @param bool $indicaTeste
     * @throws
     */
    public function setIndicaTeste($indicaTeste)
    {
        if ($indicaTeste != true && $indicaTeste != false)
            throw new \Exception('Valor inválido para indica teste. Informe true ou false');
        $this->indicaTeste = $indicaTeste;
    }

    /**
     * @return string
     */
    public function getTipoEnvio()
    {
        return $this->tipoEnvio;
    }

    /**
     * @param string $tipoEnvio
     * @throws
     */
    public function setTipoEnvio($tipoEnvio)
    {
        $tipoEnvio = strtolower($tipoEnvio);
        if($tipoEnvio != 'sync' && $tipoEnvio != 'async')
            throw new \Exception('Valor inválido para o tipo de envio. Informe "sinc" ou "async"');
        $this->tipoEnvio = $tipoEnvio;
    }



    public function addRpsFragmento(RpsFragmento $rps){
        array_push($this->rpsFragmento, $rps);
    }

    public function getAction()
    {
        // TODO: Implement getAction() method.
        if ($this->indicaTeste)
            return 'TesteEnvioLoteRPS';
        else
            return $this->action;
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

    public function toXml(){
        $strRps = '';
        foreach ($this->rpsFragmento as $rps){
            $strRps .= str_replace('<?xml version="1.0"?>','',ParseTemplate::parse($rps, $this->getXmlReplaceMark()));
        }
        $this->strFragmentos = $strRps;
        return ParseTemplate::parse($this, $this->getXmlReplaceMark());
    }

    public function toXmlSigned( $priKeyPem, $pubKeyClean){
        $strRps = '';
        foreach ($this->rpsFragmento as $rps){
            if ($rps->getAssinatura() == null){
                // Se não foi informado a assinatura da nota do milhão a cria
                $signature = Signer::getSignSP($rps, $priKeyPem);
                $rps->setAssinatura($signature);
            }
            $strRps .= str_replace('<?xml version="1.0"?>','',ParseTemplate::parse($rps, $this->getXmlReplaceMark())  );
        }
        $this->strFragmentos = $strRps;
        $xml = ParseTemplate::parse($this, $this->getXmlReplaceMark());
        return Signer::sign($xml, $priKeyPem, $pubKeyClean, ['PedidoEnvioLoteRPS']);
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

    public function getEnvelopString(){
        $return = null;
        if ($this->indicaTeste){
            $return = '<TesteEnvioLoteRPSRequest xmlns="http://www.prefeitura.sp.gov.br/nfe">
                      <VersaoSchema>1</VersaoSchema>
                      <MensagemXML>{body}</MensagemXML>
                    </TesteEnvioLoteRPSRequest>';
        }else{
            $return = '<TesteEnvioLoteRPSRequest xmlns="http://www.prefeitura.sp.gov.br/nfe">
                  <VersaoSchema>1</VersaoSchema>
                  <MensagemXML>{body}</MensagemXML>
                </TesteEnvioLoteRPSRequest>';
        }


        return $return;
    }


}