<?php
require __DIR__ . '/vendor/autoload.php';

// stupid basic auto loader to catch any dependencies
spl_autoload_register(function($class) {
    $ittr = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/code')), '/.php$/i');
    foreach($ittr as $item) {
        /** @var SplFileInfo $item */
        if ($item->getBasename('.php') == $class) {
            require_once $item->getPathname();
        }
    }
});

// PHP classes to IDL

const INDENT = '    ';

$IDL = '';

function getClassesIn($directory) {
    $ittr = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)), '/.php$/i');
    $classes = [];
    foreach($ittr as $item) {
        /** @var SplFileInfo $item */
        $classes[] = $item->getBasename('.php');
    }
    return $classes;
}

// Enums:
$enumClasses = getClassesIn(__DIR__ . '/code/enum');
foreach ($enumClasses as $classname) {
    $reflectionClass = new ReflectionClass($classname);
    $consts = $reflectionClass->getConstants();
    $IDL .= 'enum ' . $reflectionClass->getName() . ' {
    ' . implode("\n" . INDENT, $consts) . '
}

';
}

// Structs:
$structClasses = getClassesIn(__DIR__ . '/code/struct');
foreach ($structClasses as $classname) {
    $reflectionClass = new ReflectionClass($classname);
    $IDL .= 'struct ' . $reflectionClass->getName();
    $parent = $reflectionClass->getParentClass();
    if ($parent) {
        $IDL .= ' extends ' . $parent->getName();
    }
    $IDL .= " {\n";
    $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
    foreach ($properties as $property) {
        if ($property->getDeclaringClass()->getName() != $reflectionClass->getName()) {
            continue;
        }
        $phpdoc = new \phpDocumentor\Reflection\DocBlock($property);
        /** @var phpDocumentor\Reflection\DocBlock\Tag\VarTag[] $vartag */
        $vartag = $phpdoc->getTagsByName('var');
        // todo: detect bad/missing docblocks
        $type = count($vartag) ? $vartag[0]->getType() : 'UNKNOWN';
        $IDL .= INDENT . $property->getName() . '  ' . processType($type) . "\n";
    }
    $IDL .= "}\n\n";
}

// Interfaces (read: services):
$interfaceClasses = getClassesIn(__DIR__ . '/code/service');
foreach ($interfaceClasses as $classname) {
    $reflectionClass = new ReflectionClass($classname);
    $cphpdoc = new \phpDocumentor\Reflection\DocBlock($reflectionClass);
    $cdesc = trim($cphpdoc->getShortDescription() . "\n\n" . $cphpdoc->getLongDescription());
    if (!empty($cdesc)) {
        $cdesc = "// " . implode("\n// ", explode("\n", $cdesc)) . "\n";
    }
    $IDL .= $cdesc;
    $IDL .= 'interface ' . $reflectionClass->getName() . " {\n\n";

    foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {

        $phpdoc = new \phpDocumentor\Reflection\DocBlock($method);
        $desc = trim($phpdoc->getShortDescription() . "\n\n" . $phpdoc->getLongDescription());
        if (!empty($desc)) {
            $desc =  INDENT . "// " . implode("\n" . INDENT . "// ", explode("\n", $desc)) . "\n";
        }

        $params = [];
        $reflectionParams = $method->getParameters();
        $docBlockParams = $phpdoc->getTagsByName('param');
        foreach ($reflectionParams as $reflectionParam) {
            $codeParamName = $reflectionParam->getName();
            $ptype = "string"; // default...
//            $pdesc = ""; // default...
//            $optional = $reflectionParam->isOptional(); // todo: detect and throw exception -- optional things must be in structs
            foreach ($docBlockParams as $docBlockParam) {
                /** @var phpDocumentor\Reflection\DocBlock\Tag\ParamTag $docBlockParam */
                if ('$'.$codeParamName == $docBlockParam->getVariableName()) {
                    $ptype = $docBlockParam->getType();
//                    $pdesc = $docBlockParam->getDescription();
                    break;
                }
            }
            $params[] .=  $codeParamName . ' ' . processType($ptype);
//            if (!empty($pdesc)) {
//                $desc .= '// ' . $codeParamName . ' ' . $pdesc . "\n";
//            }
        }

        /** @var phpDocumentor\Reflection\DocBlock\Tag\ReturnTag $returnTag */
        $returnTag = $phpdoc->getTagsByName('return')[0];
        $returnDesc = $returnTag->getDescription();
        if (!empty($returnDesc)) {
            $desc .= INDENT . "//\n" . INDENT . "// Returns " . $returnDesc . "\n";
        }

        $IDL .= $desc;
        $IDL .= INDENT . $method->getName() . '(' . implode(', ', $params). ') ' . processType($returnTag->getType()) . "\n\n";

    }

    $IDL .= "}\n\n";
}


function processType($type) {
    $type = str_replace('\\', '', $type);
    $optional = false;
    if (substr($type, 0, 5) == 'null|') {
        $type = substr($type, 5);
        $optional = true;
    }
    if (substr($type, -2) == '[]') {
        $type = '[]' . substr($type, 0, -2);
    }
    elseif (substr($type, 0, 2) == '[]') {
        $type = substr($type, 2) . '[]';
    }
    if ($optional) {
        $type = $type . ' [optional]';
    }
    return $type;
}




echo $IDL;
exit;
//////////////////