<?php

namespace App\Console\Commands;

use GdImage;
use Illuminate\Console\Command;

/**
 * Convertit des images PNG ou JPEG en WebP optimisé, à côté du fichier d'origine.
 *
 * Pour les images du site (logos, décors…). Les photos déposées dans resources/photos
 * n'en ont pas besoin : le site crée lui-même leurs variantes WebP.
 */
class ConvertirImages extends Command
{
    protected $signature = 'images:convertir
        {fichiers* : images PNG ou JPEG à convertir}
        {--qualite=80 : qualité WebP, de 1 à 100}
        {--largeur= : largeur maximale en pixels (environ 2 à 3 fois la largeur affichée)}
        {--remplacer : remplace le fichier WebP s’il existe déjà}';

    protected $description = 'Convertit des images PNG ou JPEG en WebP optimisé';

    public function handle(): int
    {
        $qualite = (int) $this->option('qualite');
        $largeurMax = $this->option('largeur') ? (int) $this->option('largeur') : null;

        if ($qualite < 1 || $qualite > 100 || ($largeurMax !== null && $largeurMax < 1)) {
            $this->error('La qualité doit être comprise entre 1 et 100, et la largeur être positive.');

            return self::FAILURE;
        }

        $echecs = 0;

        foreach ($this->argument('fichiers') as $fichier) {
            $destination = preg_replace('/\.(png|jpe?g)$/i', '.webp', $fichier);

            if (! is_file($fichier) || $destination === $fichier) {
                $this->error("{$fichier} : fichier PNG ou JPEG introuvable.");
                $echecs++;

                continue;
            }

            if (is_file($destination) && ! $this->option('remplacer')) {
                $this->error("{$destination} existe déjà (ajoutez --remplacer pour l’écraser).");
                $echecs++;

                continue;
            }

            $image = $this->charger($fichier);

            if (! $image) {
                $this->error("{$fichier} : image illisible.");
                $echecs++;

                continue;
            }

            if ($largeurMax !== null && imagesx($image) > $largeurMax) {
                $image = $this->reduire($image, $largeurMax);
            }

            imagewebp($image, $destination, $qualite);

            $avant = filesize($fichier);
            $apres = filesize($destination);
            $this->info(sprintf(
                '%s (%s Ko) → %s (%d × %d px, %s Ko, %+d %%)',
                $fichier, $this->ko($avant), $destination, imagesx($image), imagesy($image), $this->ko($apres), round(($apres - $avant) / $avant * 100),
            ));

            if ($apres >= $avant) {
                $this->warn('  Le WebP est plus lourd que l’original : essayez une qualité plus basse (--qualite=70).');
            }
        }

        $this->newLine();
        $this->line('Utilisez le .webp dans le code (width et height du fichier, alt descriptif), puis supprimez l’original de public/.');

        return $echecs ? self::FAILURE : self::SUCCESS;
    }

    private function charger(string $fichier): ?GdImage
    {
        $image = match (strtolower(pathinfo($fichier, PATHINFO_EXTENSION))) {
            'png' => @imagecreatefrompng($fichier),
            default => @imagecreatefromjpeg($fichier),
        };

        if (! $image) {
            return null;
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // Photos de téléphone : on applique la rotation indiquée dans les données EXIF.
        $orientation = function_exists('exif_read_data') && preg_match('/\.jpe?g$/i', $fichier)
            ? (int) (@exif_read_data($fichier)['Orientation'] ?? 1)
            : 1;

        return match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };
    }

    private function reduire(GdImage $image, int $largeur): GdImage
    {
        $hauteur = (int) round(imagesy($image) * $largeur / imagesx($image));

        $reduite = imagecreatetruecolor($largeur, $hauteur);
        imagealphablending($reduite, false);
        imagesavealpha($reduite, true);
        imagecopyresampled($reduite, $image, 0, 0, 0, 0, $largeur, $hauteur, imagesx($image), imagesy($image));

        return $reduite;
    }

    private function ko(int $octets): string
    {
        return number_format($octets / 1024, 1, ',', ' ');
    }
}
