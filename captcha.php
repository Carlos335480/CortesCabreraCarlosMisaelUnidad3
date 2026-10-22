<?php
session_start();

$captcha_text = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 6);
$_SESSION['captcha_text'] = $captcha_text;

$imagen = imagecreatetruecolor(180, 60);

$fondo = imagecolorallocate($imagen, 0, 0, 0);
$linea = imagecolorallocate($imagen, 100, 100, 100); 

imagefilledrectangle($imagen, 0, 0, 180, 60, $fondo);

for ($i = 0; $i < 40; $i++) {
    imageline($imagen, rand(0,180), rand(0,60), rand(0,180), rand(0,60), $linea);
}

$fuentes = [
    __DIR__ . "/fonts/PlayfairDisplay-Regular.ttf",
    __DIR__ . "/fonts/PlayfairDisplay-Bold.ttf",
    __DIR__ . "/fonts/PlayfairDisplay-Italic.ttf",
    __DIR__ . "/fonts/PlayfairDisplay-SemiBold.ttf"
];


for ($i = 0; $i < strlen($captcha_text); $i++) {
    $fuente = $fuentes[array_rand($fuentes)];          
    $angulo = rand(-25, 25);                           
    $x = 20 + ($i * 25);                               
    $y = rand(35, 50);                                 
    $color_letra = imagecolorallocate(
        $imagen,
        rand(150,255),
        rand(150,255), 
        rand(150,255)  
    );
    imagettftext($imagen, 22, $angulo, $x, $y, $color_letra, $fuente, $captcha_text[$i]);
}

header("Content-type: image/png");
imagepng($imagen);
imagedestroy($imagen);
