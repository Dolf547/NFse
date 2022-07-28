<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 25/05/2019
 * Time: 16:51
 */

namespace Nfsews\Providers\Procempa\V3\Request;


use Nfsews\ParseTemplate;
use Nfsews\Providers\Procempa\V3\Helpers\SignerSv;

/**
 * Class PedidoCancelarNfse
 *
 * Utilizado para cancelar uma Nota Fiscal
 *
 * @package Nfsews\Providers\Procempa\V3\Request
 */
class PedidoCancelarNfseSv implements IRequest
{

    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = '\\Nfsews\\Providers\\Procempa\\V3\\Helpers\\Soap';
    private $responseNamespace = '\\Nfsews\\Providers\\Procempa\\V3\\Response\\PedidoCancelarNfseResponse';
    private $templatePath = null;
    private $action = 'mCancelamentoNFSe';
    private $idPedidoCancelamento = null;
    private $numeroNfse = null;
    private $cpfCnpjPrestador = null;
    private $inscricaoMunicipalPrestador = null;
    private $codigoMunicipio = null;
    private $codigoCancelamento = null;

    /**
     * PedidoConsultaCnpj constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'PedidoCancelarNfseSv.xml'  ;
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
    public function getIdPedidoCancelamento()
    {
        return $this->idPedidoCancelamento;
    }

    /**
     * @param null $idPedidoCancelamento
     */
    public function setIdPedidoCancelamento($idPedidoCancelamento)
    {
        $this->idPedidoCancelamento = $idPedidoCancelamento;
    }

    /**
     * @return null
     */
    public function getNumeroNfse()
    {
        return $this->numeroNfse;
    }

    /**
     * @param null $numeroNfse
     */
    public function setNumeroNfse($numeroNfse)
    {
        $this->numeroNfse = $numeroNfse;
    }

    /**
     * @return null
     */
    public function getCpfCnpjPrestador()
    {
        return $this->cpfCnpjPrestador;
    }

    /**
    // * @param null $cpfCnpjPrestador
     */
    public function cpfCnpjPrestador($cpfCnpjPrestador)
    {
        $this->cpfCnpjPrestador= preg_replace('/[\.\-\/]/', '',   $cpfCnpjPrestador);
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

    public function setCnpjRemetente($cpfCnpjRemetente)
    {
        $this->CnpjRemetente = preg_replace('/[\.\-\/]/', '',   $cpfCnpjRemetente);
    }
    
 


    /**
     * @return null
     */
    public function getCodigoMunicipio()
    {
        return $this->codigoMunicipio;
    }

    /**
     * Informar o codigo IBGE do município
     * @param null $codigoMunicipio
     */
    public function setCodigoMunicipio($codigoMunicipio)
    {
        $this->codigoMunicipio = $codigoMunicipio;
    }

    /**
     *
     * @return null
     */
    public function getCodigoCancelamento()
    {
        return $this->codigoCancelamento;
    }

    /**
     * Informações da documentação do municipio para o codigo de cancelamento.
     * Código de cancelamento com base na tabela de Erros e alertas.
     *  1 – Erro na emissão
     *  2 – Serviço não prestado
     *  3 – Erro de assinatura
     *  4 – Duplicidade da nota
     *  5 – Erro de processamento
     *  Importante: Os códigos 3 (Erro de assinatura) e 5 (Erro de processamento) são de us
     *
     * @param null $codigoCancelamento
     */
    public function setCodigoCancelamento($codigoCancelamento)
    {
        $this->codigoCancelamento = $codigoCancelamento;
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
                'value' =>  (strlen($this->cpfCnpjPrestador) == 14) ? '<Cnpj>{cpfCnpjPrestador}</Cnpj>' : '<CPF>{cpfCnpjPrestador}</CPF>'
            ]
        ];
    }

    /**
     * @return mixed
     * @throws
     */
    public function toXml()
    {
        // TODO: Implement toXml() method.
        return ParseTemplate::parse($this, $this->getXmlReplaceMark());
    }


    /**
     * @param $priKeyPem
     * @param $pubKeyClean
     * @return string
     * @throws \Exception
     */
    public function toXmlSigned($priKeyPem, $pubKeyClean){
                $xml = $this->toXml();
        return SignerSv::sign($xml, $priKeyPem, $pubKeyClean, ['InfPedidoCancelamento']);
    }

    public function getEnvelopString(){
        return '
    <mCancelamentoNFSe xmlns="http://tempuri.org/">
            <remessa>{body}</remessa>
            <cabecalho  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" versao="20.01" xmlns="http://www.nfse-tecnos.com.br/nfse.xsd">
            </cabecalho>
      </mCancelamentoNFSe> ';

    }  


}