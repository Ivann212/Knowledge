# Knowledge
# Knowledge - Plateforme d'E-learning

**Knowledge** est une plateforme d'e-learning qui propose une large gamme de cours en ligne, de leçons et de certifications. Elle permet aux utilisateurs d'explorer des formations, d'acheter des cours, de suivre leur progression et d'obtenir des certifications. Les administrateurs peuvent gérer les utilisateurs, les formations et d'autres ressources via une interface d'administration intuitive.

## Fonctionnalités

- **Tableau de bord utilisateur** : Les utilisateurs peuvent naviguer entre différents thèmes, consulter les formations et acheter des leçons ou des formations complètes.
- **Suivi de progression des formations** : Les utilisateurs peuvent marquer les leçons comme terminées, suivre leur progression et obtenir des certifications.
- **Panel administrateur** : Le panel d'administration est alimenté par EasyAdmin et permet aux administrateurs de gérer les utilisateurs, les formations, les thèmes et les leçons.
- **Intégration Stripe** : Intégration avec Stripe pour gérer les paiements et les transactions de manière sécurisée.
- **Accès basé sur les rôles** : Les utilisateurs reçoivent un rôle apres vérification de leur compte zfin de pouvoir accéder aux ressources de l'application

## Stack Technologique

- **EasyAdmin** : Un puissant outil d'interface d'administration pour gérer le contenu de la plateforme.
- **Stripe** : Passerelle de paiement utilisée pour gérer les achats et transactions.

## Prérequis
Wamp64, lien d'installation : https://www.wampserver.com/

## Installation

Pour faire fonctionner cette application localement, suivez les étapes suivantes :

1. **Téléchargez le repository** :
    https://github.com/Ivann212/Knowledge

2. **Installez les dépendances** :
    ```bash
    composer install
    ```

3. **Téléchargez la base de donnée** :
   téléchargez le fichier bddKnowledge.sql dans votre phpmyadmin

4. **Suivez les instructions plus bas pour la base de données** :
   1. **Téléchargez le fichier `database.sql`** situé à la racine du projet.
    2. **Accédez à votre PhpMyAdmin** (ou tout autre outil de gestion de base de données compatible avec MySQL).
    3. **Créez une nouvelle base de données** nommée `knowledge`.
    4. **Importez le fichier `knowledge.sql`** dans la base de données via l'interface PhpMyAdmin :
    - Allez dans votre base de données `knowledge`.
    - Allez dans l'onglet "Importer".
    - Sélectionnez le fichier `database.sql` et cliquez sur "Exécuter".
    5. **Configurez les informations de connexion** à la base de données dans le fichier `.env` du projet :
    - Ouvrez le fichier `.env` dans la racine du projet.
    - Assurez-vous que la variable `DATABASE_URL` correspond aux informations de votre base de données MySQL.

        Exemple de configuration dans `.env` :

        ```
        DATABASE_URL="mysql://root:root@127.0.0.1:3306/knowledge"
        ```

        - Remplacez `root` par votre nom d'utilisateur MySQL et `root` par votre mot de passe (si vous en avez un).
        - Assurez-vous que le nom de la base de données est correct (`knowledge`).

    6. **Lancez l'application** :
    - Après avoir configuré la base de données, vous pouvez lancer l'application en suivant les instructions d'installation du projet.


5. **Lancez l'application** :

    symfony server:start


   L'application devrait maintenant être accessible à l'adresse `http://127.0.0.1:8000`.

## Utilisation

- **Inscription des utilisateurs** : Les utilisateurs peuvent s'inscrire et se connecter pour accéder aux formations qu'ils ont achetées.
- **Parcourir les thèmes** : Les utilisateurs peuvent explorer les différents thèmes, qui contiennent des formations.
- **Acheter des formations** : Les utilisateurs peuvent acheter des formations complètes, des leçons individuelles ou des cursus entiers via Stripe.
- **Suivi de la progression** : Les utilisateurs peuvent marquer les leçons comme terminées et suivre leur progression dans la formation.

### Fonctionnalités Admin

Le panel d'administration est accessible à l'URL `/admin` et propose les fonctionnalités suivantes :

- Gérer les **Utilisateurs**, **Formations**, **Leçons**, et **Thèmes**.
- Créer, modifier ou supprimer des formations et des leçons.
- Attribuer et gérer les rôles des utilisateurs (par exemple, client, admin).
- Consulter les achats et suivre la progression des utilisateurs.

## Tests

Pour tester le flux de paiement, vous pouvez utiliser le mode test de **Stripe** avec des cartes de test. Assurez-vous que les variables d'environnement pour Stripe sont correctement configurées dans le fichier `.env`.

### Exemple de carte de test :
- Numéro de carte : `4242 4242 4242 4242`
- Date d'expiration : `Toute date future`
- CVC : `Tous les 3 chiffres`
