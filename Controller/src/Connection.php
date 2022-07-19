<?php
namespace Nfsews;
use Nfsews\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Nfsews\Certificate\Certificate;

include('adapted_vendor/nfse/src/XmlTools.php');
include('adapted_vendor/nfse/src/Providers/Procempa/V3/Helpers/Soap.php');
include('adapted_vendor/nfse/src/Providers/Prodam/V2/Helpers/Soap.php');
include('adapted_vendor/nfse/src/Providers/Prodam/V2/Helpers/Signer.php');
include('adapted_vendor/nfse/src/Providers/Procempa/V3/Helpers/Signer.php');
include('adapted_vendor/nfse/src/ParseTemplate.php');
include('adapted_vendor/nfse/src/Response.php');

class Response
{
    public $exception = null;
    public $trace = null;
    public $xmlEnvio = null;
    public $xmlResposta = null;
    public $erros = [];
    public $alertas = [];

}


class Connection{
	private $config;
	private $certificate;
	private $lastResponse = null;
	//const SYS_DS = DIRECTORY_SEPARATOR;
	public function __construct(Config $config, Certificate $certificate ){
		$this->config = $config;
		$this->certificate = $certificate;
		$this->config->setXmlDirectory(getCwd() . '/public/clientes/' . $_SESSION['id_empresa'] . '/nfe/RS202108251117.xml');
	}


	private function setLastResponse($var){
		if (is_object($var) ){
			$this->lastResponse = $var;
		}else{
			if (is_string($var)){
				$this->lastResponse = new Response();
				$this->lastResponse->exception = $var;
				$this->lastResponse->xmlResposta = $var;
			}else{
				if (is_array($var)){
					$this->lastResponse = new Response();
					if (isset($var[0])){
						$this->lastResponse->xmlResposta = $var[0];
					}
					if (isset($var[1])){
						$this->lastResponse->trace = $this->getTrace($var[1]);
					}
					if (isset($var[2])){
						$this->lastResponse->exception = $var[2];
						/* var_dump($var[2]);
						die; */
					}
				}else{
					// Resposta indefinida
					$this->lastResponse = $var;
				}
			}
		}
	}
	public function getLastResponse(){
		return $this->lastResponse;
	}
	private function getTrace($soap){
		$soapDebug = '';
		if (is_object($soap)){
			$soapDebug = '<h3>Request</h3>';
			$soapDebug .= "\n" . $soap->__getLastRequestHeaders();
			$soapDebug .= "\n" . $soap->__getLastRequest();
			$soapDebug .= "\n" . '<h3>Response</h3>';
			$soapDebug .= "\n" . $soap->__getLastResponseHeaders();
			$soapDebug .= "\n" . $soap->__getLastResponse();
		}else{
			$soapDebug = date('Y-m-d H:i:s') . ' - $soap não é um objeto';
		}
		return $soapDebug;
	}
	public function listWsOperations($returnTrace = false){
		$localCert = $this->certificate->createPubPemFile();
		$localPk = $this->certificate->createPriPemFile();
		$options = $this->config->getSoapOptions();
		$options['ssl']['local_pk'] = $localPk;
		$options['ssl']['local_cert'] = $localCert;
		$options['ssl']['passphrase'] = $this->config->getPasswordCert();
		use_soap_error_handler(true);
		$soap = null;
		$response = null;
		$logDirectory = $this->config->getLogDirectory();
		try {
			libxml_disable_entity_loader(false);
			$options['stream_context'] = stream_context_create(['ssl' => $options['ssl']]);
			$soap = new \SoapClient($this->config->getWsdl(), $options);
			$response = $soap->__getFunctions();
		}catch (\SoapFault $e){
			$response = new Response($soap, $logDirectory);
			echo $e->getMessage();
			exit;
		}catch (\Exception $e){
			$response = new Response($soap, $logDirectory);
			echo $e->getMessage();
			exit;
		}
		$this->certificate->deletePemFile([$localPk, $localCert]);
		if ($returnTrace){
			return [$response, $this->getTrace($soap)];
		}else{
			return $response;
		}
	}
	public function dispatch($request, $returnXml = false){
		$wsdl = $this->config->getWsdl();
		$options = $this->config->getSoapOptions();
		$logDirectory = $this->config->getLogDirectory();
		$localPk = $this->certificate->createPriPemFile();
		$localCert = $this->certificate->createPubPemFile();
		 $options['ssl']['local_pk'] = $localPk;
		$options['ssl']['local_cert'] = $localCert;
		$options['ssl']['passphrase'] = $this->config->getPasswordCert();
		$options['stream_context'] = stream_context_create(['ssl' => $options['ssl']]); 
		$nameSoapHelper = $request->getSoapHelper();
	
	 	 if (! class_exists($nameSoapHelper)){
			throw new \Exception('Um HELPER parece não ser uma classe. Nome do HELPER: '. $nameSoapHelper);
		}  
		
		$soap = new $nameSoapHelper($wsdl, $options, $logDirectory);
	
	
		$response = $soap->send($request);
		

		

		$this->certificate->deletePemFile([$localPk, $localCert]);
		$this->setLastResponse($response);
		if(! empty($this->config->getXmlDirectory())){
			$this->saveXml($request->getAction());
		}
	
		if ($returnXml == true){
			return $this->lastResponse->xmlResposta ;
		}else{
			return $this->lastResponse;
		}
	}
	private function saveXml($name){
	//	$a = $this->config->getXmlDirectory;
		
		$name = ucwords(strtolower(str_replace('envio','',$name)));
		$rand = date('YmdHis') . rand(10, 99);
		$nameRequest = $name. 'Envio_' . $rand . '.xml';
		$nameResponse = $name. 'Resposta_' . $rand . '.xml';
		$pasta = getCwd() . '/public/clientes/' . $_SESSION['id_empresa'] . '/nfe/RS202108251117.xml';
		if(is_dir($pasta)){
	
		}else{
			mkdir(dirname(__FILE__).getCwd() . '/public/clientes/' . $_SESSION['id_empresa'] . '/nfe/RS202108251117.xml/', 0777, true);

		}
	//	var_dump($a);
		$caminhosXml = getCwd() . '/public/clientes/' . $_SESSION['id_empresa'] . '/nfe/';
		if ( empty($this->lastResponse->exception) ){
			XmlTools::saveXml($this->lastResponse->xmlEnvio, $nameRequest, $this->config->getXmlDirectory());
			XmlTools::saveXml($this->lastResponse->xmlResposta, $nameResponse, $caminhosXml);
			$this->lastResponse->xmlEnvio = $nameRequest;
			$this->lastResponse->xmlResposta = $nameResponse;
		}
	}
}