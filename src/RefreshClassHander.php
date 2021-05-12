<?php

namespace CodeGenerator;

use ReflectionClass;

class RefreshClassHander extends GenerateApiHandler
{
    private $filePath;
    public function handle()
    {
        $dir = "app/Dto/"; 
        correctPath($dir);
        $filePaths = rglob($dir.'*.php');
        foreach($filePaths as $path)
        {
            if(str_contains($path, $this->modelName))
            {
                $this->filePath = $path;
                $pathInfos = pathinfo($path);
                $className = str_replace("/","\\", ucfirst($pathInfos['dirname']."/".$pathInfos['filename']));
                $this->refreshClass($className);
                return;
            }
        }
        $this->command->info("invalid class name!");
    }

    protected function refreshClass(string $className){
        $class = new ReflectionClass($className);
        $properties = $class->getProperties();
        $content = fileGetContents($this->filePath);
        $strProperties = "";
        $separater = "properties = {\n";
        $swaggerContents = explode($separater,$content);
        foreach($properties as $property){
            $propertyType = $property->getType();
            $propertyName = $property->getName();
            if(in_array($propertyName,["data","ignoreMissing","exceptKeys","onlyKeys"]))
            {
                continue;
            }
            if(!is_null($propertyType))
            {
                $propertyClassName = pathinfo(str_replace("\\","/",$propertyType->getName()),PATHINFO_FILENAME);
                $strProperties = $strProperties." *                  @OA\Property(property=\"{$propertyName}\", type=\"object\", ref=\"#/components/schemas/{$propertyClassName}\"),\n";
            }
            else
            {
                $strProperties = $strProperties." *                  @OA\Property(property=\"{$propertyName}\", type=\"string\"),\n";
            }
        }
        $strProperties = $strProperties." *              }\n *         ),\n *     }\n * )\n */";
        $swaggerContents[1] = $strProperties;
        $content = join($separater,$swaggerContents);
        filePutContents($this->filePath, $content); 
    }
}
