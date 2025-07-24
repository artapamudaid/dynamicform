<?php
include 'config/database.php';

if (empty($_GET['slug'])) {
    die("Slug tidak valid.");
}

$slug = $_GET['slug'];
$stmt = $conn->prepare("SELECT * FROM forms WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result_form = $stmt->get_result();
$form = $result_form->fetch_assoc();

if (!$form) {
    die("Form tidak ditemukan.");
}

$stmt = $conn->prepare("SELECT * FROM questions WHERE form_id = ? AND deleted_at IS NULL");
$stmt->bind_param("i", $form['id']);
$stmt->execute();
$result_questions = $stmt->get_result();
$questions = $result_questions->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Formulir: <?= htmlspecialchars($form['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1><?= htmlspecialchars($form['title']) ?></h1>
        <form method="POST" action="save_answer.php" enctype="multipart/form-data">
            <input type="hidden" name="form_id" value="<?= $form['id'] ?>">
            <?php foreach ($questions as $question): ?>
                <div class="mb-3">
                    <label class="form-label">
                        <?= htmlspecialchars($question['text']) ?>
                        <?= $question['is_required'] ? '*' : '' ?>
                    </label>
                    <?php
                    $name = "answers[{$question['id']}]";
                    $required = $question['is_required'] ? 'required' : '';
                    $options = array_map('trim', explode(',', $question['options'] ?? ''));

                    switch ($question['type']) {
                        case 'text':
                            echo "<input type='text' class='form-control' name='$name' $required>";
                            break;
                        case 'date':
                            echo "<input type='date' class='form-control' name='$name' $required>";
                            break;
                        case 'number':
                            echo "<input type='number' class='form-control' name='$name' $required>";
                            break;
                        case 'file':
                            echo "<input type='file' class='form-control' name='$name' $required>";
                            break;
                        case 'textarea':
                            echo "<textarea class='form-control' name='$name' $required></textarea>";
                            break;
                        case 'radio':
                            foreach ($options as $i => $opt) {
                                $id = "radio-{$question['id']}-$i";
                                echo "
                                <div class='form-check'>
                                    <input class='form-check-input' type='radio' name='$name' value='" . htmlspecialchars($opt) . "' id='$id' $required>
                                    <label class='form-check-label' for='$id'>" . htmlspecialchars($opt) . "</label>
                                </div>";
                            }
                            break;
                        case 'checkbox':
                            foreach ($options as $i => $opt) {
                                $id = "checkbox-{$question['id']}-$i";
                                echo "
                                <div class='form-check'>
                                    <input class='form-check-input' type='checkbox' name='{$name}[]' value='" . htmlspecialchars($opt) . "' id='$id' $required>
                                    <label class='form-check-label' for='$id'>" . htmlspecialchars($opt) . "</label>
                                </div>";
                            }
                            break;
                        case 'select':
                            echo "<select class='form-select' name='$name' $required>";
                            echo "<option value=''>-- Pilih --</option>";
                            foreach ($options as $opt) {
                                echo "<option value='" . htmlspecialchars($opt) . "'>" . htmlspecialchars($opt) . "</option>";
                            }
                            echo "</select>";
                            break;
                    }
                    ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </form>
    </div>
</body>

</html>