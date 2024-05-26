<?php

# This's a regular example

/**
 * Componente de prueba: si no tiene un contenido dice hello world 
 * 
 * @param array $main contiene valores que requiere el componente
 * 
 * @return string Retorna el html
 */
function HelloWorld(array $main = []): string
{

    $msg = $main["children"] ?: "Hello World";

    return <<<HTML
    <p>{$msg}</p>
    HTML;
}
