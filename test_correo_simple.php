<?php
// test_correo_simple.php

$destinatario = "samitoskater22@gmail.com"; // << ¡CAMBIA ESTO POR TU EMAIL REAL!
$asunto = "Prueba de correo desde RollXpress (Método Simple)";
$cuerpo = "¡Hola! Si estás leyendo esto, significa que la función mail() de PHP en tu hosting está funcionando.";
$cabeceras = "From: web@rollxpress.cl"; // Un remitente genérico de tu sitio

echo "Intentando enviar correo de prueba a: " . $destinatario . "<br>";

if (mail($destinatario, $asunto, $cuerpo, $cabeceras)) {
    echo "¡Éxito! El correo de prueba fue enviado. Revisa tu bandeja de entrada y la carpeta de SPAM.";
} else {
    echo "Error: El correo de prueba no pudo ser enviado. Es probable que esta función esté deshabilitada en tu hosting.";
}
?>