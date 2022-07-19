<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 07/05/2019
 * Time: 20:24
 */

namespace Nfsews\Providers\Prodam\V2\Request;


use Nfsews\ParseTemplate;
use Nfsews\Providers\Prodam\V2\Helpers\Signer;

class PedidoConsultaCnpj implements IRequest
{
    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = '\\Nfsews\\Providers\\Prodam\\V2\\Helpers\\Soap';
    private $responseNamespace = '\\Nfsews\\Providers\\Prodam\\V2\\Response\\PedidoConsultaCnpjResponse';
    private $templatePath = null;
    private $action = 'ConsultaCNPJ';
    private $cpfCnpjRemetente = null;
    private $cpfCnpjContribuinte = null;

    /**
     * PedidoConsultaCnpj constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'PedidoConsultaCnpj.xml'  ;
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
    public function getCpfCnpjContribuinte()
    {
        return $this->cpfCnpjContribuinte;
    }

    /**
     * @param null $cpfCnpjContribuinte
     */
    public function setCpfCnpjContribuinte($cpfCnpjContribuinte)
    {
        $this->cpfCnpjContribuinte = preg_replace('/[\.\-\/]/', '',   $cpfCnpjContribuinte);
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

        return ParseTemplate::parse($this);
    }

    public function toXmlSigned( $priKeyPem, $pubKeyClean){

        $xml = ParseTemplate::parse($this, $this->getXmlReplaceMark());
        return Signer::sign($xml, $priKeyPem, $pubKeyClean, ['PedidoConsultaCNPJ']);
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
                'mark' =>  '{CpxCpfCnpjContribuinte}',
                'value' =>  (strlen($this->cpfCnpjContribuinte) == 14) ? '<CNPJ>{cpfCnpjContribuinte}</CNPJ>' : '<CPF>{cpfCnpjContribuinte}</CPF>'
            ]
        ];
    }

    public function getEnvelopString(){
        return '<ConsultaCNPJRequest xmlns="http://www.prefeitura.sp.gov.br/nfe">
                  <VersaoSchema>1</VersaoSchema>
                  <MensagemXML>{body}</MensagemXML>
                </ConsultaCNPJRequest>';
    }
}