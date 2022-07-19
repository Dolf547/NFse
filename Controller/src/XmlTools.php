<?php

namespace Nfsews;


class XmlTools
{
    public static function saveXml($xml, $fileName, $directory){
        $return = false;
        if (! file_exists($directory) || ! is_dir($directory)){
            @mkdir($directory);
        }

        if (is_dir($directory) && ! empty($fileName)){
            if (@file_put_contents($directory . '/' . $fileName, $xml)){
                $return = true;
            }
        }

        return $return;

    }


    /**
     * @param $xml String xml a ser validada
     * @param $pathToXsd Path para o arquivo XSD
     * @return bool|array
     * @throws \Exception
     */
    public static function validateXml($xml, $pathToXsd){

        if (empty($xml))
            throw new \Exception('A string XML fornecida para validção contra o XSD não pode ser nula');

        if (! file_exists($pathToXsd))
            throw new \Exception('O path para o arquivo XSD não existe. Path informado: '. $pathToXsd);

        $dom = null;

        try{
            libxml_use_internal_errors(true);
            libxml_clear_errors();
            $dom = new \DOMDocument('1.0', 'utf-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = false;
            $dom->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
            libxml_clear_errors();
            $errors = [];
            if (! $dom->schemaValidate($pathToXsd)) {
                $libXmlErrors =   libxml_get_errors();
                foreach ($libXmlErrors as $error) {
                    array_push($errors, self::zTranslateError($error->message));
                }

                return $errors;
            }else
                return true;
        }catch (\Exception $e){
            echo $e->getTraceAsString();
            exit;
        }

    }


    /**
     * zTranslateError
     *
     * @param  string $msg
     * @return string
     */
    protected static function zTranslateError($msg)
    {
        $enErr = array(
            "{http://www.portalfiscal.inf.br/nfe}",
            "[facet 'pattern']",
            "The value",
            "is not accepted by the pattern",
            "has a length of",
            "[facet 'minLength']",
            "this underruns the allowed minimum length of",
            "[facet 'maxLength']",
            "this exceeds the allowed maximum length of",
            "Element",
            "attribute",
            "is not a valid value of the local atomic type",
            "is not a valid value of the atomic type",
            "Missing child element(s). Expected is",
            "The document has no document element",
            "[facet 'enumeration']",
            "one of",
            "failed to load external entity",
            "Failed to locate the main schema resource at",
            "This element is not expected. Expected is",
            "is not an element of the set"
        );
        $ptErr = array(
            "",
            "[Erro 'Layout']",
            "O valor",
            "não é aceito para o padrão.",
            "tem o tamanho",
            "[Erro 'Tam. Min']",
            "deve ter o tamanho mínimo de",
            "[Erro 'Tam. Max']",
            "Tamanho máximo permitido",
            "Elemento",
            "Atributo",
            "não é um valor válido",
            "não é um valor válido",
            "Elemento filho faltando. Era esperado",
            "Falta uma tag no documento",
            "[Erro 'Conteúdo']",
            "um de",
            "falha ao carregar entidade externa",
            "Falha ao tentar localizar o schema principal em",
            "Este elemento não é esperado. Esperado é",
            "não é um dos seguintes possiveis"
        );
        return str_replace($enErr, $ptErr, $msg);
    }
}