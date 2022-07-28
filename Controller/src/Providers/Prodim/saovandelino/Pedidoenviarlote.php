<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 27/05/2019
 * Time: 20:56
 */

namespace Nfsews\Providers\Prodim\saovandelino;


use Nfsews\ParseTemplate;
use Nfsews\Providers\Procempa\V3\Helpers\Signer;


/**
 * Class PedidoGerarNfse
 *
 * Utilizada para realizar o envio síncrono de RPS para ser convertido em nota fiscal
 * Este serviço realiza a emissão da NFS-e no mesmo instante, no entanto, a documentação do município
 * estabelece o máximo de 3 RPS para cada envio
 *
 * @package Nfsews\Providers\Procempa\V3\Request
 */
class Pedidoenviarlote //implements IRequest
{

    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '2.00';
    private $soapHelper = '\\Nfsews\\Providers\\Procempa\\V3\\Helpers\\Soap';
    private $responseNamespace = '\\Nfsews\\Providers\\Procempa\\V3\\Response\\PedidoGerarNfseResponse';
    private $templatePath = null;
    private $action = 'mEnvioLoteRPSSincrono';
    private $idLoteRps = null;
    private $cpfCnpjPrestador = null;
    private $inscricaoMunicipalPrestador = null;
    private $quantidadeRps = null;
    private $numeroLote = null;
    private $fragmentos = [];
    private $rpsFragmentos = '';

    /**
     * PedidoEnviarLoteRps constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'PedidoEnviarLoteSv.xml'  ;
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
     * @return string|null
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }


    /**
     * @return null
     */
    public function getIdLoteRps()
    {
        return $this->idLoteRps;
    }

    /**
     * @param null $idLoteRps
     */
    public function setIdLoteRps($idLoteRps)
    {
        $this->idLoteRps = $idLoteRps;
    }

    /**
     * @return null
     */
    public function getCpfCnpjPrestador()
    {
        return $this->cpfCnpjPrestador;
    }

    /**
     * @param null $cpfCnpjPrestador
     */
    public function setCpfCnpjPrestador($cpfCnpjPrestador)
    {
        $this->cpfCnpjPrestador = preg_replace('/[\.\-\/]/', '',   $cpfCnpjPrestador);
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
    public function getNumeroLote()
    {
        return $this->numeroLote;
    }

    /**
     * @param null $numeroLote
     */
    public function setNumeroLote($numeroLote)
    {
        $this->numeroLote = $numeroLote;
    }


    /**
     * @param array $fragmentos
     */
    public function addFragmento($fragmento)
    {
        array_push($this->fragmentos, $fragmento);
    }

    /**
     * @return string
     */
    public function getRpsFragmentos()
    {
        return $this->rpsFragmentos;
    }

    /**
     * @param string $rpsFragmentos
     */
    public function setRpsFragmentos($rpsFragmentos)
    {
        $this->rpsFragmentos = $rpsFragmentos;
    }



    /**
     * @return mixed
     */
    public function getResponseNamespace()
    {
        // TODO: Implement getResponseNamespace() method.
        return $this->responseNamespace;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        // TODO: Implement getAction() method.
        return $this->action;
    }

    /**
     * @return mixed
     */
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

    /**
     * Utilizado para substituir TAGs que podem ter mais de um nome, como ocorre por exemplo com a CPFCNPJ
     * na qual pode assumir tanto o valor CNPJ quanto o valor CPF
     * @return array
     */
    private function getXmlReplaceMark(){
        return [
            [
                'mark' =>  '{cpxCpfCnpjPrestador}',
                'value' =>  (strlen($this->cpfCnpjPrestador) == 14) ? '<Cnpj>{cpfCnpjPrestador}</Cnpj>' : '<Cpf>{cpfCnpjPrestador}</Cpf>'
            ],

            [
            'mark' => '{InfDeclaracaoPrestacaoServico}',
            'value' => '1'.$this->cpfCnpjPrestador.'000000000000001',
            ],

            [
            'mark' => '{date}',
            'value' => date('YmdHis'),
            ],


        ];
    }

    /**
     * @return mixed
     * @throws
     */
    public function toXml()
    {
        // TODO: Implement toXml() method.
        if (empty($this->idLoteRps))
            $this->idLoteRps = 'Lote'. date('YmdHis'). rand(10, 99);

        $xml = '';
        $i = 0;
        foreach ($this->fragmentos as $rps){
            $xml .= str_replace('<?xml version="1.0"?>','', $rps->toXml()  );
            if (++$i == 1){
                if(empty($this->cpfCnpjPrestador))
                    $this->cpfCnpjPrestador = $rps->getCpfCnpjPrestador();
                if(empty($this->inscricaoMunicipalPrestador))
                    $this->inscricaoMunicipalPrestador = $rps->getInscricaoMunicipalPrestador();
            }
        }
        if (empty($this->quantidadeRps))
            $this->quantidadeRps = count($this->fragmentos);

        $this->rpsFragmentos = $xml;
        return ParseTemplate::parse($this, $this->getXmlReplaceMark());
    }

    /**
     * @param $priKeyPem
     * @param $pubKeyClean
     * @return string
     * @throws \Exception
     */
    public function toXmlSigned($priKeyPem, $pubKeyClean){
        if (empty($this->numeroLote))
            throw new \Exception('O numero do lote não pode ser nulo');

        if (empty($this->idLoteRps))
            $this->idLoteRps = 'Lote_'. time();

        $xml = $this->toXml();
        return Signer::sign($xml, $priKeyPem, $pubKeyClean, ['InfRps','LoteRps']);
    }

     public function getEnvelopString(){
        return '
    <mEnvioLoteRPSSincrono xmlns="http://tempuri.org/">
    <remessa>{body}</remessa>
     <cabecalho  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" versao="20.01" xmlns="http://www.nfse-tecnos.com.br/nfse.xsd">
     </cabecalho>
        
      </mEnvioLoteRPSSincrono> ';

    }  

 /*    public function getEnvelopStringFeliz(){
        return '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
        <soapenv:Header>
           <tem:cabecalho versao="?">
              <!--Optional:-->
              <tem:versaoDados>1.00</tem:versaoDados>
           </tem:cabecalho>
        </soapenv:Header>
        <soapenv:Body>
           <tem:SubstituirNfse>
              <!--Optional:-->
              <tem:xmlEnvio>?</tem:xmlEnvio>
           </tem:SubstituirNfse>
        </soapenv:Body>
     </soapenv:Envelope>';

    }  */


   
    

}