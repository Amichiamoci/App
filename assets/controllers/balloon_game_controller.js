import { Controller } from '@hotwired/stimulus';
import { Baloon } from '../baloon.js';

function RandomInt(max) {
    if (max < 0) return 0;
    return Math.floor(max * Math.random());
}

export default class extends Controller {
    static targets = ['game', 
                      'counter', 
                      'restartButton', 
                      'baloonKeeper', 
                      'errorBar'];
    static values = {
        imagePaths: Object,
    };

    connect() {
        this.counter = 0;
        this.mistakes = 0;
        this.isGameRunning = false;
        this.gameLoopId = null;
        this.previousGameCall = performance.now();

        /**
         * @type Baloon[]
         */
        this.baloons = [];
        this.speedYStart = -0.6;
        this.minDelayBetweenSpawns = 1500;
        this.nightDayInterval = null;
        this.hasInitialized = false;
        this.gameContainer = this.element; // The element with data-controller="balloon-game"

        // Setup event listeners
        this.onKeyPress = this.handleKeyPress.bind(this);
        this.onRestart = this.restart.bind(this);
        this.onVisibilityChange = this.handleVisibilityChange.bind(this);
        this.onStartGame = this.start.bind(this);

        document.addEventListener('keypress', this.onKeyPress);
        this.restartButtonTarget.addEventListener('click', this.onRestart);
        document.addEventListener('visibilitychange', this.onVisibilityChange);
        document.addEventListener('offline-game:start', this.onStartGame);

        this.preloadAssets();
    }

    disconnect() {
        document.removeEventListener('keypress', this.onKeyPress);
        this.restartButtonTarget.removeEventListener('click', this.onRestart);
        document.removeEventListener('visibilitychange', this.onVisibilityChange);
        document.removeEventListener('offline-game:start', this.onStartGame);

        if (this.gameLoopId) {
            cancelAnimationFrame(this.gameLoopId);
        }
        if (this.nightDayInterval) {
            clearInterval(this.nightDayInterval);
        }
    }

    isGamePanelVisible() {
        // Check if the game container is visible in the DOM
        return this.gameContainer.offsetParent !== null;
    }

    initializeGame() {
        this.generateStars(30);
        this.generateClouds(5);
        this.generateSun();
        this.generateMoon();
        this.generateBaloons(5, { x: 0, y: -0.45 });
        this.showBaloons();
        this.startNightAndDay();
        setTimeout(() => this.addNewBaloons(3), 5000);
    }

    start() {
        if (!this.hasInitialized) {
            this.initializeGame();
            this.hasInitialized = true;
        }

        if (!this.isGameRunning) {
            this.isGameRunning = true;
            this.previousGameCall = performance.now();
            this.gameLoop();
        }
    }

    generateStars(count) {
        const stars = [];
        for (let i = 0; i < count; i++) {
            let x = 0, y = 0, okay = true;
            do {
                x = RandomInt(95) + 2;
                y = RandomInt(93) + 5;
                okay = true;
                for (const star of stars) {
                    if (Math.pow(x - star.x, 2) + Math.pow(y - star.y, 2) < 7) {
                        okay = false;
                        break;
                    }
                }
            } while (!okay);
            stars.push({ x: x, y: y });
        }

        for (const coords of stars) {
            const star = document.createElement('img');
            star.src = this.getAssetUrl('star.svg');
            star.alt = 'Stella';
            star.className = 'star';
            star.style.left = coords.x.toFixed(2) + '%';
            star.style.top = coords.y.toFixed(2) + '%';
            star.loading = 'lazy';
            this.gameTarget.appendChild(star);
        }
    }

    generateClouds(count) {
        for (let i = 0; i < count; i++) {
            const cloud = document.createElement('img');
            cloud.src = this.getAssetUrl('cloud.svg');
            cloud.alt = 'Nuvola';
            cloud.className = 'cloud';
            cloud.style.top = (RandomInt(82) + 10) + '%';
            cloud.loading = 'lazy';
            if (RandomInt(1024) % 2 === 0) {
                cloud.style.left = (RandomInt(50) - 50) + 'vw';
            } else {
                cloud.style.left = (RandomInt(50) + 100) + 'vw';
                cloud.classList.add('inverted');
            }
            this.gameTarget.appendChild(cloud);
        }
    }

    generateSun() {
        const sun = document.createElement('img');
        sun.id = 'sun';
        sun.src = this.getAssetUrl('sun.svg');
        sun.alt = 'Sole';
        this.gameTarget.appendChild(sun);
    }

    generateMoon() {
        const moon = document.createElement('img');
        moon.id = 'moon';
        moon.src = this.getAssetUrl('moon.svg');
        moon.alt = 'Luna';
        this.gameTarget.appendChild(moon);
    }

    generateBaloons(count, speed) {
        if (count == null || isNaN(count)) return;

        const newBaloons = [];
        for (let i = 0; i < count; i++) {
            const created = this.createDefaultBaloon().clone(this.gameTarget);
            created.speed.x = speed.x;
            created.speed.y = speed.y;
            created.onHit = () => {
                this.counter++;
                this.counterTarget.innerHTML = this.counter;
            };
            newBaloons.push(created);
        }
        this.baloons.push(...newBaloons);
    }

