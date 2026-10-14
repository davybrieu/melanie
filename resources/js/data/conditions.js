// Conditions validées par Mélanie en octobre 2026, sauf les moyens de paiement, encore à
// confirmer. Reprises dans la FAQ, les CGV, l'accueil et la page bon cadeau : ajustez-les ici,
// tout le site suit (llms.txt, à la main).

// Durée de validité d'un bon cadeau, en mois : texte du site et date proposée à la création
// d'un bon (espace de gestion).
export const validiteBonCadeauMois = 6;

export const conditions = {
    // Réservation et paiement : un acompte pour une séance ; un bon cadeau se règle en totalité.
    acompte: '30 %',
    solde: 'le jour même, après la séance',
    paiements: 'espèces ou virement bancaire',
    paiementBonCadeau: 'en totalité à l’achat',

    // Livraison des photos retouchées, en haute définition, après le choix des photos. Mélanie les
    // envoie elle-même, sans galerie en ligne : à sa demande, le site ne dit pas par quel moyen.
    livraison: 'sous 3 jours',

    // Délai de prévenance : report sans frais, et annulation avec remboursement de l'acompte
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
