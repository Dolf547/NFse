<?php
/**
 * Created by PhpStorm.
 * User: moisesferreira
 * Date: 28/05/2019
 * Time: 14:15
 */

namespace Nfsews\Providers\Procempa\V3\Request;


use Nfsews\ParseTemplate;


/**
 * Class PedidoConsultarLoteRps
 *
 * Realiza a consulta de notas fiscais pesquisando pelo numero do protocolo que as gerou
 *
 * @package Nfsews\Providers\Procempa\V3\Request
 */
class PedidoConsultarLoteRps implements IRequest
{

    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = '\\Nfsews\\Providers\\Procempa\\V3\\Helpers\\Soap';
    private $responseNamespace = '\\Nfsews\\Providers\\Procempa\\V3\\Response\\PedidoConsultarLoteRpsResponse';
    private $templatePath = null;
    private $action = 'ConsultarLoteRps';
    private $cpfCnpjPrestador = null;
    private $inscricaoMunicipalPrestador = null;
    private $protocolo = null;

    /**
     * PedidoEnviarLoteRps constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'PedidoConsultarLoteRps.xml'  ;
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
     * @return null|string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
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
    public function getProtocolo()
    {
        return $this->protocolo;
    }

    /**
     * @param null $protocolo
     */
    public function setProtocolo($protocolo)
    {
        $this->protocolo = $protocolo;
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


    public function getEnvelopString(){

        return '<ns2:ConsultarLoteRpsRequest xmlns:ns2="http://ws.bhiss.pbh.gov.br">
                    <nfseCabecMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                        <cabecalho xmlns="http://www.abrasf.org.br/nfse.xsd" versao="1.00">
                            <versaoDados>1.00</versaoDados>
                        </cabecalho>]]>
                    </nfseCabecMsg>
                    <nfseDadosMsg>{body}</nfseDadosMsg>
                </ns2:ConsultarLoteRpsRequest>';

    }
}