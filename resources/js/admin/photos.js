// Photos ajoutées dans l'espace de gestion : préparées dans le navigateur avant l'envoi.

// Plus grand côté envoyé : les variantes du site vont jusqu'à 2000 px de large.
const COTE_MAX = 2400;

const nomSansExtension = (nom) => nom.replace(/\.[^.]+$/, '');

/**
 * Réduit la photo à 2400 px au plus sur le grand côté, en JPEG, sous la taille d'envoi qu'accepte
 * le serveur (tailleMax, en octets). L'image est redessinée : aucune métadonnée (position GPS,
 * appareil, date…) ne part avec elle. Si le navigateur ne sait pas la lire, ou si elle est plus
 * petite que coteMinimum, le fichier d'origine est envoyé tel quel et le serveur tranche.
 */
export async function preparerPhoto(fichier, tailleMax, coteMinimum) {
    const url = URL.createObjectURL(fichier);

    try {
        const image = new Image();
        image.src = url;
        await image.decode();

        // Marge pour les autres champs du formulaire.
        const limite = (tailleMax - 64 * 1024) * 0.97;
        const grandCote = Math.max(image.naturalWidth, image.naturalHeight);
        let cote = Math.min(COTE_MAX, grandCote);

        while (cote >= coteMinimum) {
            const echelle = cote / grandCote;
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(image.naturalWidth * echelle);
            canvas.height = Math.round(image.naturalHeight * echelle);

            const contexte = canvas.getContext('2d');
            contexte.fillStyle = '#ffffff';
            contexte.fillRect(0, 0, canvas.width, canvas.height);
            contexte.imageSmoothingQuality = 'high';
            contexte.drawImage(image, 0, 0, canvas.width, canvas.height);

            for (const qualite of [0.9, 0.85, 0.8]) {
                const blob = await new Promise((resoudre) => canvas.toBlob(resoudre, 'image/jpeg', qualite));

                if (blob && blob.size <= limite) {
                    return new File([blob], `${nomSansExtension(fichier.name)}.jpg`, { type: 'image/jpeg', lastModified: Date.now() });
                }
            }

            cote = Math.floor(cote * 0.85);
        }
    } catch {
        // Format que le navigateur ne sait pas lire : le serveur répond avec un message clair.
    } finally {
        URL.revokeObjectURL(url);
    }

    return fichier;
}

/**
 * Texte alternatif proposé d'après le nom du fichier (« 03-ventre-rond-au-lac.jpg » : « Ventre rond
 * au lac »), sauf pour les noms donnés par un appareil ou une application (« IMG_1234.jpg »).
 */
export function altPropose(nomFichier) {
    const texte = nomSansExtension(nomFichier)
        .replace(/[_-]+/g, ' ')
        .replace(/^\d+\s*[.)]*\s*/, '')
        .replace(/\s+/g, ' ')
        .trim();

    if (!texte || /^(img|dsc|dscf|dji|pxl|photo|image|_?mg|p)\s*\d+/i.test(texte) || /^(whatsapp|screenshot|capture|signal|snapchat)\b/i.test(texte) || !/\p{L}{3,}/u.test(texte)) {
        return '';
    }

    return (texte.charAt(0).toUpperCase() + texte.slice(1)).slice(0, 125);
}

/** Message d'erreur du texte alternatif, ou null s'il convient (mêmes règles que PhotoRequest). */
export function verifierAlt(alt) {
    const texte = alt.trim().replace(/\s+/g, ' ');

    if (!texte) {
        return 'Décrivez la photo : ce texte est lu par Google et par les lecteurs d’écran.';
    }

    if (texte.length < 10) {
        return 'Décrivez un peu plus la photo (10 caractères au moins).';
    }

    return texte.length > 125 ? 'Le texte alternatif ne doit pas dépasser 125 caractères.' : null;
}
