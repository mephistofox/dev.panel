<?php
    use jucksearm\barcode\Datamatrix;

    function genDatamatrix (){
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $code = $_GET['code'];
            return Datamatrix::factory()->setCode($code)->setSize(150)->renderPNG();
        }
    }

    genDatamatrix();