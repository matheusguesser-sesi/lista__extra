<?php

function removerEspacosDuplicados($texto) {
    return trim(preg_replace('/\s+/u', ' ', $texto));
}

function extrairPalavras($texto) {
    $limpo = preg_replace('/[^\p{L}\p{N}\s-]/u', '', mb_strtolower($texto, 'UTF-8'));
    return array_filter(explode(' ', removerEspacosDuplicados($limpo)), 'strlen');
}

function contarFrases($texto) {
    return count(preg_split('/[.!?]+/u', $texto, -1, PREG_SPLIT_NO_EMPTY));
}

function encontrarMaiorEMenorPalavra($palavras) {
    $maior = $menor = $palavras[array_key_first($palavras)] ?? "";

    foreach ($palavras as $palavra) {
        if (mb_strlen($palavra) > mb_strlen($maior)) $maior = $palavra;
        if (mb_strlen($palavra) < mb_strlen($menor)) $menor = $palavra;
    }

    return [$maior, $menor];
}

function contarPalavrasRepetidas($palavras) {
    return count(array_filter(array_count_values($palavras), fn($q) => $q > 1));
}

function palavrasMaisFrequentes($palavras, $limite = 5) {
    $frequencias = array_count_values($palavras);
    arsort($frequencias);
    return array_slice($frequencias, 0, $limite, true);
}

function formatarTexto($texto) {
    return mb_convert_case(mb_strtolower($texto), MB_CASE_TITLE);
}

function processarTexto($texto) {

    $textoLimpo = removerEspacosDuplicados($texto);
    $palavras = extrairPalavras($textoLimpo);
    [$maior, $menor] = encontrarMaiorEMenorPalavra($palavras);

    return [
        "caracteres" => mb_strlen($texto),
        "palavras" => count($palavras),
        "frases" => contarFrases($textoLimpo),
        "maior" => $maior,
        "menor" => $menor,
        "repetidas" => contarPalavrasRepetidas($palavras),
        "frequentes" => palavrasMaisFrequentes($palavras),
        "semEspacos" => $textoLimpo,
        "formatado" => formatarTexto($textoLimpo)
    ];
}


$texto = "Vai Corinthians!  Mais um gol do time do povo.";

$resultado = processarTexto($texto);

echo "Texto original: $texto <br><br>";

echo "Caracteres: " . $resultado["caracteres"] . "<br>";
echo "Palavras: " . $resultado["palavras"] . "<br>";
echo "Frases: " . $resultado["frases"] . "<br>";
echo "Palavra mais longa: " . $resultado["maior"] . "<br>";
echo "Palavra mais curta: " . $resultado["menor"] . "<br>";
echo "Palavras repetidas: " . $resultado["repetidas"] . "<br><br>";

echo "Cinco palavras mais frequentes:<br>";
foreach ($resultado["frequentes"] as $palavra => $quantidade) {
    echo "- $palavra ($quantidade)<br>";
}

echo "<br>Texto sem espaços duplicados: " . $resultado["semEspacos"] . "<br>";
echo "Texto formatado: " . $resultado["formatado"] . "<br>";

?>