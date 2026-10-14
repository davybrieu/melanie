import { seances } from '../data/seances';

// Image à imprimer d'un bon cadeau, dessinée dans le navigateur avec les polices du site :
// A5 paysage (210 × 148 mm) à 300 points par pouce, téléchargée en JPEG.
export const LARGEUR = 2480;
export const HAUTEUR = 1748;

// Couleurs du site (bloc @theme de resources/css/app.css).
const couleurs = {
    fond: '#fdfaf6', // creme-50
    panneau: '#f6e8df', // poudre-100
    titre: '#4b3428', // cacao-800
    texte: '#5b4337', // cacao-700
    etiquette: '#8b6638', // or-600
    filet: '#dcc39c', // or-300
};

const familles = {
    titre: "'Gilda Display'",
    texte: 'Jost',
    manuscrit: 'Allison',
};

const secours = { titre: 'serif', texte: 'sans-serif', manuscrit: 'cursive' };

const images = {
    monogramme: '/images/marque/monogramme-mp-impression.webp',
    gypsophile: '/images/marque/fleurs-gypso-1.webp',
    pampa: '/images/marque/fleurs-pampa.webp',
};

const nomsMois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];

/** 2027-04-01 → « 1er avril 2027 ». */
export function dateLongue(iso) {
    const [annee, mois, jour] = iso.split('-').map(Number);

    return `${jour === 1 ? '1er' : jour} ${nomsMois[mois - 1]} ${annee}`;
}

/** Date ISO décalée de quelques mois (31 août + 6 mois → 28 ou 29 février). */
export function ajouterMois(iso, mois) {
    const [annee, moisDepart, jour] = iso.split('-').map(Number);
    const date = new Date(Date.UTC(annee, moisDepart - 1 + mois, 1));
    const dernierJour = new Date(Date.UTC(date.getUTCFullYear(), date.getUTCMonth() + 1, 0)).getUTCDate();
    date.setUTCDate(Math.min(jour, dernierJour));

    return date.toISOString().slice(0, 10);
}

let ressources = null;

function chargerImage(src) {
    return new Promise((resolve) => {
        const image = new Image();
        image.onload = () => resolve(image);
        // Image introuvable : le bon est dessiné sans elle.
        image.onerror = () => resolve(null);
        image.src = src;
    });
}

// Fleurs détourées : un voile clair, coupé net au bord de l'image, se verrait sur le panneau
// poudré. Leurs bords sont donc fondus vers la transparence.
function fondreBords(image, proportion = 0.12) {
    const canvas = document.createElement('canvas');
    const { width: largeur, height: hauteur } = image;
    canvas.width = largeur;
    canvas.height = hauteur;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(image, 0, 0);
    ctx.globalCompositeOperation = 'destination-in';

    const bords = [
        [0, 0, largeur * proportion, 0],
        [largeur, 0, largeur * (1 - proportion), 0],
        [0, 0, 0, hauteur * proportion],
        [0, hauteur, 0, hauteur * (1 - proportion)],
    ];

    for (const [x0, y0, x1, y1] of bords) {
        const degrade = ctx.createLinearGradient(x0, y0, x1, y1);
        degrade.addColorStop(0, 'rgba(0, 0, 0, 0)');
        degrade.addColorStop(1, 'rgba(0, 0, 0, 1)');
        ctx.fillStyle = degrade;
        ctx.fillRect(0, 0, largeur, hauteur);
    }

    return canvas;
}

/** Polices et images, chargées une seule fois. */
function chargerRessources() {
    ressources ??= Promise.all([
        document.fonts.load(`400 100px ${familles.titre}`),
        document.fonts.load(`300 100px ${familles.texte}`),
        document.fonts.load(`400 100px ${familles.texte}`),
        document.fonts.load(`500 100px ${familles.texte}`),
        document.fonts.load(`400 100px ${familles.manuscrit}`),
    ]).then(async () => {
        const [monogramme, gypsophile, pampa] = await Promise.all([images.monogramme, images.gypsophile, images.pampa].map(chargerImage));

        return { monogramme, gypsophile: gypsophile && fondreBords(gypsophile), pampa: pampa && fondreBords(pampa) };
    });

    return ressources;
}

function police(ctx, famille, taille, graisse = 400) {
    ctx.font = `${graisse} ${taille}px ${familles[famille]}, ${secours[famille]}`;
}

