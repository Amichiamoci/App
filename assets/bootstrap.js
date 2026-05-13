import { startStimulusApp } from '@symfony/stimulus-bundle';
import OfflineGameController from './controllers/offline_game_controller.js';
import BalloonGameController from './controllers/balloon_game_controller.js';

const app = startStimulusApp();
app.register('offline-game', OfflineGameController);
app.register('balloon-game', BalloonGameController);
