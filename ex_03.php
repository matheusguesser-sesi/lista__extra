<!-- Exercício 18 – Gerenciador de Agenda
Uma clínica deseja organizar automaticamente sua agenda de consultas.
Cada consulta possui:
● Nome do paciente;
● Especialidade;
● Data;
● Horário.
Crie uma função chamada organizarAgenda() que receba um vetor multidimensional contendo todas as consultas.
Ela deverá retornar:
● Quantidade total de consultas;
● Quantidade de pacientes diferentes;
● Quantidade de consultas por especialidade;
● Primeiro atendimento do dia;
● Último atendimento do dia;
● Lista ordenada pelo horário;
● Pesquisa de um paciente informado pelo usuário;
● Verificar se existem horários duplicados.
Requisitos
● Utilizar vetores multidimensionais.
● Modularizar a solução em pelo menos 6 funções.
● Retornar todas as informações em um único array. -->

<?php

function contarPacientesDiferentes($consultas) {
    $nomes = array_map('mb_strtolower', array_column($consultas, 'paciente'));
    return count(array_unique($nomes));
}

function contarPorEspecialidade($consultas) {
    $contagem = [];

    foreach ($consultas as $consulta) {
        $especialidade = $consulta['especialidade'];
        $contagem[$especialidade] = ($contagem[$especialidade] ?? 0) + 1;
    }

    return $contagem;
}

function ordenarAgenda($consultas) {
    usort($consultas, fn($a, $b) => ($a['data'] . $a['horario']) <=> ($b['data'] . $b['horario']));
    return $consultas;
}

function pesquisarPaciente($consultas, $busca) {
    if (trim($busca) === '') {
        return [];
    }

    $encontradas = array_filter($consultas, fn($c) => mb_stripos($c['paciente'], $busca) !== false);
    return array_values($encontradas);
}

function encontrarHorariosDuplicados($consultas) {
    $horarios = array_map(fn($c) => $c['data'] . ' ' . $c['horario'], $consultas);
    $repetidos = array_keys(array_filter(array_count_values($horarios), fn($q) => $q > 1));

    return array_map(fn($h) => date('d/m/Y H:i', strtotime($h)), $repetidos);
}

function formatarConsulta($consulta) {
    return date('d/m/Y', strtotime($consulta['data'])) . " às " . $consulta['horario']
        . " - " . $consulta['paciente'] . " (" . $consulta['especialidade'] . ")";
}

function organizarAgenda($consultas, $busca) {

    $ordenada = ordenarAgenda($consultas);
    $duplicados = encontrarHorariosDuplicados($consultas);

    return [
        "total" => count($consultas),
        "pacientes" => contarPacientesDiferentes($consultas),
        "especialidades" => contarPorEspecialidade($consultas),
        "primeiro" => $ordenada[array_key_first($ordenada)] ?? null,
        "ultimo" => $ordenada[array_key_last($ordenada)] ?? null,
        "ordenada" => $ordenada,
        "pesquisa" => pesquisarPaciente($consultas, $busca),
        "temDuplicados" => !empty($duplicados),
        "duplicados" => $duplicados
    ];
}


$consultas = [
    ["paciente" => "Ana Souza",      "especialidade" => "Cardiologia",  "data" => "2026-10-05", "horario" => "09:00"],
    ["paciente" => "Carlos Lima",    "especialidade" => "Dermatologia", "data" => "2026-10-05", "horario" => "08:30"],
    ["paciente" => "Beatriz Alves",  "especialidade" => "Cardiologia",  "data" => "2026-10-05", "horario" => "10:00"],
    ["paciente" => "Ana Souza",      "especialidade" => "Ortopedia",    "data" => "2026-10-06", "horario" => "14:00"],
    ["paciente" => "Diego Rocha",    "especialidade" => "Dermatologia", "data" => "2026-10-05", "horario" => "09:00"],
    ["paciente" => "Fernanda Costa", "especialidade" => "Pediatria",    "data" => "2026-10-06", "horario" => "08:00"],
    ["paciente" => "Carlos Lima",    "especialidade" => "Cardiologia",  "data" => "2026-10-06", "horario" => "16:30"]
];

$busca = "Ana";

$resultado = organizarAgenda($consultas, $busca);

echo "Total de consultas: " . $resultado["total"] . "<br>";
echo "Pacientes diferentes: " . $resultado["pacientes"] . "<br><br>";

echo "Consultas por especialidade:<br>";
foreach ($resultado["especialidades"] as $especialidade => $quantidade) {
    echo "- $especialidade: $quantidade<br>";
}

echo "<br>Primeiro atendimento: " . formatarConsulta($resultado["primeiro"]) . "<br>";
echo "Último atendimento: " . formatarConsulta($resultado["ultimo"]) . "<br><br>";

echo "Agenda ordenada:<br>";
foreach ($resultado["ordenada"] as $consulta) {
    echo "- " . formatarConsulta($consulta) . "<br>";
}

echo "<br>Pesquisa por \"$busca\":<br>";
foreach ($resultado["pesquisa"] as $consulta) {
    echo "- " . formatarConsulta($consulta) . "<br>";
}

echo "<br>Horários duplicados: " . ($resultado["temDuplicados"] ? "Sim" : "Não") . "<br>";
foreach ($resultado["duplicados"] as $horario) {
    echo "- $horario<br>";
}

?>