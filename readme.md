# Swap

Swap est une application web développée avec **Symfony** qui connecte des **clients** et des **agents** pour la réalisation de tâches diverses. Le projet propose une interface publique pour la création et la consultation de tâches, ainsi qu’un back-office d’administration permettant de gérer les utilisateurs, les tags, les compétences et bien plus.

## Fonctionnalités

- **Inscription & Authentification**  
  - Inscription et connexion pour les clients et les agents.
  - Gestion sécurisée du mot de passe, avec possibilité de réinitialisation par email.

- **Gestion des tâches**  
  - Création, modification et suppression de tâches.
  - Ajout et suppression d’images (upload multiple) pour chaque tâche.
  - Filtrage des tâches par tags.

- **Messagerie et Conversations**  
  - Espace de messagerie pour faciliter la communication entre clients et agents.
  - Historique des conversations.

- **Espace Administration**  
  - Interface d’administration (basée sur EasyAdmin ou custom) pour la gestion des utilisateurs, des tags, et des compétences.
  - Possibilité d’éditer, supprimer et consulter les entités.

- **Gestion des propositions**  
  - Les agents peuvent soumettre une proposition de prix pour la réalisation d’une tâche.
  - Suivi du statut des propositions (pending, annulé, terminé…).

- **Avis et évaluations**  
  - Les clients peuvent laisser un avis et noter les agents après l’exécution d’une tâche.

- **Interface Moderne et Responsive**  
  - Utilisation de **Tailwind CSS** pour un design moderne et adaptable.
  - Interactions dynamiques avec **Alpine.js**.

## Prérequis

- **PHP 8.1** (ou version supérieure compatible avec Symfony)
- **Composer** (pour la gestion des dépendances PHP)
- **Node.js et npm** (pour compiler les assets avec Tailwind CSS)
- Un serveur de base de données compatible (MySQL, PostgreSQL, etc.)

## Installation

1. **Cloner le dépôt**

   ```bash
   git clone https://github.com/votre-utilisateur/swap.git
   cd swap
   ```

2. **Installer les dépendances PHP**

   ```bash
   composer install
   ```

3. **Installer les dépendances Node**

   ```bash
   npm install
   ```

4. **Compiler les assets**

   ```bash
   npm run dev
   ```

5. **Configurer l’environnement**

   Copie le fichier `.env` en `.env.local` et ajuste les paramètres (base de données, mailer, etc.) :

   ```bash
   cp .env .env.local
   # Modifier le fichier .env.local en fonction de tes paramètres
   ```

6. **Créer la base de données et exécuter les migrations**

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

7. **Charger les fixtures (facultatif)**

   Pour pré-remplir la base de données avec des données de test :

   ```bash
   php bin/console doctrine:fixtures:load
   ```

## Configuration

- **Variables d’environnement**  
  Le fichier `.env` (et son équivalent local `.env.local`) contient les variables de configuration essentielles, notamment :
  - `DATABASE_URL` : URL de connexion à la base de données.
  - `MAILER_DSN` : Paramètres du mailer pour l’envoi d’emails (par exemple, pour la réinitialisation du mot de passe).
  - `APP_SECRET` : Clé secrète de l’application.

- **Assets**  
  La gestion des styles se fait avec **Tailwind CSS**. La configuration se trouve dans le fichier `tailwind.config.js`.

## Utilisation

- **Interface Publique**  
  Rendez-vous sur la page d’accueil pour consulter les tâches existantes.  
  Les visiteurs peuvent filtrer les tâches par tags et consulter les détails d’une tâche.

- **Inscription & Connexion**  
  - Les utilisateurs peuvent s’inscrire en tant que **client** ou **agent** via des formulaires dédiés.
  - Un système de réinitialisation de mot de passe est disponible en cas d’oubli.

- **Création de Tâches**  
  Une fois connectés, les clients peuvent créer des tâches en remplissant un formulaire (titre, description, images, tags…).

- **Messagerie**  
  L’interface de messagerie permet aux utilisateurs de gérer leurs conversations et de communiquer efficacement.

- **Administration**  
  Les administrateurs disposent d’un tableau de bord pour gérer :
  - Les utilisateurs (création, modification, suppression)
  - Les tags et compétences
  - Les tâches et propositions

## Tests

Le projet utilise **PHPUnit** pour les tests unitaires et fonctionnels. Pour lancer les tests :

   ```bash
   php bin/phpunit
   ```

Le fichier `tests/bootstrap.php` s’assure que l’environnement de test est correctement initialisé (chargement de l’autoloader et configuration de l’environnement).

## Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. **Forker le dépôt**
2. **Créer une branche** pour ta fonctionnalité ou correction :

   ```bash
   git checkout -b feature/ma-nouvelle-fonctionnalite
   ```

3. **Committer tes changements**

   ```bash
   git commit -am 'Ajout d’une nouvelle fonctionnalité'
   ```

4. **Pousser ta branche**

   ```bash
   git push origin feature/ma-nouvelle-fonctionnalite
   ```

5. **Ouvrir une Pull Request** sur GitHub.
