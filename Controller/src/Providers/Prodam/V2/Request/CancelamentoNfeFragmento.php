<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 06/05/2019
 * Time: 20:42
 */

namespace Nfsews\Providers\Prodam\V2\Request;


use Nfsews\ParseTemplate;

class CancelamentoNfeFragmento //implements IRequest
{
    const SYS_DS = DIRECTORY_SEPARATOR;
    private $templatePath = null;
    private $inscricaoMunicipalPrestador = null;
    private $numeroNfe = null;
    private $assinaturaCancelamento = null;

    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'CancelamentoNfeFragmento.xml'  ;
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
    public function getAssinaturaCancelamento()
    {
        return $this->assinaturaCancelamento;
    }

    /**
     * @param null $assinaturaCancelamento
     */
    public function setAssinaturaCancelamento($assinaturaCancelamento)
    {
        $this->assinaturaCancelamento = $assinaturaCancelamento;
    }

    public function getAction()
    {
        // TODO: Implement getAction() method.
        return null;
    }


    /**
     * Retorna os atributos da classe
     *
     * @return array
     */
    public function getAllAttributes()
    {
        $array = [];

        foreach ($this as $key => $value) {
            if (property_exists($this, $key)) {
                array_push($array, array($key => $value));
            }
        }
        return $array;

    }

    public function toXml(){
        return str_replace('<?xml version="1.0"?>','',ParseTemplate::parse($this) );
    }


}