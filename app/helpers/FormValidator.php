<?php 

namespace App\Helpers;

class FormValidator {
    private $errors = [];
    private $fields = [];

    public function addField(string $fieldName, string $fieldLabel) {
        $this->fields[$fieldName] = [
            'label' => $fieldLabel,
            'value' => '',
            'error' => '',
        ];
    }

    public function setValue(string $fieldName, string $value) {
        $this->fields[$fieldName]['value'] = $value;
    }

    public function validate() {
        $this->errors = [];

        foreach ($this->fields as $fieldName => $field) {
            $label = $field['label'];
            $value = $field['value'];

            if (empty($value)) {
                $this->errors[$fieldName] = "$label es un campo obligatorio";
            }
        }

        return count($this->errors) == 0;
    }

    public function getErrors() {
        return $this->errors;
    }
}

/*

$validator = new FormValidator();
$validator->addField('email', 'Email');
$validator->addField('password', 'Contraseña');

$validator->setValue('email', $_POST['email']);
$validator->setValue('password', $_POST['password']);

if ($validator->validate()) {
    // Validación exitosa
} else {
    $errors = $validator->getErrors();
    // Mostrar errores
}

*/