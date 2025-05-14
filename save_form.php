<?php

include 'config/database.php';
include 'helpers/common_helper.php';

// Ambil nama form dari input
$form_name = $_POST['form_name'];
$form_name = $conn->real_escape_string($form_name);
$form_slug = create_slug($form_name);

// Simpan form_name ke tabel forms
$sql = "INSERT INTO forms (title, slug) VALUES ('$form_name', '$form_slug')";
if ($conn->query($sql) === TRUE) {
    // Ambil ID form yang baru saja disimpan
    $form_id = $conn->insert_id;

    // Simpan setiap pertanyaan ke tabel questions
    if (!empty($_POST['questions'])) {
        foreach ($_POST['questions'] as $question) {
            $text = $conn->real_escape_string($question['text']);
            $type = $conn->real_escape_string($question['type']);

            if ($type == 'file') {
                $allowed_types = isset($question['options']) ? $conn->real_escape_string($question['options']) : '';
                $question_options = '';
            } else {
                $allowed_types = '';
                $question_options = isset($question['options']) ? $conn->real_escape_string($question['options']) : '';
            }

            $question_required = isset($question['required']) ? 1 : 0;

            $sql = "INSERT INTO questions (`form_id`, `text`, `type`, `options`, `allowed_types`, `is_required`)
                    VALUES ('$form_id', '$text', '$type', '$question_options', '$allowed_types', '$question_required')";

            $conn->query($sql);
        }
    }
    echo "Form berhasil disimpan!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
