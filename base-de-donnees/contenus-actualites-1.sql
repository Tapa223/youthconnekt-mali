-- YouthConnekt Mali : textes des actualités retravaillés (chapeaux, articles, catégories, dates, lieux).
-- À importer dans phpMyAdmin APRÈS mise-a-jour-26.sql. Les photos ne sont pas touchées.
SET NAMES utf8mb4;

UPDATE actualites SET titre = 'Lancement officiel de YouthConnekt Mali', slug = 'lancement-officiel-youthconnekt-mali', categorie = 'actualite', date_evenement = '2021-10-07', date_fin = NULL, lieu = 'CICB, Bamako',
  chapeau = 'Le 7 octobre 2021, le Mali a officiellement lancé son chapitre YouthConnekt au Centre international de conférences de Bamako, avec l''appui du PNUD.',
  contenu = 'La cérémonie de lancement s''est tenue le jeudi 7 octobre 2021 au Centre international de conférences de Bamako, sous la présidence de Mossa Ag Attaher, ministre de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, en présence d''Alfredo Teixeira, représentant résident adjoint du PNUD au Mali.
Le Mali avait endossé le programme en novembre 2020, dans le cadre de sa stratégie nationale pour la jeunesse.
Un programme pensé en Afrique, pour l''Afrique
YouthConnekt Africa a été créé en 2012 par le Gouvernement du Rwanda, en partenariat avec le PNUD. C''est une solution africaine qui vise à connecter la jeunesse du continent et à l''autonomiser en développant ses connaissances, ses expériences et ses compétences, tout en investissant dans ses idées et ses initiatives.
Placé sous la tutelle du ministère de la Jeunesse et des Sports, YouthConnekt Mali reprend ce modèle. Il s''inscrit dans la Stratégie du PNUD pour la jeunesse, qui reconnaît le rôle essentiel des jeunes dans le développement humain durable et leur volonté de prendre une part active aux décisions économiques, sociales et politiques.
Ce qu''ils ont dit
« Notre pays, au regard de sa population extrêmement jeune, environ 70 %, doit se saisir de cette opportunité pour rendre plus active la jeunesse, la mettre à l''abri des déviances et de l''oisiveté, et créer les conditions de sa pleine participation à la construction nationale. »
— Mossa Ag Attaher, ministre de la Jeunesse et des Sports
Le ministre a également souhaité que l''initiative travaille en synergie avec les autres plateformes existantes, pour favoriser l''engagement des jeunes.
« L''engagement et la participation des jeunes à la consolidation de la paix et au processus électoral, axes d''intervention de YouthConnekt au Mali, jouent un rôle important au regard de la crise sécuritaire, socio-économique et politique, exacerbée par la pandémie de COVID-19. »
— Alfredo Teixeira, représentant résident adjoint du PNUD
Des ambitions à l''échelle du continent
À l''échelle de l''Afrique, YouthConnekt s''est fixé des objectifs clairs :
- créer 10 millions d''emplois durables dans les secteurs émergents ;
- offrir 25 millions d''opportunités de formation et d''insertion professionnelle ;
- connecter 100 millions de jeunes.',
  modifie_le = NOW()
WHERE id = 25;

UPDATE actualites SET titre = 'Le Mali au Forum YouthConnekt Sahel 2026 à Niamey', slug = 'mali-forum-youthconnekt-sahel-2026-niamey', categorie = 'evenement', date_evenement = '2026-08-10', date_fin = '2026-08-12', lieu = 'Niamey, Niger',
  chapeau = 'Du 10 au 12 août 2026, la 3ᵉ édition du Forum YouthConnekt Sahel a réuni à Niamey la jeunesse de plus de dix pays. Le Mali y était présent au plus haut niveau, avec une délégation conduite par le ministre de la Jeunesse et des Sports.',
  contenu = 'La 3ᵉ édition du Forum YouthConnekt Sahel s''est tenue du 10 au 12 août 2026 au Centre international de conférences Mahatma Gandhi de Niamey, au Niger, sous le thème « Jeunesse sahélienne au cœur de la sécurité et de l''innovation, pour un Sahel souverain, résilient et prospère ».