    createDefaultBaloon() {
        const defaultBaloons = [
            new Baloon('', this.gameTarget, 'F2C54B'),
            new Baloon('', this.gameTarget, 'DC5455'),
            new Baloon('', this.gameTarget, '89E31F'),
            new Baloon('', this.gameTarget, 'F68FB4'),
            new Baloon('', this.gameTarget, '26FFCE'),
            new Baloon('', this.gameTarget, 'CACCC9'),
            new Baloon('', this.gameTarget, '00C6C5'),
            new Baloon('', this.gameTarget, 'FFFFFF'),
        ];

        // Add baloons with logos from baloon-keeper
        const logos = Array.from(this.baloonKeeperTarget.children).map(img => {
            let motto = '';
            if (img.hasAttribute('data-motto')) {
                motto = img.getAttribute('data-motto');
            }
            const bg = img.getAttribute('data-bg');
            let dist = 0;
            if (img.hasAttribute('data-dist')) {
                dist = Number(img.getAttribute('data-dist'));
            }
            return new Baloon(img.src, this.gameTarget, bg, motto, dist);
        });

        defaultBaloons.push(...logos);
        return defaultBaloons[RandomInt(defaultBaloons.length)];
    }

    showBaloons() {
        for (let baloon of this.baloons) {
            if (baloon.isHidden()) {
                baloon.show();
            }
        }
    }

    addNewBaloons(howMany) {
        if (!this.isGameRunning) return;

        this.generateBaloons(howMany, { x: 0, y: this.speedYStart });
        this.showBaloons();
        this.speedYStart = Math.max(this.speedYStart - 0.075, -1.3);
        this.minDelayBetweenSpawns = Math.max(this.minDelayBetweenSpawns - 25, 500);
        setTimeout(() => this.addNewBaloons(RandomInt(2) + 1), RandomInt(2800) + this.minDelayBetweenSpawns);
    }

    addMistake() {
        if (this.mistakes < 10) {
            const div = document.createElement('div');
            div.innerHTML = `<img src="${this.getAssetUrl('red-x.svg')}" alt="Errore" />`;
            this.errorBarTarget.appendChild(div);
        }
        this.mistakes++;

        if (this.mistakes < 10) return;

        this.isGameRunning = false;
        setTimeout(() => this.baloons.forEach(b => b.removeDiv()), 1500);
        setTimeout(() => {
            this.errorBarTarget.innerHTML = '';
            this.restartButtonTarget.style.display = 'block';
        }, 4000);
    }

    gameLoop = () => {
        const now = performance.now();
        const timeUpdate = (now - this.previousGameCall) / 10;
        this.previousGameCall = now;

        if (!this.isGameRunning)
            return;

        for (let baloon of this.baloons) {
            baloon.move(timeUpdate);
            baloon.updatePos();
        }

        this.cleanupHiddenBaloons();

        for (let i = 0; i < this.baloons.length; i++) {
            if (this.baloons[i].isToDelete(this.gameTarget.clientWidth, this.gameTarget.clientHeight)) {
                if (this.baloons[i].isGood) {
                    if (this.baloons[i].isOutTop()) {
                        this.addMistake();
                    }
                } else {
                    if (this.baloons[i].isOutBottom(this.gameTarget.clientHeight)) {
                        this.addMistake();
                    }
                }
            }
        }

        // Continue loop if game is still running and visible
        if (this.isGamePanelVisible()) {
            this.gameLoopId = requestAnimationFrame(this.gameLoop);
        } else {
            this.isGameRunning = false;
        }
    }

    cleanupHiddenBaloons() {
        for (let i = 0; i < this.baloons.length; i++) {
            if (this.baloons[i].isHidden()) {
                this.baloons[i].removeDiv();
                this.baloons.splice(i, 1);
                i--;
            }
        }
    }

    handleKeyPress(evt) {
        if (evt.key === ' ') {
            evt.preventDefault();
            if (this.isGameRunning) {
                this.isGameRunning = false;
            } else {
                this.isGameRunning = true;
                this.previousGameCall = performance.now();
                this.gameLoop();
            }
        }
    }

    handleVisibilityChange() {
        if (this.isGamePanelVisible()) {
            // Visible
            this.isGameRunning = true;
            this.previousGameCall = performance.now();
            this.gameLoop();
        } else {
            // Hidden
            this.isGameRunning = false;
            if (this.gameLoopId) {
                cancelAnimationFrame(this.gameLoopId);
            }
        }
    }

    startNightAndDay() {
        const gameClasses = this.gameContainer.classList;
        gameClasses.add('day');

        this.nightDayInterval = setInterval(() => {
            if (gameClasses.contains('day')) {
                gameClasses.remove('day');
                gameClasses.add('night');
            } else {
                gameClasses.remove('night');
                gameClasses.add('day');
            }
        }, 30 * 1000);
    }

    preloadAssets() {
        if (!this.imagePathsValue)
            return;

        const urls = Array.isArray(this.imagePathsValue)
            ? this.imagePathsValue
            : Object.values(this.imagePathsValue);

        urls.forEach((src) => {
            const image = new Image();
            image.src = src;
        });
    }

    getAssetUrl(fileName) {
        if (this.imagePathsValue && this.imagePathsValue[fileName])
            return this.imagePathsValue[fileName];

        return `${this.assetPathValue}/${fileName}`;
    }

    restart() {
        this.restartButtonTarget.style.display = 'none';
        this.mistakes = 0;
        this.counter = 0;
        this.counterTarget.innerHTML = '0';
        this.speedYStart = -0.6;
        this.minDelayBetweenSpawns = 1500;

        this.baloons.forEach(b => b.removeDiv());
        this.baloons = [];

        this.generateBaloons(5, { x: 0, y: -0.45 });
        this.isGameRunning = true;
        this.showBaloons();
        setTimeout(() => this.addNewBaloons(3), 5000);
        this.previousGameCall = performance.now();
        this.gameLoop();
    }
}
