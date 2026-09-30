import { conditions as c } from './conditions';
import { euros, photoSupplementaire, photosIncluses, seances } from './seances';

// Réponses en HTML (liens internes gérés par le composant Faq).
const g = seances.grossesse;
const n = seances['nouveau-ne'];
const f = seances.famille;

export const faqGrossesse = [
    {
        q: 'Quand faire une séance photo grossesse ?',
        r: `<p>Le moment idéal se situe entre le 7e et le 8e mois de grossesse (environ 28 à 34 semaines) : votre ventre est bien rond et vous êtes encore à l’aise pour bouger et prendre la pose. Pour une grossesse gémellaire, on avance un peu la séance, autour du 6e ou du 7e mois.</p>`,
    },
    {
        q: 'À quel mois faire ses photos de grossesse ?',
        r: `<p>Au 7e mois ou au début du 8e. Avant, le ventre est parfois encore discret ; après, la fatigue peut se faire sentir. Je vous conseille de réserver dès le 5e mois, pour choisir votre date sereinement.</p>`,
    },
    {
        q: 'Quelle tenue porter pour une séance grossesse ?',
        r: `<p>Des matières fluides qui épousent le ventre : robe longue, robe en maille, chemise ouverte… Privilégiez des couleurs douces et unies (crème, beige, blanc, vieux rose, terracotta) et évitez les gros motifs et les logos.</p><p>Et bonne nouvelle : <strong>le prêt de tenues est inclus</strong> dans la séance.</p>`,
    },
    {
        q: 'Que porter pour une séance photo enceinte ?',
        r: `<p>Ce qui vous ressemble et dans lequel vous vous sentez belle ! Une robe longue et fluide pour un rendu romantique, un jean et une chemise ouverte pour un style plus naturel, ou un simple drapé pour des images plus intimes. Pensez aussi aux tenues du papa et des enfants, dans des tons qui s’accordent avec la vôtre : nous en parlons ensemble avant la séance.</p>`,
    },
    {
        q: 'Comment préparer une séance photo grossesse ?',
        r: `<p>Quelques conseils simples :</p><ul><li>hydratez votre peau les jours précédents ;</li><li>prévoyez des sous-vêtements couleur chair et des chaussures confortables ;</li><li>apportez de l’eau et un petit encas ;</li><li>si vous le souhaitez, des objets qui ont du sens pour vous : échographie, premiers chaussons, doudou…</li></ul><p>Avant la séance, nous échangeons sur vos envies, vos tenues et le lieu : vous arrivez sereine.</p>`,
    },
    {
        q: '10 idées de poses grossesse',
        r: `<ul><li>Les mains en cœur sur le ventre.</li><li>De profil, une main sous le ventre.</li><li>Le regard posé sur votre ventre.</li><li>Le papa qui embrasse le ventre.</li><li>Les aînés, l’oreille collée contre le ventre.</li><li>L’échographie ou des chaussons tenus contre le ventre.</li><li>Assise dans l’herbe, la robe étalée autour de vous.</li><li>Votre silhouette en contre-jour au coucher du soleil.</li><li>Front contre front avec votre partenaire.</li><li>Une balade main dans la main, de dos.</li></ul><p>Pas besoin de les retenir : je vous guide pour chacune d’elles.</p>`,
    },
    {
        q: 'Peut-on faire une séance grossesse en extérieur ?',
        r: `<p>Oui, et c’est l’une de mes préférées ! La lumière douce de fin de journée sublime les ventres ronds. Nous choisissons ensemble un lieu autour de Dijon : un parc, un champ, un bord d’eau, une forêt… En cas de météo capricieuse, on décale la séance.</p>`,
    },
    {
        q: 'Séance photo grossesse avec le papa',
        r: `<p>Bien sûr ! Le papa (ou votre partenaire) a toute sa place dans la séance : complicité, regards, mains posées sur le ventre… Ces images à deux sont souvent les plus émouvantes. Je vous guide tous les deux pour que chacun soit à l’aise, même celui qui n’aime pas être pris en photo.</p>`,
    },
    {
        q: 'Séance photo grossesse avec les enfants',
        r: `<p>Oui, les grands frères et sœurs sont les bienvenus : c’est une jolie façon de les associer à l’arrivée du bébé. Je prévois des moments de jeu pour qu’ils restent détendus, et nous gardons aussi du temps pour des images de vous seule.</p>`,
    },
    {
        q: 'Faut-il faire des photos grossesse en studio ?',
        r: `<p>Pas forcément. Je vous propose des séances en extérieur, dans la lumière naturelle, ou chez vous pour une ambiance cocooning. L’essentiel est que vous vous sentiez bien : nous choisissons ensemble le cadre qui vous ressemble.</p>`,
    },
];

