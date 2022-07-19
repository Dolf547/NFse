<?php

namespace Nfsews\Certificate;

define('SYS_DS', DIRECTORY_SEPARATOR);

use mysql_xdevapi\Exception;
use NFePHP\Common\Certificate as CertificateBase;
use NFePHP\Common\Certificate\CertificationChain as CertificateChainBase;
use Nfsews\Config;

class Certificate
{
    const SYS_DS = DIRECTORY_SEPARATOR;
    private $certificate = null;
    private $certsTmpDir = null;
    /**
     * Certificate constructor.
     */
    public function __construct(Config $config)
    {

        if (! is_object($config) || get_class($config) !=  'Nfsews\Config' ){
            throw  new \Exception('O parâmetro passado para o construtor precisa ser do tipo: '. 'Phpnfsews\Config');
        }


        if (! file_exists($config->getPfxCert())){
            throw new \Exception('O caminho para o PFX não existe em: '. $config->getPfxCert()  );
        }

        if (! is_dir($config->getTmpDirectory())){
            throw new \Exception('O diretório tmp não foi definido');
        }

        if (! is_dir($config->getTmpDirectory() . SYS_DS . 'certs')){
            mkdir($config->getTmpDirectory() . SYS_DS . 'certs');
        }


        $pfx = file_get_contents($config->getPfxCert());
        $cert = CertificateBase::readPfx($pfx, $config->getPasswordCert());
        $this->certificate = $cert;

        $tmpCertsDir = $config->getTmpDirectory() .self::SYS_DS . 'certs' ;

        try{
            if (! file_exists($tmpCertsDir))
                mkdir($tmpCertsDir);
        }catch (\Exception $e){
            throw new Exception('Não foi possível criar o diretorio temporario dos certificados em: '.
                $tmpCertsDir . '\r\n '. $e->getMessage());
        }

        $this->certsTmpDir = $tmpCertsDir;


    }

    /**
     * @return null
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    public function getCertsTmpDir(){
        return $this->certsTmpDir;
    }


    public function getPriKeyPem(){
        return $this->certificate->privateKey->__toString();
    }

    public function getPubKeyPem(){
        return $this->certificate->publicKey->__toString();
    }

    public function getPubKeyClean(){
        return $this->certificate->publicKey->unFormated();
    }

    public function createPriPemFile(){
        $priKeyPath = $this->getCertsTmpDir() . SYS_DS . uniqid() . '_priKey.pem';

        try{
            file_put_contents($priKeyPath, $this->certificate->privateKey->__toString()  );
        }catch (\Exception $e){
            echo 'Não foi possível gerar o arquivos PEM da chave privada\n' . $e->getMessage();
        }
        return $priKeyPath;
    }

    public function createPubPemFile(){
        $pubKeyPath = $this->getCertsTmpDir() . SYS_DS . uniqid() . '_pubKey.pem';
        try{
            file_put_contents($pubKeyPath, $this->certificate->publicKey->__toString()  );
        }catch (\Exception $e){
            echo 'Não foi possível gerar o arquivos PEM da chave publica\n' . $e->getMessage();
        }

        return $pubKeyPath;

    }

    public function deletePemFile($path){

        if (is_array($path)){
            foreach ($path as $p){
                if ( file_exists($p))
                    @unlink($p);
            }
        }else{
            if(is_string($path)){
                if ( file_exists($path))
                    @unlink($path);
            }
        }



    }





}