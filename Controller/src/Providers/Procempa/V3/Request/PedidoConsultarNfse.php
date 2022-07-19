<?php
/**
 * Created by PhpStorm.
 * User: moisesferreira
 * Date: 28/05/2019
 * Time: 15:47
 */

namespace Nfsews\Providers\Procempa\V3\Request;


use Nfsews\ParseTemplate;

/**
 * Class PedidoConsultarNfse
 *
 * Realiza a consulta de notas ficais emitidas em determinado período
 *
 * @package Nfsews\Providers\Procempa\V3\Request
 */
class PedidoConsultarNfse implements IRequest
{
    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = '\\Nfsews\\Providers\\Procempa\\V3\\Helpers\\Soap';
    private $responseNamespace = '\\Nfsews\\Providers\\Procempa\\V3\\Response\\PedidoConsultarNfseResponse';
    private $templatePath = null;
    private $action = 'ConsultarNfse';
    private $cpfCnpjPrestador = null;
    private $inscricaoMunicipalPrestador = null;
    private $cpfCnpjTomador = null;
    private $inscricaoMunicipalTomador = null;
    private $cpfCnpjIntermediario = null;
    private $inscricaoMunicipalIntermediario = null;
    private $numeroNfse = null;
    private $dataInicialNfse = null;
    private $dataFinalNfse = null;

    /**
     * PedidoEnviarLoteRps constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'PedidoConsultarNfse.xml'  ;
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
    public function getCpfCnpjTomador()
    {
        return $this->cpfCnpjTomador;
    }

    /**
     * @param null $cpfCnpjTomador
     */
    public function setCpfCnpjTomador($cpfCnpjTomador)
    {
        $this->cpfCnpjTomador = preg_replace('/[\.\-\/]/', '',   $cpfCnpjTomador);
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
    public function getCpfCnpjIntermediario()
    {
        return $this->cpfCnpjIntermediario;
    }

    /**
     * @param null $cpfCnpjIntermediario
     */
    public function setCpfCnpjIntermediario($cpfCnpjIntermediario)
    {
        $this->cpfCnpjIntermediario = preg_replace('/[\.\-\/]/', '',   $cpfCnpjIntermediario);
    }

    /**
     * @return null
     */
    public function getInscricaoMunicipalIntermediario()
    {
        return $this->inscricaoMunicipalIntermediario;
    }

    /**
     * @param null $inscricaoMunicipalIntermediario
     */
    public function setInscricaoMunicipalIntermediario($inscricaoMunicipalIntermediario)
    {
        $this->inscricaoMunicipalIntermediario = $inscricaoMunicipalIntermediario;
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
    public function getDataInicialNfse()
    {
        return $this->dataInicioNfse;
    }

    /**
     * @param null $dataInicioNfse
     * @throws \Exception
     */
    public function setDataInicialNfse($dataInicioNfse)
    {
        try{
            $date = \DateTime::createFromFormat('Y-m-d' , $dataInicioNfse );
            if ($date == null){
                throw new \Exception('A data de inicio é nula ou não está no formato YYYY-MM-DD. Valor informado: '. $dataInicioNfse);
            }

        }catch (\Exception $e) {
            throw new \Exception('A data de inicio é nula ou não está no formato YYYY-MM-DD. Valor informado: ' . $dataInicioNfse);
        }

        $this->dataInicialNfse = $date->format('Y-m-d');;
    }

    /**
     * @return null
     */
    public function getDataFinalNfse()
    {
        return $this->dataFinalNfse;
    }

    /**
     * @param null $dataFinalNfse
     * @throws \Exception
     */
    public function setDataFinalNfse($dataFinalNfse)
    {
        try{
            $date = \DateTime::createFromFormat('Y-m-d' , $dataFinalNfse );
            if ($date == null){
                throw new \Exception('A data de final é nula ou não está no formato YYYY-MM-DD. Valor informado: '. $dataFinalNfse);
            }

        }catch (\Exception $e) {
            throw new \Exception('A data de inicio é nula ou não está no formato YYYY-MM-DD. Valor informado: ' . $dataFinalNfse);
        }

        $this->dataFinalNfse = $date->format('Y-m-d');
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
                'mark' =>  '{cpxCpfCnpjTomador}',
                'value' =>  (strlen($this->cpfCnpjTomador) == 14) ? '<Cnpj>{cpfCnpjTomador}</Cnpj>' : '<Cpf>{cpfCnpjTomador}</Cpf>'
            ],
            [
                'mark' =>  '{cpxCpfCnpjIntermediario}',
                'value' =>  (strlen($this->cpfCnpjIntermediario) == 14) ? '<Cnpj>{cpfCnpjIntermediario}</Cnpj>' : '<Cpf>{cpfCnpjIntermediario}</Cpf>'
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

        return ParseTemplate::parse($this, $this->getXmlReplaceMark());
    }


    public function getEnvelopString(){

        return '<ns2:ConsultarNfseRequest xmlns:ns2="http://ws.bhiss.pbh.gov.br">
                    <nfseCabecMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                        <cabecalho xmlns="http://www.abrasf.org.br/nfse.xsd" versao="1.00">
                            <versaoDados>1.00</versaoDados>
                        </cabecalho>]]>
                    </nfseCabecMsg>
                    <nfseDadosMsg>{body}</nfseDadosMsg>
                </ns2:ConsultarNfseRequest>';

    }
}