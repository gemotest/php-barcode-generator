<?php

namespace Picqer\Barcode;

class BarcodeGeneratorSVG extends BarcodeGenerator
{
    /**
     * Return a SVG string representation of barcode.
     *
     * @param $barcode_data (array) массив данных штрихкодов
     * @param $type (const) тип штрихкода
     * @param $totalHeight (int) высота штрихкода
     * @param $color (string) цвет.
     * @param $pLName (string) фамилия пациента.
     * @param $pFName (string) имя-отчество пациента.
     * @param $footer (string)
     * @return string SVG code.
     * @public
     */
    public function getBarcode(
        $barcode_data,
        $type,
        $totalHeight = 30,
        $color = 'black',
        $pLName = "",
        $pFName = "",
        $footer
    )
    {
        $repstr = array("\0" => '', '&' => '&amp;', '<' => '&lt;', '>' => '&gt;');

        $svg = '<html><head>';
        $svg .= '<link rel="stylesheet" href="/../../fonts/gemotest_icons/gemo-font.css" type="text/css"/>';
        $svg .= '<style>
            @media print { 
                @page {
                  padding: 0;
                  margin: 0;
                }
                div.break {page-break-after: always;}
            }
            body {
            margin-top:0;
            padding-top:0;
            }
            .txt {
                font-size:6pt; 
                font-family:Arial; 
                font-weight: bold;
            }
            .footer {
                font-size:5pt; 
                font-family:Arial; 
                font-weight: bold;
            }
            .new_page {
                position: relative;
                padding-top: 5pt;
            }
         </style>';
        $svg .= '</head><body>' . "\n";

        foreach($barcode_data as $barcode_element)
        {
            $barcodeData = $this->getBarcodeData($barcode_element['code'], $type);

            // определяем отступ слева в зависимости от количества символов в пиктограмме
            $x = strlen($barcode_element['icon']) > 1 ? 23 : 26;
            $icon = str_replace("/", "<span style='font-size:14pt;'>/</span>", $barcode_element['icon']);

            $svg .= '<div class="new_page">';
            $svg .= '<div style="position:absolute; left: ' . $x . 'mm; top: 1mm; font-family: gemotest_fontregular; font-size: 25pt">' . $icon . '</div>';
            $svg .= '<table cellspacing="0" cellpadding="0">';
            $svg .= '<tr><td class="txt">' . $pLName . '</td></tr>';
            $svg .= '<tr><td class="txt" style="padding-bottom: 2pt">' . $pFName . '</td></tr>';
            $svg .= '<tr><td style="padding-left:12pt">';
            $svg .= '<svg width="20mm" height="7.5mm" version="1.1" xmlns="http://www.w3.org/2000/svg"  viewBox="30 30 20 30">' . "\n";
            $svg .= "\t" . '<desc>' . strtr($barcodeData['code'], $repstr) . '</desc>' . "\n";
            $svg .= "\t" . '<g id="bars" fill="' . $color . '" stroke="none" vector-effect="non-scaling-stroke">' . "\n";
            $positionHorizontal = 0;

            foreach ($barcodeData['bars'] as $bar) {
                $barWidth = round(($bar['width'] - 0.01), 3);
                $barHeight = round(($bar['height'] * $totalHeight / $barcodeData['maxHeight']), 3);

                if ($bar['drawBar']) {
                    $positionVertical = round(($bar['positionVertical'] * $totalHeight / $barcodeData['maxHeight']), 3);
                    $svg .= "\t\t" . '<rect x="' . $positionHorizontal . '" y="' . $positionVertical . '" width="' . $barWidth . '" height="' . $barHeight . '" 
                stroke="white" 
                stroke-width="0" 
                vector-effect="non-scaling-stroke"/>' . "\n";
                }

                $positionHorizontal += $barWidth;
            }

            $svg .= "\t" . '</g>' . "\n";
            $svg .= '</svg>' . "\n";
            $svg .= '</td></tr>';
            $svg .= '<tr><td class="txt" style="padding-top: 2pt">' . $barcode_element['order_num'] . '</td></tr>';
            $svg .= '<tr><td class="txt">' . $barcode_element['serv_name'] . '</td></tr>';
            $svg .= '<tr><td class="footer" style="padding-top: 2pt"><nobr>' . $footer . '</nobr></td></tr>';
            $svg .= '</table>';
            $svg .= '</div><div class="break"></div>';
        }

        $svg .= '</body></html>';

        return $svg;
    }
}