// ⚠️ À VALIDER PAR MÉLANIE AVANT LA MISE EN LIGNE
// Ces conditions ne figurent pas sur le flyer : ce sont des valeurs courantes chez les
// photographes, reprises dans la FAQ et les CGV. Ajustez-les ici, tout le site suit.

// Durée de validité d'un bon cadeau, en mois : texte du site et date proposée à la création
// d'un bon (espace de gestion).
export const validiteBonCadeauMois = 6;

export const conditions = {
    // Réservation et paiement
    acompte: '30 %',
    solde: 'le jour de la séance',
    paiements: 'espèces ou virement bancaire',

    // Livraison des photos
    livraison: 'sous 3 semaines',
    livraisonMode: 'dans une galerie en ligne privée, à télécharger en haute définition',

    // Report et annulation : délai de prévenance
    prevenance: '48 heures',

    // Bon cadeau
    validiteBonCadeau: `${validiteBonCadeauMois} mois`,

    // Lieux : extérieur et domicile (pas de studio mentionné sur le flyer). Le rayon et les
    // communes desservies ont été fournis par Mélanie : ils sont repris sur l'accueil, dans la
    // FAQ, les CGV, les données structurées (areaServed) et llms.txt.
    zone: 'Dijon et ses alentours, dans un rayon de 40 km',
    communes: [
        'Dijon',
        'Beaune',
        'Chenôve',
        'Talant',
        'Chevigny-Saint-Sauveur',
        'Fontaine-lès-Dijon',
        'Quetigny',
        'Longvic',
        'Auxonne',
        'Saint-Apollinaire',
        'Marsannay-la-Côte',
        'Nuits-Saint-Georges',
        'Genlis',
        'Is-sur-Tille',
        'Neuilly-Crimolois',
        'Gevrey-Chambertin',
        'Arc-sur-Tille',
        'Plombières-lès-Dijon',
        'Brazey-en-Plaine',
        'Selongey',
    ],
};
