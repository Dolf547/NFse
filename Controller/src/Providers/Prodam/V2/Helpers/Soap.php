<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 11/05/2019
 * Time: 23:32
 */

namespace Nfsews\Providers\Prodam\V2\Helpers;


class Soap
{
    private $wsdl = null;
    private $options = null;
    private $logDirectory = null;
    private $priKeyPem = null;
    private $pubKeyClean = null;

    public function __construct($wsdl, $options, $logDirectory)
    {
        $this->wsdl = $wsdl;
        $this->options = $options;
        $this->logDirectory = $logDirectory;

        if (file_exists($options['ssl']['local_pk']))
            $this->priKeyPem = file_get_contents($options['ssl']['local_pk']);

        if(file_exists($options['ssl']['local_cert'])){
            $pem = file_get_contents($options['ssl']['local_cert']);
            $clean = $this->cleanKeyPem($pem);
            $this->pubKeyClean = $clean;
        }


    }

    private function cleanKeyPem($pem){
        $ret = preg_replace('/-----.*[\n]?/', '', $pem);
        return preg_replace('/[\n\r]/', '', $ret);
    }

    public function send($request){
        // Obtem a string de envelopamento conforme o WSDL do servidor da Prefeitura especifica
        $envelop = $request->getEnvelopString();

        $xml = '';
        // verifica se existe o método toXmlSigned e o chama, senão chama o método toXml a fim de obter o XML a ser enviado
        if (method_exists($request, 'toXmlSigned')){
            if (empty($this->priKeyPem) || empty($this->pubKeyClean) ){
                throw new \Exception('Para esta operação é obrigatório informar a chave privada e publica do certificado digital');
            }
            $xml = $request->toXmlSigned($this->priKeyPem, $this->pubKeyClean );
        }else{
            $xml = $request->toXml();
        }
        // Substitui o {body} da string de envelope pelo o XML a ser enviado
        $message = str_replace('{body}', '<![CDATA['. $xml . ']]>', $envelop);

        $soap = null;
        $callResult = null;
        try {
            libxml_disable_entity_loader(false);
            $soap = new \SoapClient($this->wsdl , $this->options);
            // obtem o action name para chamada do método do webservice
            $actionName = $request->getAction();
            // Configura a string XML para o formato do soapclient
            $envelopedMessage = new \SoapVar($message, XSD_ANYXML);
            // faz o envio do xml para servidor da prefeitura
            $callResult = $soap->$actionName($envelopedMessage);
            // Se callResult estiver no formato de objeto, o transforma em string
            if (is_object($callResult) && ! empty($callResult->RetornoXML) && is_string($callResult->RetornoXML))
                $callResult = $callResult->RetornoXML;
            else{
                if ( stripos($soap->__getLastResponse(), 'soap:Envelope') !== false)
                    $callResult = $soap->__getLastResponse();
            }


            // cria uma response
            if (! empty($request->getResponseNamespace())) {
                $nameSpaceResponse = $request->getResponseNamespace();
                $objResponse = new $nameSpaceResponse();

                if (!$objResponse->parseXml($callResult)) {
                    // Se houver algum problema com a resposta do servidor da prefeitura e consequentemente não ser
                    // possível converter a string retorno em um objeto do tipo Response será retornado um array
                    $response = [$callResult, $soap];
                } else {
                    $response = $objResponse;
                    @$response->trace = $this->getTrace($soap);
                    // adiciona o XML de envio na Response
                    @$response->xmlEnvio = $xml;
                }
            }else
                $response = [$callResult, $soap];

        }catch (\SoapFault $e){
            $response = [$callResult, $soap, $e->getMessage()];
           // $response = new Response($soap, $this->logDirectory, $e->getMessage());
        }catch (\Exception $e){
            //throw new \Exception($e->getTraceAsString());
            $response = [$callResult, $soap, $e->getMessage()];
        }



        return $response;

    }

    private function getTrace($soap){
        $soapDebug = '';
        if (is_object($soap)){
            $soapDebug = '<h3>Request</h3>';
            //$soapDebug .= "\n" . $soapFault;
            $soapDebug.= "\n" . $soap->__getLastRequestHeaders();
            $soapDebug .= "\n" . $soap->__getLastRequest();
            $soapDebug .= "\n" . '<h3>Response</h3>';
            $soapDebug.= "\n" . $soap->__getLastResponseHeaders();
            $soapDebug .= "\n" . $soap->__getLastResponse();
        }else{
            $soapDebug = date('Y-m-d H:i:s') . ' - $soap não é um objeto';
        }

        return $soapDebug;
    }
}