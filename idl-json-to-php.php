<?php
require __DIR__ . '/vendor/autoload.php';

$idl = json_decode(file_get_contents($argv[1])); // todo: only a few things could go wrong here, right?

if (!$idl) {
    exit;
}

//print_r($idl);

const INDENT = '    ';

foreach ($idl as $entity) {
    switch ($entity->type) {
        case 'enum': processEnum($entity); break;
        case 'struct': processStruct($entity); break;
        case 'interface': processInterface($entity); break;
        default: break; // do nothing
    }
}

function processEnum($entity) {
    $codeStr = "<?php\n\n";
    $comment = trim($entity->comment);
    if (!empty($comment)) {
        $codeStr .= "/**\n * " . implode(' * ', explode("\n", $comment)) . "\n */\n";
    }
    $codeStr .= "class " . $entity->name . " {\n";
    foreach ($entity->values as $evp) {
        $comment = trim($evp->comment);
        if (!empty($comment)) {
            $codeStr .= INDENT . "/**\n" . INDENT . " * " . implode("\n" . INDENT . ' * ', explode("\n", $comment)) . "\n" . INDENT . " */\n";
        }
        $codeStr .= INDENT . "const ". $evp->value ." = '". $evp->value ."';\n";
    }
    $codeStr .= "}\n";
//    echo $codeStr;
    $filename = __DIR__ . '/generated/enum/' . $entity->name . '.php';
    echo "Generated: $filename\n";
    file_put_contents($filename, $codeStr);
}

function processStruct($entity) {
    $codeStr = "<?php\n\n";
    $comment = trim($entity->comment);
    if (!empty($comment)) {
        $codeStr .= "/**\n * " . implode(' * ', explode("\n", $comment)) . "\n */\n";
    }
    $codeStr .= "class " . $entity->name . ($entity->extends ? ' extends ' . $entity->extends : '') . " {\n\n";
    foreach ($entity->fields as $field) {
        $comment = trim($field->comment);
        if (!empty($comment)) {
            $comment = ' ' . trim(str_replace(array("\r\n", "\r", "\n"), ' ', $comment));
        }
        $codeStr .= INDENT . "/** @var " . ($field->optional ? 'null|' : '') . $field->type . ($field->is_array ? '[]' : '') . $comment . " */\n";
        $codeStr .= INDENT . "public $" . $field->name . ";\n\n";
    }
    $codeStr .= "}\n";
//    echo $codeStr;
    $filename = __DIR__ . '/generated/struct/' . $entity->name . '.php';
    echo "Generated: $filename\n";
    file_put_contents($filename, $codeStr);
}

function processInterface($entity) {
    $codeStr = "<?php\n\n";
    $comment = trim($entity->comment);
    if (!empty($comment)) {
        $codeStr .= "/**\n * " . implode(' * ', explode("\n", $comment)) . "\n */\n";
    }
    $codeStr .= "class " . $entity->name . " {\n\n";
    foreach ($entity->functions as $function) {
        $comment = trim($function->comment);

        // @param
        $params = '';
        foreach ($function->params as $param) {
            $params .= "@var " . $param->type . ($param->is_array ? '[]' : '') . ' $' . $param->name . "\n";
        }
        if (!empty($params)) {
            $comment .= "\n\n" . $params . "\n";
        }

        // @return
        $comment .= "@return " . ($function->returns->optional ? 'null|' : '') . $function->returns->type . ($function->returns->is_array ? '[]' : '') . "\n";

        // docblock
        if (!empty($comment)) {
            $codeStr .= INDENT . "/**\n" . INDENT . " * " . implode("\n" . INDENT . ' * ', explode("\n", trim($comment))) . "\n" . INDENT . " */\n";
        }

        // function
        $codeStr .= INDENT . "public function " . $function->name . "(";
        foreach ($function->params as $param) {
            if ($param->is_array) {
                $codeStr .= 'array ';
            }
            elseif (!in_array($param->type, array('string', 'int', 'float', 'bool'))) {
                // type checking works for classes but not basic types :(
                $codeStr .= $param->type . ' ';
            }
            $codeStr .= "$" . $param->name . ", ";
        }
        // clear last param
        if (count($function->params)) {
            $codeStr = substr($codeStr, 0, -2);
        }
        $codeStr .= ") {}\n\n";
    }
    $codeStr .= "}\n";
//    echo $codeStr;
    $filename = __DIR__ . '/generated/service/' . $entity->name . '.php';
    echo "Generated: $filename\n";
    file_put_contents($filename, $codeStr);
}

function reverseArr($type) {
    if (substr($type, 0, 2) == '[]') {
        $type = substr($type, 2) . '[]';
    }
    return $type;
}