# Connexion Google — EMPLOI_RDC

La connexion e-mail/mot de passe est active. Le parcours Google utilise le code d’autorisation, `state`, `nonce`, PKCE et la vérification RSA des ID tokens. Il ne conserve aucun jeton d’accès Google.

## Configuration Google

1. Dans Google Cloud Console, créer ou choisir un projet puis configurer Google Auth Platform (nom, e-mail de support, audience). En mode test, ajouter les adresses Google autorisées comme utilisateurs de test.
2. Créer un client OAuth de type **Application Web**.
3. Enregistrer exactement cette URI de redirection pour XAMPP :

   `http://localhost/Emploi_RDC/public/?page=google-callback`

4. Renseigner `client_id` et `client_secret` dans `config/google.local.php` en utilisant le modèle `config/google.local.php.example`. Ce fichier est ignoré par Git et son répertoire est interdit via HTTP. Les variables d’environnement `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` et `GOOGLE_REDIRECT_URI` sont également acceptées ; le fichier local est prioritaire.
5. Ouvrir la connexion et cliquer sur **Se connecter avec Google**. Autoriser le compte. Au premier accès, compléter le profil candidat ou entreprise ; les accès suivants ouvrent directement le compte.

Le mode entreprise doit être choisi sur la page d’inscription avant de continuer avec Google. Un nouveau compte Google ne peut pas recevoir de rôle administrateur.

Pour une adresse déjà inscrite par mot de passe, se connecter d’abord avec le mot de passe puis cliquer sur **Associer Google à mon compte**, dans les dix minutes suivant la connexion. L’adresse Google doit être identique. Aucun rapprochement automatique par e-mail n’est effectué.

## Base de données et environnement

La migration `database/auth-google.sql` ajoute le rôle `company` sans retirer les anciens rôles et crée la table des identités Google. Elle a été appliquée à la base XAMPP locale. Sur une autre installation, l’appliquer à la base `emploi_rdc` avant le premier essai. Ne pas réimporter `sql.sql` : il contient un `DROP DATABASE`.

PHP doit disposer de cURL, OpenSSL, PDO MySQL et d’un magasin de certificats valide. Ne jamais désactiver la vérification TLS en cas d’erreur de certificat. Sur un domaine public, utiliser HTTPS, mettre à jour `BASE_URL` et l’URI de redirection locale/Google en conséquence.

Les comptes de démonstration de `sql.sql` contiennent des chaînes telles que `hashed_password_1`, qui ne sont pas des mots de passe hachés valides. Ils ne permettent pas une connexion par mot de passe. Créer un compte via le formulaire pour tester une vraie connexion. Les comptes avec double authentification activée sont refusés tant que le second facteur n’est pas implémenté.

Les sessions durent au plus huit heures. Les tentatives de connexion sont limitées à dix échecs par adresse ou IP sur quinze minutes. La réinitialisation des mots de passe et la connexion persistante ne sont pas implémentées.

## Vérification

Depuis la racine du projet :

```
C:\xampp\php\php.exe tests/google-oauth.php
C:\xampp\php\php.exe tests/auth-http.php
```

Le premier test vérifie la signature et les claims sur des jetons signés avec une clé de test locale. Le second nécessite XAMPP et crée des comptes de test uniques, puis les supprime. Aucun de ces tests ne remplace un essai Google réel avec le projet OAuth configuré.

Documentation Google : https://developers.google.com/identity/openid-connect/openid-connect
