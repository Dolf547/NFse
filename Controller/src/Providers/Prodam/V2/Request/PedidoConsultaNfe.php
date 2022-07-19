<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 07/05/2019
 * Time: 20:40
 */

namespace Nfsews\Providers\Prodam\V2\Request;


use Nfsews\ParseTemplate;
use Nfsews\Providers\Prodam\V2\Helpers\Signer;

class PedidoConsultaNfe implements IRequest
{
    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = '\\Nfsews\\Providers\\Prodam\\V2\\Helpers\\Soap';
    private $responseNamespace = '\\Nfsews\\Providers\\Prodam\\V2\\Response\\PedidoConsultaResponse';
    private $templatePath = null;
    private $action = 'ConsultaNFe';
    private $cpfCnpjRemetente = null;
    private $detalhesFragmentos = [];
    private $strFragmentos = '';


    /**
     * PedidoEnvioLoteRps constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'PedidoConsultaNfe.xml'  ;
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

    public function getAction()
    {
        // TODO: Implement getAction() method.
        return $this->action;
    }

    public function addConsultaNfeFragmento(ConsultaNfeFragmento $fragmento){
        array_push($this->detalhesFragmentos, $fragmento);
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
        $xml = '';
        foreach ($this->detalhesFragmentos as $fragmento){
            $xml .= $fragmento->toXml();
        }

        $this->strFragmentos = $xml;
        return ParseTemplate::parse($this, $this->getXmlReplaceMark() );
    }

    public function toXmlSigned( $priKeyPem, $pubKeyClean){
        $xml = $this->toXml();
        return Signer::sign($xml, $priKeyPem, $pubKeyClean, ['PedidoConsultaNFe']);
    }

    public function getEnvelopString(){
        return '<ConsultaNFeRequest xmlns="http://www.prefeitura.sp.gov.br/nfe">
                      <VersaoSchema>1</VersaoSchema>
                      <MensagemXML>{body}</MensagemXML>
                    </ConsultaNFeRequest>';

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
            ]
        ];
    }

}