export const faqNouveauNe = [
    {
        q: 'Quand faire une séance photo nouveau-né ?',
        r: `<p>Idéalement entre le 5e et le 15e jour de vie : bébé dort beaucoup et se laisse facilement installer. Pour cela, réservez pendant la grossesse ; la date exacte est fixée après la naissance.</p>`,
    },
    {
        q: 'Quel âge idéal pour une séance nouveau-né ?',
        r: `<p>Entre 5 et 15 jours. Bébé garde encore ses petites positions enroulées et dort profondément. Plus tard, la séance reste tout à fait possible, avec des images plus éveillées et naturelles.</p>`,
    },
    {
        q: 'Comment préparer bébé pour une séance photo ?',
        r: `<ul><li>Nourrissez-le juste avant la séance (ou à mon arrivée) : un bébé repu est un bébé serein.</li><li>Habillez-le d’une tenue facile à retirer, sans élastique serré, pour éviter les marques sur la peau.</li><li>Évitez le bain juste avant : il est souvent stimulant.</li></ul><p>Et surtout, pas de pression : on suit son rythme.</p>`,
    },
    {
        q: 'Que prévoir pour une séance nouveau-né ?',
        r: `<p>Une pièce bien chauffée (autour de 25 °C, car bébé sera parfois en body ou en couche), de quoi le nourrir, des couches et des lingettes, sa tétine s’il en a une, et du temps devant vous. Je m’occupe du reste : le prêt de tenues est inclus.</p>`,
    },
    {
        q: 'Bébé pleure pendant la séance photo : que faire ?',
        r: `<p>C’est normal, et ce n’est jamais un problème ! On fait une pause, un câlin, une tétée, et on reprend quand il est apaisé. C’est pour cela que la séance dure ${n.duree} : on prend le temps qu’il faut, sans jamais rien forcer.</p>`,
    },
    {
        q: 'Combien de temps dure une séance nouveau-né ?',
        r: `<p>Comptez ${n.duree}. Ce temps permet de suivre le rythme de bébé : tétées, changes, câlins et pauses font partie de la séance.</p>`,
    },
    {
        q: 'Peut-on faire une séance nouveau-né si bébé a plus de 15 jours ?',
        r: `<p>Oui ! Bébé sera sans doute plus éveillé ; nous privilégions alors des images naturelles et spontanées : blotti dans vos bras, ses grands yeux ouverts, ses petites mains, ses premiers regards… C’est tout aussi beau.</p>`,
    },
    {
        q: 'Pourquoi réserver sa séance nouveau-né pendant la grossesse ?',
        r: `<p>Parce que la période idéale est courte : en réservant pendant la grossesse, je bloque une période autour de votre terme ; vous me prévenez à la naissance et nous fixons la date ensemble.</p>`,
    },
    {
        q: 'Séance photo nouveau-né avec les frères et sœurs',
        r: `<p>Avec plaisir ! Je commence par les photos de famille et de fratrie, tant que les aînés sont bien disposés ; ils peuvent ensuite aller jouer pendant que je photographie bébé. Pendant les photos de fratrie, un parent reste toujours à portée de main.</p>`,
    },
    {
        q: 'Comment se passe une séance photo nouveau-né ?',
        r: `<p>J’arrive avec les tenues et nous installons un coin chaud et lumineux, près d’une fenêtre. On commence par nourrir bébé, puis on alterne photos de bébé seul, dans vos bras, avec ses frères et sœurs… Toujours à son rythme, avec des pauses dès qu’il en a besoin. Vous n’avez rien d’autre à faire que profiter.</p>`,
    },
];

