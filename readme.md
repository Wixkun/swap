# 📌 Swap

**Lien** : https://main-bvxea6i-zkvc3qbnbenee.fr-3.platformsh.site

Ajouts après soutenance:

- Refonte de la nav bar
- Ajout de la page profil
- Ajout de la page conversation
- Ajouts des entités Skill et Review + Modifications d'anciennes entités
- Possibilité pour un agent de devenir customer et inversement
- Possibilité d'accepter une offre depuis les messages
- Controller dédie pour une api avec serializer et denormalizer ( /api/agent avec POST et GET)
- Ajout de paiement par Stripe après l'acceptation d'une offre et envoi de facture par mail (tous les mails sur Mailtrap)
- Hébergement de l'application via Platform.sh
___

**Swap** est une application web qui connecte des clients et des agents pour la réalisation de tâches diverses.

## 🚀 Fonctionnalités principales

- **Authentification & Rôles** : Clients et agents avec rôles spécifiques.  
- **Gestion des tâches** : Création, modification et suppression de tâches.  
- **Messagerie** : Communication entre clients et agents.  
- **Propositions & Évaluations** : Agents soumettent des offres, clients laissent des avis.  
- **Admin Dashboard** : Gestion des utilisateurs, tâches, tags et compétences.  

**Users** utilisables pour des tests

- **Customer:** customer@gmail.com : customer12345678
- **Agent:** agent@gmail.com : agent12345678
- **Admin:** admin@gmail.com : admin12345678

