// Offres et tarifs (offre de lancement, reprise du flyer).
// Modifier un prix ici le met à jour partout : tarifs, pages séances, FAQ, bon cadeau.

export const seances = {
    grossesse: {
        cle: 'grossesse',
        nom: 'Grossesse',
        titre: 'Séance grossesse',
        accroche: 'Un moment rien qu’à vous',
        prix: 100,
        duree: '1 h',
        inclus: ['1h de séance', 'Prêt de tenues', '10 photos retouchées incluses'],
        route: 'grossesse',
        icone: 'grossesse',
    },
    'nouveau-ne': {
        cle: 'nouveau-ne',
        nom: 'Nouveau-né',
        titre: 'Séance nouveau-né',
        accroche: 'Des premiers instants précieux',
        prix: 100,
        duree: '1h30 à 2h',
        inclus: ['1h30 à 2h de séance', 'Prêt de tenues', '10 photos retouchées incluses'],
        route: 'nouveau-ne',
        icone: 'bebe',
    },
    famille: {
        cle: 'famille',
        nom: 'Famille',
        titre: 'Séance famille',
        accroche: 'Votre tribu, votre histoire',
        prix: 60,
        duree: '1 h',
        inclus: ['1h de séance', 'Prêt de tenues', '10 photos retouchées incluses'],
        route: 'famille',
        icone: 'famille',
    },
};

export const photoSupplementaire = 10;

export const photosIncluses = 10;

export const euros = (montant) => `${montant} €`;
