<div align="center">

# 🎬 Lantern.ai

### AI-Powered Movie Script Editor with Scene Analysis, Story Structuring & Creative Assistance

<p align="center">
  A powerful AI-assisted platform built for screenwriters, filmmakers, storytellers, and creators to write, refine, analyze, and structure movie scripts more effectively.
</p>

<p align="center">
  <a href="https://github.com/Lantern-ai/Lantern.ai/stargazers">⭐ Star</a>
  ·
  <a href="https://github.com/Lantern-ai/Lantern.ai/issues">🐛 Report Bug</a>
  ·
  <a href="https://github.com/Lantern-ai/Lantern.ai/issues">💡 Request Feature</a>
</p>

</div>

---

## 📌 Overview

**Lantern.ai** is an **AI-powered movie script editor platform** designed to help writers and creators craft better screenplays with the support of intelligent editing, story analysis, and creative structuring tools.

It is built for storytelling workflows where **dialogue, pacing, scenes, emotional flow, and narrative structure** matter — such as:

- Movie scripts
- Short film scripts
- Web series scripts
- Screenplay drafts
- Dialogue writing
- Scene planning
- Story outlining

Lantern.ai goes beyond a normal text editor by combining **AI writing assistance**, **script analysis**, and **story visualization tools** to help creators write more cinematic and compelling stories.

---

## 🚀 Core Features

### ✍️ AI Movie Script Editing
Enhance your screenplay with AI-powered editing support for:

- dialogue refinement
- screenplay formatting
- pacing improvements
- emotional tone
- scene transitions
- readability and clarity

### 🎭 Scene & Story Analysis
Analyze your script to better understand:

- scene flow
- character dialogue balance
- pacing and tension
- screenplay structure
- narrative consistency
- story progression

### 🧠 Story Structuring & Idea Mapping
Break your story into a more organized creative workflow by helping users:

- visualize scene relationships
- map story arcs
- structure acts and sequences
- brainstorm plot progression
- organize screenplay ideas clearly

### ⚡ Modern Web App Experience
Built as a full-stack web application with a clean development workflow and scalable architecture for modern creative tools.

---

## 🛠️ Tech Stack

Based on the repository structure, the project is built using:

### Backend
- **PHP**
- **Laravel**

### Frontend
- **JavaScript**
- **Blade / Laravel Views**
- **Vite**

### Development Tools
- **Composer**
- **NPM**
- **Laravel Artisan**

---

## 📂 Project Structure

```bash
Lantern.ai/
│
├── app/                # Core application logic
├── bootstrap/          # Framework bootstrapping
├── config/             # App configuration files
├── database/           # Migrations / seeders / factories
├── public/             # Public assets and entry point
├── resources/          # Views, frontend assets, UI resources
├── routes/             # Application routes
├── storage/            # Logs, cache, uploaded/generated files
├── tests/              # Application tests
│
├── .env.example        # Environment configuration example
├── artisan             # Laravel CLI entry
├── composer.json       # PHP dependencies
├── package.json        # Frontend dependencies
├── vite.config.js      # Vite configuration
└── README.md
⚙️ Installation
```
Follow these steps to run Lantern.ai locally.

1) Clone the repository
git clone https://github.com/Lantern-ai/Lantern.ai.git
```cd Lantern.ai```
2) Install PHP dependencies
composer install
3) Install frontend dependencies
npm install
4) Set up environment variables

Copy the example environment file:

cp .env.example .env
On Windows CMD:
copy .env.example .env
On PowerShell:
Copy-Item .env.example .env
5) Generate application key
php artisan key:generate
6) Configure database

Update your .env file with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lantern_ai
DB_USERNAME=root
DB_PASSWORD=
```
You can also use SQLite if preferred.

7) Run database migrations
```php artisan migrate```

8) Start the development servers

Run Laravel backend:
```
php artisan serve
```

Run Vite frontend:
```
npm run dev
```
Now open:
```
http://127.0.0.1:8000
```
🔐 Environment Variables

Depending on your AI integration, your .env may include keys like:
```
APP_NAME=Lantern.ai
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lantern_ai
DB_USERNAME=root
DB_PASSWORD=

# Example AI/API configs
OPENAI_API_KEY=
GEMINI_API_KEY=
ANTHROPIC_API_KEY=
```
Add only the API keys your project actually uses.

🧪 Running Tests

To run backend tests:

php artisan test

Or:

vendor/bin/phpunit
💡 Use Cases

Lantern.ai can be useful for:

Screenwriters writing feature film scripts
Filmmakers structuring short film ideas
Content creators planning cinematic storytelling
Students learning screenplay writing
Writers improving scenes and dialogue
Creative teams brainstorming story structure collaboratively
🧠 How It Works

A typical Lantern.ai workflow might look like this:

Write or paste your movie script
Refine scenes and dialogue with AI
Analyze structure, pacing, and flow
Organize ideas and story progression visually
Use the improved screenplay for production, pitching, or development

This makes Lantern.ai useful not just as a writing tool, but as a creative screenplay development assistant.

🎯 Vision

Lantern.ai aims to become more than just a screenplay editor.

The goal is to create a creative workspace where writers can:

write cinematic stories,
improve screenplay quality,
organize scenes and narrative flow,
and build stronger scripts faster.
🌱 Future Improvements

Planned or possible future features:

 AI character consistency tracking
 Screenplay formatting assistant
 Real-time collaborative writing
 Scene-by-scene breakdown generation
 Character arc visualization
 Export to PDF / Final Draft style formats
 Version history for script drafts
 Genre-based AI writing modes
 Voice-to-script ideation
 Production planning integration
🤝 Contributing

Contributions are welcome!

If you'd like to improve Lantern.ai:

Fork the repository
Create a new branch
git checkout -b feature/your-feature-name
Commit your changes
git commit -m "Add: your feature"
Push to your branch
git push origin feature/your-feature-name
Open a Pull Request
🐛 Issues

If you find a bug or want to suggest a feature, feel free to open an issue:

👉 https://github.com/Lantern-ai/Lantern.ai/issues

📄 License

This project is open-source and available under the MIT License.

If you haven't added a license yet, you can create one in GitHub by adding a LICENSE file.

👨‍💻 Author / Team

Built with passion by the Lantern.ai team.

If you’re the main maintainer, you can customize this section like:

## 👨‍💻 Maintainers

**Vijay Sivadas V S**
- GitHub: [@vijaysivadas](https://github.com/vijaysivadas)
- Portfolio: https://vijay.to

⭐ Support



If you like this project, please consider:

⭐ Starring the repository
🍴 Forking it
🧠 Sharing feedback
🚀 Contributing to the project
<div align="center">
Built for writers who want to turn ideas into cinematic stories.

Lantern.ai 🎬

</div> 
