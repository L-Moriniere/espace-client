# MGP Espace Client

Ce projet est une application basée sur le framework Symfony version **7.3.4**.

## ⚙️ **Installation**

### **Prérequis :**

- **Docker** et **Docker Compose** (préconisé pour ce projet).
- **PHP >= 8.0** et **Composer** (si installation sans Docker).
- **Node.js** avec **npm** (pour gérer les dépendances front-end).

### **Étapes pour démarrer le projet :**

1. Installez les dépendances PHP :
   ```bash
   make install
   ```
   Cela installera les dépendances Symfony et VueJS

   Ou sinon
   ```bash
   composer install
   cd frontend
   npm install
   ```

2. Générer les clés JWT :
   ```bash
   make keys
   ```

3. Créer la BDD de test
   ```bash
   make db-test
   ```
   
4. Lancer les conteneurs Docker :
   ```bash
   docker-compose up -d
   ```
   ou
    ```bash
   make up
   ```
   
### Autres commandes utiles :
- Pour arrêter les conteneurs :
  ```bash
  docker-compose down
  ```
  ou
  ```bash
  make down
  ```

- Charger les fixtures de test :
  ```bash
  make fixtures
  ```
  
- Lancer la commande Cron pour voir le nombre d'utilisateurs connectés:
    ```bash
    make users
    ```