// Espacement des lettres écrit à la main : ctx.letterSpacing n'existe pas dans tous les navigateurs.
function largeurEspacee(ctx, texte, espacement) {
    if (!espacement) return ctx.measureText(texte).width;

    return [...texte].reduce((total, lettre) => total + ctx.measureText(lettre).width, 0) + espacement * Math.max(0, [...texte].length - 1);
}

function ecrire(ctx, texte, x, y, { espacement = 0, alignement = 'left' } = {}) {
    if (!espacement) {
        ctx.textAlign = alignement;
        ctx.fillText(texte, x, y);

        return;
    }

    const largeur = largeurEspacee(ctx, texte, espacement);
    let curseur = alignement === 'center' ? x - largeur / 2 : alignement === 'right' ? x - largeur : x;
    ctx.textAlign = 'left';

    for (const lettre of texte) {
        ctx.fillText(lettre, curseur, y);
        curseur += ctx.measureText(lettre).width + espacement;
    }
}

/** Plus grande taille (jusqu'à `taille`) à laquelle le texte tient dans `largeurMax`. */
function ajuster(ctx, texte, famille, taille, largeurMax, { minimum = taille / 2, graisse = 400, espacement = 0 } = {}) {
    let actuelle = taille;
    police(ctx, famille, actuelle, graisse);

    while (actuelle > minimum && largeurEspacee(ctx, texte, espacement) > largeurMax) {
        actuelle -= 2;
        police(ctx, famille, actuelle, graisse);
    }

    return actuelle;
}

/** Coupe un texte en deux lignes, au mot qui donne la plus longue ligne la plus courte. */
function deuxLignes(ctx, texte) {
    const mots = texte.split(/[ \t]+/);
    let meilleures = [texte];
    let meilleureLargeur = Infinity;

    for (let i = 1; i < mots.length; i++) {
        const lignes = [mots.slice(0, i).join(' '), mots.slice(i).join(' ')];
        const largeur = Math.max(...lignes.map((ligne) => ctx.measureText(ligne).width));

        if (largeur < meilleureLargeur) {
            meilleures = lignes;
            meilleureLargeur = largeur;
        }
    }

    return meilleures;
}

/** Découpe un texte en lignes de `largeurMax` au plus, en gardant ses retours à la ligne. */
function couper(ctx, texte, largeurMax) {
    const lignes = [];

    for (const paragraphe of texte.trim().split(/\n+/)) {
        let ligne = '';

        // Coupure aux espaces ordinaires seulement : jamais aux espaces insécables.
        for (const mot of paragraphe.split(/[ \t]+/).filter(Boolean)) {
            const essai = ligne ? `${ligne} ${mot}` : mot;

            if (ligne && ctx.measureText(essai).width > largeurMax) {
                lignes.push(ligne);
                ligne = mot;
            } else {
                ligne = essai;
            }
        }

        if (ligne) lignes.push(ligne);
    }

    return lignes;
}

function filet(ctx, x1, x2, y, epaisseur = 3) {
    ctx.fillStyle = couleurs.filet;
    ctx.fillRect(x1, y - epaisseur / 2, x2 - x1, epaisseur);
}

/**
 * Dessine le bon sur le canvas : à sa taille d'impression, ou réduit pour l'aperçu (échelle < 1,
 * plus net à l'écran qu'une image d'impression réduite par le navigateur).
 * bon : { code, beneficiaire, offert_par, seance, message, valable_jusqu_au } ; site : useSite().
 */
