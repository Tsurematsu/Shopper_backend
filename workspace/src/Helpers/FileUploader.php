<?php
// src/Helpers/FileUploader.php
namespace App\Helpers;

use Psr\Http\Message\UploadedFileInterface;

class FileUploader
{
    private string $uploadDirectory;
    private array $allowedExtensions;
    private int $maxSize;
    
    public function __construct(
        string $uploadDirectory = 'uploads',
        array $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
        int $maxSize = 5242880 // 5MB
    ) {
        // Usar ruta absoluta desde la raíz del proyecto
        $this->uploadDirectory = __DIR__ . '/../../' . $uploadDirectory;
        $this->allowedExtensions = $allowedExtensions;
        $this->maxSize = $maxSize;
        
        // Crear directorio si no existe
        if (!is_dir($this->uploadDirectory)) {
            if (!mkdir($this->uploadDirectory, 0777, true)) {
                throw new \Exception('No se pudo crear el directorio de uploads');
            }
            chmod($this->uploadDirectory, 0777);
        }
        
        // Verificar que sea escribible
        if (!is_writable($this->uploadDirectory)) {
            throw new \Exception('El directorio de uploads no tiene permisos de escritura: ' . $this->uploadDirectory);
        }
    }
    
    public function upload(UploadedFileInterface $uploadedFile): array
    {
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new \Exception('Error al subir el archivo: ' . $this->getUploadErrorMessage($uploadedFile->getError()));
        }
        
        if ($uploadedFile->getSize() > $this->maxSize) {
            throw new \Exception('El archivo excede el tamaño máximo permitido (' . ($this->maxSize / 1048576) . 'MB)');
        }
        
        $filename = $uploadedFile->getClientFilename();
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new \Exception('Tipo de archivo no permitido. Extensiones permitidas: ' . implode(', ', $this->allowedExtensions));
        }
        
        $newFilename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $this->uploadDirectory . '/' . $newFilename;
        
        // Mover archivo
        $uploadedFile->moveTo($filepath);
        
        // Dar permisos al archivo
        chmod($filepath, 0644);
        
        return [
            'filename' => $newFilename,
            'original_name' => $filename,
            'path' => $filepath,
            'relative_path' => 'uploads/' . $newFilename,
            'size' => $uploadedFile->getSize(),
            'extension' => $extension
        ];
    }
    
    private function getUploadErrorMessage(int $errorCode): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo del formulario',
            UPLOAD_ERR_PARTIAL => 'El archivo fue subido parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida',
        ];
        
        return $errors[$errorCode] ?? 'Error desconocido';
    }
}