export const faqFamille = [
    {
        q: 'Comment habiller sa famille pour une séance photo ?',
        r: `<p>Choisissez une palette de deux ou trois couleurs douces qui s’accordent (beige, blanc, bleu jean, vieux rose, vert sauge…), sans forcément être assortis à l’identique. Évitez les logos, les gros motifs et le noir intégral, et privilégiez des vêtements dans lesquels les enfants peuvent bouger. Si besoin, je vous prête des tenues.</p>`,
    },
    {
        q: 'Que faire si mon enfant ne tient pas en place ?',
        r: `<p>Rien du tout : c’est prévu ! Une séance famille, ce n’est pas rester sagement assis. Les enfants courent, jouent, rient… et c’est justement ce que j’aime photographier. Je m’adapte à leur énergie et je leur propose des jeux pour capter leurs vrais sourires.</p>`,
    },
    {
        q: 'Quelle heure choisir pour une séance photo famille ?',
        r: `<p>En extérieur, la fin de journée offre la plus belle lumière, douce et dorée ; le matin fonctionne très bien aussi. Mais le plus important reste le rythme des enfants : on évite l’heure de la sieste et les moments de fatigue. Nous choisissons le créneau ensemble.</p>`,
    },
    {
        q: 'Séance photo famille en extérieur : comment ça se passe ?',
        r: `<p>Nous nous retrouvons dans un lieu choisi ensemble autour de Dijon : parc, forêt, champ, bord d’eau… Je commence par quelques photos de groupe, puis je laisse place aux jeux, aux câlins et à la balade. Je vous guide sans figer les choses. La séance dure ${f.duree}.</p>`,
    },
    {
        q: 'Que faire avec un bébé pendant une séance famille ?',
        r: `<p>Bébé fait partie de la fête ! La séance s’adapte à ses besoins : tétée, change ou sieste, on fait une pause quand il le faut. Les photos de bébé dans les bras de ses parents ou entouré de ses frères et sœurs sont souvent les plus tendres.</p>`,
    },
    {
        q: 'Comment préparer les enfants à une séance photo ?',
        r: `<p>Présentez-leur la séance comme une sortie en famille, pas comme une corvée, et évitez les « Souris ! » : je m’occupe de les faire rire. Prévoyez un goûter, de l’eau et, pourquoi pas, un doudou ou un jouet. Un enfant reposé et nourri est un enfant détendu.</p>`,
    },
];

