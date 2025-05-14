<?php
include 'config/database.php';

// Ambil form_id dan data jawaban dari form
$form_id = $_POST['form_id'];
$answers = $_POST['answers'];

foreach ($answers as $question_id => $answer) {

    // Cek apakah jawaban ini adalah file

    // Bukan file atau tidak diupload
    if (is_array($answer)) {
        $answerValue = implode(',', $answer); // Checkbox
    } else {
        $answerValue = $answer;
    }

    // Simpan jawaban
    $stmt = $conn->prepare("INSERT INTO answers (form_id, question_id, answer) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $form_id, $question_id, $answerValue);
    $stmt->execute();
}


$fileCount = 0;
if (isset($_FILES['answers']['name']) && is_array($_FILES['answers']['name'])) {
    foreach ($_FILES['answers']['name'] as $question_id => $filename) {
        if (!empty($filename) && $_FILES['answers']['error'][$question_id] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['answers']['tmp_name'][$question_id];
            $fileName = basename($_FILES['answers']['name'][$question_id]);
            $fileSize = $_FILES['answers']['size'][$question_id];

            // Ambil ekstensi file
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Sanitasi nama file
            $safeFileName = time() . '-' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $fileName);

            // Tentukan folder tujuan
            $uploadFolder = 'uploads/';
            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0755, true);
            }

            $destPath = $uploadFolder . $safeFileName;

            // Ambil allowed types dari DB (asumsinya berupa ekstensi, contoh: 'pdf,jpg,png')
            $getAllowedTypes = $conn->query("SELECT allowed_types FROM questions WHERE id = $question_id")->fetch_assoc();
            $allowedTypes = array_map('strtolower', array_map('trim', explode(',', $getAllowedTypes['allowed_types'])));

            if (in_array($fileExtension, $allowedTypes) && move_uploaded_file($fileTmpPath, $destPath)) {
                $answerValue = $destPath; // Simpan path sebagai jawaban
            } else {
                $answerValue = 'Gagal upload file';
            }


            $stmt = $conn->prepare("INSERT INTO answers (form_id, question_id, answer) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $form_id, $question_id, $answerValue);
            $stmt->execute();
            $fileCount++;
        }
    }
}

echo "Berhasil menyimpan $fileCount file.";

echo "Formulir telah berhasil dikirim!";
