<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/vendor/fontawesome-5.15.3/css/all.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="./assets/style.css">
    <style>

        .tendencia-alta,
        .tendencia-estabilidade,
        .tendencia-queda{
            margin-left: 15px;
        }
        .tendencia-alta, .tendencia-alta *{
            background-color: #8fbc94!important;
            border-radius: 30px;
            color: #FFF!important;
        }

        .tendencia-queda, .tendencia-queda *{
            background-color: #548c2f!important;
            border-radius: 30px;
            color: #FFF!important;
        }

        .tendencia-estabilidade, .tendencia-estabilidade *{
            background-color: #054a91!important;
            border-radius: 30px;
            color: #FFF!important;
        }

        #tendencia{
            display: flex;
            align-items: center;
            font-size: 3rem;
            justify-content: center;
        }

        .p10{
            padding: 10px;
        }
    </style>
    <title>Painel Covid-19 Timóteo - MG</title>
</head>
<body>
    <?php

        function parse_csv ($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true)
        {
            $enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string);
            $enc = preg_replace_callback(
                '/"(.*?)"/s',
                function ($field) {
                    return urlencode(utf8_encode($field[1]));
                },
                $enc
            );
            $lines = preg_split($skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s', $enc);
            return array_map(
                function ($line) use ($delimiter, $trim_fields) {
                    $fields = $trim_fields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line);
                    return array_map(
                        function ($field) {
                            return str_replace('!!Q!!', '"', utf8_decode(urldecode($field)));
                        },
                        $fields
                    );
                },
                $lines
            );
        }
        
        $data = parse_csv(
            // file_get_contents("https://raw.githubusercontent.com/andre-luiz1997/covid_timoteo/master/covid_timoteo_generated.csv"),
            file_get_contents("https://raw.githubusercontent.com/andre-luiz1997/covid_timoteo/master/covid_timoteo_data.csv"),
        );
    ?>
    <nav class="nav">
        <div class="nav-logo"><img src="./assets/img/logo-timoteo.jpg" alt=""></div>
        <h5 class="nav-title">Painel de Monitoramento Covid - 19</h5>
    </nav>
    <div class="container">
        <div class="row" style="margin-top: 50px;">
            <div class="col-sm-12">
                <div class="card">
                    <h5 class="card_header">Dados Última Atualização</h5>
                    <h5 class="dados_ultima">Última atualização em: <span id="data_ultima"></span></h5>
                    <div class="row">
                        <div class="col-sm-3 p10">
                            <span>Variação nos casos</span>
                            <div class="col_info" id="tendencia"><i class="fas fa-spinner fa-pulse"></i></div>
                        </div>
                        <div class="col-sm-3">
                            <div class="col_info" id="casos_ativos"><i class="fas fa-spinner fa-pulse"></i></div>
                            <span>casos ativos</span>
                        </div>
                        <div class="col-sm-3">
                            <div class="col_info" id="casos_novos_24_horas"><i class="fas fa-spinner fa-pulse"></i></div>
                            <span>novos casos em 24 horas</span>
                        </div>
                        <div class="col-sm-3">
                            <div class="col_info" id="total_recuperados"><i class="fas fa-spinner fa-pulse"></i></div>
                            <span>recuperados</span>
                        </div>
                        <div class="col-sm-3">
                            <div class="col_info" id="total_obitos"><i class="fas fa-spinner fa-pulse"></i></div>
                            <span>total de óbitos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card" >
                    <h5 class="card_header">Casos Ativos e Óbitos</h5>
                    <div id="chart_div">
                    
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <h5 class="card_header">Primeira Dose</h5>
                    <div id="chart_vacinas">

                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <h5 class="card_header">Segunda Dose</h5>
                    <div id="chart_vacinas_segunda">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <div class="col-sm-6" style="flex-flow: column;">
            <span>Este site não corresponde a um domínio oficial da administração pública de Timóteo - MG.</span>
            <p class="text-center">As informações aqui apresentadas foram retiradas do <a href="https://www.timoteo.mg.gov.br/161/covid">Site da Prefeitura</a> 
            e do <a href="https://coronavirus.saude.mg.gov.br/painel">painel da 
            Secretaria de Estado de Saúde de Minas Gerais</a> <br>
            e são utilizadas apenas para fins de estudo </p>
            <span style="font-size: 0.9rem;margin-top: 20px;"><a href="mailto:andreluizsilveir@gmail.com"> 2021, André Luiz Silveira Lucas.</a></span>
        </div>
    </div>

    <script src="./assets/vendor/amcharts_4.10.18/amcharts4/core.js"></script>
    <script src="./assets/vendor/amcharts_4.10.18/amcharts4/charts.js"></script>
    <script src="./assets/vendor/amcharts_4.10.18/amcharts4/themes/animated.js"></script>
    <script src="./assets/vendor/jquery-3.6.0/jquery-3.6.0.min.js"></script>
    <script src="//www.amcharts.com/lib/4/lang/pt_BR.js"></script>
    <script>
        var dados = JSON.parse('<?= json_encode($data) ?>');
        var pop_total_timoteo = 90568; //https://cidades.ibge.gov.br/brasil/mg/timoteo/panorama
        var iterations = true;
        $(document).ready(function(){
            
            prepareDados(dados);
        });



        function prepareDados(dados){

            var medias_moveis = [];
            var vacinas_hoje_prim = parseFloat(dados[1][12]);
            
            var vacinas_hoje_seg = parseFloat(dados[1][13]);
            dados.shift();
            for (let index = 0; index < dados.length; index++) { //SKIP FIRST LINE (HEADERS)
                const row = dados[index];
                let data = convertDate(row[0]);
                let casos = parseFloat(row[3]);
                let total_confirmados = parseInt(row[3]) + parseInt(row[4]) + parseInt(row[9]);
                let recuperados = row[4];
                let internados = row[9];
                let obitos = parseFloat(row[10]);
                let vacinados_prim = row[12];
                let vacinados_seg = row[13];
                if(index == 0){ //ÚLTIMO DIA INSERIDO
                    let total_ontem = parseInt(dados[2][3]) + parseInt(dados[2][4]) + parseInt(dados[2][9])
                    let casos_24_horas = total_confirmados - total_ontem;
                    casos_24_horas > 0 ? casos_24_horas : 0;
                    setTimeout(() => {
                        $("#data_ultima").html(row[0]);
                        $("#casos_ativos").html(casos);
                        $("#casos_novos_24_horas").html(casos_24_horas > 0 ? casos_24_horas : 0);
                        $("#total_recuperados").html(recuperados);
                        $("#total_obitos").html(obitos);
                    }, 1000);
                   
                }

                let media_movel_casos = 0;
                let media_movel_obitos = 0;
                let media_movel_vacinas_prim = 0;
                let media_movel_vacinas_seg = 0;
                let novos_casos = casos;
                let tendencia = 0;
                if(index < dados.length - 14){ // A TENDÊNCIA É CALCULADA COM BASE NO INTERVALO DE 14 DIAS. (VARIAÇÃO PERCENTUAL)
                    tendencia = variacao_percentual(dados[index + 14][3], casos);
                    if(index == 0){
                        setTimeout(() => {
                            if(tendencia > 15){
                                //ALTA
                                $("#tendencia").html("<span>"+Math.floor(tendencia)+" %"+"</span>");
                                $("#tendencia").parent().addClass("tendencia-alta");
                                $("#tendencia").parent().append("<span>tendência de alta</span>");
                            }else if(tendencia < -15){
                                //QUEDA
                                $("#tendencia").html("<span>"+Math.floor(tendencia)+" %"+"</span>");
                                $("#tendencia").parent().addClass("tendencia-queda");
                                $("#tendencia").parent().append("<span>tendência de queda</span>");
                            }else{
                                //ESTABILIDADE
                                $("#tendencia").html("<span>"+Math.floor(tendencia)+" %"+"</span>");
                                $("#tendencia").parent().addClass("tendencia-estabilidade");
                                $("#tendencia").parent().append("<span>estabilidade</span>");
                            }
                        }, 1000);
                       
                    }
                }

                if(index < dados.length - 6){ //OS 5 PRIMEIROS DIAS
                    media_movel_casos = (
                        casos +
                        parseFloat(dados[index + 1][3]) + 
                        parseFloat(dados[index + 2][3]) + 
                        parseFloat(dados[index + 3][3]) + 
                        parseFloat(dados[index + 4][3]) +                        
                        parseFloat(dados[index + 5][3]) + 
                        parseFloat(dados[index + 6][3]) 
                    ) / 7; 
                    media_movel_obitos = (
                        obitos + 
                        parseFloat(dados[index + 1][9]) + 
                        parseFloat(dados[index + 2][9]) + 
                        parseFloat(dados[index + 3][9]) + 
                        parseFloat(dados[index + 4][9]) + 
                        parseFloat(dados[index + 5][9]) +
                        parseFloat(dados[index + 6][9])
                    ) / 7; 
                    media_movel_vacinas_prim = (
                        vacinados_prim + 
                        dados[index + 1][12] + 
                        dados[index + 2][12] + 
                        dados[index + 3][12] + 
                        dados[index + 4][12] +
                        dados[index + 5][12] +
                        dados[index + 6][12]
                    ) / 7;
                    media_movel_vacinas_seg= (
                        vacinados_seg + 
                        dados[index + 1][13] + 
                        dados[index + 2][13] + 
                        dados[index + 3][13] + 
                        dados[index + 4][13] +
                        dados[index + 5][13] +
                        dados[index + 6][13]
                    ) / 7;
                }

                if(index > 2 && index+1 < dados.length - 1){
                    novos_casos = casos - parseFloat(dados[index + 1][3]);
                }

                medias_moveis.push({
                    "date": data,
                    "format_date": data.split("-")[2]+"/"+data.split("-")[1]+"/"+data.split("-")[0],
                    "casos": casos,
                    "media_movel_casos": media_movel_casos,
                    "novos_casos": novos_casos,
                    "obitos": obitos,
                    "vacinados_prim": vacinados_prim,
                    "vacinados_seg": vacinados_seg,
                    "total_confirmados": total_confirmados,
                    "media_movel_obitos": media_movel_obitos,
                    "media_movel_vacinas_prim": media_movel_vacinas_prim,
                    "media_movel_vacinas_seg": media_movel_vacinas_seg,
                    "tendencia": tendencia
                });
            }
            
            am4core.ready(function() {
                // Themes begin
                am4core.useTheme(am4themes_animated);
                // Themes end

                // Create chart instance
                var chart = am4core.create("chart_div", am4charts.XYChart);
                chart.colors.step = 1;

                vacinasChart(medias_moveis,vacinas_hoje_prim);
                vacinasChartSegunda(medias_moveis,vacinas_hoje_seg);

                chart.data = medias_moveis;
                chart.dateFormatter.inputDateFormat = "yyyy-MM-dd";
                chart.language.locale = am4lang_pt_BR;

                // Create axes
                var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
                dateAxis.baseInterval = {
                    "timeUnit": "day",
                    "gridIntervals": 1
                } 
                dateAxis.renderer.minGridDistance = 50;
                dateAxis.keepSelection = true;
                var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

                // SÉRIE COM CASOS ATIVOS ---------------------------
                var series = chart.series.push(new am4charts.LineSeries());
                let segment = series.segments.template;
                segment.interactionsEnabled = true;
                let hs = segment.states.create("hover");
                hs.properties.strokeWidth = 5;
                series.tooltipText = "({format_date}) {casos} casos ativos";
                series.dataFields.valueY = "casos";
                series.dataFields.dateX = "date";
                series.strokeWidth = 2;
                series.stroke = am4core.color("#230E70");
                series.minBulletDistance = 1;
                series.name = "Casos ativos";
              

                // SÉRIE COM TOTAL ÓBITOS ---------------------------
                var series_obitos = chart.series.push(new am4charts.LineSeries());
                series_obitos.tooltipText = "({format_date}) {obitos} óbitos";
                series_obitos.dataFields.valueY = "obitos";
                series_obitos.dataFields.dateX = "date";
                series_obitos.strokeWidth = 2;
                series_obitos.stroke = am4core.color("#C21E18");
                series_obitos.minBulletDistance = 1;
                series_obitos.name = "Óbitos";

                // SÉRIE COM MÉDIA MÓVEL ÓBITOS ---------------------------
                var series_obitos = chart.series.push(new am4charts.LineSeries());
                series_obitos.tooltipText = "({format_date}) {media_movel_obitos} óbitos (média)";
                series_obitos.dataFields.valueY = "media_movel_obitos";
                series_obitos.dataFields.dateX = "date";
                series_obitos.strokeWidth = 2;
                series_obitos.stroke = am4core.color("#09e85e");
                series_obitos.minBulletDistance = 1;
                series_obitos.name = "Média Móvel Óbitos";

                //SÉRIE COM TOTAL DE CASOS ---------------------------
                // var series_total_casos = chart.series.push(new am4charts.LineSeries());
                // series_total_casos.tooltipText = "({format_date}) {total_confirmados} casos no total";
                // series_total_casos.dataFields.valueY = "total_confirmados";
                // series_total_casos.dataFields.dateX = "date";
                // series_total_casos.strokeWidth = 2;
                // series_total_casos.stroke = am4core.color("#FF7F00");
                // series_total_casos.minBulletDistance = 15;
                // series_total_casos.name = "Casos Confirmados";

                //SÉRIE COM VACINADOS PRIMEIRA DOSE ---------------------------
                // var series_total_primeira_dose = chart.series.push(new am4charts.LineSeries());
                // series_total_primeira_dose.tooltipText = "({format_date}) {vacinados_prim} vacinados primeira dose";
                // series_total_primeira_dose.dataFields.valueY = "vacinados_prim";
                // series_total_primeira_dose.dataFields.dateX = "date";
                // series_total_primeira_dose.strokeWidth = 2;
                // series_total_primeira_dose.stroke = am4core.color("#3AB795");
                // series_total_primeira_dose.minBulletDistance = 15;
                // series_total_primeira_dose.name = "Vacinados Primeira Dose";

                //SÉRIE COM VACINADOS SEGUNDA DOSE ---------------------------
                // var series_total_primeira_dose = chart.series.push(new am4charts.LineSeries());
                // series_total_primeira_dose.tooltipText = "({format_date}) {vacinados_seg} vacinados primeira dose";
                // series_total_primeira_dose.dataFields.valueY = "vacinados_seg";
                // series_total_primeira_dose.dataFields.dateX = "date";
                // series_total_primeira_dose.strokeWidth = 2;
                // series_total_primeira_dose.stroke = am4core.color("#D2BF55");
                // series_total_primeira_dose.minBulletDistance = 15;
                // series_total_primeira_dose.name = "Vacinados Segunda Dose";

                
                chart.scrollbarX = new am4charts.XYChartScrollbar();
                chart.scrollbarX.series.push(series);
                chart.scrollbarX.series.push(series_obitos);
                chart.scrollbarX.parent = chart.topAxesContainer;
                chart.legend = new am4charts.Legend();
                chart.legend.scrollable = true;  
                chart.cursor = new am4charts.XYCursor();
                chart.cursor.maxTooltipDistance = -1;
                chart.cursor.lineY.disabled = true;
                chart.cursor.lineX.disabled = true;
            });
        }

        function convertDate(br_date){
            let date = br_date.split("/");
            return date[2] + "-" + date[1] + "-" + date[0];
        }
        
        function vacinasChart(medias_moveis,vacinas_hoje_prim){
            
            var chart_vacinas = am4core.create("chart_vacinas", am4charts.PieChart);
            chart_vacinas.data = [
                {
                    "title": "Primeira Dose",
                    "vacinas_hoje": (vacinas_hoje_prim / pop_total_timoteo) * 100
                },
                {
                    "title": "Restante",
                    "vacinas_hoje": 100 - ((vacinas_hoje_prim / pop_total_timoteo) * 100),
                    "labelDisabled":true,
                    "disabled": true,
                    "color": am4core.color("#8c8c8c"),
                    "opacity": 0.3,
                    "tooltip": ""
                }
            ];
            
            chart_vacinas.language.locale = am4lang_pt_BR;
            chart_vacinas.innerRadius = am4core.percent(50);

            var pieSeries = chart_vacinas.series.push(new am4charts.PieSeries());
            var slice = pieSeries.slices.template;

            pieSeries.dataFields.value = "vacinas_hoje";
            pieSeries.dataFields.category = "title";
            pieSeries.slices.template.fill = am4core.color("#230E70");
            pieSeries.slices.template.stroke = am4core.color("#230E70");
            pieSeries.slices.template.strokeWidth = 2;
            pieSeries.slices.template.strokeOpacity = 0;

            pieSeries.ticks.template.propertyFields.disabled = "disabled";
            pieSeries.labels.template.propertyFields.disabled = "disabled";

            
            slice.propertyFields.fill = "color";
            slice.propertyFields.fillOpacity = "opacity";
            slice.propertyFields.stroke = "color";
            slice.propertyFields.strokeDasharray = "strokeDasharray";
            slice.propertyFields.tooltipText = "tooltip";

            pieSeries.slices.template.states.getKey("hover").properties.shiftRadius = 0;
            pieSeries.slices.template.states.getKey("hover").properties.scale = 1;

            var label = pieSeries.createChild(am4core.Label);
            label.text = vacinas_hoje_prim+"\n vacinas";
            label.horizontalCenter = "middle";
            label.verticalCenter = "middle";
            label.fontSize = 20;

            // This creates initial animation
            pieSeries.hiddenState.properties.opacity = 1;
            pieSeries.hiddenState.properties.endAngle = -90;
            pieSeries.hiddenState.properties.startAngle = -90;

        }

        function vacinasChartSegunda(medias_moveis,vacinas_hoje_seg){
            
            var chart_vacinas_segunda = am4core.create("chart_vacinas_segunda", am4charts.PieChart);

            chart_vacinas_segunda.data = [
                {
                    "title": "Segunda Dose",
                    "vacinas_hoje": (vacinas_hoje_seg / pop_total_timoteo) * 100
                },
                {
                    "title": "Restante",
                    "vacinas_hoje": 100 - ((vacinas_hoje_seg / pop_total_timoteo) * 100),
                    "labelDisabled":true,
                    "disabled": true,
                    "color": am4core.color("#8c8c8c"),
                    "opacity": 0.3,
                    "tooltip": ""
                }
            ];
            
            chart_vacinas_segunda.language.locale = am4lang_pt_BR;
            chart_vacinas_segunda.innerRadius = am4core.percent(50);

            var pieSeries = chart_vacinas_segunda.series.push(new am4charts.PieSeries());
            var slice = pieSeries.slices.template;

            pieSeries.dataFields.value = "vacinas_hoje";
            pieSeries.dataFields.category = "title";
            pieSeries.slices.template.fill = am4core.color("#230E70");
            pieSeries.slices.template.stroke = am4core.color("#230E70");
            pieSeries.slices.template.strokeWidth = 2;
            pieSeries.slices.template.strokeOpacity = 0;
            // pieSeries.slices.template.propertyFields.disabled = "labelDisabled";
            // pieSeries.labels.template.propertyFields.disabled = "labelDisabled";
            // pieSeries.ticks.template.propertyFields.disabled = "labelDisabled";

            pieSeries.ticks.template.propertyFields.disabled = "disabled";
            pieSeries.labels.template.propertyFields.disabled = "disabled";

            
            slice.propertyFields.fill = "color";
            slice.propertyFields.fillOpacity = "opacity";
            slice.propertyFields.stroke = "color";
            slice.propertyFields.strokeDasharray = "strokeDasharray";
            slice.propertyFields.tooltipText = "tooltip";

            pieSeries.slices.template.states.getKey("hover").properties.shiftRadius = 0;
            pieSeries.slices.template.states.getKey("hover").properties.scale = 1;

            var label = pieSeries.createChild(am4core.Label);
            label.text = vacinas_hoje_seg+"\n vacinas";
            label.horizontalCenter = "middle";
            label.verticalCenter = "middle";
            label.fontSize = 20;

            // This creates initial animation
            pieSeries.hiddenState.properties.opacity = 1;
            pieSeries.hiddenState.properties.endAngle = -90;
            pieSeries.hiddenState.properties.startAngle = -90;

        }

        function variacao_percentual(v1, v2){
            let tendencia = 100 * (     (      parseFloat(v2) - parseFloat(v1)   ) / (  parseFloat(v1) ));
            return tendencia;
        }

    </script>
</body>
</html>

