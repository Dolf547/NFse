<?php

namespace Nfsews;


class ParseTemplate
{

    /**
     * Realiza o parse de uma Request ao seu respectivo template tendo como resultado o XML
     *
     * @param $request
     * @param array $preReplace (OPCIONAL) Indica que será feito um pre replace antes do replace final entre a Request e o template.
     * Utilize-o para configurar campos que tem valor indeterminados como é o caso do CPF/CNPJ
     * Ex: array(array('mark' => '{CpxCpfCnpjTomador}', 'value' => '<CNPJ>{cpfCnpjTomador}</CNPJ>'));
     * @return Request XML do Template com os campos substituídos pelos valores da Request
     * @throws \Exception
     */
    public static function parse($request, array $preReplace = []){
        // verifica se é um objeto
        if (! is_object($request)){
            throw new \Exception('$request não é um objeto do tipo Request');
        }

        $attributes = $request->getAllAttributes();

        if (! is_array($attributes))
            throw new \Exception('Ocorreu erro ao tentar verificar os atributos do objeto do tipo Request');

        if (! file_exists($request->getTemplatePath())){
            throw new \Exception('Path para o template da request não encontrado em: ' . $request->getTemplatePath());
        }
        // Load template and replace fields
        $template = file_get_contents($request->getTemplatePath());

        // realiza o pre-replace
        foreach ($preReplace as $config){
            $template = str_replace($config['mark'], $config['value'], $template);
        }

        foreach ($attributes as $key => $value){

            $fieldName = key($value);
            $fieldValue = $value[$fieldName];

            if (! empty($fieldValue) && ! is_array($fieldValue)){
                $template = str_replace('{'. $fieldName . '}', $fieldValue, $template);
            }
        }

        // Clear fields set empty to TAG
        foreach ($attributes as $key => $value){
            $fieldName = key($value);
            $template = str_replace('{'. $fieldName . '}', '', $template);
        }

        // Remove empty TAGs
        $xml = new \SimpleXMLElement($template);
        $xpath = '/*//*[
          normalize-space(.) = "" and
          not(
            @* or 
            .//*[@*] or 
            .//comment() or
            .//processing-instruction()
          )
        ]';
        foreach (array_reverse($xml->xpath($xpath)) as $remove) {
            unset($remove[0]);
        }
        // Normalize the XML removing line feeds tags
        //$template =   preg_replace('/(\>)\s*(\<)/m', '$1$2', $xml->asXml());
        // echo $template;
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML( $xml->asXml(), LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        return  $dom->saveXML();

    }


}