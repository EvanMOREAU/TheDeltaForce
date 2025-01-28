<?php
namespace App\Services;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploaderHelper {

    private $slugger;
    private $params;

    public function __construct(SluggerInterface $slugger, ParameterBagInterface $params) {
        $this->slugger = $slugger;
        $this->params = $params;
    }

    public function uploadImage(UploadedFile $imageFile, string $entityName): ?string {
        // Initialiser une variable pour le nom final du fichier
        $newFilename = null;
    
        if ($imageFile) {
            // Générer un nom de fichier sécurisé
            $safeFilename = $this->slugger->slug($entityName);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
    
            try {
                // Déplacer le fichier vers le répertoire cible
                $imageFile->move(
                    $this->params->get('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                throw new \Exception('Une erreur est survenue lors du téléchargement de l\'image : ' . $e->getMessage());
            }
        }
    
        // Retourner le nom du fichier ou null si rien n'a été déplacé
        return $newFilename;
    }
    
}