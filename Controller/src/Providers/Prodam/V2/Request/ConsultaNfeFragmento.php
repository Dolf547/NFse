<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 16/05/2019
 * Time: 20:38
 */

namespace Nfsews\Providers\Prodam\V2\Request;


use Nfsews\ParseTemplate;

class ConsultaNfeFragmento implements IRequest
{

    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = null;
    private $templatePath = null;
    private $inscricaoMunicipalPrestador = null;
    private $nfeInscricaoMunicipalPrestador = null;
    private $rpsInscricaoMunicipalPrestador = null;
    private $serieRps = null;
    private $numeroRps = null;
    private $numeroNfe = null;
    private $codigoVerificacao = null;


    /**
     * ConsultaNfeFragmento constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'ConsultaNfeFragmento.xml'  ;
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
    public function getSerieRps()
    {
        return $this->serieRps;
    }

    /**
     * @param null $serieRps
     */
    public function setSerieRps($serieRps)
    {
        $this->serieRps = $serieRps;
    }

    /**
     * @return null
     */
    public function getNumeroRps()
    {
        return $this->numeroRps;
    }

    /**
     * @param null $numeroRps
     */
    public function setNumeroRps($numeroRps)
    {
        $this->numeroRps = $numeroRps;
    }

    /**
     * @return null
     */
    public function getNumeroNfe()
    {
        return $this->numeroNfe;
    }

    /**
     * @param null $numeroNfe
     */
    public function setNumeroNfe($numeroNfe)
    {
        $this->numeroNfe = $numeroNfe;
    }

    /**
     * @return null
     */
    public function getCodigoVerificacao()
    {
        return $this->codigoVerificacao;
    }

    /**
     * @param null $codigoVerificacao
     */
    public function setCodigoVerificacao($codigoVerificacao)
    {
        $this->codigoVerificacao = $codigoVerificacao;
    }



    public function getAction()
    {
        // TODO: Implement getAction() method.
        return null;
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

        // Verifica se foi preenchido informações de NFe e RPS, caso sim, a informação de NFe terá prioridade e da RPS
        // será apagada, a fim de manter a estrutura do XML correta
        if ( (! empty($this->numeroNfe) || ! empty($this->codigoVerificacao)  )
            &&   (! empty($this->serieRps)  || ! empty($this->numeroRps) )
        ){
            $this->numeroRps = null;
            $this->serieRps = null;
            $this->rpsInscricaoMunicipalPrestador = null;
        }else{
            if (! empty($this->serieRps) || ! (empty($this->numeroRps))){
                // se foi informado o RPS
                $this->rpsInscricaoMunicipalPrestador = $this->inscricaoMunicipalPrestador;
                $this->numeroNfe = null;
                $this->codigoVerificacao = null;
                $this->nfeInscricaoMunicipalPrestador = null;
            }else{
                $this->nfeInscricaoMunicipalPrestador = $this->inscricaoMunicipalPrestador;
                $this->numeroRps = null;
                $this->serieRps = null;
                $this->rpsInscricaoMunicipalPrestador = null;
            }

        }


        return str_replace('<?xml version="1.0"?>','', ParseTemplate::parse($this) );
    }


}