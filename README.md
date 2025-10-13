# MGP Espace Client

Ce projet est une application basée sur le framework Symfony version **7.3.4**.

## ⚙️ **Installation**

### **Prérequis :**

- **Docker** et **Docker Compose** (préconisé pour ce projet).
- **PHP >= 8.0** et **Composer** (si installation sans Docker).
- **Node.js** avec **npm** (pour gérer les dépendances front-end).

### **Étapes pour démarrer le projet :**

1. Clonez le dépôt :
   ```bash
   git clone <url-du-depot>
   cd <nom-du-dossier>
   ```

2. Lancez les conteneurs Docker :
   ```bash
   docker-compose up -d
   ```

3. Installez les dépendances PHP :
   Si vous utilisez Docker, ouvrez un shell dans le conteneur Symfony :
   ```bash
   docker exec -it <nom_du_conteneur_php> bash
   composer install
   ```
   Ou, si vous n'utilisez pas Docker :
   ```bash
   composer install
   ```

4. Installez les dépendances npm :
   ```bash
   npm install
   ```

5. Compilez les fichiers CSS avec TailwindCSS :
   ```bash
   npm run build
   ```

6. Configurez la base de données :
    - L'application utilise **SQLite** par défaut. Le fichier de configuration se trouve dans `.env`.
      Si nécessaire, changez la configuration pour une autre base de données (MySQL, PostgreSQL, etc.).

    - Initialisez la base :
      ```bash
      symfony console doctrine:database:create
      symfony console doctrine:migrations:migrate
      ```

7. Démarrez le serveur Symfony en mode développement :
   ```bash
   symfony serve
   ```
   Ou si vous utilisez Docker et exposez un port HTTP (par exemple, `http://localhost:8000`).

---

## ⚙️ **Configuration**

### **Fichier `.env`**

Voici les principales variables d'environnement que vous pouvez ajuster dans votre fichier `.env` :
```env
### Base de données (par défaut SQLite)
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

### JWT (clé privée et publique)
JWT_SECRET_KEY=/path/to/private.pem
JWT_PUBLIC_KEY=/path/to/public.pem
JWT_PASSPHRASE=your_secret_phrase

### Mailer
MAILER_DSN=smtp://localhost

### Application
APP_ENV=dev
APP_SECRET=your_secret_key
```

Pour les bases MySQL ou PostgreSQL, remplacez `DATABASE_URL` comme suit :
- MySQL :
  ```
  DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
  ```
- PostgreSQL :
  ```
  DATABASE_URL="pgsql://db_user:db_password@127.0.0.1:5432/db_name"
  ```

---

## 🛠️ **Commandes principales**

### **Symfony console :**
- Lancer le serveur Symfony :
  ```bash
  symfony serve
  ```
- Créer une migration après modification du schéma :
  ```bash
  symfony console doctrine:migrations:diff
  ```
- Appliquer les migrations sur la base de données :
  ```bash
  symfony console doctrine:migrations:migrate
  ```
- Créer un utilisateur via une commande personnalisée (exemple) :
  ```bash
  symfony console app:create-user --email=email@example.com --password=your_password
  ```

### **npm :**
- Installer les packages :
  ```bash
  npm install
  ```
- Lancer le mode de développement (watch) avec TailwindCSS :
  ```bash
  npm run dev
  ```
- Compiler les fichiers CSS pour la production :
  ```bash
  npm run build
  ```

---

## 📂 **Architecture du projet**

### **Backend :**