export async function dessinerBonCadeau(canvas, bon, site, echelle = 1) {
    const { monogramme, gypsophile, pampa } = await chargerRessources();

    canvas.width = Math.round(LARGEUR * echelle);
    canvas.height = Math.round(HAUTEUR * echelle);
    const ctx = canvas.getContext('2d');
    ctx.scale(echelle, echelle);
    ctx.imageSmoothingQuality = 'high';
    ctx.textBaseline = 'alphabetic';

    const marge = 64;
    const panneau = 960;
    const seance = seances[bon.seance] ?? null;

    // Fond, panneau poudré à gauche, fleurs et cadre doré.
    ctx.fillStyle = couleurs.fond;
    ctx.fillRect(0, 0, LARGEUR, HAUTEUR);
    ctx.fillStyle = couleurs.panneau;
    ctx.fillRect(0, 0, panneau, HAUTEUR);

    if (gypsophile) ctx.drawImage(gypsophile, -70, -60, 250 * 2, 290 * 2);

    // Bouquet de pampa retourné, ses tiges dans le coin en bas à gauche.
    if (pampa) {
        ctx.save();
        ctx.scale(-1, 1);
        ctx.drawImage(pampa, -(276 * 1.5) + 40, HAUTEUR - 405 * 1.5 + 60, 276 * 1.5, 405 * 1.5);
        ctx.restore();
    }

    ctx.strokeStyle = couleurs.filet;
    ctx.lineWidth = 4;
    ctx.strokeRect(marge, marge, LARGEUR - 2 * marge, HAUTEUR - 2 * marge);

    // Panneau gauche : monogramme, « Bon cadeau » et accroche de la séance.
    const centre = panneau / 2;

    if (monogramme) {
        const largeurMonogramme = 500;
        const hauteurMonogramme = (largeurMonogramme * monogramme.height) / monogramme.width;
        ctx.drawImage(monogramme, centre - largeurMonogramme / 2, 400, largeurMonogramme, hauteurMonogramme);
    }

    ctx.fillStyle = couleurs.titre;
    police(ctx, 'titre', 92);
    ecrire(ctx, 'BON CADEAU', centre, 890, { espacement: 20, alignement: 'center' });
    filet(ctx, centre - 70, centre + 70, 952);

    if (seance) {
        ctx.fillStyle = couleurs.texte;
        ajuster(ctx, seance.accroche, 'manuscrit', 128, panneau - 2 * marge - 140);
        ecrire(ctx, seance.accroche, centre, 1090, { alignement: 'center' });
    }

    // Panneau droit : pour qui, de la part de qui, quelle séance, message.
    const gauche = panneau + 150;
    const droite = LARGEUR - marge - 150;
    const largeur = droite - gauche;

    function etiquette(texte, y) {
        ctx.fillStyle = couleurs.etiquette;
        police(ctx, 'texte', 34, 500);
        ecrire(ctx, texte, gauche, y, { espacement: 9 });
    }

    // Nom sur une ligne, réduit jusqu'à `minimum` ; s'il ne tient toujours pas, sur deux lignes
    // de longueurs proches.
    function blocNom(texteEtiquette, texte, taille, minimum) {
        let actuelle = ajuster(ctx, texte, 'titre', taille, largeur, { minimum });
        let lignes = [texte];

        while (Math.max(...lignes.map((ligne) => ctx.measureText(ligne).width)) > largeur && actuelle > minimum / 2) {
            police(ctx, 'titre', actuelle);
            lignes = deuxLignes(ctx, texte);

            if (Math.max(...lignes.map((ligne) => ctx.measureText(ligne).width)) > largeur) {
                actuelle -= 2;
            }
        }

        const premiere = 30 + Math.round(actuelle * 0.92);
        const interligne = Math.round(actuelle * 1.15);
        const trait = premiere + interligne * (lignes.length - 1) + 50;

        return {
            hauteur: trait + 40,
            dessiner(y) {
                etiquette(texteEtiquette, y);
                ctx.fillStyle = couleurs.titre;
                police(ctx, 'titre', actuelle);
                lignes.forEach((ligne, i) => ecrire(ctx, ligne, gauche, y + premiere + i * interligne));
                filet(ctx, gauche, droite, y + trait, 2);
            },
        };
    }

    const blocs = [blocNom('POUR', bon.beneficiaire || 'Prénom', 120, 84)];

    if (bon.offert_par) {
        blocs.push(blocNom('DE LA PART DE', bon.offert_par, 84, 60));
    }

    blocs.push({
        hauteur: 200,
        dessiner(y) {
            etiquette('SÉANCE OFFERTE', y);
            ctx.fillStyle = couleurs.titre;
            const titre = seance ? `Séance photo ${seance.nom.toLowerCase()}` : 'Séance photo';
            ajuster(ctx, titre, 'titre', 84, largeur, { minimum: 52 });
            ecrire(ctx, titre, gauche, y + 112);

            if (seance) {
                const details = seance.inclus.map((element, i) => (i ? element.charAt(0).toLowerCase() + element.slice(1) : element)).join('  ·  ');
                ctx.fillStyle = couleurs.texte;
                ajuster(ctx, details, 'texte', 40, largeur, { graisse: 300, minimum: 30 });
                ecrire(ctx, details, gauche, y + 180);
            }
        },
    });

    // Blocs centrés verticalement au-dessus du pied du bon ; l'espace entre eux se resserre, puis
    // le message rapetisse, quand les textes sont longs.
    const haut = 190;
    const bas = 1390;
    const espaceMinimum = 36;
    const hauteurBlocs = () => blocs.reduce((somme, bloc) => somme + bloc.hauteur, 0);

    if (bon.message?.trim()) {
        // Entre guillemets français, attachés au premier et au dernier mot (espaces insécables).
        const message = `«\u00a0${bon.message.trim()}\u00a0»`;
        const hauteurMax = bas - haut - hauteurBlocs() - espaceMinimum * blocs.length;
        const hauteurMessage = (taille, lignes) => 40 + Math.round(taille * 1.45) * lignes.length;
        let taille = 46;
        police(ctx, 'texte', taille, 300);
        let lignes = couper(ctx, message, largeur);

        while ((lignes.length > 4 || hauteurMessage(taille, lignes) > hauteurMax) && taille > 32) {
            taille -= 2;
            police(ctx, 'texte', taille, 300);
            lignes = couper(ctx, message, largeur);
        }

        const interligne = Math.round(taille * 1.45);
        lignes = lignes.slice(0, Math.max(1, Math.floor((hauteurMax - 40) / interligne)));

        blocs.push({
            hauteur: hauteurMessage(taille, lignes),
            dessiner(y) {
                ctx.fillStyle = couleurs.texte;
                police(ctx, 'texte', taille, 300);
                lignes.forEach((ligne, i) => ecrire(ctx, ligne, gauche, y + 40 + taille + i * interligne));
            },
        });
    }

    const espace = Math.min(70, Math.max(espaceMinimum, (bas - haut - hauteurBlocs()) / (blocs.length - 1)));
    let y = haut + Math.max(0, (bas - haut - hauteurBlocs() - espace * (blocs.length - 1)) / 2);

    for (const bloc of blocs) {
        bloc.dessiner(y);
        y += bloc.hauteur + espace;
    }

    // Pied : validité, numéro et réservation.
    filet(ctx, gauche, droite, 1450, 2);
    ctx.fillStyle = couleurs.titre;
    police(ctx, 'texte', 38, 400);
    ecrire(ctx, `Valable jusqu’au ${dateLongue(bon.valable_jusqu_au)}`, gauche, 1530);
    police(ctx, 'texte', 36, 500);
    ecrire(ctx, `N° ${bon.code || 'MP-······'}`, droite, 1530, { espacement: 4, alignement: 'right' });

    const domaine = new URL(site.url).host.replace(/^www\./, '');
    ctx.fillStyle = couleurs.texte;
    police(ctx, 'texte', 34, 300);
    ecrire(ctx, `Réservation : ${site.telephone}  ·  ${domaine}`, gauche, 1600);
}

/** JPEG, avec une densité de 300 points par pouce dans son en-tête JFIF : il s'imprime en A5. */
async function versJpeg(canvas) {
    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.95));
    const octets = new Uint8Array(await blob.arrayBuffer());
    const jfif = String.fromCharCode(...octets.slice(6, 11));

    if (octets[2] === 0xff && octets[3] === 0xe0 && jfif === 'JFIF\0') {
        octets.set([1, 300 >> 8, 300 & 0xff, 300 >> 8, 300 & 0xff], 13);
    }

    return new Blob([octets], { type: 'image/jpeg' });
}

/** Dessine le bon et le télécharge (bon-cadeau-MP-7KQ4MZ.jpg). */
export async function telechargerBonCadeau(bon, site) {
    const canvas = document.createElement('canvas');
    await dessinerBonCadeau(canvas, bon, site);

    const lien = document.createElement('a');
    lien.href = URL.createObjectURL(await versJpeg(canvas));
    lien.download = `bon-cadeau-${bon.code}.jpg`;
    document.body.append(lien);
    lien.click();
    lien.remove();
    setTimeout(() => URL.revokeObjectURL(lien.href), 60_000);
}
