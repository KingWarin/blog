<?php
    function getCaptcha($session) {
        $font = './hack.ttf';
        $image = imagecreate(145, 30);
        imagecolorallocate($image, 255, 255, 255);
        $left = 5;
        $signs = 'qwertzuiopasdfghjklyxcvbnm1234567890YXCVBNMASDFGHJKLQWERTZUIOP';
        $cap = '';
        for($i = 1; $i < 8; $i++) {
            $sign = $signs{rand(0, strlen($signs) - 1)};
            $string.=$sign;
            imagettftext(
                $image,
                20,
                rand(-10, 10),
                $left + (($i == 1?5:15) * $i),
                25,
                imagecolorallocate($image, 200, 200, 200),
                $font,
                $sign);
            imagettftext(
                $image,
                16,
                rand(-15, 15),
                $left + (($i == 1?5:15) * $i),
                25,
                imagecolorallocate($image, 69, 103, 137),
                $font,
                $sign);
        }
        $_SESSION[$session] = $string;

        header("Content-type: image/png");
        imagepng($image);
        imagedestroy($image);
    }
    if(isset($_GET['mode'])) {
        getCaptcha($_GET['mode'].'captcha');
    }
    exit();
?>
