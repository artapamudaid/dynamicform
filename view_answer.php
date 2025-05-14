<?php
include 'config/database.php'; // Menghubungkan ke database

if (empty($_GET['slug'])) {
    die("Slug tidak valid.");
}

$slug = $_GET['slug'];

// Ambil semua pertanyaan beserta type-nya
$questions = [];
$stmt = $conn->prepare("SELECT questions.id, text, type FROM questions JOIN forms ON forms.id = questions.form_id WHERE slug = ? ORDER BY questions.id");
$stmt->bind_param("s", $slug);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $questions[$row['id']] = [
        'text' => $row['text'],
        'type' => $row['type']
    ];
}
$stmt->close();

// Ambil semua jawaban
$stmt = $conn->prepare("SELECT form_id, question_id, answer FROM answers JOIN forms ON forms.id = answers.form_id WHERE slug = ? ORDER BY form_id, question_id, answers.id");
$stmt->bind_param("s", $slug);
$stmt->execute();
$res = $stmt->get_result();

// Siapkan array rekap
$data = [];
while ($row = $res->fetch_assoc()) {
    $form_id = $row['form_id'];
    $question_id = $row['question_id'];
    $question_text = $questions[$question_id]['text'];
    $data[$form_id][$question_id] = $row['answer'];
}

// Tampilkan dalam HTML table
echo "<table border='1' cellpadding='5'><thead><tr><th>Form ID</th>";
foreach ($questions as $q) {
    echo "<th>" . htmlspecialchars($q['text']) . "</th>";
}
echo "</tr></thead><tbody>";

foreach ($data as $form_id => $answers) {
    echo "<tr><td>{$form_id}</td>";
    foreach ($questions as $qid => $q) {
        $type = $q['type'];
        $val = isset($answers[$qid]) ? $answers[$qid] : '';

        // Cek apakah file
        if ($type === 'file' && $val) {
            $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $val = "<img src='$val' alt='File' width='100'>";
            } else {
                $val = "<a href='$val' target='_blank'>Download</a>";
            }
        } else {
            $val = htmlspecialchars($val);
        }

        echo "<td>{$val}</td>";
    }
    echo "</tr>";
}

echo "</tbody></table>";