export const faqTarifs = [
    {
        q: 'Quels sont les tarifs des séances photo ?',
        r: `<p>Dans le cadre de mon offre de lancement :</p><ul><li>séance grossesse : ${euros(g.prix)} ;</li><li>séance nouveau-né : ${euros(n.prix)} ;</li><li>séance famille : ${euros(f.prix)}.</li></ul><p>Tous les détails sont sur la page <a href="/tarifs">tarifs</a>.</p>`,
    },
    {
        q: 'Qu’est-ce qui est inclus dans le tarif de la séance ?',
        r: `<p>La séance (${g.duree} pour la grossesse et la famille, ${n.duree} pour le nouveau-né), le prêt de tenues, la retouche et ${photosIncluses} photos retouchées en haute définition.</p>`,
    },
    {
        q: 'Les fichiers numériques sont-ils inclus ?',
        r: `<p>Oui : vos photos retouchées vous sont remises en fichiers numériques haute définition, ${c.livraisonMode}.</p>`,
    },
    {
        q: 'Combien de photos sont incluses dans chaque formule ?',
        r: `<p>Chaque formule comprend ${photosIncluses} photos retouchées. Vous hésitez entre plusieurs images ? Vous pouvez en ajouter autant que vous le souhaitez, à ${euros(photoSupplementaire)} la photo.</p>`,
    },
    {
        q: 'Peut-on acheter des photos supplémentaires ?',
        r: `<p>Oui, bien sûr : chaque photo supplémentaire est à ${euros(photoSupplementaire)}. Vous les choisissez dans votre galerie de sélection, en même temps que vos photos incluses.</p>`,
    },
    {
        q: 'Proposes-tu des tirages et des albums photo ?',
        r: `<p>Oui, sur demande : parlez-m’en lors de la réservation et je vous propose des tirages ou un album adaptés à vos envies.</p>`,
    },
    {
        q: 'Proposes-tu des collections ou des forfaits grossesse + nouveau-né ?',
        r: `<p>Pas pour le moment : les séances grossesse (${euros(g.prix)}) et nouveau-né (${euros(n.prix)}) se réservent séparément. Vous pouvez tout à fait réserver les deux en même temps, pendant la grossesse : je bloque alors une période autour de votre terme pour la séance nouveau-né.</p>`,
    },
    {
        q: 'Les frais de déplacement sont-ils compris ?',
        r: `<p>Je me déplace sur ${c.zone} sans frais supplémentaires. Pour un lieu plus éloigné, contactez-moi et nous en parlons ensemble.</p>`,
    },
    {
        q: 'Comment réserver une séance photo ?',
        r: `<p>Via le <a href="/contact">formulaire de contact</a> ou en message privé sur Instagram. Je vous réponds rapidement pour fixer une date, puis votre réservation est confirmée à réception de l’acompte.</p>`,
    },
    {
        q: 'Combien de temps à l’avance faut-il réserver ?',
        r: `<p>Le plus tôt possible : dès le 5e mois pour une séance grossesse, pendant la grossesse pour une séance nouveau-né, et trois à quatre semaines à l’avance pour une séance famille.</p>`,
    },
    {
        q: 'Faut-il verser un acompte pour réserver ?',
        r: `<p>Oui, un acompte de ${c.acompte} du montant de la séance est demandé pour bloquer votre date.</p>`,
    },
    {
        q: 'Quand le solde doit-il être réglé ?',
        r: `<p>Le solde est réglé ${c.solde}.</p>`,
    },
    {
        q: 'Quels moyens de paiement acceptes-tu ?',
        r: `<p>J’accepte les paiements par ${c.paiements}.</p>`,
    },
    {
        q: 'La réservation est-elle définitive une fois l’acompte versé ?',
        r: `<p>Oui : dès réception de l’acompte, votre date vous est réservée. Pour une séance nouveau-né, c’est une période autour de votre terme qui est bloquée, la date exacte étant fixée après la naissance.</p>`,
    },
    {
        q: 'Peut-on offrir une séance photo ?',
        r: `<p>Oui ! Toutes les séances peuvent être offertes grâce à un <a href="/bon-cadeau">bon cadeau</a>, valable ${c.validiteBonCadeau}. Une jolie idée pour une future maman, une naissance ou une fête de famille.</p>`,
    },
    {
        q: 'Que se passe-t-il si je dois reporter ma séance ?',
        r: `<p>Pas d’inquiétude : prévenez-moi au moins ${c.prevenance} à l’avance et nous fixons ensemble une nouvelle date, sans frais.</p>`,
    },
    {
        q: 'Que se passe-t-il si mon bébé arrive avant ou après la date prévue ?',
        r: `<p>C’est prévu ! Envoyez-moi un petit message dès la naissance et nous fixons la séance dans ses premiers jours. Et si bébé pointe le bout de son nez avant votre séance grossesse, nous trouvons ensemble une solution.</p>`,
    },
    {
        q: 'Que se passe-t-il si mon enfant est malade ?',
        r: `<p>La santé passe avant tout : prévenez-moi dès que possible et nous reportons la séance, sans frais.</p>`,
    },
    {
        q: 'Que se passe-t-il en cas de mauvais temps pour une séance en extérieur ?',
        r: `<p>Nous surveillons la météo ensemble : en cas de pluie ou de vent fort, la séance est décalée sans frais à une date qui vous convient.</p>`,
    },
    {
        q: 'Que se passe-t-il si je dois annuler ma séance ?',
        r: `<p>En cas d’annulation, l’acompte reste acquis, car la date vous était réservée. Il peut toutefois être reporté sur une nouvelle date si vous me prévenez au moins ${c.prevenance} à l’avance.</p>`,
    },
];

