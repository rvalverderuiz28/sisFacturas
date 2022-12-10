<?php
if (!function_exists("generate_bar_code")) {
    /**
     * @param string|numeric $data
     * @param int $width
     * @param int $height
     * @param string $color
     * @param bool $exportUrlData
     * @param string $type
     * @return object|string
     * @throws \Com\Tecnick\Barcode\Exception
     */
    function generate_bar_code($data, int $width=-2, int $height=-100, string $color='black', bool $exportUrlData = true, string $type="C39")
    {
        $barcode = new \Com\Tecnick\Barcode\Barcode();

        if($width>0){
            $width=-$width;
        }
        if($height>0){
            $height=-$height;
        }

        $bobj = $barcode->getBarcodeObj(
            $type,            // Tipo de Barcode o Qr
            $data,    // Datos
            $width,            // Width
            $height,            // Height
            $color,        // Color del codigo
            array(0, 0, 0, 0)    // Padding
        );

        $data = $bobj->getPngData();
        if ($exportUrlData) {
            return "data:image/png;base64," . base64_encode($data);
        }
        return $data;
    }
}

if (!function_exists("generate_correlativo")) {
    function generate_correlativo($prefix,$next,$digit=4 )
    {
        return $prefix . str_pad($next, $digit, '0', STR_PAD_LEFT);
    }
}

if (!function_exists("money_f")) {
    function money_f($amount,$currency='PEN',$locale='es-PE')
    {
        $a = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        return $a->formatCurrency($amount, $currency);
    }
}
