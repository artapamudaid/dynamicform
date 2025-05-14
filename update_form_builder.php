<?php

include 'config/database.php';
include 'helpers/common_helper.php';

// Ambil dan validasi data utama
$form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
$form_name = isset($_POST['form_name']) ? trim($_POST['form_name']) : '';

if ($form_id <= 0 || empty($form_name)) {
    die("Form ID atau Nama Form tidak valid.");
}

$form_slug = create_slug($form_name);

// Update form
$conn->query("UPDATE forms SET title = '$form_name', slug = '$form_slug' WHERE id = '$form_id'");

// Inisialisasi ID pertanyaan yang dikirim
$input_question_ids = [];

if (!empty($_POST['questions'])) {
    foreach ($_POST['questions'] as $question) {
        // Escape input
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

        $current_time = date('Y-m-d H:i:s');

        if (!empty($question['id'])) {
            // Update pertanyaan lama
            $question_id = $question['id'];
            $input_question_ids[] = $question_id;
            $sql = "UPDATE questions SET text = '$text', type = '$type', options = '$question_options', allowed_types = '$allowed_types', is_required = '$question_required', updated_at = '$current_time' WHERE id = '$question_id' AND form_id = '$form_id'";

            $conn->query($sql);
        } else {
            // Tambah pertanyaan baru
            $sql = "INSERT INTO questions (form_id, text, type, options, allowed_types, is_required) VALUES ('$form_id','$text','$type','$question_options','$allowed_types','$question_required')";

            $conn->query($sql);

            if ($conn->insert_id) {
                $input_question_ids[] = $conn->insert_id; // Simpan ID pertanyaan baru
            }
        }
    }
}

// Hapus pertanyaan yang tidak ada di input
if (!empty($input_question_ids)) {
    $placeholders = implode(',', $input_question_ids);

    $conn->query("UPDATE questions SET deleted_at = '$current_time' WHERE form_id = '$form_id' AND id NOT IN ($placeholders) AND deleted_at IS NULL");
} else {
    // Hapus semua pertanyaan jika tidak ada yang tersisa
    $conn->query("UPDATE questions SET deleted_at = '$current_time' WHERE form_id = '$form_id' AND deleted_at IS NULL");
}

echo "Form dan pertanyaan berhasil diperbarui!";
$conn->close();