export const faqGeneral = [
    {
        q: 'Comment se déroule une séance photo avec Mélanie ?',
        r: `<p>Tout commence par un échange pour parler de vos envies, du lieu et des tenues. Le jour J, je vous guide tout en douceur : pas de poses figées, beaucoup de moments naturels. Après la séance, vous choisissez vos photos préférées dans une galerie ; je les retouche et vous les recevez en haute définition.</p>`,
    },
    {
        q: 'Dois-je savoir poser pour une séance photo ?',
        r: `<p>Pas du tout ! C’est mon rôle de vous guider : où vous placer, quoi faire de vos mains, où poser votre regard… Mes indications sont simples, souvent sous forme de petits jeux, pour des images naturelles.</p>`,
    },
    {
        q: 'Je ne suis pas à l’aise devant l’objectif, est-ce un problème ?',
        r: `<p>Pas du tout, c’est très courant ! Nous prenons le temps de discuter, je vous guide pas à pas et l’appareil se fait vite oublier. Mon objectif : que vous passiez un bon moment, et que cela se voie sur les photos.</p>`,
    },
    {
        q: 'Est-ce que tu nous guides pendant toute la séance ?',
        r: `<p>Oui, du début à la fin. Je vous indique les positions, je vous donne de petites choses à faire pour que vos gestes restent naturels, et je laisse aussi place à la spontanéité.</p>`,
    },
    {
        q: 'Combien de temps dure une séance photo ?',
        r: `<p>${g.duree} pour une séance grossesse ou famille, ${n.duree} pour une séance nouveau-né, afin de suivre le rythme de bébé.</p>`,
    },
    {
        q: 'Où se déroulent les séances photo ?',
        r: `<p>Je me déplace sur ${c.zone} : en extérieur, dans un lieu choisi ensemble, ou chez vous, notamment pour les séances nouveau-né.</p>`,
    },
    {
        q: 'Proposes-tu des séances en studio, en extérieur ou à domicile ?',
        r: `<p>Je propose des séances en extérieur, dans la lumière naturelle, et à domicile, pour une ambiance cocooning, idéale avec un nouveau-né.</p>`,
    },
    {
        q: 'Est-il possible de personnaliser la séance selon nos envies ?',
        r: `<p>Évidemment ! Lieu, ambiance, tenues, objets symboliques, moments à immortaliser : nous préparons la séance ensemble pour qu’elle vous ressemble.</p>`,
    },
    {
        q: 'Comment dois-je m’habiller pour ma séance photo ?',
        r: `<p>Des tenues confortables, dans des tons doux et unis qui s’accordent entre eux. Évitez les logos et les gros motifs. Nous en parlons avant la séance, et je peux vous prêter des tenues.</p>`,
    },
    {
        q: 'Est-ce que tu proposes des tenues ou des accessoires ?',
        r: `<p>Oui, le prêt de tenues est inclus dans toutes les séances : robes pour les futures mamans, tenues et accessoires pour les nouveau-nés…</p>`,
    },
    {
        q: 'Faut-il prévoir plusieurs tenues ?',
        r: `<p>Deux tenues suffisent largement pour une séance d’une heure. Pour une séance grossesse, vous pouvez aussi piocher parmi les tenues que je prête.</p>`,
    },
    {
        q: 'Comment préparer les enfants avant une séance photo ?',
        r: `<p>Parlez-leur de la séance comme d’un moment de jeu en famille, prévoyez qu’ils soient reposés et qu’ils aient mangé, et glissez un goûter dans votre sac. Pour le reste, je m’en occupe !</p>`,
    },
    {
        q: 'Que devons-nous apporter le jour de la séance ?',
        r: `<p>Vos tenues, de l’eau, un goûter et un doudou pour les enfants, de quoi nourrir et changer bébé, et si vous le souhaitez des objets qui comptent pour vous (échographie, chaussons, peluche…).</p>`,
    },
    {
        q: 'Que se passe-t-il si mon enfant ne tient pas en place ?',
        r: `<p>C’est tout à fait normal, et même bienvenu ! Je photographie vos enfants tels qu’ils sont : en mouvement, curieux, rieurs. Je m’adapte à leur énergie plutôt que l’inverse.</p>`,
    },
    {
        q: 'Que se passe-t-il si mon bébé pleure pendant la séance ?',
        r: `<p>On prend une pause, le temps d’un câlin ou d’une tétée, puis on reprend tranquillement. Les séances sont pensées pour laisser ce temps à bébé.</p>`,
    },
    {
        q: 'Peut-on faire des pauses pendant la séance ?',
        r: `<p>Bien sûr, autant que nécessaire : tétée, change, goûter, câlin… Les pauses font partie de la séance.</p>`,
    },
    {
        q: 'Les parents et les frères et sœurs peuvent-ils participer ?',
        r: `<p>Oui, avec plaisir ! Parents, frères et sœurs sont les bienvenus, y compris pour les séances grossesse et nouveau-né. Pour d’autres membres de la famille, parlons-en ensemble.</p>`,
    },
    {
        q: 'Peut-on venir avec notre animal de compagnie ?',
        r: `<p>Oui, s’il est à l’aise avec le monde et qu’une personne peut s’en occuper pendant la séance. Il fait partie de la famille, après tout !</p>`,
    },
    {
        q: 'Peut-on faire des photos naturelles sans poser ?',
        r: `<p>C’est même ce que je préfère ! Je vous guide un peu au début, puis je laisse place à vos interactions : c’est là que naissent les plus belles images.</p>`,
    },
    {
        q: 'Les photos sont-elles toutes retouchées ?',
        r: `<p>Oui, toutes les photos que je vous livre sont retouchées une à une : lumière, couleurs, petites imperfections passagères (rougeurs de bébé, petits boutons…), dans le respect du naturel.</p>`,
    },
    {
        q: 'Quel est ton style de retouche ?',
        r: `<p>Un style doux, lumineux et naturel, aux tons chauds. Je ne transforme ni les visages ni les corps : vous restez vous.</p>`,
    },
    {
        q: 'Pouvons-nous choisir nos photos ?',
        r: `<p>Oui : après la séance, je vous envoie une galerie de sélection dans laquelle vous choisissez vos ${photosIncluses} photos préférées, et davantage si vous le souhaitez.</p>`,
    },
    {
        q: 'Les photos brutes sont-elles disponibles ?',
        r: `<p>Non : la retouche fait partie intégrante de mon travail et de mon style. Je ne livre donc que des photos retouchées.</p>`,
    },
    {
        q: 'Combien de photos allons-nous recevoir ?',
        r: `<p>${photosIncluses} photos retouchées sont incluses dans chaque formule, et vous pouvez en ajouter à ${euros(photoSupplementaire)} la photo.</p>`,
    },
    {
        q: 'Comment recevons-nous nos photos ?',
        r: `<p>Vos photos vous sont remises ${c.livraisonMode}. Vous pouvez ensuite les partager facilement avec vos proches.</p>`,
    },
    {
        q: 'Combien de temps faut-il attendre pour recevoir les photos ?',
        r: `<p>Vos photos retouchées vous sont livrées ${c.livraison} après votre sélection.</p>`,
    },
    {
        q: 'Proposes-tu des tirages, albums ou autres supports ?',
        r: `<p>Oui, sur demande : tirages, album… Dites-moi ce qui vous ferait plaisir et je vous fais une proposition.</p>`,
    },
];

export const categoriesFaq = [
    { id: 'general', titre: 'Général', questions: faqGeneral },
    { id: 'grossesse', titre: 'Grossesse', questions: faqGrossesse },
    { id: 'nouveau-ne', titre: 'Nouveau-né', questions: faqNouveauNe },
    { id: 'famille', titre: 'Famille', questions: faqFamille },
    { id: 'reservations-tarifs', titre: 'Réservations & tarifs', questions: faqTarifs },
];
