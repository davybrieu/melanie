// Conditions validées par Mélanie en octobre 2026, sauf les moyens de paiement, encore à
// confirmer. Reprises dans la FAQ, les CGV, l'accueil et la page bon cadeau : ajustez-les ici,
// tout le site suit (llms.txt, à la main).

// Durée de validité d'un bon cadeau, en mois : texte du site et date proposée à la création
// d'un bon (espace de gestion).
export const validiteBonCadeauMois = 6;

// Rayon des déplacements autour de Dijon, à mes frais (fourni par Mélanie).
const rayon = '40\u00a0km';

export const conditions = {
    // Réservation et paiement : un acompte pour une séance ; un bon cadeau se règle en totalité.
    acompte: '30 %',
    solde: 'le jour même, après la séance',
    paiements: 'espèces ou virement bancaire',
    paiementBonCadeau: 'en totalité à l’achat',

    // Les photos se choisissent avec Mélanie, sur place, à la fin de la séance ; elle les livre
    // retouchées, en haute définition, dans ce délai après la séance. Elle les envoie elle-même,
    // sans galerie en ligne : à sa demande, le site ne dit pas par quel moyen.
    livraison: 'sous 3\u00a0jours',

    // Délai de prévenance : report sans frais, et annulation avec remboursement de l'acompte
    prevenance: '48\u00a0heures',

    // Bon cadeau
    validiteBonCadeau: `${validiteBonCadeauMois}\u00a0mois`,

    // Lieux : extérieur et domicile (pas de studio mentionné sur le flyer). Le rayon et les
    // communes desservies ont été fournis par Mélanie : ils sont repris sur l'accueil, dans la
    // FAQ, les CGV, les données structurées (areaServed) et llms.txt. Codes postaux vérifiés sur
    // geo.api.gouv.fr.
    zone: `Dijon et ses alentours, dans un rayon de ${rayon}`,
    // Mention courte : en-tête de l'accueil, page tarifs, appel à réserver, pied de page…
    deplacements: `Déplacements sur Dijon et alentours (${rayon})`,
    communes: [
        { nom: 'Dijon', codePostal: '21000' },
        { nom: 'Beaune', codePostal: '21200' },
        { nom: 'Chenôve', codePostal: '21300' },
        { nom: 'Talant', codePostal: '21240' },
        { nom: 'Chevigny-Saint-Sauveur', codePostal: '21800' },
        { nom: 'Fontaine-lès-Dijon', codePostal: '21121' },
        { nom: 'Quetigny', codePostal: '21800' },
        { nom: 'Longvic', codePostal: '21600' },
        { nom: 'Auxonne', codePostal: '21130' },
        { nom: 'Saint-Apollinaire', codePostal: '21850' },
        { nom: 'Marsannay-la-Côte', codePostal: '21160' },
        { nom: 'Nuits-Saint-Georges', codePostal: '21700' },
        { nom: 'Genlis', codePostal: '21110' },
        { nom: 'Is-sur-Tille', codePostal: '21120' },
        { nom: 'Neuilly-Crimolois', codePostal: '21800' },
        { nom: 'Gevrey-Chambertin', codePostal: '21220' },
        { nom: 'Arc-sur-Tille', codePostal: '21560' },
        { nom: 'Plombières-lès-Dijon', codePostal: '21370' },
        { nom: 'Brazey-en-Plaine', codePostal: '21470' },
        { nom: 'Selongey', codePostal: '21260' },
    ],
};