Organisé par l''État du Niger avec le Système des Nations unies (PNUD, UNFPA, UNICEF), ce forum sous-régional veut faire de la jeunesse un levier de transformation économique, sociale et sécuritaire du Sahel. Il a rassemblé près de 2 500 participants, sur place et en ligne, dont 500 jeunes innovateurs venus de plus de dix pays : Mali, Niger, Burkina Faso, Tchad, Togo, Sénégal, Mauritanie, Guinée, Cameroun, Nigeria et Gambie.
Quatre grands axes de travail
Les échanges se sont organisés autour de quatre axes :
- jeunesse et refondation des États du Sahel ;
- entrepreneuriat, innovation et transformation économique verte ;
- jeunesse, paix, cohésion sociale et prévention de l''extrémisme violent ;
- jeunesse, climat et résilience des territoires.
Au programme : des panels de haut niveau et des dialogues entre générations, le Sahel Innovation Lab où de jeunes porteurs de projets ont présenté leurs solutions à des investisseurs, le Marché du Sahel et ses rencontres d''affaires, et la Nuit de l''excellence sahélienne, qui a récompensé les meilleures initiatives de jeunes.
Une délégation malienne de premier plan
Arrivé à Niamey le 9 août, Abdoul Kassim Ibrahim Fomba, ministre de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, a conduit la délégation malienne. Il a été accueilli par son homologue nigérien, Sidi Mohamed Almahmoud.
Lors de la réunion ministérielle et des panels, le ministre a rappelé la responsabilité historique de la jeunesse africaine dans la stabilisation de ses pays, et invité les jeunes à accompagner leurs propositions d''actes concrets.
De jeunes Maliens de 16 à 35 ans ont pris part aux ateliers techniques, présenté leur savoir-faire au Marché du Sahel et défendu leurs projets devant des partenaires techniques et financiers.
Et maintenant
Le forum s''est clôturé par l''adoption de la Déclaration de Niamey 2026, feuille de route régionale à laquelle le Mali a contribué : réduire la fracture numérique, mieux intégrer la jeunesse rurale et promouvoir une gestion souveraine des ressources naturelles.',
  modifie_le = NOW()
WHERE id = 26;

UPDATE actualites SET titre = 'Le Mali au Sommet YouthConnekt Africa 2023 à Nairobi', slug = 'mali-sommet-youthconnekt-africa-2023-nairobi', categorie = 'evenement', date_evenement = '2023-12-08', date_fin = '2023-12-12', lieu = 'Nairobi, Kenya',
  chapeau = 'Du 8 au 12 décembre 2023, de jeunes Maliens ont participé à Nairobi au 6ᵉ Sommet YouthConnekt Africa. Le Mali y a remporté le prix du hackathon de l''innovation pour la paix et la sécurité.',
  contenu = 'Le 6ᵉ Sommet YouthConnekt Africa s''est tenu du 8 au 12 décembre 2023 à Nairobi, au Kenya, autour du thème « Les jeunes innovent pour une renaissance africaine sans frontières ».
Organisé par le Gouvernement kényan avec le Programme des Nations unies pour le développement (PNUD), ce rendez-vous continental a réuni des milliers de jeunes venus de tout le continent, aux côtés de décideurs politiques et de partenaires au développement.
La participation du Mali
De jeunes entrepreneurs et représentants maliens ont pris part au sommet, avec l''appui du PNUD Mali. Pour plusieurs d''entre eux, c''était l''occasion de participer aux foires, de nouer des partenariats régionaux et de rencontrer de nouveaux financeurs. C''est le cas d''Astan Tangara, fondatrice de l''entreprise Jeke Balo.
Le Mali récompensé
Temps fort de cette édition pour la délégation : le Mali a remporté le prix du hackathon de l''innovation consacré à la paix et à la sécurité.',
  modifie_le = NOW()
WHERE id = 27;

UPDATE actualites SET titre = 'Le Mali au premier Forum YouthConnekt Sahel à Dakar', slug = 'mali-forum-youthconnekt-sahel-2021-dakar', categorie = 'evenement', date_evenement = '2021-11-30', date_fin = '2021-12-03', lieu = 'Dakar, Sénégal',
  chapeau = 'Du 30 novembre au 3 décembre 2021, le Mali a pris part à Dakar à la toute première édition du Forum YouthConnekt Sahel. C''est un jeune Malien qui y a présenté la déclaration finale au nom de la jeunesse sahélienne.',
  contenu = 'La première édition du Forum YouthConnekt Sahel s''est tenue du 30 novembre au 3 décembre 2021 à Dakar, au Sénégal, autour du thème « Connecter les jeunes du Sahel pour la paix et la prospérité ». Une délégation malienne de jeunes entrepreneurs, de responsables associatifs et de représentants du ministère en charge de la Jeunesse y a participé.
