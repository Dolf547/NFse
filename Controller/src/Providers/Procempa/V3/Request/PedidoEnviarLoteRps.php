<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 26/05/2019
 * Time: 15:46
 */

namespace Nfsews\Providers\Procempa\V3\Request;


use Nfsews\ParseTemplate;
use Nfsews\Providers\Procempa\V3\Helpers\Signer;


/**
 * Class PedidoEnviarLoteRps
 *
 * Realiza a emissão de uma ou várias notas fiscais
 * A documentação do município estabelece que serão aceitos neste serviço o máximo de 50 RPS para serem convertidos em NFS-e
 *
 * @package Nfsews\Providers\Procempa\V3\Request
 */
class PedidoEnviarLoteRps implements IRequest
{
    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = '\\Nfsews\\Providers\\Procempa\\V3\\Helpers\\Soap';
    private $responseNamespace = '\\Nfsews\\Providers\\Procempa\\V3\\Response\\PedidoEnvioLoteRpsResponse';
    private $templatePath = null;
    private $action = 'RecepcionarLoteRps';
    private $idLoteRps = null;
    private $cpfCnpjPrestador = null;
    private $inscricaoMunicipalPrestador = null;
    private $quantidadeRps = null;
    private $numeroLote = null;
    private $fragmentos = [];
    private $rpsFragmentos = '';

    /**
     * PedidoEnviarLoteRps constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'PedidoEnviarLoteRps.xml'  ;
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
    public function getIdLoteRps()
    {
        return $this->idLoteRps;
    }

    /**
     * @param null $idLoteRps
     */
    public function setIdLoteRps($idLoteRps)
    {
        $this->idLoteRps = $idLoteRps;
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
    public function getQuantidadeRps()
    {
        return $this->quantidadeRps;
    }

    /**
     * @param null $quantidadeRps
     */
    public function setQuantidadeRps($quantidadeRps)
    {
        $this->quantidadeRps = $quantidadeRps;
    }

    /**
     * @return null
     */
    public function getNumeroLote()
    {
        return $this->numeroLote;
    }

    /**
     * @param null $numeroLote
     */
    public function setNumeroLote($numeroLote)
    {
        $this->numeroLote = $numeroLote;
    }



    /**
     * @param array $fragmento
     */
    public function addFragmento(RpsFragmento $fragmento)
    {
        array_push($this->fragmentos, $fragmento);
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
        if (empty($this->idLoteRps))
            $this->idLoteRps = 'Lote'. date('YmdHis'). rand(10, 99);

        $xml = '';

        $i = 0;
        foreach ($this->fragmentos as $rps){
            $xml .= str_replace('<?xml version="1.0"?>','', $rps->toXml()  );
            if (++$i == 1){
                if(empty($this->cpfCnpjPrestador))
                    $this->cpfCnpjPrestador = $rps->getCpfCnpjPrestador();
                if(empty($this->inscricaoMunicipalPrestador))
                    $this->inscricaoMunicipalPrestador = $rps->getInscricaoMunicipalPrestador();
            }
        }
        if (empty($this->quantidadeRps))
            $this->quantidadeRps = count($this->fragmentos);

        $this->rpsFragmentos = $xml;
        return ParseTemplate::parse($this, $this->getXmlReplaceMark());
    }


    /**
     * @param $priKeyPem
     * @param $pubKeyClean
     * @return string
     * @throws \Exception
     */
    public function toXmlSigned($priKeyPem, $pubKeyClean){
        if (empty($this->numeroLote))
            throw new \Exception('O numero do lote não pode ser nulo');

        if (empty($this->idLoteRps))
            $this->idLoteRps = 'Lote_'. time();

        $xml = $this->toXml();
        return Signer::sign($xml, $priKeyPem, $pubKeyClean, ['InfRps','LoteRps']);
    }

    public function getEnvelopString(){

        return '<ns2:RecepcionarLoteRpsRequest xmlns:ns2="http://ws.bhiss.pbh.gov.br">
                    <nfseCabecMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                        <cabecalho xmlns="http://www.abrasf.org.br/nfse.xsd" versao="1.00">
                            <versaoDados>1.00</versaoDados>
                        </cabecalho>]]>
                    </nfseCabecMsg>
                    <nfseDadosMsg>{body}</nfseDadosMsg>
                </ns2:RecepcionarLoteRpsRequest>';

    }


}