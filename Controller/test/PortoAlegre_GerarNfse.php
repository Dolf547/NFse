<?php
ini_set("default_socket_timeout", 60);

define('DS', DIRECTORY_SEPARATOR);

include '../vendor/autoload.php';

use Nfsews\Config;
use Nfsews\Certificate\Certificate;
use Nfsews\Connection;
use Nfsews\Providers\Procempa\V3\Request\PedidoGerarNfse;
use Nfsews\Providers\Procempa\V3\Request\RpsFragmento;

// PARA CERTIFICADO VÁLIDO
$options = [
    'soapOptions' => [
        'ssl'	=>	[
            'cafile'	=>	__dir__ . DS . 'ca_mozilla_2019.pem',

        ]
    ]
];


$config = new Config($options);
$config->setPfxCert(__dir__ . DS . 'moises.pfx');
$config->setPasswordCert('demo1');
$config->setTmpDirectory(__dir__ . DS . 'tmp');
//$config->setXmlDirectory(__dir__ . DS . 'tmp' . DS . 'xml');

$config->setWsdl('https://nfse-hom.procempa.com.br/bhiss-ws/nfse?wsdl');

$certificate = new Certificate($config);

// A classe PedidoGerarNfse utiliza o serviço sincrono para a geração da notafiscal diferentemente da classe PedidoEnviarLoteRps (Assincrono)
// o fato dela ser sincrona indica que a resposta que obtiver da prefeitura, caso não seja erro, já será a nota fiscal,
// enquanto em PedidoEnviarLoteRps você obtém o número de protocolo para posteriormente consultar se o lote enviado já foi processado pelo servidor.
// no entanto, existe o porém de que o serviço sincrono aceita apenas 3 RPS de cada vez enquanto o outro aceita 50
$pedido = new PedidoGerarNfse();
$pedido->setCpfCnpjPrestador('14254587000148');
$pedido->setInscricaoMunicipalPrestador('102030');
$pedido->setNumeroLote(1);

$rps = new RpsFragmento();
$rps->setCpfCnpjPrestador('14254587000148');
$rps->setInscricaoMunicipalPrestador('102030');
$rps->setNumeroRps(1);
$rps->setSerieRps('R1');
$rps->setTipoRps(1);
$rps->setDataEmissao(date('Y-m-d\TH:i:s'));
$rps->setStatus(1);
$rps->setItemListaServico('1401');
$rps->setCodigoTributacaoMunicipio('1401');
$rps->setCodigoMunicipioPrestacao('1425417');
$rps->setNaturezaOperacao(1);
$rps->setOptanteSimplesNacional(2);
$rps->setIncentivadorCultural(2);
$rps->setValorServicos(5000.00);
$rps->setAliquota(0.03);
$rps->setBaseCalculo(5000.00);
$rps->setValorIss(150.00);
$rps->setDiscriminacao('Informe aqui o texto da nota fiscal');
// Os campos preenchidos acima são apenas os obrigatórios do ponto de vista estrutural do XML
// no entanto você deve continuar preenchendo até satisfazer todas as informações que a Prefeitura de Porto Alegre
// exige
$pedido->addFragmento($rps);

//echo $pedido->toXml(); exit;

//echo $pedido->toXmlSigned($certificate->getPriKeyPem(), $certificate->getPubKeyClean()); exit;


// Realizar a conexao
$connection = new Connection($config, $certificate);
//var_dump($connection->listWsOperations()); exit;

// Enviar o RPS
$response = $connection->dispatch($pedido);


var_dump( $response);




