<?php 
    ini_set('memory_limit', '4095M');
    
    if(isset($_POST["arr"])){

        $arr = $_POST["arr"];
        $fp = fopen('output.csv', 'w');

        fputcsv($fp, array(
            'Data',
            'Em_Investigacao',
            'Descartados',
            'Em_Isolamento_Domiciliar',
            'Recuperados',
            'Obitos_Descartados',
            'Quarentena_Domiciliar',
            'Quarentena_Cumprida',
            'Internados_Em_Investigacao',
            'Internados_Confirmados',
            'Obitos_Confirmados',
            'Positivo_Obito_Outra_Comorbidade',
            'Vacinados_primeira_dose',
            'Vacinados_segunda_dose'
        ));

        foreach($arr as $row){
            fputcsv($fp, $row);
        }
        fclose($fp);
        echo json_encode("success!");
    }
?>