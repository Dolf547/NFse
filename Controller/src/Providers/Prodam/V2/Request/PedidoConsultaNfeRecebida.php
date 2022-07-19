<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 07/05/2019
 * Time: 20:56
 */

namespace Nfsews\Providers\Prodam\V2\Request;


use Nfsews\ParseTemplate;
use Nfsews\Providers\Prodam\V2\Helpers\Signer;

class PedidoConsultaNfeRecebida implements IRequest
{
    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = '\\Nfsews\\Providers\\Prodam\\V2\\Helpers\\Soap';
    private $responseNamespace = '\\Nfsews\\Providers\\Prodam\\V2\\Response\\PedidoConsultaResponse';
    private $templatePath = null;
    private $action = 'ConsultaNFeRecebidas';
    private $cpfCnpjRemetente = null;
    private $cpfCnpjConsulta = null;
    private $inscricaoMunicipalConsulta = null;
    private $dataInicio = null;
    private $dataFim = null;
    private $numeroPagina = null;

    /**
     * PedidoConsultaNfeRecebida constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'PedidoConsultaNfePeriodo.xml'  ;
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
        $this->cpfCnpjRemetente = preg_replace('/[\.\-\/]/', '', $cpfCnpjRemetente);
    }

    /**
     * @return null
     */
    public function getCpfCnpjConsulta()
    {
        return $this->cpfCnpjConsulta;
    }

    /**
     * @param null $cpfCnpjConsulta
     */
    public function setCpfCnpjConsulta($cpfCnpjConsulta)
    {
        $this->cpfCnpjConsulta = preg_replace('/[\.\-\/]/', '', $cpfCnpjConsulta);
    }

    /**
     * @return null
     */
    public function getInscricaoMunicipalConsulta()
    {
        return $this->inscricaoMunicipalConsulta;
    }

    /**
     * @param null $inscricaoMunicipalConsulta
     */
    public function setInscricaoMunicipalConsulta($inscricaoMunicipalConsulta)
    {
        $this->inscricaoMunicipalConsulta = $inscricaoMunicipalConsulta;
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
    public function getNumeroPagina()
    {
        return $this->numeroPagina;
    }

    /**
     * @param null $numeroPagina
     */
    public function setNumeroPagina($numeroPagina)
    {
        $this->numeroPagina = $numeroPagina;
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


    /**
     * @return \Nfsews\Request
     * @throws \Exception
     */
    public function toXml()
    {
        // TODO: Implement toXml() method.
        return ParseTemplate::parse($this, $this->getXmlReplaceMark());
    }

    public function toXmlSigned( $priKeyPem, $pubKeyClean){

        $xml = $this->toXml();
        return Signer::sign($xml, $priKeyPem, $pubKeyClean, ['PedidoConsultaNFePeriodo']);
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
                'mark' =>  '{CpxCpfCnpjConsulta}',
                'value' =>  (strlen($this->cpfCnpjConsulta) == 14) ? '<CNPJ>{cpfCnpjConsulta}</CNPJ>' : '<CPF>{cpfCnpjConsulta}</CPF>'
            ]
        ];
    }

    public function getEnvelopString(){

        return '<ConsultaNFeRecebidasRequest xmlns="http://www.prefeitura.sp.gov.br/nfe">
                  <VersaoSchema>1</VersaoSchema>
                  <MensagemXML>{body}</MensagemXML>
                </ConsultaNFeRecebidasRequest>';

    }

}