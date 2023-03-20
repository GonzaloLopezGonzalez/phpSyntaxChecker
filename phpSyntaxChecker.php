<?php

class phpSyntaxChecker
{
    private $systemFunctionIsenabled;
    private $filesWithSyntaxErrors;

    public function __construct()
    {

        $this->checkIfSyntaxCheckerCommandCanBeExecuted();
    }
    public function checkPhpFileSyntax(array $arrPhpFiles)
    {
        foreach ($arrPhpFiles as $file) {
            $result = 0;
            $comando = 'php -l ' . $file;
            system($comando, $result);
            $pos = strpos($result, 'No syntax errors detected in');
            if ($pos === false){
                return array($file,$result);
            }else{
                return array($file,'');
            }
        }
    }

    private function getPhpFiles(string $dir): array
    {
        return glob("$dir/*.php");
    }

    private function checkIfSyntaxCheckerCommandCanBeExecuted(): void
    {
        $disabledFunctions = explode(',', ini_get('disable_functions'));
        $this->systemFunctionIsenabled = (in_array('system', $disabledFunctions)) ? false : true;
    }

    public function analyzePhpFilesSyntax(string $ruta = __DIR__, bool $checkRoot = true)
    {
        $dh = opendir($ruta);
        $arrResult = array();
        if ($this->systemFunctionIsenabled) {
            while (($file = readdir($dh)) !== false) {
                $dir = $ruta.'/'.$file;
                if (is_dir($dir) && $file != '.' && $file != '..') {
                    $arrPhpFiles = $this->getPhpFiles($dir);
                    $arrResult = $this->checkPhpFileSyntax($arrPhpFiles);
                    unset($arrPhpFiles);
                    $this->analyzePhpFilesSyntax($dir, false);
                }
            }
            closedir($dh);

            if ($checkRoot) {
                $arrPhpFiles = $this->getPhpFiles(__DIR__);
                $arrResult = $this->checkPhpFileSyntax($arrPhpFiles);
            }
        } else {
            echo 'It is necessary to enable system function in php.ini';
        }
    }
}

$syntaxAnalyser = new phpSyntaxChecker();
$syntaxAnalyser->analyzePhpFilesSyntax();
