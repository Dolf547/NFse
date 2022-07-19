<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 06/05/2019
 * Time: 20:57
 */

namespace Nfsews\Providers\Prodam\V2\Request;




use Nfsews\ParseTemplate;
use Nfsews\Providers\Prodam\V2\Helpers\Signer;

class PedidoCancelamentoNfe //implements IRequest
{
    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = '\\Nfsews\\Providers\\Prodam\\V2\\Helpers\\Soap';
    private $responseNamespace = '\\Nfsews\\Providers\\Prodam\\V2\\Response\\PedidoCancelamentoNfeResponse';
    private $action = 'CancelarNfse';
    private $templatePath = null;
    private $cpfCnpjRemetente = null;
    private $transacao = false;
    private $cancelamentoNfeFragmento = [];
    private $strFragmentos = '';

    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'PedidoCancelamentoNfe.xml'  ;
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
     * @param array $cancelamentoNfeFragmento
     */
    public function addCancelamentoNfeFragmento(CancelamentoNfeFragmento $cancelamentoNfeFragmento)
    {
        array_push($this->cancelamentoNfeFragmento,  $cancelamentoNfeFragmento);
    }

    /**
     * @return string
     */
    public function getResponseNamespace()
    {
        return $this->responseNamespace;

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
        $xml = '';
        // TODO: Implement toXml() method.
        foreach ($this->cancelamentoNfeFragmento as $nfe){
            $xml .= str_replace('<?xml version="1.0"?>','',ParseTemplate::parse($nfe));
        }
        $this->strFragmentos = $xml;
        return ParseTemplate::parse($this, $this->getXmlReplaceMark());
    }


    public function toXmlSigned( $priKeyPem, $pubKeyClean){
        $strRps = '';
        foreach ($this->cancelamentoNfeFragmento as $nfe){
            if ($nfe->getAssinaturaCancelamento() == null){
                // Se não foi informado a assinatura da nota do milhão a cria
                $signature = Signer::getSignCancelamentoSP($nfe, $priKeyPem);
                $nfe->setAssinaturaCancelamento($signature);
            }
            $strRps .= str_replace('<?xml version="1.0"?>','', ParseTemplate::parse($nfe)  );
        }
        $this->strFragmentos = $strRps;
        $xml = ParseTemplate::parse($this, $this->getXmlReplaceMark());
        return Signer::sign($xml, $priKeyPem, $pubKeyClean, ['PedidoCancelamentoNFe']);
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

  

    public function getEnvelopString(){
        return '
        <CancelarNfseRequest xmlns="http://ws.bhiss.pbh.gov.br">
                  <VersaoSchema>http://schemas.xmlsoap.org/soap/envelope/</VersaoSchema>
                  <mensagem>{body}</mensagem>
                </CancelarNfseRequest>';
    }

    public function getEnvelopHeader2(){
        return "<nfseCabecMsg>  <![CDATA[<?xml version='1.0' encoding='utf-8'?><cabecalho
        xmlns='http://www.abrasf.org.br/nfse.xsd' versao='1.00'>
        <versaoDados>1.00</versaoDados></cabecalho>]]>
        </nfseCabecMsg>";
    }


    
}