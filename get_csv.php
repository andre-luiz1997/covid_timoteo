<?php 
ini_set('memory_limit', '4095M');

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

$confirmados = parse_csv(file_get_contents('painel_confirmados.csv'));
$internados = parse_csv(file_get_contents('painel_internados.csv'));
$obitos = parse_csv(file_get_contents('painel_obitos.csv'));
$recuperados = parse_csv(file_get_contents('painel_recuperados.csv'));
$raw = parse_csv(file_get_contents('https://raw.githubusercontent.com/andre-luiz1997/covid_timoteo/master/covid_timoteo_data.csv'));
echo json_encode(
    array(
        "confirmados" => $confirmados, 
        "internados" => $internados, 
        "obitos" => $obitos, 
        "recuperados" => $recuperados,
        "raw" => $raw
    )
);
?>