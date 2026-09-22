<?php
declare(strict_types=1);

namespace Healcare\Controllers;

final class ProfileController
{
    public function save(): void
    {
        $_SESSION['patient_profile'] = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'age' => max(0, (int) ($_POST['age'] ?? 0)),
            'gender' => trim((string) ($_POST['gender'] ?? '')),
            'conditions' => array_values(array_filter(array_map('strval', (array) ($_POST['conditions'] ?? [])))),
            'allergies' => trim((string) ($_POST['allergies'] ?? '')),
            'diet' => trim((string) ($_POST['diet'] ?? '')),
            'medication' => trim((string) ($_POST['medication'] ?? '')),
            'notes' => trim((string) ($_POST['notes'] ?? '')),
        ];
        header('Location: index.php?page=recommendations&saved=1');
    }
}