Dix pays du Sahel réunis
Le Mali faisait partie des dix pays représentés, aux côtés du Burkina Faso, du Cameroun, de la Gambie, de la Guinée, de la Mauritanie, du Niger, du Nigeria, du Sénégal et du Tchad.
La délégation rassemblait de jeunes porteurs de projets, des acteurs de la société civile et des créateurs culturels. Ils ont animé des stands pour présenter le savoir-faire malien, l''artisanat et des solutions technologiques développées au Mali.
Faire entendre la voix des jeunes Maliens
Pendant quatre jours, les jeunes Maliens ont participé aux plénières et aux ateliers, portés par le mot d''ordre du forum : « Ne faites rien pour nous, sans nous ». Ils ont échangé avec des décideurs, des chefs d''entreprise et les agences des Nations unies sur les priorités de la jeunesse malienne : l''emploi, la cohésion sociale et la résilience face aux crises sécuritaires et climatiques. Ils ont aussi partagé leurs initiatives de consolidation de la paix et d''engagement citoyen dans les régions.
Le Sahel Innovate Challenge
Temps fort du forum, ce concours a retenu dix finalistes, un par pays, parmi plus de 300 candidatures. Chacun a reçu une subvention de démarrage : de 20 000 à 3 000 dollars pour les cinq premiers, 1 000 dollars pour les cinq suivants. Le représentant du Mali a ensuite rejoint le programme régional d''accompagnement du PNUD destiné aux dix lauréats.
Le Mali, porte-parole de la jeunesse du Sahel
À la clôture, c''est Habib Dakouo Mani, alors président du Conseil national de la jeunesse du Mali, qui a présenté la déclaration finale du forum devant les ministres et les responsables des Nations unies. Il y a porté l''appel de toutes les délégations à inclure les jeunes femmes, les personnes vivant avec un handicap et les jeunes entrepreneurs dans les décisions publiques.',
  modifie_le = NOW()
WHERE id = 28;

UPDATE actualites SET titre = 'Le Mali au Sommet YouthConnekt Africa 2022 à Kigali', slug = 'mali-sommet-youthconnekt-africa-2022-kigali', categorie = 'evenement', date_evenement = '2022-10-13', date_fin = '2022-10-15', lieu = 'Kigali, Rwanda',
  chapeau = 'Du 13 au 15 octobre 2022, un an après le lancement de son chapitre national, le Mali a pris part à Kigali à la 5ᵉ édition du Sommet YouthConnekt Africa.',
  contenu = 'La 5ᵉ édition du Sommet YouthConnekt Africa s''est tenue du 13 au 15 octobre 2022 à Kigali, au Rwanda, et a réuni près de 10 000 participants autour du thème « Accélérer l''investissement dans la jeunesse : une jeunesse résiliente, une Afrique résiliente ».
Un an après le lancement
YouthConnekt Mali avait été lancé en octobre 2021 avec l''appui du PNUD Mali et du ministère en charge de la Jeunesse, pour offrir aux jeunes Maliens des espaces de réseautage, de formation et d''accès au financement. Kigali était l''une de ses premières occasions de rencontrer les autres chapitres du continent.
Au programme pour la délégation
Organisé à la BK Arena et à l''Intare Arena, le sommet a permis aux délégations nationales de :
- rencontrer des investisseurs du continent ;
- suivre des masterclasses sur le numérique, le climat, la santé et le commerce ;
- partager leurs expériences en matière d''insertion des jeunes.',
  modifie_le = NOW()
WHERE id = 29;

UPDATE actualites SET titre = 'Le Mali au Sommet YouthConnekt Africa 2024 à Kigali', slug = 'mali-sommet-youthconnekt-africa-2024-kigali', categorie = 'evenement', date_evenement = '2024-11-08', date_fin = '2024-11-11', lieu = 'Kigali, Rwanda',
  chapeau = 'Du 8 au 11 novembre 2024, une délégation malienne a participé à Kigali à la 7ᵉ édition du Sommet YouthConnekt Africa, consacrée à l''emploi des jeunes par les compétences.',
  contenu = 'La 7ᵉ édition du Sommet YouthConnekt Africa s''est tenue du 8 au 11 novembre 2024 à Kigali, au Rwanda, autour du thème de l''emploi des jeunes par le développement des compétences.
Une délégation malienne mobilisée
Le Mali était représenté par une délégation conduite par Mme Bagayoko Aminata Traoré, ministre de l''Emploi et de la Formation professionnelle, accompagnée de partenaires techniques et institutionnels, dont le PNUD Mali et l''UNFPA Mali.
Les échanges de la délégation ont porté sur les stratégies nationales d''insertion professionnelle, le passage de la formation à l''emploi et les opportunités du secteur agroalimentaire.
Les jeunes innovateurs aussi
De jeunes innovateurs maliens ont pris part aux activités du sommet, notamment au mini-hackathon de l''initiative African Girls Can Code.',
  modifie_le = NOW()
WHERE id = 30;
