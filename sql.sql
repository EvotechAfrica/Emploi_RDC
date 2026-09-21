-- =========================================================
-- PROJET : Emploi_RDC
-- Description : Plateforme de publication d'offres d'emploi (RDC)
-- SGBD : MySQL 
-- Encodage : utf8mb4
-- =========================================================

DROP DATABASE IF EXISTS emploi_rdc;
CREATE DATABASE emploi_rdc
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE emploi_rdc;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- 1. UTILISATEUR (table pivot pour l'authentification)
-- =========================================================
CREATE TABLE utilisateur (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(150) NOT NULL UNIQUE,
    telephone       VARCHAR(20)  UNIQUE,
    mot_de_passe    VARCHAR(255) NOT NULL,
    role            ENUM('admin','IT') NOT NULL DEFAULT 'admin',
    statut          ENUM('actif','inactif','suspendu') NOT NULL DEFAULT 'actif',
    email_verifie   BOOLEAN NOT NULL DEFAULT FALSE,
    deux_fa_active  BOOLEAN NOT NULL DEFAULT FALSE,
    deux_fa_secret  VARCHAR(255),
    derniere_connexion DATETIME,
    deleted_at      DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_statut (statut),
    INDEX idx_deleted (deleted_at)
) ENGINE=InnoDB;

-- =========================================================
-- 2. COMPANY (entreprise)
-- =========================================================
CREATE TABLE company (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id  BIGINT UNSIGNED NOT NULL UNIQUE,
    nom             VARCHAR(150) NOT NULL,
    slug            VARCHAR(180) NOT NULL UNIQUE,
    description     TEXT,
    secteur         VARCHAR(100),
    taille          ENUM('1-10','11-50','51-200','201-500','500+') DEFAULT '1-10',
    adresse         VARCHAR(255),
    ville           VARCHAR(100),
    province        VARCHAR(100),
    email  VARCHAR(150),
    telephone_contact VARCHAR(20),
    pays            VARCHAR(80) DEFAULT 'RD Congo',
    site_web        VARCHAR(255),
    logo_url        VARCHAR(500),
    rccm            VARCHAR(100),
    id_national     VARCHAR(100),
    verifiee        BOOLEAN NOT NULL DEFAULT FALSE,
    note_moyenne    DECIMAL(3,2) DEFAULT 0.00,
    nb_avis         INT UNSIGNED DEFAULT 0,
    password_hash    VARCHAR(255),
    deleted_at      DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ville (ville),
    INDEX idx_province (province),
    INDEX idx_secteur (secteur),
    FULLTEXT INDEX ft_company (nom, description)
) ENGINE=InnoDB;

-- =========================================================
-- 3. CANDIDAT_PROFIL
-- =========================================================
CREATE TABLE candidat (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(100) NOT NULL,
    post_nom        VARCHAR(100),
    prenom          VARCHAR(100) NOT NULL,
    date_naissance  DATE,
    genre           ENUM('M','F','autre'),
    adresse         VARCHAR(255),
    ville           VARCHAR(100),
    province        VARCHAR(100),
    pays            VARCHAR(80) DEFAULT 'RD Congo',
    email           VARCHAR(150) NOT NULL UNIQUE,
    telephone       VARCHAR(20) UNIQUE,
    password_hash    VARCHAR(255) NOT NULL,
    deleted_at      DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE candidat_profil (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    candidat_id     BIGINT UNSIGNED NOT NULL UNIQUE,   
    surnom          VARCHAR(150),
    bio             TEXT,
    cv_url          VARCHAR(500),
    photo_url       VARCHAR(500),
    linkedin        VARCHAR(255),
    portfolio       VARCHAR(255),
    experience_annees INT UNSIGNED DEFAULT 0,
    niveau_etude    ENUM('primaire','secondaire','graduat','licence','master','doctorat'),
    disponibilite   ENUM('immediate','1_mois','3_mois','negociable') DEFAULT 'immediate',
    pretention_salariale DECIMAL(12,2),
    devise          VARCHAR(5) DEFAULT 'USD',
    note_moyenne    DECIMAL(3,2) DEFAULT 0.00,
    nb_avis         INT UNSIGNED DEFAULT 0,
    deleted_at      DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_candidat_candidat FOREIGN KEY (candidat_id)
        REFERENCES candidat(id) ON DELETE CASCADE,
    INDEX idx_ville (ville),
    INDEX idx_province (province),
    INDEX idx_surnom (surnom),
    FULLTEXT INDEX ft_candidat (surnom, bio)
) ENGINE=InnoDB;

-- =========================================================
-- 4. CATEGORIE (catégories d'offres)
-- =========================================================
CREATE TABLE categorie (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(100) NOT NULL UNIQUE,
    slug            VARCHAR(120) NOT NULL UNIQUE,
    description     VARCHAR(255),
    icone           VARCHAR(100),
    ordre_affichage INT DEFAULT 0,
    active          BOOLEAN NOT NULL DEFAULT TRUE,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- 5. COMPETENCE (référentiel skills)
-- =========================================================
CREATE TABLE competence (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(100) NOT NULL UNIQUE,
    slug            VARCHAR(120) NOT NULL UNIQUE,
    categorie_id    BIGINT UNSIGNED NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_competence_categorie FOREIGN KEY (categorie_id)
        REFERENCES categorie(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================
-- 6. PLAN (packs tarifaires)
-- =========================================================
CREATE TABLE plan (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(100) NOT NULL,
    slug            VARCHAR(120) NOT NULL UNIQUE,
    description     TEXT,
    prix            DECIMAL(12,2) NOT NULL,
    devise          VARCHAR(5) DEFAULT 'USD',
    duree_jours     INT UNSIGNED NOT NULL,
    nb_offres       INT NOT NULL DEFAULT 1,      -- -1 = illimité
    mise_en_avant   BOOLEAN DEFAULT FALSE,
    duree_offre_jours INT UNSIGNED DEFAULT 30,
    actif           BOOLEAN DEFAULT TRUE,
    ordre_affichage INT DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- 7. OFFRE
-- =========================================================
CREATE TABLE offre (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      BIGINT UNSIGNED NOT NULL,
    categorie_id    BIGINT UNSIGNED NOT NULL,
    titre           VARCHAR(200) NOT NULL,
    slug            VARCHAR(220) NOT NULL UNIQUE,
    description     LONGTEXT NOT NULL,
    missions        TEXT,
    profil_recherche TEXT,
    type_contrat    ENUM('CDI','CDD','Stage','Freelance','Temps_partiel','Interim') NOT NULL,
    mode_travail    ENUM('presentiel','hybride','teletravail') DEFAULT 'presentiel',
    experience_requise ENUM('debutant','1-2_ans','3-5_ans','5+_ans') DEFAULT 'debutant',
    niveau_etude    ENUM('primaire','secondaire','graduat','licence','master','doctorat'),
    salaire_min     DECIMAL(12,2),
    salaire_max     DECIMAL(12,2),
    devise          VARCHAR(5) DEFAULT 'USD',
    ville           VARCHAR(100),
    province        VARCHAR(100),
    pays            VARCHAR(80) DEFAULT 'RD Congo',
    date_limite     DATE,
    statut          ENUM('brouillon','en_attente_paiement','publiee','expiree','archivee')
                    NOT NULL DEFAULT 'brouillon',
    plan_id         BIGINT UNSIGNED NULL,
    vues            INT UNSIGNED NOT NULL DEFAULT 0,
    date_publication DATETIME,
    deleted_at      DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_offre_company FOREIGN KEY (company_id)
        REFERENCES company(id) ON DELETE CASCADE,
    CONSTRAINT fk_offre_categorie FOREIGN KEY (categorie_id)
        REFERENCES categorie(id) ON DELETE RESTRICT,
    INDEX idx_statut (statut),
    INDEX idx_type_contrat (type_contrat),
    INDEX idx_ville (ville),
    INDEX idx_province (province),
    INDEX idx_date_pub (date_publication),
    INDEX idx_deleted (deleted_at),
    INDEX idx_company_statut (company_id, statut),
    FULLTEXT INDEX ft_offre (titre, description, profil_recherche)
) ENGINE=InnoDB;

-- =========================================================
-- 8. OFFRE_COMPETENCE (liaison N-N)
-- =========================================================
CREATE TABLE offre_competence (
    offre_id        BIGINT UNSIGNED NOT NULL,
    competence_id   BIGINT UNSIGNED NOT NULL,
    obligatoire     BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (offre_id, competence_id),
    CONSTRAINT fk_oc_offre FOREIGN KEY (offre_id)
        REFERENCES offre(id) ON DELETE CASCADE,
    CONSTRAINT fk_oc_competence FOREIGN KEY (competence_id)
        REFERENCES competence(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- 9. CANDIDAT_COMPETENCE (liaison N-N)
-- =========================================================
CREATE TABLE candidat_competence (
    candidat_id     BIGINT UNSIGNED NOT NULL,
    competence_id   BIGINT UNSIGNED NOT NULL,
    niveau          ENUM('debutant','intermediaire','avance','expert') DEFAULT 'intermediaire',
    annees          INT UNSIGNED DEFAULT 0,
    PRIMARY KEY (candidat_id, competence_id),
    CONSTRAINT fk_cc_candidat FOREIGN KEY (candidat_id)
        REFERENCES candidat_profil(id) ON DELETE CASCADE,
    CONSTRAINT fk_cc_competence FOREIGN KEY (competence_id)
        REFERENCES competence(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- 10. PAIEMENT
-- =========================================================
CREATE TABLE paiement (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      BIGINT UNSIGNED NOT NULL,
    offre_id        BIGINT UNSIGNED NULL,
    plan_id         BIGINT UNSIGNED NULL,
    reference       VARCHAR(100) NOT NULL UNIQUE,
    transaction_id  VARCHAR(150),
    montant         DECIMAL(12,2) NOT NULL,
    devise          VARCHAR(5) NOT NULL DEFAULT 'USD',
    methode         ENUM('mobile_money','carte_bancaire','virement','paypal','autre'),
    operateur       VARCHAR(50),
    statut          ENUM('en_attente','reussi','echoue','rembourse') NOT NULL DEFAULT 'en_attente',
    date_paiement   DATETIME,
    payload         JSON,
    ip              VARCHAR(45),
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_paiement_company FOREIGN KEY (company_id)
        REFERENCES company(id) ON DELETE CASCADE,
    CONSTRAINT fk_paiement_offre FOREIGN KEY (offre_id)
        REFERENCES offre(id) ON DELETE SET NULL,
    CONSTRAINT fk_paiement_plan FOREIGN KEY (plan_id)
        REFERENCES plan(id) ON DELETE SET NULL,
    INDEX idx_statut (statut),
    INDEX idx_transaction (transaction_id),
    INDEX idx_company_date (company_id, created_at)
) ENGINE=InnoDB;

-- =========================================================
-- 11. ABONNEMENT
-- =========================================================
CREATE TABLE abonnement (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      BIGINT UNSIGNED NOT NULL,
    plan_id         BIGINT UNSIGNED NOT NULL,
    paiement_id     BIGINT UNSIGNED NOT NULL,
    offres_restantes INT UNSIGNED NOT NULL,
    date_debut      DATETIME NOT NULL,
    date_fin        DATETIME NOT NULL,
    statut          ENUM('actif','expire','annule') DEFAULT 'actif',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_abo_company FOREIGN KEY (company_id)
        REFERENCES company(id) ON DELETE CASCADE,
    CONSTRAINT fk_abo_plan FOREIGN KEY (plan_id)
        REFERENCES plan(id) ON DELETE RESTRICT,
    CONSTRAINT fk_abo_paiement FOREIGN KEY (paiement_id)
        REFERENCES paiement(id) ON DELETE RESTRICT,
    INDEX idx_statut (statut),
    INDEX idx_company_statut (company_id, statut)
) ENGINE=InnoDB;

-- =========================================================
-- 12. CANDIDATURE
-- =========================================================
CREATE TABLE candidature (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    offre_id        BIGINT UNSIGNED NOT NULL,
    candidat_id     BIGINT UNSIGNED NOT NULL,
    lettre_motivation TEXT,
    cv_url          VARCHAR(500),
    statut          ENUM('envoyee','vue','preselectionnee','entretien',
                         'acceptee','refusee','retiree') NOT NULL DEFAULT 'envoyee',
    note_entreprise TINYINT UNSIGNED,
    commentaire     TEXT,
    deleted_at      DATETIME NULL,
    date_candidature DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cand_offre FOREIGN KEY (offre_id)
        REFERENCES offre(id) ON DELETE CASCADE,
    CONSTRAINT fk_cand_profil FOREIGN KEY (candidat_id)
        REFERENCES candidat_profil(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_offre_candidat (offre_id, candidat_id),
    INDEX idx_statut (statut),
    INDEX idx_date (date_candidature),
    INDEX idx_offre_statut (offre_id, statut)
) ENGINE=InnoDB;

-- =========================================================
-- 13. CANDIDATURE_STATUT_HISTORIQUE
-- =========================================================
CREATE TABLE candidature_statut_historique (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    candidature_id  BIGINT UNSIGNED NOT NULL,
    ancien_statut   VARCHAR(50),
    nouveau_statut  VARCHAR(50) NOT NULL,
    modifie_par     BIGINT UNSIGNED NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_csh_candidature FOREIGN KEY (candidature_id)
        REFERENCES candidature(id) ON DELETE CASCADE,
    CONSTRAINT fk_csh_user FOREIGN KEY (modifie_par)
        REFERENCES utilisateur(id) ON DELETE SET NULL,
    INDEX idx_cand_date (candidature_id, created_at)
) ENGINE=InnoDB;

-- =========================================================
-- 15. CONVERSATION
-- =========================================================
CREATE TABLE conversation (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      BIGINT UNSIGNED NOT NULL,
    candidat_id     BIGINT UNSIGNED NOT NULL,
    offre_id        BIGINT UNSIGNED NULL,
    candidature_id  BIGINT UNSIGNED NULL,
    dernier_message_at DATETIME,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_conv_company FOREIGN KEY (company_id)
        REFERENCES company(id) ON DELETE CASCADE,
    CONSTRAINT fk_conv_candidat FOREIGN KEY (candidat_id)
        REFERENCES candidat_profil(id) ON DELETE CASCADE,
    CONSTRAINT fk_conv_offre FOREIGN KEY (offre_id)
        REFERENCES offre(id) ON DELETE SET NULL,
    CONSTRAINT fk_conv_candidature FOREIGN KEY (candidature_id)
        REFERENCES candidature(id) ON DELETE SET NULL,
    UNIQUE KEY uniq_conv (company_id, candidat_id, offre_id),
    INDEX idx_dernier_msg (dernier_message_at)
) ENGINE=InnoDB;

-- =========================================================
-- 16. MESSAGE
-- =========================================================
CREATE TABLE message (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL,
    expediteur_id   BIGINT UNSIGNED NOT NULL,
    contenu         TEXT NOT NULL,
    piece_jointe    VARCHAR(500),
    lu              BOOLEAN NOT NULL DEFAULT FALSE,
    date_envoi      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_msg_conv FOREIGN KEY (conversation_id)
        REFERENCES conversation(id) ON DELETE CASCADE,
    CONSTRAINT fk_msg_user FOREIGN KEY (expediteur_id)
        REFERENCES utilisateur(id) ON DELETE CASCADE,
    INDEX idx_conv_date (conversation_id, date_envoi),
    INDEX idx_non_lu (conversation_id, lu)
) ENGINE=InnoDB;

-- =========================================================
-- 17. FAVORI
-- =========================================================
CREATE TABLE favori (
    candidat_id     BIGINT UNSIGNED NOT NULL,
    offre_id        BIGINT UNSIGNED NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (candidat_id, offre_id),
    CONSTRAINT fk_fav_candidat FOREIGN KEY (candidat_id)
        REFERENCES candidat_profil(id) ON DELETE CASCADE,
    CONSTRAINT fk_fav_offre FOREIGN KEY (offre_id)
        REFERENCES offre(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- 18. ALERTE_EMPLOI
-- =========================================================
CREATE TABLE alerte_emploi (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    candidat_id     BIGINT UNSIGNED NOT NULL,
    mot_cle         VARCHAR(200),
    categorie_id    BIGINT UNSIGNED NULL,
    ville           VARCHAR(100),
    type_contrat    VARCHAR(50),
    frequence       ENUM('immediate','quotidien','hebdomadaire') DEFAULT 'quotidien',
    active          BOOLEAN DEFAULT TRUE,
    derniere_envoi  DATETIME,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_alerte_candidat FOREIGN KEY (candidat_id)
        REFERENCES candidat_profil(id) ON DELETE CASCADE,
    CONSTRAINT fk_alerte_categorie FOREIGN KEY (categorie_id)
        REFERENCES categorie(id) ON DELETE SET NULL,
    INDEX idx_active (active)
) ENGINE=InnoDB;

-- =========================================================
-- 19. AVIS (réputation)
-- =========================================================
CREATE TABLE avis (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    auteur_id       BIGINT UNSIGNED NOT NULL,
    cible_company_id BIGINT UNSIGNED NULL,
    cible_candidat_id BIGINT UNSIGNED NULL,
    note            TINYINT UNSIGNED NOT NULL,
    commentaire     TEXT,
    publie          BOOLEAN DEFAULT TRUE,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_avis_auteur FOREIGN KEY (auteur_id)
        REFERENCES utilisateur(id) ON DELETE CASCADE,
    CONSTRAINT fk_avis_company FOREIGN KEY (cible_company_id)
        REFERENCES company(id) ON DELETE CASCADE,
    CONSTRAINT fk_avis_candidat FOREIGN KEY (cible_candidat_id)
        REFERENCES candidat_profil(id) ON DELETE CASCADE,
    CONSTRAINT chk_avis_cible CHECK (
        (cible_company_id IS NOT NULL AND cible_candidat_id IS NULL) OR
        (cible_company_id IS NULL AND cible_candidat_id IS NOT NULL)
    ),
    CONSTRAINT chk_note CHECK (note BETWEEN 1 AND 5),
    UNIQUE KEY uniq_avis_company (auteur_id, cible_company_id),
    UNIQUE KEY uniq_avis_candidat (auteur_id, cible_candidat_id)
) ENGINE=InnoDB;

-- =========================================================
-- 20. SIGNALEMENT
-- =========================================================
CREATE TABLE signalement (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    signale_par     BIGINT UNSIGNED NOT NULL,
    type_cible      ENUM('offre','utilisateur','message','company') NOT NULL,
    cible_id        BIGINT UNSIGNED NOT NULL,
    motif           VARCHAR(200) NOT NULL,
    description     TEXT,
    statut          ENUM('nouveau','en_cours','traite','rejete') DEFAULT 'nouveau',
    traite_par      BIGINT UNSIGNED NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_signal_user FOREIGN KEY (signale_par)
        REFERENCES utilisateur(id) ON DELETE CASCADE,
    CONSTRAINT fk_signal_traite FOREIGN KEY (traite_par)
        REFERENCES utilisateur(id) ON DELETE SET NULL,
    INDEX idx_statut (statut),
    INDEX idx_type_cible (type_cible, cible_id)
) ENGINE=InnoDB;

-- =========================================================
-- 21. NOTIFICATION
-- =========================================================
CREATE TABLE notification (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id  BIGINT UNSIGNED NOT NULL,
    type            VARCHAR(50) NOT NULL,
    titre           VARCHAR(200) NOT NULL,
    contenu         TEXT,
    lien            VARCHAR(500),
    lue             BOOLEAN NOT NULL DEFAULT FALSE,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_user FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateur(id) ON DELETE CASCADE,
    INDEX idx_user_lue (utilisateur_id, lue),
    INDEX idx_date (created_at)
) ENGINE=InnoDB;

-- =========================================================
-- 22. OFFRE_VUE (analytics)
-- =========================================================
CREATE TABLE offre_vue (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    offre_id        BIGINT UNSIGNED NOT NULL,
    utilisateur_id  BIGINT UNSIGNED NULL,
    ip              VARCHAR(45),
    referer         VARCHAR(500),
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vue_offre FOREIGN KEY (offre_id)
        REFERENCES offre(id) ON DELETE CASCADE,
    CONSTRAINT fk_vue_user FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateur(id) ON DELETE SET NULL,
    INDEX idx_offre_date (offre_id, created_at)
) ENGINE=InnoDB;

-- =========================================================
-- 23. REFRESH_TOKEN (sécurité)
-- =========================================================
CREATE TABLE refresh_token (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id  BIGINT UNSIGNED NOT NULL,
    token_hash      VARCHAR(255) NOT NULL UNIQUE,
    expire_at       DATETIME NOT NULL,
    revoque         BOOLEAN DEFAULT FALSE,
    ip              VARCHAR(45),
    user_agent      VARCHAR(255),
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rt_user FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateur(id) ON DELETE CASCADE,
    INDEX idx_user_revoque (utilisateur_id, revoque)
) ENGINE=InnoDB;


-- =========================================================
-- 25. LOGIN_ATTEMPT (anti-brute-force)
-- =========================================================
CREATE TABLE login_attempt (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(150) NOT NULL,
    ip              VARCHAR(45) NOT NULL,
    succes          BOOLEAN NOT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email_date (email, created_at),
    INDEX idx_ip_date (ip, created_at)
) ENGINE=InnoDB;

-- =========================================================
-- 26. AUDIT_LOG
-- =========================================================
CREATE TABLE audit_log (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id  BIGINT UNSIGNED NULL,
    action          VARCHAR(100) NOT NULL,
    entite          VARCHAR(50) NOT NULL,
    entite_id       BIGINT UNSIGNED,
    ancienne_valeur JSON,
    nouvelle_valeur JSON,
    ip              VARCHAR(45),
    user_agent      VARCHAR(255),
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateur(id) ON DELETE SET NULL,
    INDEX idx_entite (entite, entite_id),
    INDEX idx_user_date (utilisateur_id, created_at),
    INDEX idx_action (action)
) ENGINE=InnoDB;

-- =========================================================
-- 27. RATE_LIMIT
-- =========================================================
CREATE TABLE rate_limit (
    cle             VARCHAR(255) NOT NULL PRIMARY KEY,
    compteur        INT UNSIGNED NOT NULL DEFAULT 1,
    expire_at       DATETIME NOT NULL,
    INDEX idx_expire (expire_at)
) ENGINE=InnoDB;


-- =========================================================
-- DONNÉES DE DÉMARRAGE
-- =========================================================

-- Catégories
INSERT INTO categorie (nom, slug, description, ordre_affichage) VALUES
('Informatique & IT', 'informatique-it', 'Développement, réseaux, support technique', 1),
('Finance & Comptabilité', 'finance-comptabilite', 'Banque, comptabilité, audit', 2),
('Santé', 'sante', 'Médecine, soins, pharmacie', 3),
('Éducation & Formation', 'education-formation', 'Enseignement, formation', 4),
('Commerce & Vente', 'commerce-vente', 'Vente, marketing, business development', 5),
('Construction & BTP', 'construction-btp', 'Génie civil, chantier, architecture', 6),
('Transport & Logistique', 'transport-logistique', 'Chauffeur, supply chain', 7),
('Administration & RH', 'administration-rh', 'Ressources humaines, secrétariat', 8),
('Agriculture', 'agriculture', 'Agronomie, élevage', 9),
('Mines & Énergie', 'mines-energie', 'Extraction, énergie', 10);

-- Compétences IT
INSERT INTO competence (nom, slug, categorie_id) VALUES
('PHP', 'php', 1), ('Laravel', 'laravel', 1), ('Symfony', 'symfony', 1),
('MySQL', 'mysql', 1), ('PostgreSQL', 'postgresql', 1),
('JavaScript', 'javascript', 1), ('React', 'react', 1), ('Vue.js', 'vuejs', 1),
('Node.js', 'nodejs', 1), ('Python', 'python', 1),
('Docker', 'docker', 1), ('Git', 'git', 1), ('Linux', 'linux', 1),
('Réseaux', 'reseaux', 1), ('Cybersécurité', 'cybersecurite', 1);

-- Plans
INSERT INTO plan (nom, slug, description, prix, duree_jours, nb_offres, mise_en_avant, duree_offre_jours, ordre_affichage) VALUES
('Basic', 'basic', 'Idéal pour débuter', 10.00, 30, 1, FALSE, 30, 1),
('Standard', 'standard', 'Pour recruter régulièrement', 40.00, 30, 5, FALSE, 30, 2),
('Premium', 'premium', 'Visibilité maximale', 100.00, 30, 20, TRUE, 60, 3),
('Entreprise', 'entreprise', 'Illimité pour les gros recruteurs', 250.00, 30, -1, TRUE, 60, 4);

-- users
INSERT INTO utilisateur (email, mot_de_passe, role, statut, email_verifie) VALUES
('gladkombigs@gmail.com', 'hashed_password_1', 'admin', 'actif', TRUE),
('jane.smith@gmail.com', 'hashed_password_2', 'IT', 'actif', TRUE);

-- =========================================================
-- FIN DU SCRIPT
-- =========================================================