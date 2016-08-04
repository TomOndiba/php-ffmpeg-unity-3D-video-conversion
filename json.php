<?php 
/* Start XML to JSON code*/ 
class XmlToJsonConverter {
    public function ParseXML ($url) {
        $fileContents= file_get_contents($url);
        // Remove tabs, newline, whitespaces in the content array
        $fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
        $fileContents = trim(str_replace('"', "'", $fileContents));
        $myXml = simplexml_load_string($fileContents);
        $json = json_encode($myXml);
        return $json;
    }
}
//Path of the XML file
$url= 'data.xml';
 
//Create object of the class
$jsonObj = new XmlToJsonConverter();
 
//Pass the xml document to the class function
$myjson = $jsonObj->ParseXMl($url);
print_r ($myjson);
?> 